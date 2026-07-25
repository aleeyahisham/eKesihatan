<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use App\Models\Appointment;
use App\Models\AppointmentSlot;
use App\Models\MedicalDocument;
 
class User extends Authenticatable
{
    use HasFactory, Notifiable;
 
    public const ROLE_STAFF = 'staff';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_DOCTOR = 'doctor';
    public const ROLE_PATIENT = 'patient';
 
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'student_id',
        'blood_type',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'allergies',
        'staff_id',
        'phone_number',
        'specialization',
        'service_focus',
        'service_keywords',
    ];
 
    protected $hidden = ['password','remember_token'];
 
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'service_keywords' => 'array',
        ];
    }
 
    public function isStaff(): bool { return in_array($this->role, [self::ROLE_STAFF, self::ROLE_ADMIN], true); }
    public function isAdmin(): bool { return $this->isStaff(); }
    public function isDoctor(): bool { return $this->role === self::ROLE_DOCTOR; }
    public function isPatient(): bool { return $this->role === self::ROLE_PATIENT; }

    public static function syncDoctorCatalog(): Collection
    {
        $definitions = [
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
        ];

        collect($definitions)->each(function (array $definition): void {
            $doctor = static::where('email', $definition['email'])->first();

            if (!$doctor) {
                $doctor = new static();
                $doctor->email = $definition['email'];
                $doctor->password = Hash::make('password');
                $doctor->role = self::ROLE_DOCTOR;
            }

            $doctor->forceFill([
                'name' => $definition['name'],
                'staff_id' => $definition['staff_id'],
                'phone_number' => $definition['phone_number'],
                'specialization' => $definition['specialization'],
                'service_focus' => $definition['service_focus'],
                'service_keywords' => $definition['service_keywords'],
            ]);
            $doctor->save();
        });

        return static::where('role', self::ROLE_DOCTOR)->orderBy('name')->get();
    }

    public static function doctorsForService(?string $serviceName = null): Collection
    {
        $serviceName = strtolower($serviceName ?? '');

        $doctors = static::syncDoctorCatalog();

        if ($serviceName === '') {
            return $doctors->values();
        }

        $matched = $doctors->filter(function (User $doctor) use ($serviceName): bool {
            $keywords = collect($doctor->getAttribute('service_keywords') ?? [])
                ->map(fn ($keyword) => strtolower((string) $keyword))
                ->filter();

            $specialization = strtolower((string) ($doctor->specialization ?? ''));
            $focus = strtolower((string) ($doctor->service_focus ?? ''));

            if ($keywords->contains(fn ($keyword) => str_contains($serviceName, $keyword))) {
                return true;
            }

            if ($specialization !== '' && str_contains($serviceName, $specialization)) {
                return true;
            }

            if ($focus !== '' && str_contains($focus, $serviceName)) {
                return true;
            }

            return false;
        })->values();

        return $matched->isNotEmpty() ? $matched : $doctors->values();
    }
 
    public function appointmentSlots() { return $this->hasMany(AppointmentSlot::class, 'doctor_id'); }
    public function appointmentsAsPatient() { return $this->hasMany(Appointment::class, 'patient_id'); }
    public function appointmentsAsDoctor() { return $this->hasMany(Appointment::class, 'doctor_id'); }
    public function uploadedDocuments() { return $this->hasMany(MedicalDocument::class, 'uploaded_by'); }
}