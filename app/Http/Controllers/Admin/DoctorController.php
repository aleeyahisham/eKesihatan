<?php
 
namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
 
class DoctorController extends Controller
{
    public function index()
    {
        return view('admin.doctors.index', [
            'doctors' => User::where('role', User::ROLE_DOCTOR)->orderBy('name')->get(),
        ]);
    }
 
    public function create()
    {
        return view('admin.doctors.create');
    }
 
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'staff_id' => ['nullable', 'string', 'max:50', 'unique:users,staff_id'],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);
 
        $data['password'] = Hash::make($data['password']);
        $data['role'] = User::ROLE_DOCTOR;
 
        User::create($data);
 
        return redirect()->route('admin.doctors.index')->with('status', 'Doctor added.');
    }
 
    public function edit(User $doctor)
    {
        if ($doctor->role !== User::ROLE_DOCTOR) {
            abort(404);
        }
 
        return view('admin.doctors.edit', [
            'doctor' => $doctor,
        ]);
    }
 
    public function update(Request $request, User $doctor)
    {
        if ($doctor->role !== User::ROLE_DOCTOR) {
            abort(404);
        }
 
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $doctor->id],
            'staff_id' => ['nullable', 'string', 'max:50', 'unique:users,staff_id,' . $doctor->id],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'min:8', 'confirmed'],
        ]);
 
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
 
        $doctor->update($data);
 
        return redirect()->route('admin.doctors.index')->with('status', 'Doctor updated.');
    }
 
    public function destroy(User $doctor)
    {
        if ($doctor->role !== User::ROLE_DOCTOR) {
            abort(404);
        }
 
        $doctor->delete();
 
        return redirect()->route('admin.doctors.index')->with('status', 'Doctor removed.');
    }
}