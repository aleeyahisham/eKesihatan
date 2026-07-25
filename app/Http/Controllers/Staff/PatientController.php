<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = trim((string) $request->input('q', ''));

        $patientsQuery = User::where('role', User::ROLE_PATIENT);

        if ($query !== '') {
            $patientsQuery->where(function ($builder) use ($query) {
                $builder
                    ->where('name', 'like', '%' . $query . '%')
                    ->orWhere('student_id', 'like', '%' . $query . '%');
            });
        }

        return view('staff.patients.index', [
            'patients' => $patientsQuery
                ->orderBy('name')
                ->get(),
            'query' => $query,
        ]);
    }

    public function show(Request $request, User $patient)
    {
        if (!$patient->isPatient()) {
            abort(404);
        }

        return view('staff.patients.show', [
            'patient' => $patient,
        ]);
    }
}
