<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
class QueueTicket extends Model
{
    use HasFactory;
 
    protected $fillable = ['appointment_id','issued_on','number'];
    protected $casts = ['issued_on' => 'date'];
 
    public function appointment() { return $this->belongsTo(Appointment::class); }
}