<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
class AppointmentSlot extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'doctor_id','slot_date','start_time','end_time','capacity','location','is_active',
    ];
 
    protected $casts = [
        'slot_date' => 'date',
        'is_active' => 'boolean',
    ];
 
    public function doctor() { return $this->belongsTo(User::class, 'doctor_id'); }
    public function appointments() { return $this->hasMany(Appointment::class); }
}