<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentBookedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Appointment $appointment)
    {
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
            ->subject('Appointment Booking Successful')
            ->greeting('Hello ' . ($notifiable->name ?? 'there') . ',')
            ->line('Your appointment booking has been received successfully.')
            ->line('Service: ' . $serviceName)
            ->line('Doctor: ' . $doctorName)
            ->line('Scheduled Time: ' . $scheduledAt)
            ->line('Queue Number: ' . ($this->appointment->queue_number ?? 'To be assigned'))
            ->action('View Appointment', route('patient.appointments.show', $this->appointment))
            ->line('You will be notified again if any schedule changes are required.');
    }
}
