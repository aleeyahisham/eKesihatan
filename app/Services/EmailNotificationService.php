<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\User;
use App\Notifications\AppointmentBookedNotification;
use App\Notifications\AppointmentReminderNotification;
use App\Notifications\AppointmentRescheduledNotification;
use App\Notifications\RegistrationSuccessfulNotification;
use Illuminate\Support\Facades\Log;

class EmailNotificationService
{
    public function sendRegistrationSuccess(User $user): bool
    {
        if (!$this->canSendTo($user)) {
            return false;
        }

        try {
            $user->notifyNow(
                new RegistrationSuccessfulNotification($user),
                ['mail']
            );

            return true;
        } catch (\Throwable $exception) {
            Log::error('Unable to send registration success email.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    public function sendBookingSuccess(Appointment $appointment): bool
    {
        $appointment->loadMissing([
            'patient',
            'doctor',
            'service',
        ]);

        $patient = $appointment->patient;

        if (!$patient || !$this->canSendTo($patient)) {
            return false;
        }

        try {
            $patient->notifyNow(
                new AppointmentBookedNotification($appointment),
                ['mail']
            );

            return true;
        } catch (\Throwable $exception) {
            Log::error('Unable to send appointment booking email.', [
                'appointment_id' => $appointment->id,
                'patient_id' => $patient->id,
                'email' => $patient->email,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    public function sendRescheduleNotice(Appointment $appointment): bool
    {
        $appointment->loadMissing([
            'patient',
            'doctor',
            'service',
            'slot',
        ]);

        $patient = $appointment->patient;

        if (!$patient || !$this->canSendTo($patient)) {
            Log::warning(
                'Reschedule email was not sent because the patient email is unavailable.',
                [
                    'appointment_id' => $appointment->id,
                    'patient_id' => $appointment->patient_id,
                    'email' => $patient?->email,
                ]
            );

            return false;
        }

        try {
            $patient->notifyNow(
                new AppointmentRescheduledNotification($appointment),
                ['mail']
            );

            Log::info('Appointment reschedule email sent.', [
                'appointment_id' => $appointment->id,
                'patient_id' => $patient->id,
                'email' => $patient->email,
            ]);

            return true;
        } catch (\Throwable $exception) {
            Log::error('Unable to send appointment reschedule email.', [
                'appointment_id' => $appointment->id,
                'patient_id' => $patient->id,
                'email' => $patient->email,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    public function sendReminder(
        Appointment $appointment,
        string $window
    ): bool {
        $appointment->loadMissing([
            'patient',
            'doctor',
            'service',
        ]);

        $patient = $appointment->patient;

        if (!$patient || !$this->canSendTo($patient)) {
            return false;
        }

        try {
            $patient->notifyNow(
                new AppointmentReminderNotification(
                    $appointment,
                    $window
                ),
                ['mail']
            );

            return true;
        } catch (\Throwable $exception) {
            Log::error('Unable to send appointment reminder email.', [
                'appointment_id' => $appointment->id,
                'patient_id' => $patient->id,
                'email' => $patient->email,
                'window' => $window,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    private function canSendTo(User $user): bool
    {
        return filled($user->email)
            && filter_var(
                $user->email,
                FILTER_VALIDATE_EMAIL
            ) !== false;
    }
}
