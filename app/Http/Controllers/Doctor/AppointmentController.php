<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\HealthService;
use App\Models\QueueTicket;
use App\Models\User;
use App\Notifications\DoctorEmergencyRescheduleNotification;
use App\Services\AppointmentScheduler;
use App\Services\EmailNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('date', now()->toDateString());

        $appointments = Appointment::with([
            'patient',
            'service',
            'slot',
        ])
            ->where('doctor_id', $request->user()->id)
            ->whereDate('scheduled_at', $date)
            ->orderBy('scheduled_at')
            ->get();

        $notifications = $request->user()
            ->unreadNotifications()
            ->latest()
            ->limit(10)
            ->get();

        return view('doctor.appointments.index', [
            'appointments' => $appointments,
            'date' => $date,
            'notifications' => $notifications,
        ]);
    }

    public function show(Request $request, Appointment $appointment)
    {
        $this->authorizeDoctor($request, $appointment);

        return view('doctor.appointments.show', [
            'appointment' => $appointment->load([
                'patient',
                'service',
                'slot',
                'documents',
            ]),
        ]);
    }

    public function update(Request $request, Appointment $appointment)
    {
        $this->authorizeDoctor($request, $appointment);

        if (
            !$appointment->checked_in_at
            && $appointment->status !== 'checked-in'
        ) {
            return back()->withErrors([
                'status' => 'The patient must check in before the appointment status can be updated.',
            ]);
        }

        $data = $request->validate([
            'status' => ['required', 'in:completed,no-show'],
        ]);

        $appointment->update([
            'status' => $data['status'],
        ]);

        return redirect()
            ->route('doctor.appointments.show', $appointment)
            ->with(
                'status',
                'Appointment status updated successfully.'
            );
    }

    public function emergencyReschedule(
        Request $request,
        AppointmentScheduler $scheduler,
        EmailNotificationService $emailNotificationService
    ) {
        $data = $request->validate([
            'date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
            'reason' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);

        $unavailableDoctor = $request->user();

        $targetDate = Carbon::parse(
            $data['date']
        )->toDateString();

        $reason = trim(
            (string) ($data['reason'] ?? '')
        );

        if ($reason === '') {
            $reason = 'Emergency doctor unavailability.';
        }

        $replacementDoctor = $this->findEmergencyReplacementDoctor(
            $unavailableDoctor
        );

        if (!$replacementDoctor) {
            return back()->withErrors([
                'date' => 'No matching replacement doctor was found for '
                    . (
                        $unavailableDoctor->specialization
                        ?: $unavailableDoctor->name
                    )
                    . '.',
            ]);
        }

        $appointments = Appointment::with([
            'service',
            'patient',
            'doctor',
            'slot',
            'queueTicket',
        ])
            ->where(
                'doctor_id',
                $unavailableDoctor->id
            )
            ->whereDate(
                'scheduled_at',
                $targetDate
            )
            ->whereIn(
                'status',
                [
                    'approved',
                    'rescheduled',
                    'pending',
                ]
            )
            ->where(
                'scheduled_at',
                '>=',
                now()
            )
            ->orderBy('scheduled_at')
            ->get();

        if ($appointments->isEmpty()) {
            return back()->withErrors([
                'date' => 'No upcoming appointments were found for emergency rescheduling on the selected date.',
            ]);
        }

        $successCount = 0;
        $failedCount = 0;
        $emailSuccessCount = 0;
        $emailFailedCount = 0;
        $doctorNotificationCount = 0;

        foreach ($appointments as $appointment) {
            try {
                $service = $appointment->service
                    ?? HealthService::find(
                        $appointment->health_service_id
                    );

                if (!$service) {
                    $failedCount++;

                    Log::warning(
                        'Emergency reschedule skipped: service unavailable.',
                        [
                            'appointment_id' => $appointment->id,
                        ]
                    );

                    continue;
                }

                if (
                    !$this->doctorCanHandleService(
                        $replacementDoctor,
                        $service
                    )
                ) {
                    $failedCount++;

                    Log::warning(
                        'Emergency reschedule skipped: replacement doctor does not support service.',
                        [
                            'appointment_id' => $appointment->id,
                            'replacement_doctor_id' => $replacementDoctor->id,
                            'replacement_specialization' => $replacementDoctor->specialization,
                            'service' => $service->name,
                        ]
                    );

                    continue;
                }

                $previousDoctorName = $appointment
                    ->doctor?->name
                    ?? $unavailableDoctor->name;

                $previousSchedule = $this->formatDateTime(
                    $appointment->scheduled_at
                );

                $preferredDate = Carbon::parse(
                    $appointment->scheduled_at
                );

                $preferredTime = $preferredDate->format(
                    'H:i'
                );

                $replacementSlot = $scheduler->findBestSlot(
                    $service,
                    $preferredDate,
                    $replacementDoctor->id,
                    $preferredTime,
                    [$unavailableDoctor->id]
                );

                if (!$replacementSlot) {
                    $failedCount++;

                    Log::warning(
                        'No replacement-doctor slot was available.',
                        [
                            'appointment_id' => $appointment->id,
                            'unavailable_doctor_id' => $unavailableDoctor->id,
                            'replacement_doctor_id' => $replacementDoctor->id,
                            'target_date' => $targetDate,
                        ]
                    );

                    continue;
                }

                $slotDate = Carbon::parse(
                    $replacementSlot->slot_date
                )->format('Y-m-d');

                $newScheduledAt = Carbon::parse(
                    $slotDate
                    . ' '
                    . $replacementSlot->start_time
                )->format('Y-m-d H:i:s');

                DB::transaction(function () use (
                    $appointment,
                    $replacementSlot,
                    $replacementDoctor,
                    $newScheduledAt,
                    $reason,
                    $slotDate
                ) {
                    $appointment->update([
                        'appointment_slot_id' => $replacementSlot->id,
                        'doctor_id' => $replacementDoctor->id,
                        'scheduled_at' => $newScheduledAt,
                        'status' => 'rescheduled',
                        'approved_at' => now(),
                        'checked_in_at' => null,
                        'queue_number' => null,
                        'reminder_day_sent_at' => null,
                        'reminder_hour_sent_at' => null,
                        'reminder_fifteen_minutes_sent_at' => null,
                        'notes' => trim(
                            (
                                $appointment->notes
                                ? $appointment->notes . "\n"
                                : ''
                            )
                            . '[Emergency reschedule] '
                            . $reason
                        ),
                    ]);

                    $nextNumber = (
                        (int) QueueTicket::query()
                            ->where(
                                'issued_on',
                                $slotDate
                            )
                            ->lockForUpdate()
                            ->max('number')
                    ) + 1;

                    $ticket = QueueTicket::query()
                        ->where(
                            'appointment_id',
                            $appointment->id
                        )
                        ->first();

                    if ($ticket) {
                        $ticket->update([
                            'issued_on' => $slotDate,
                            'number' => $nextNumber,
                        ]);
                    } else {
                        QueueTicket::create([
                            'appointment_id' => $appointment->id,
                            'issued_on' => $slotDate,
                            'number' => $nextNumber,
                        ]);
                    }

                    $appointment->update([
                        'queue_number' => $nextNumber,
                    ]);
                });

                $updatedAppointment = $appointment->fresh([
                    'patient',
                    'doctor',
                    'service',
                    'slot',
                ]);

                if (!$updatedAppointment) {
                    $failedCount++;
                    continue;
                }

                $successCount++;

                /*
                 * Email sending is still attempted.
                 * The result is recorded in the log, but it is not
                 * displayed in the success message.
                 */
                try {
                    $emailSent = $emailNotificationService
                        ->sendRescheduleNotice(
                            $updatedAppointment
                        );

                    if ($emailSent) {
                        $emailSuccessCount++;
                    } else {
                        $emailFailedCount++;
                    }
                } catch (\Throwable $exception) {
                    $emailFailedCount++;

                    Log::error(
                        'Patient reschedule email failed.',
                        [
                            'appointment_id' => $updatedAppointment->id,
                            'patient_id' => $updatedAppointment->patient_id,
                            'patient_email' => $updatedAppointment->patient?->email,
                            'error' => $exception->getMessage(),
                        ]
                    );
                }

                try {
                    $replacementDoctor->notify(
                        new DoctorEmergencyRescheduleNotification(
                            appointment: $updatedAppointment,
                            previousDoctorName: $previousDoctorName,
                            previousSchedule: $previousSchedule,
                            reason: $reason
                        )
                    );

                    $doctorNotificationCount++;
                } catch (\Throwable $exception) {
                    Log::error(
                        'Replacement doctor notification failed.',
                        [
                            'appointment_id' => $updatedAppointment->id,
                            'replacement_doctor_id' => $replacementDoctor->id,
                            'error' => $exception->getMessage(),
                        ]
                    );
                }
            } catch (\Throwable $exception) {
                $failedCount++;

                Log::error(
                    'Emergency appointment reschedule failed.',
                    [
                        'appointment_id' => $appointment->id,
                        'unavailable_doctor_id' => $unavailableDoctor->id,
                        'replacement_doctor_id' => $replacementDoctor->id,
                        'error' => $exception->getMessage(),
                        'trace' => $exception->getTraceAsString(),
                    ]
                );
            }
        }

        /*
         * Email success and failure counters are intentionally
         * not displayed in the user-facing message.
         */
        $message = 'Emergency rescheduling completed. '
            . "{$successCount} appointment(s) were reassigned to "
            . "{$replacementDoctor->name}. "
            . "{$doctorNotificationCount} notification(s) were created for "
            . "{$replacementDoctor->name}.";

        if ($failedCount > 0) {
            $message .= " {$failedCount} appointment(s) could not be reassigned because no valid free matching slot was available.";
        }

        return redirect()
            ->route(
                'doctor.appointments.index',
                ['date' => $targetDate]
            )
            ->with('status', $message);
    }

    private function findEmergencyReplacementDoctor(
        User $unavailableDoctor
    ): ?User {
        $doctorName = mb_strtolower(
            (string) $unavailableDoctor->name
        );

        $doctorEmail = mb_strtolower(
            (string) $unavailableDoctor->email
        );

        /*
         * Exact operational pairings.
         */
        if (
            str_contains($doctorName, 'fadzli')
            || str_contains($doctorEmail, 'fadzli')
        ) {
            return $this->findDoctorByIdentity(
                'Rosmawati',
                'rosmawati'
            );
        }

        if (
            str_contains($doctorName, 'rosmawati')
            || str_contains($doctorEmail, 'rosmawati')
        ) {
            return $this->findDoctorByIdentity(
                'Fadzli',
                'fadzli'
            );
        }

        if (
            str_contains($doctorName, 'hidayat')
            || str_contains($doctorEmail, 'hidayat')
        ) {
            return $this->findDoctorByIdentity(
                'Khairi',
                'khairi'
            );
        }

        if (
            str_contains($doctorName, 'khairi')
            || str_contains($doctorEmail, 'khairi')
        ) {
            return $this->findDoctorByIdentity(
                'Hidayat',
                'hidayat'
            );
        }

        /*
         * Fallback for future doctors:
         * find another doctor with the same specialization.
         */
        $specialization = trim(
            (string) $unavailableDoctor->specialization
        );

        if ($specialization === '') {
            return null;
        }

        return User::query()
            ->where(
                'role',
                User::ROLE_DOCTOR
            )
            ->where(
                'id',
                '!=',
                $unavailableDoctor->id
            )
            ->whereRaw(
                'LOWER(TRIM(specialization)) = ?',
                [
                    mb_strtolower(
                        $specialization
                    ),
                ]
            )
            ->orderBy('name')
            ->first();
    }

    private function findDoctorByIdentity(
        string $namePart,
        string $emailPart
    ): ?User {
        return User::query()
            ->where(
                'role',
                User::ROLE_DOCTOR
            )
            ->where(function ($query) use (
                $namePart,
                $emailPart
            ) {
                $query
                    ->where(
                        'name',
                        'like',
                        '%' . $namePart . '%'
                    )
                    ->orWhere(
                        'email',
                        'like',
                        '%' . $emailPart . '%'
                    );
            })
            ->orderBy('id')
            ->first();
    }

    private function doctorCanHandleService(
        User $doctor,
        HealthService $service
    ): bool {
        $specialization = mb_strtolower(
            trim(
                (string) $doctor->specialization
            )
        );

        $serviceName = mb_strtolower(
            trim(
                (string) $service->name
            )
        );

        if (
            str_contains(
                $specialization,
                'general practitioner'
            )
            || str_contains(
                $specialization,
                'family medicine'
            )
            || str_contains(
                $specialization,
                'general practice'
            )
        ) {
            return str_contains(
                $serviceName,
                'general consultation'
            );
        }

        if (
            str_contains(
                $specialization,
                'preventive'
            )
        ) {
            return str_contains(
                $serviceName,
                'preventive'
            )
                || str_contains(
                    $serviceName,
                    'vaccine'
                )
                || str_contains(
                    $serviceName,
                    'screening'
                );
        }

        return false;
    }

    public function markNotificationRead(
        Request $request,
        string $notification
    ) {
        $target = $request->user()
            ->notifications()
            ->where(
                'id',
                $notification
            )
            ->first();

        if (!$target) {
            return back()->withErrors([
                'status' => 'The selected notification could not be found.',
            ]);
        }

        $target->markAsRead();

        return back()->with(
            'status',
            'Notification marked as read.'
        );
    }

    private function authorizeDoctor(
        Request $request,
        Appointment $appointment
    ): void {
        if (
            (int) $appointment->doctor_id
            !== (int) $request->user()->id
        ) {
            abort(403);
        }
    }

    private function formatDateTime(
        mixed $value
    ): ?string {
        if (blank($value)) {
            return null;
        }

        try {
            return Carbon::parse($value)
                ->timezone(
                    config(
                        'app.timezone',
                        'Asia/Kuala_Lumpur'
                    )
                )
                ->format(
                    'd M Y, h:i A'
                );
        } catch (\Throwable $exception) {
            return (string) $value;
        }
    }
}