<?php

namespace App\Notifications;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentRescheduledNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Appointment $appointment
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->appointment->loadMissing([
            'patient',
            'doctor',
            'service',
            'slot',
        ]);

        $doctorName = $this->appointment->doctor?->name
            ?? 'Assigned Doctor';

        $serviceName = $this->appointment->service?->name
            ?? 'General Consultation';

        $scheduledAt = $this->appointment->scheduled_at
            ? Carbon::parse($this->appointment->scheduled_at)
                ->timezone(config('app.timezone', 'Asia/Kuala_Lumpur'))
                ->format('d M Y, h:i A')
            : 'Schedule unavailable';

        return (new MailMessage)
            ->subject('Important: Your Clinic Appointment Has Been Rescheduled')
            ->greeting('Hello ' . ($notifiable->name ?? 'Patient') . ',')
            ->line(
                'Your appointment has been rescheduled because your original doctor is unavailable.'
            )
            ->line('Service: ' . $serviceName)
            ->line('New Doctor: ' . $doctorName)
            ->line('New Date and Time: ' . $scheduledAt)
            ->line(
                'Please review the updated appointment details before attending the clinic.'
            )
            ->action(
                'View Updated Appointment',
                route(
                    'patient.appointments.show',
                    $this->appointment->id
                )
            )
            ->line(
                'Please contact Unit Kesihatan UiTM Perlis if the new schedule is unsuitable.'
            )
            ->salutation('Unit Kesihatan UiTM Perlis');
    }
}
