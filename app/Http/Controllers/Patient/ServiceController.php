<?php
 
namespace App\Http\Controllers\Patient;
 
use App\Http\Controllers\Controller;
use App\Models\HealthService;
use App\Models\User;
 
class ServiceController extends Controller
{
    public function index()
    {
        $services = HealthService::syncPatientCatalog();
        $doctors = collect([
            [
                'name' => 'Dr. Rosmawati Binti Abd Wahab',
                'specialization' => 'Family Medicine',
                'service_focus' => 'General consultation and routine family medicine care.',
            ],
            [
                'name' => 'Dr. Hidayat Bin Mustafa',
                'specialization' => 'Preventive Care',
                'service_focus' => 'Preventive care, health screening, and vaccine support.',
            ],
            [
                'name' => 'Dr. Mohd Ariff Bin Awang',
                'specialization' => 'Minor Procedures',
                'service_focus' => 'Minor procedures including wound care and simple day-care treatment.',
            ],
            [
                'name' => 'Dr. Mohd Fadzli Bin Jamalludin',
                'specialization' => 'General Practitioner',
                'service_focus' => 'General consultation and short follow-up visits.',
            ],
            [
                'name' => 'Dr. Mohd Khairi Bin Zahari',
                'specialization' => 'Preventive Care',
                'service_focus' => 'Preventive care and vaccine-related appointments.',
            ],
        ])->map(function (array $doctorData) {
            $doctor = new User();
            $doctor->forceFill([
                'name' => $doctorData['name'],
                'specialization' => $doctorData['specialization'],
                'service_focus' => $doctorData['service_focus'],
            ]);
            $doctor->image_url = $this->doctorImageFor($doctorData['name']);
            return $doctor;
        });

        return view('patient.services.index', [
            'services' => $services,
            'doctors' => $doctors,
        ]);
    }

    private function doctorImageFor(string $doctorName): string
    {
        $name = strtolower($doctorName);

        $image = match (true) {
            str_contains($name, 'rosmawati') => 'images/drRos.jpg',
            str_contains($name, 'hidayat') => 'images/drHidayat.jpg',
            str_contains($name, 'ariff') || str_contains($name, 'arif') => 'images/drArif.jpg',
            str_contains($name, 'fadzli') => 'images/drFadzli.jpg',
            str_contains($name, 'khairi') => 'images/drKhairi.jpg',
            default => 'images/inside.jpg',
        };

        return asset($image);
    }
}