<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
class HealthService extends Model
{
    use HasFactory;
 
    protected $fillable = ['name','description','duration_minutes','is_active'];
    protected $casts = ['is_active' => 'boolean'];
 
    public static function syncPatientCatalog(): \Illuminate\Support\Collection
    {
        $definitions = [
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
                'duration_minutes' => 30,
            ],
        ];

        foreach ($definitions as $definition) {
            $service = static::firstOrNew(['name' => $definition['name']]);
            $service->fill([
                'description' => $definition['description'],
                'duration_minutes' => $definition['duration_minutes'],
                'is_active' => true,
            ]);
            $service->save();
        }

        return static::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }
 
    public function appointments() { return $this->hasMany(Appointment::class); }
}