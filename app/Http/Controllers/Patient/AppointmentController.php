<?php
 
namespace App\Http\Controllers\Patient;
 
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AppointmentSlot;
use App\Models\HealthService;
use App\Models\QueueTicket;
use App\Models\User;
use App\Services\AppointmentScheduler;
use App\Services\EmailNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
 
class AppointmentController extends Controller
{
    private const MAX_BOOKING_DAYS_AHEAD = 30;
    private const MINIMUM_ADVANCE_MINUTES = 85;

    public function index(Request $request)
    {
        return view('patient.appointments.index', [
            'appointments' => Appointment::with(['doctor', 'service', 'slot'])
                ->where('patient_id', $request->user()->id)
                ->orderByDesc('scheduled_at')
                ->get(),
        ]);
    }
 
    public function create(Request $request)
    {
        $services = HealthService::syncPatientCatalog();
        $selectedServiceId = (int) $request->query('service_id');
        $selectedService = $services->firstWhere('id', $selectedServiceId);

        $doctors = $this->getDoctorsForService($selectedService);

        return view('patient.appointments.create', [
            'services' => $services,
            'doctors' => $doctors,
            'selectedServiceId' => $selectedServiceId,
        ]);
    }

    public function recommendations(Request $request, AppointmentScheduler $scheduler): JsonResponse
    {
        $data = $request->validate([
            'health_service_id' => ['required', Rule::exists('health_services', 'id')->where('is_active', true)],
            'doctor_id' => ['nullable', Rule::exists('users', 'id')->where('role', User::ROLE_DOCTOR)],
            'preferred_date' => $this->bookingDateRules(),
            'preferred_time' => ['nullable', 'date_format:H:i'],
        ]);

        $service = HealthService::findOrFail($data['health_service_id']);
        $preferredDate = Carbon::parse($data['preferred_date']);

        $recommendations = $scheduler->recommendSlots(
            $service,
            $preferredDate,
            $data['doctor_id'] ?? null,
            $data['preferred_time'] ?? null,
            3
        );

        return response()->json([
            'recommendations' => $recommendations,
        ]);
    }
 
