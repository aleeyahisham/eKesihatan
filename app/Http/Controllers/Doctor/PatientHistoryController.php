<?php
 
namespace App\Http\Controllers\Doctor;
 
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
 
class PatientHistoryController extends Controller
{
    public function show(Request $request, User $patient)
    {
        if (!$patient->isPatient()) {
            abort(404);
        }
 
        $appointments = Appointment::with(['service', 'doctor', 'documents'])
            ->where('patient_id', $patient->id)
            ->orderByDesc('scheduled_at')
            ->get();
 
        return view('doctor.patients.history', [
            'patient' => $patient,
            'appointments' => $appointments,
        ]);
    }
}