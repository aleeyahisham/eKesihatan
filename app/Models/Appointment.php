<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
class Appointment extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'health_service_id',
        'appointment_slot_id',
        'scheduled_at',
        'status',
        'notes',
        'queue_number',
        'check_in_token',
        'checked_in_at',
        'approved_at',
        'cancelled_at',
        'reminder_day_sent_at',
        'reminder_hour_sent_at',
        'reminder_fifteen_minutes_sent_at',
    ];
 
    protected $casts = [
        'scheduled_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'approved_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'reminder_day_sent_at' => 'datetime',
        'reminder_hour_sent_at' => 'datetime',
        'reminder_fifteen_minutes_sent_at' => 'datetime',
    ];
 
    public function patient() { return $this->belongsTo(User::class, 'patient_id'); }
    public function doctor() { return $this->belongsTo(User::class, 'doctor_id'); }
    public function service() { return $this->belongsTo(HealthService::class, 'health_service_id'); }
    public function slot() { return $this->belongsTo(AppointmentSlot::class, 'appointment_slot_id'); }
    public function documents() { return $this->hasMany(MedicalDocument::class); }
    public function queueTicket() { return $this->hasOne(QueueTicket::class); }
}