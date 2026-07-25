<?php
 
namespace Database\Seeders;
 
use App\Models\Appointment;
use App\Models\AppointmentSlot;
use App\Models\HealthService;
use App\Models\QueueTicket;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
 
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;
 
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin Hasya Shamsul',
            'email' => 'hasya@ekesihatan.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
            'phone_number' => '016-678 9012',
            'staff_id' => 'ADM-001',
        ]);

        $doctorUsers = collect([
            [
                'name' => 'Dr. Rosmawati Binti Abd Wahab',
                'email' => 'rosmawati@ekesihatan.com',
                'staff_id' => 'DOC-101',
                'phone_number' => '012-300 1101',
                'specialization' => 'Family Medicine',
                'service_focus' => 'General consultation and routine family medicine care.',
                'service_keywords' => ['general consultation', 'general practitioner', 'family medicine'],
            ],
            [
                'name' => 'Dr. Hidayat Bin Mustafa',
                'email' => 'hidayat@ekesihatan.com',
                'staff_id' => 'DOC-102',
                'phone_number' => '012-300 1102',
                'specialization' => 'Preventive Care',
                'service_focus' => 'Preventive care, health screening, and vaccine support.',
                'service_keywords' => ['preventive care', 'vaccines', 'vaccination', 'preventive'],
            ],
            [
                'name' => 'Dr. Mohd Ariff Bin Awang',
                'email' => 'ariff@ekesihatan.com',
                'staff_id' => 'DOC-103',
                'phone_number' => '012-300 1103',
                'specialization' => 'Minor Procedures',
                'service_focus' => 'Minor procedures including wound care and simple day-care treatment.',
                'service_keywords' => ['minor procedures', 'minor procedure', 'procedure', 'wound', 'stitching'],
            ],
            [
                'name' => 'Dr. Mohd Fadzli Bin Jamalludin',
                'email' => 'fadzli@ekesihatan.com',
                'staff_id' => 'DOC-104',
                'phone_number' => '012-300 1104',
                'specialization' => 'General Practitioner',
                'service_focus' => 'General consultation and short follow-up visits.',
                'service_keywords' => ['general consultation', 'general practitioner'],
            ],
            [
                'name' => 'Dr. Mohd Khairi Bin Zahari',
                'email' => 'khairi@ekesihatan.com',
                'staff_id' => 'DOC-105',
                'phone_number' => '012-300 1105',
                'specialization' => 'Preventive Care',
                'service_focus' => 'Preventive care and vaccine-related appointments.',
                'service_keywords' => ['preventive care', 'vaccines', 'vaccination', 'preventive'],
            ],
        ])->map(function (array $doctor) {
            $user = User::firstOrCreate(['email' => $doctor['email']], [
                'name' => $doctor['name'],
                'password' => Hash::make('password'),
                'role' => User::ROLE_DOCTOR,
                'phone_number' => $doctor['phone_number'],
                'specialization' => $doctor['specialization'],
                'staff_id' => $doctor['staff_id'],
            ]);
            $user->setAttribute('service_focus', $doctor['service_focus']);
            $user->setAttribute('service_keywords', $doctor['service_keywords']);
            $user->save();
            return $user;
        });

        $patients = collect([
            [
                'name' => 'Patient Example',
                'email' => 'patient@ekesihatan.test',
                'student_id' => 'STU-12345',
                'phone_number' => '0123456791',
                'blood_type' => 'O+',
                'emergency_contact_name' => 'Ahmad Example',
                'emergency_contact_phone' => '019-555 0101',
                'emergency_contact_relationship' => 'Father',
                'allergies' => 'No known allergies.',
            ],
            [
                'name' => 'Nurul Safiah',
                'email' => 'nurul@ekesihatan.com',
                'student_id' => 'STU-56789',
                'phone_number' => '0123456792',
                'blood_type' => 'A-',
                'emergency_contact_name' => 'Siti Zulaikha',
                'emergency_contact_phone' => '019-555 0102',
                'emergency_contact_relationship' => 'Mother',
                'allergies' => 'Penicillin sensitivity.',
            ],
            [
                'name' => 'Amir Nazri',
                'email' => 'amir@ekesihatan.com',
                'student_id' => 'STU-24680',
                'phone_number' => '0123456793',
                'blood_type' => 'B+',
                'emergency_contact_name' => 'Noraini Nazri',
                'emergency_contact_phone' => '019-555 0103',
                'emergency_contact_relationship' => 'Sibling',
                'allergies' => 'Asthma; avoid smoke exposure.',
            ],
        ])->map(function (array $patient) {
            return User::create([
                'name' => $patient['name'],
                'email' => $patient['email'],
                'password' => Hash::make('password'),
                'role' => User::ROLE_PATIENT,
                'phone_number' => $patient['phone_number'],
                'student_id' => $patient['student_id'],
                'blood_type' => $patient['blood_type'],
                'emergency_contact_name' => $patient['emergency_contact_name'],
                'emergency_contact_phone' => $patient['emergency_contact_phone'],
                'emergency_contact_relationship' => $patient['emergency_contact_relationship'],
                'allergies' => $patient['allergies'],
            ]);
        });

        $services = collect([
            [
                'name' => 'General Consultation',
                'description' => 'Meet a doctor for routine health concerns, symptoms, or general medical advice.',
                'duration_minutes' => 20,
            ],
            [
                'name' => 'Preventive Care & Vaccines',
                'description' => 'Book preventive care and immunisation visits, including HPV vaccine support and health screening guidance.',
                'duration_minutes' => 30,
            ],
            [
                'name' => 'Minor Procedures',
                'description' => 'Book quick in-clinic treatment for wound care, stitching, and simple day-care procedures.',
                'duration_minutes' => 45,
            ],
        ])->map(function (array $service) {
            return HealthService::create([
                'name' => $service['name'],
                'description' => $service['description'],
                'duration_minutes' => $service['duration_minutes'],
                'is_active' => true,
            ]);
        });

        $slotRows = collect([
            [
                'doctor' => $doctorUsers[0],
                'date' => now()->addDays(1)->toDateString(),
                'start' => '08:30',
                'end' => '09:00',
            ],
            [
                'doctor' => $doctorUsers[0],
                'date' => now()->addDays(1)->toDateString(),
                'start' => '09:10',
                'end' => '09:40',
            ],
            [
                'doctor' => $doctorUsers[1],
                'date' => now()->addDays(2)->toDateString(),
                'start' => '10:00',
                'end' => '10:30',
            ],
            [
                'doctor' => $doctorUsers[1],
                'date' => now()->addDays(2)->toDateString(),
                'start' => '10:40',
                'end' => '11:10',
            ],
            [
                'doctor' => $doctorUsers[2],
                'date' => now()->addDays(3)->toDateString(),
                'start' => '14:00',
                'end' => '14:30',
            ],
            [
                'doctor' => $doctorUsers[2],
                'date' => now()->addDays(3)->toDateString(),
                'start' => '15:00',
                'end' => '15:30',
            ],
        ])->map(function (array $slot) {
            return AppointmentSlot::create([
                'doctor_id' => $slot['doctor']->id,
                'slot_date' => $slot['date'],
                'start_time' => $slot['start'],
                'end_time' => $slot['end'],
                'capacity' => 5,
                'location' => 'Unit Kesihatan UiTM Perlis',
                'is_active' => true,
            ]);
        });

        $queueCounters = [];

        collect([
            [
                'patient' => $patients[0],
                'doctor' => $doctorUsers[0],
                'slot' => $slotRows[0],
                'service' => $services[0],
                'status' => 'approved',
            ],
            [
                'patient' => $patients[1],
                'doctor' => $doctorUsers[0],
                'slot' => $slotRows[1],
                'service' => $services[1],
                'status' => 'pending',
            ],
            [
                'patient' => $patients[2],
                'doctor' => $doctorUsers[1],
                'slot' => $slotRows[2],
                'service' => $services[0],
                'status' => 'approved',
            ],
            [
                'patient' => $patients[0],
                'doctor' => $doctorUsers[1],
                'slot' => $slotRows[3],
                'service' => $services[2],
                'status' => 'pending',
            ],
            [
                'patient' => $patients[1],
                'doctor' => $doctorUsers[2],
                'slot' => $slotRows[4],
                'service' => $services[1],
                'status' => 'approved',
            ],
            [
                'patient' => $patients[2],
                'doctor' => $doctorUsers[2],
                'slot' => $slotRows[5],
                'service' => $services[2],
                'status' => 'pending',
            ],
        ])->each(function (array $data, int $index) use (&$queueCounters) {
            $slot = $data['slot'];
            $scheduledAt = $slot->slot_date->format('Y-m-d') . ' ' . $slot->start_time;

            $appointment = Appointment::create([
                'patient_id' => $data['patient']->id,
                'doctor_id' => $data['doctor']->id,
                'health_service_id' => $data['service']->id,
                'appointment_slot_id' => $slot->id,
                'scheduled_at' => $scheduledAt,
                'status' => $data['status'],
                'check_in_token' => (string) Str::uuid(),
                'approved_at' => $data['status'] === 'approved' ? now() : null,
                'checked_in_at' => $index === 0 ? now() : null,
            ]);

            $issuedOn = $slot->slot_date->format('Y-m-d');
            $queueCounters[$issuedOn] = ($queueCounters[$issuedOn] ?? 0) + 1;

            QueueTicket::create([
                'appointment_id' => $appointment->id,
                'issued_on' => $issuedOn,
                'number' => $queueCounters[$issuedOn],
            ]);

            $appointment->update(['queue_number' => $queueCounters[$issuedOn]]);
        });
    }
}