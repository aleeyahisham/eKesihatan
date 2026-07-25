@extends('layouts.app')

@section('content')
<div class="auth-shell">
    <div class="auth-card">
        <div class="auth-card__header">
            <h2 data-i18n="Staff Login">Staff Login</h2>
        </div>

        <form method="POST" action="{{ route('staff.login.attempt') }}" class="auth-form">
            @csrf
            <div class="auth-field">
                <label for="role" data-i18n="Role">Role</label>
                <select id="role" name="role" required>
                    <option value="doctor" @selected(old('role') === 'doctor') data-i18n="Doctor">Doctor</option>
                    <option value="staff" @selected(old('role') === 'staff') data-i18n="Staff">Staff</option>
                </select>
            </div>
            <div class="auth-field">
                <label for="email" data-i18n="Email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required>
            </div>
            <div class="auth-field">
                <label for="password" data-i18n="Password">Password</label>
                <input id="password" name="password" type="password" required>
            </div>
            <button type="submit" data-i18n="Staff Login">Staff Login</button>
        </form>

        <div class="auth-footer">
            <span data-i18n="Patient?">Patient?</span>
            <a href="{{ route('login') }}" data-i18n="Patient Login">Patient Login</a>
        </div>
    </div>
</div>
@endsection
