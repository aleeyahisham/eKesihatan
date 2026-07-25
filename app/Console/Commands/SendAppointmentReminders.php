<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Services\EmailNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';

    protected $description = 'Send email reminders for upcoming appointments.';

    public function handle(
        EmailNotificationService $emailNotificationService
    ): int {
        $this->sendWindowReminder(
            $emailNotificationService,
            '1 hour',
            'reminder_hour_sent_at',
            now()->addMinutes(60)->startOfMinute(),
            now()->addMinutes(74)->endOfMinute()
        );

        $this->sendWindowReminder(
            $emailNotificationService,
            '15 minutes',
            'reminder_fifteen_minutes_sent_at',
            now()->addMinutes(15)->startOfMinute(),
            now()->addMinutes(29)->endOfMinute()
        );

        $this->info('Appointment reminders processed.');

        return self::SUCCESS;
    }

    private function sendWindowReminder(
        EmailNotificationService $emailNotificationService,
        string $window,
        string $column,
        $start,
        $end
    ): void {
        $appointments = Appointment::with(['patient', 'doctor'])
            ->whereIn('status', ['approved', 'rescheduled'])
            ->whereNull($column)
            ->whereBetween('scheduled_at', [$start, $end])
            ->get();

        Log::info('Reminder window checked.', [
            'window' => $window,
            'start' => $start->toDateTimeString(),
            'end' => $end->toDateTimeString(),
            'appointments_found' => $appointments->count(),
        ]);

        foreach ($appointments as $appointment) {
            try {
                $emailNotificationService->sendReminder(
                    $appointment,
                    $window
                );

                $appointment->update([
                    $column => now(),
                ]);

                Log::info('Reminder email sent successfully.', [
                    'appointment_id' => $appointment->id,
                    'window' => $window,
                    'patient_email' => $appointment->patient?->email,
                ]);
            } catch (Throwable $exception) {
                Log::error('Reminder email failed.', [
                    'appointment_id' => $appointment->id,
                    'window' => $window,
                    'patient_email' => $appointment->patient?->email,
                    'error' => $exception->getMessage(),
                ]);
            }
        }
    }
}