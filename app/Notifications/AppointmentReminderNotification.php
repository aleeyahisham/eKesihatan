<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Appointment $appointment,
        private readonly string $window
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $doctorName = $this->appointment->doctor?->name ?? 'Assigned Doctor';
        $serviceName = $this->appointment->service?->name ?? 'Clinic Service';
        $scheduledAt = $this->appointment->scheduled_at
            ? $this->appointment->scheduled_at->format('d M Y, h:i A')
            : '-';

        return (new MailMessage)
            ->subject('Appointment Reminder: ' . $this->window)
            ->greeting('Hello ' . ($notifiable->name ?? 'there') . ',')
            ->line('This is your ' . $this->window . ' reminder for an upcoming appointment.')
            ->line('Service: ' . $serviceName)
            ->line('Doctor: ' . $doctorName)
            ->line('Scheduled Time: ' . $scheduledAt)
            ->action('View Appointment', route('patient.appointments.show', $this->appointment))
            ->line('Please arrive a few minutes earlier for smoother check-in.');
    }
}