    public function store(
        Request $request,
        EmailNotificationService $emailNotificationService
    )
    {
        $data = $request->validate([
            'health_service_id' => ['required', Rule::exists('health_services', 'id')->where('is_active', true)],
            'doctor_id' => ['nullable', Rule::exists('users', 'id')->where('role', User::ROLE_DOCTOR)],
            'preferred_date' => $this->bookingDateRules(),
            'preferred_time' => ['nullable', 'date_format:H:i'],
            'selected_slot_id' => ['required', 'exists:appointment_slots,id'],
            'notes' => ['nullable', 'string'],
        ]);
 
        $service = HealthService::findOrFail($data['health_service_id']);
        $slot = AppointmentSlot::query()->where('is_active', true)->find($data['selected_slot_id']);

        if (!$slot) {
            return back()->withErrors([
                'selected_slot_id' => 'The selected recommendation is no longer available. Please pick another option.',
            ])->withInput();
        }

        if (!empty($data['doctor_id']) && (int) $slot->doctor_id !== (int) $data['doctor_id']) {
            return back()->withErrors([
                'selected_slot_id' => 'Please select a recommended option that matches your preferred doctor.',
            ])->withInput();
        }

        $eligibleDoctorIds = User::doctorsForService($service->name)->pluck('id')->all();
        if (!in_array((int) $slot->doctor_id, array_map('intval', $eligibleDoctorIds), true)) {
            return back()->withErrors([
                'selected_slot_id' => 'The selected slot does not match this health service. Please choose another recommendation.',
            ])->withInput();
        }

        $activeCount = Appointment::query()
            ->where('appointment_slot_id', $slot->id)
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->count();

        if ($activeCount >= (int) $slot->capacity) {
            return back()->withErrors([
                'selected_slot_id' => 'The selected recommendation is full. Please choose another available option.',
            ])->withInput();
        }
 
        $scheduledAt = Carbon::parse(
            $slot->slot_date->format('Y-m-d') . ' ' . $slot->start_time
        );

        $slotEnd = Carbon::parse(
            $slot->slot_date->format('Y-m-d') . ' ' . $slot->end_time
        );

        if ($slotEnd->lessThanOrEqualTo($scheduledAt)) {
            return back()->withErrors([
                'selected_slot_id' => 'The selected slot time is invalid. Please choose another recommendation.',
            ])->withInput();
        }

        if ($service->duration_minutes && $scheduledAt->diffInMinutes($slotEnd) < $service->duration_minutes) {
            return back()->withErrors([
                'selected_slot_id' => 'The selected slot duration is not suitable for this service. Please choose another recommendation.',
            ])->withInput();
        }

        if (!$this->isAtLeast85MinutesFromNow($scheduledAt)) {
            return back()->withErrors([
                'selected_slot_id' => 'Selected time must be at least 85 minutes from now.',
            ])->withInput();
        }

        if (!$this->isWithinBookingHorizon($scheduledAt)) {
            return back()->withErrors([
                'selected_slot_id' => 'Appointments can only be booked up to 30 days in advance.',
            ])->withInput();
        }
 
        $scheduledAt = $scheduledAt->format('Y-m-d H:i:s');
 
        $appointment = DB::transaction(function () use ($request, $data, $slot, $scheduledAt) {
            $appointment = Appointment::create([
                'patient_id' => $request->user()->id,
                'doctor_id' => $slot->doctor_id,
                'health_service_id' => $data['health_service_id'],
                'appointment_slot_id' => $slot->id,
                'scheduled_at' => $scheduledAt,
                'status' => 'approved',
                'approved_at' => now(),
                'notes' => $data['notes'] ?? null,
                'check_in_token' => (string) Str::uuid(),
            ]);
 
            $issuedOn = $slot->slot_date->format('Y-m-d');
            $nextNumber = (int) QueueTicket::where('issued_on', $issuedOn)->max('number') + 1;
 
            QueueTicket::create([
                'appointment_id' => $appointment->id,
                'issued_on' => $issuedOn,
                'number' => $nextNumber,
            ]);
 
            $appointment->update(['queue_number' => $nextNumber]);
 
            return $appointment;
        });
 
        $emailNotificationService->sendBookingSuccess($appointment);

        return redirect()->route('patient.appointments.show', $appointment)
            ->with('status', 'Appointment booked and confirmed successfully. A confirmation email has been sent to your inbox.');
    }
 
    public function show(Request $request, Appointment $appointment)
    {
        $this->authorizeAppointment($request, $appointment);
 
        return view('patient.appointments.show', [
            'appointment' => $appointment->load(['doctor', 'service', 'slot', 'documents']),
        ]);
    }
 
    public function edit(Request $request, Appointment $appointment)
    {
        $this->authorizeAppointment($request, $appointment);
 
        if ($this->isLockedForPatientChanges($appointment)) {
            return redirect()->route('patient.appointments.index')
                ->withErrors(['appointment' => 'This appointment can no longer be rescheduled.']);
        }
 
        return view('patient.appointments.edit', [
            'appointment' => $appointment,
            'doctors' => User::where('role', User::ROLE_DOCTOR)->orderBy('name')->get(),
        ]);
    }
 
