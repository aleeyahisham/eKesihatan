<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
 
class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }
 
    public function update(Request $request)
    {
        $user = $request->user();

        $rules = [
            'allergies' => ['nullable', 'string', 'max:1000'],
        ];

        if ($user->isPatient()) {
            $rules = array_merge($rules, [
                'blood_type' => ['required', Rule::in(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])],
                'emergency_contact_name' => ['required', 'string', 'max:255'],
                'emergency_contact_phone' => ['required', 'string', 'max:30'],
                'emergency_contact_relationship' => ['required', 'string', 'max:100'],
            ]);
        }

        if ($user->isDoctor()) {
            $rules['specialization'] = ['nullable', 'string', 'max:255'];
        }

        $data = $request->validate($rules);
        $user->update($data);
 
        return redirect()->route('profile.edit')->with('status', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('profile.edit')->with('status', 'Password updated successfully.');
    }
}