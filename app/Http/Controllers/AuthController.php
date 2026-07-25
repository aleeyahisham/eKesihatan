<?php
 
namespace App\Http\Controllers;
 
use App\Models\User;
use App\Services\EmailNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
 
class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showStaffLogin()
    {
        return view('auth.staff-login');
    }
 
    public function login(Request $request)
    {
        return $this->attemptLoginForRoles($request, [User::ROLE_PATIENT]);
    }

    public function loginStaff(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'role' => ['required', Rule::in([User::ROLE_DOCTOR, User::ROLE_STAFF])],
        ]);

        $selectedRole = $credentials['role'];

        return $this->attemptLoginForRoles($request, [
            ...$this->resolveAllowedRoles($selectedRole),
        ], $selectedRole);
    }

    private function attemptLoginForRoles(Request $request, array $allowedRoles, ?string $selectedRole = null)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();
        if (!$user || !in_array($user->role, $allowedRoles, true)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->withInput();
        }

        if ($selectedRole !== null && !$this->matchesSelectedRole($user->role, $selectedRole)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->withInput();
        }

        if ($user && !$this->isBcryptHash($user->password)) {
            // Migrate legacy/plain passwords to bcrypt to avoid login errors.
            if ($this->matchesLegacyPassword($user->password, $credentials['password'])) {
                $user->password = Hash::make($credentials['password']);
                $user->save();
 
                Auth::login($user);
                $request->session()->regenerate();
 
                return redirect()->route('dashboard');
            }
 
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->withInput();
        }
 
        if (Auth::attempt($credentials)) {
            if (!in_array(Auth::user()?->role, $allowedRoles, true)) {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'The provided credentials do not match our records.',
                ])->withInput();
            }

            $request->session()->regenerate();
 
            return redirect()->route('dashboard');
        }
 
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput();
    }

    private function resolveAllowedRoles(string $selectedRole): array
    {
        if ($selectedRole === User::ROLE_STAFF) {
            return [User::ROLE_STAFF, User::ROLE_ADMIN];
        }

        return [$selectedRole];
    }

    private function matchesSelectedRole(string $actualRole, string $selectedRole): bool
    {
        if ($selectedRole === User::ROLE_STAFF) {
            return in_array($actualRole, [User::ROLE_STAFF, User::ROLE_ADMIN], true);
        }

        return $actualRole === $selectedRole;
    }
 
    public function showRegister()
    {
        return view('auth.register');
    }
 
    public function register(Request $request, EmailNotificationService $emailNotificationService)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[^0-9]+$/'],
            'student_id' => ['required', 'string', 'max:50', 'regex:/^\d+$/', 'unique:users,student_id'],
            'email' => [
                'required',
                'email',
                'max:255',
                'regex:/^\d+@student\.uitm\.edu\.my$/',
                'unique:users,email',
            ],
            'phone_number' => ['required', 'string', 'max:30', 'regex:/^\d+$/'],
            'password' => ['required', 'min:8', 'confirmed'],
        ], [
            'name.regex' => 'Full name must not contain numbers.',
            'student_id.regex' => 'Student ID must contain numbers only.',
            'email.regex' => 'Email must use the format 2023415142@student.uitm.edu.my.',
            'phone_number.regex' => 'Phone number must contain numbers only.',
            'password.min' => 'Password must be at least 8 characters.',
        ]);

        $expectedEmail = $data['student_id'] . '@student.uitm.edu.my';
        if (strtolower($data['email']) !== strtolower($expectedEmail)) {
            return back()->withErrors([
                'email' => 'Email must match your student ID in the format ' . $expectedEmail . '.',
            ])->withInput();
        }
 
        $user = User::create([
            'name' => $data['name'],
            'student_id' => $data['student_id'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'password' => Hash::make($data['password']),
            'role' => User::ROLE_PATIENT,
        ]);
 
        Auth::login($user);
        $emailNotificationService->sendRegistrationSuccess($user);
 
        return redirect()->route('dashboard');
    }
 
    public function logout(Request $request)
    {
        Auth::logout();
 
        $request->session()->invalidate();
        $request->session()->regenerateToken();
 
        return redirect()->route('landing');
    }
 
    private function isBcryptHash(?string $hash): bool
    {
        if (!$hash) {
            return false;
        }
 
        $info = password_get_info($hash);
 
        return $info['algoName'] === 'bcrypt';
    }
 
    private function matchesLegacyPassword(string $storedHash, string $plain): bool
    {
        $info = password_get_info($storedHash);
 
        if ($info['algo'] !== 0) {
            return password_verify($plain, $storedHash);
        }
 
        return hash_equals($storedHash, $plain);
    }
}