    public function update(
        Request $request,
        Appointment $appointment,
        AppointmentScheduler $scheduler,
        EmailNotificationService $emailNotificationService
    )
    {
        $this->authorizeAppointment($request, $appointment);
 
        $data = $request->validate([
            'preferred_date' => $this->bookingDateRules(),
            'preferred_time' => ['nullable', 'date_format:H:i'],
            'doctor_id' => ['nullable', Rule::exists('users', 'id')->where('role', User::ROLE_DOCTOR)],
        ]);

        if ($this->isLockedForPatientChanges($appointment)) {
            return redirect()->route('patient.appointments.index')
                ->withErrors(['appointment' => 'This appointment can no longer be rescheduled.']);
        }

        if (!$this->isAtLeast85MinutesFromNow($appointment->scheduled_at)) {
            return back()->withErrors([
                'preferred_date' => 'Rescheduling is only allowed at least 85 minutes before your current appointment.',
            ])->withInput();
        }
 
        $service = $appointment->service ?? HealthService::find($appointment->health_service_id);
        $preferredDate = Carbon::parse($data['preferred_date']);
 
        $slot = $service
            ? $scheduler->findBestSlot(
                $service,
                $preferredDate,
                $data['doctor_id'] ?? null,
                $data['preferred_time'] ?? null
            )
            : null;

        if (!$slot) {
            return back()->withErrors(['preferred_date' => 'No available slots found for the selected date.'])->withInput();
        }
 
        $scheduledAt = Carbon::parse(
            $slot->slot_date->format('Y-m-d') . ' ' . $slot->start_time
        );

        if (!$this->isAtLeast85MinutesFromNow($scheduledAt)) {
            return back()->withErrors([
                'preferred_date' => 'Selected time must be at least 85 minutes from now.',
            ])->withInput();
        }

        if (!$this->isWithinBookingHorizon($scheduledAt)) {
            return back()->withErrors([
                'preferred_date' => 'Appointments can only be booked up to 30 days in advance.',
            ])->withInput();
        }

        $scheduledAt = $scheduledAt->format('Y-m-d H:i:s');
 
        DB::transaction(function () use ($appointment, $slot, $scheduledAt) {
            $appointment->update([
                'appointment_slot_id' => $slot->id,
                'doctor_id' => $slot->doctor_id,
                'scheduled_at' => $scheduledAt,
                'status' => 'rescheduled',
                'approved_at' => now(),
                'reminder_hour_sent_at' => null,
                'reminder_fifteen_minutes_sent_at' => null,
            ]);
 
            $issuedOn = $slot->slot_date->format('Y-m-d');
            $nextNumber = (int) QueueTicket::where('issued_on', $issuedOn)->max('number') + 1;
 
            $ticket = $appointment->queueTicket;
            if ($ticket) {
                $ticket->update([
                    'issued_on' => $issuedOn,
                    'number' => $nextNumber,
                ]);
            } else {
                QueueTicket::create([
                    'appointment_id' => $appointment->id,
                    'issued_on' => $issuedOn,
                    'number' => $nextNumber,
                ]);
            }
 
            $appointment->update(['queue_number' => $nextNumber]);
        });

        $emailNotificationService->sendRescheduleNotice($appointment->fresh(['patient', 'doctor', 'service']));
 
        return redirect()->route('patient.appointments.show', $appointment)
            ->with('status', 'Appointment rescheduled and confirmed successfully.');
    }
 
    public function destroy(Request $request, Appointment $appointment)
    {
        $this->authorizeAppointment($request, $appointment);

        if ($this->isLockedForPatientChanges($appointment)) {
            return redirect()->route('patient.appointments.index')
                ->withErrors(['appointment' => 'This appointment can no longer be cancelled.']);
        }
 
        $appointment->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
 
        return redirect()->route('patient.appointments.index')->with('status', 'Appointment cancelled.');
    }
 
    private function getDoctorsForService(?HealthService $service): \Illuminate\Support\Collection
    {
        if (!$service) {
            return collect([]);
        }

        return User::doctorsForService($service->name)->map(function (User $doctor) {
            $doctor->image_url = asset('images/inside.jpg');
            return $doctor;
        });
    }

    private function authorizeAppointment(Request $request, Appointment $appointment): void
    {
        if ($appointment->patient_id !== $request->user()->id) {
            abort(403);
        }
    }

    private function isAtLeast85MinutesFromNow(Carbon $dateTime): bool
    {
        return $dateTime->greaterThanOrEqualTo(now()->addMinutes(self::MINIMUM_ADVANCE_MINUTES));
    }

    private function isLockedForPatientChanges(Appointment $appointment): bool
    {
        return $appointment->checked_in_at !== null || in_array($appointment->status, ['checked-in', 'cancelled'], true);
    }

    private function isWithinBookingHorizon(Carbon $dateTime): bool
    {
        return $dateTime->lessThanOrEqualTo(now()->addDays(self::MAX_BOOKING_DAYS_AHEAD)->endOfDay());
    }

    private function bookingDateRules(): array
    {
        return [
            'required',
            'date',
            'after_or_equal:today',
            'before_or_equal:' . now()->addDays(self::MAX_BOOKING_DAYS_AHEAD)->toDateString(),
        ];
    }
}