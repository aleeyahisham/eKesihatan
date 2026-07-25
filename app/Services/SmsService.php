<?php
 
namespace App\Services;
 
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
 
class SmsService
{
    public function send(string $to, string $message): void
    {
        $provider = config('services.sms.provider', 'log');
 
        if ($provider === 'twilio') {
            $sid = config('services.sms.twilio_sid');
            $token = config('services.sms.twilio_token');
            $from = config('services.sms.twilio_from');
 
            if (!$sid || !$token || !$from) {
                Log::warning('Twilio credentials missing, skipping SMS.', ['to' => $to]);
                return;
            }
 
            Http::withBasicAuth($sid, $token)->asForm()->post(
                "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json",
                [
                    'From' => $from,
                    'To' => $to,
                    'Body' => $message,
                ]
            );
 
            return;
        }
 
        Log::info('SMS (log provider)', ['to' => $to, 'message' => $message]);
    }
 
    public function sendAppointmentConfirmation(User $patient, Appointment $appointment): void
    {
        if (!$patient->phone_number) {
            return;
        }
 
        $message = sprintf(
            'eKesihatan: Your appointment request is received for %s with Dr. %s. Status: %s.',
            $appointment->scheduled_at->format('d M Y, h:i A'),
            $appointment->doctor->name ?? 'Assigned Doctor',
            ucfirst($appointment->status)
        );
 
        $this->send($patient->phone_number, $message);
    }
 
    public function sendAppointmentUpdate(User $patient, Appointment $appointment, string $status): void
    {
        if (!$patient->phone_number) {
            return;
        }
 
        $message = sprintf(
            'eKesihatan: Your appointment is %s for %s with Dr. %s.',
            $status,
            $appointment->scheduled_at->format('d M Y, h:i A'),
            $appointment->doctor->name ?? 'Assigned Doctor'
        );
 
        $this->send($patient->phone_number, $message);
    }
 
    public function sendReminder(User $patient, Appointment $appointment, string $window): void
    {
        if (!$patient->phone_number) {
            return;
        }
 
        $message = sprintf(
            'eKesihatan reminder (%s): Your appointment is scheduled at %s with Dr. %s.',
            $window,
            $appointment->scheduled_at->format('d M Y, h:i A'),
            $appointment->doctor->name ?? 'Assigned Doctor'
        );
 
        $this->send($patient->phone_number, $message);
    }
}