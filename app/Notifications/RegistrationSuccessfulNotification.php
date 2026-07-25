<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RegistrationSuccessfulNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly User $user)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to eKesihatan')
            ->greeting('Hello ' . ($this->user->name ?? 'there') . ',')
            ->line('Your eKesihatan account has been registered successfully.')
            ->line('You can now sign in and start booking your clinic appointments online.')
            ->action('Open eKesihatan', route('dashboard'))
            ->line('Thank you for using eKesihatan.');
    }
}
