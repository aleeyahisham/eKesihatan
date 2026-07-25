<?php

namespace App\Notifications;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DoctorEmergencyRescheduleNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Appointment $appointment,
        private readonly ?string $previousDoctorName = null,
        private readonly ?string $previousSchedule = null,
        private readonly ?string $reason = null
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $this->appointment->loadMissing([
            'patient',
            'doctor',
            'service',
            'slot',
        ]);

        $newSchedule = $this->formatDateTime(
            $this->appointment->scheduled_at
        );

        return [
            'type' => 'emergency_appointment_reassignment',
            'title' => 'New Appointment Reassigned to You',
            'appointment_id' => $this->appointment->id,
            'patient_name' => $this->appointment->patient?->name ?? 'Patient',
            'patient_email' => $this->appointment->patient?->email,
            'service_name' => $this->appointment->service?->name
                ?? 'General Consultation',
            'previous_doctor' => $this->previousDoctorName
                ?? 'Previous Doctor',
            'previous_schedule' => $this->previousSchedule,
            'new_doctor' => $this->appointment->doctor?->name
                ?? 'Assigned Doctor',
            'new_schedule' => $newSchedule,
            'scheduled_at' => $newSchedule,
            'reason' => $this->reason ?: 'Emergency schedule change',
            'message' => sprintf(
                '%s has been reassigned to you for %s on %s due to an emergency schedule change.',
                $this->appointment->patient?->name ?? 'A patient',
                $this->appointment->service?->name ?? 'General Consultation',
                $newSchedule
            ),
            'url' => route(
                'doctor.appointments.show',
                $this->appointment->id
            ),
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    private function formatDateTime(mixed $value): string
    {
        if (blank($value)) {
            return 'Schedule unavailable';
        }

        try {
            return Carbon::parse($value)
                ->timezone(config('app.timezone', 'Asia/Kuala_Lumpur'))
                ->format('d M Y, h:i A');
        } catch (\Throwable $exception) {
            return (string) $value;
        }
    }
}
