<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
class MedicalDocument extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'appointment_id','uploaded_by','document_type','filename','mime_type','size_bytes','document_data','uploaded_at',
    ];
 
    protected $casts = ['uploaded_at' => 'datetime'];
 
    public function appointment() { return $this->belongsTo(Appointment::class); }
    public function uploader() { return $this->belongsTo(User::class, 'uploaded_by'); }
}