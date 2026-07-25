<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AppointmentSlot;
use App\Models\User;
use App\Services\EmailNotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AppointmentController extends Controller
{
    public function index(): View
    {
        $appointments = Appointment::with(['patient', 'doctor', 'service', 'slot'])
            ->orderByDesc('scheduled_at')
            ->get();

        return view('admin.appointments.index', [
            'appointments' => $appointments,
            'metrics' => [
                'upcoming' => $appointments
                    ->whereIn('status', ['approved', 'rescheduled', 'checked-in'])
                    ->where('scheduled_at', '>=', now())
                    ->count(),

                'requiresAction' => $appointments
                    ->whereIn('status', ['rejected', 'cancelled', 'no-show'])
                    ->count(),

                'completed' => $appointments
                    ->where('status', 'completed')
                    ->count(),
            ],
        ]);
    }

    public function downloadReport(Request $request)
    {
        $validated = $request->validate([
            'month' => ['required', 'date_format:Y-m'],
            'week' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $monthStart = Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $start = $monthStart->copy()->addDays(((int) $validated['week'] - 1) * 7);
        $end = $start->copy()->addDays(6);

        if ($end->greaterThan($monthEnd)) {
            $end = $monthEnd->copy();
        }

        if ($start->greaterThan($monthEnd)) {
            return back()->withErrors([
                'week' => 'The selected week is outside the selected month.',
            ]);
        }

        $reportStart = $start->copy()->startOfDay();
        $reportEnd = $end->copy()->endOfDay();

        $appointments = Appointment::with(['patient', 'doctor', 'service', 'slot'])
            ->whereBetween('scheduled_at', [$reportStart, $reportEnd])
            ->orderBy('scheduled_at')
            ->get();

        $summary = [
            'completed' => $appointments->where('status', 'completed')->count(),
            'cancelled' => $appointments->where('status', 'cancelled')->count(),
            'noShow' => $appointments->where('status', 'no-show')->count(),
            'total' => $appointments->count(),
        ];

        $filename = 'eKesihatan_Weekly_Report_' .
            $validated['month'] .
            '_Week_' .
            $validated['week'] .
            '.pdf';

        $pdf = Pdf::loadView('admin.appointments.report', [
            'appointments' => $appointments,
            'summary' => $summary,
            'month' => $monthStart,
            'week' => (int) $validated['week'],
            'start' => $reportStart,
            'end' => $reportEnd,
        ])->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }

    public function show(Appointment $appointment): View
    {
        return view('admin.appointments.show', [
            'appointment' => $appointment->load([
                'patient',
                'doctor',
                'service',
                'slot',
                'documents',
            ]),
            'doctors' => User::where('role', User::ROLE_DOCTOR)
                ->orderBy('name')
                ->get(),
            'slots' => AppointmentSlot::with('doctor')
                ->orderBy('slot_date')
                ->orderBy('start_time')
                ->get(),
        ]);
    }

    public function update(
        Request $request,
        Appointment $appointment,
        EmailNotificationService $emailNotificationService
    ): RedirectResponse {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:approved,rejected,rescheduled,cancelled,checked-in,completed,no-show',
            ],
            'doctor_id' => [
                'required',
                Rule::exists('users', 'id')->where('role', User::ROLE_DOCTOR),
            ],
            'appointment_slot_id' => ['required', 'exists:appointment_slots,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $slot = AppointmentSlot::findOrFail($validated['appointment_slot_id']);

        if ((int) $slot->doctor_id !== (int) $validated['doctor_id']) {
            return back()->withErrors([
                'appointment_slot_id' => 'Selected slot does not match the chosen doctor.',
            ]);
        }

        $scheduledAt = Carbon::parse(
            $slot->slot_date->format('Y-m-d') . ' ' . $slot->start_time
        );

        $appointment->update([
            'status' => $validated['status'],
            'doctor_id' => $validated['doctor_id'],
            'appointment_slot_id' => $slot->id,
            'scheduled_at' => $scheduledAt,
            'notes' => $validated['notes'] ?? $appointment->notes,
            'approved_at' => in_array(
                $validated['status'],
                ['approved', 'rescheduled', 'checked-in', 'completed'],
                true
            ) ? now() : $appointment->approved_at,
            'cancelled_at' => in_array(
                $validated['status'],
                ['rejected', 'cancelled'],
                true
            ) ? now() : $appointment->cancelled_at,
        ]);

        if ($validated['status'] === 'rescheduled') {
            $appointment->load(['patient', 'doctor', 'service']);
            $emailNotificationService->sendRescheduleNotice($appointment);
        }

        return redirect()
            ->route('admin.appointments.show', $appointment)
            ->with('status', 'Appointment updated.');
    }
}