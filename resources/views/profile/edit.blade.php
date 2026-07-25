@extends('layouts.app')
 
@section('content')
<div class="page-header profile-page-header">
    <div>
        <h2 data-i18n="Profile">Profile</h2>
        <p>Manage your medical profile and password. Account credentials are protected.</p>
    </div>
</div>

<section class="profile-panel profile-panel--locked">
    <div class="profile-panel__header">
        <h3>Account Credentials</h3>
        <span class="status-chip">Locked</span>
    </div>
    <p class="profile-help">For security, identity details can only be changed by staff support.</p>
    <div class="profile-grid profile-grid--credentials">
        <div class="auth-field">
            <label>Name</label>
            <input type="text" value="{{ $user->name }}" readonly>
        </div>
        <div class="auth-field">
            <label>Email</label>
            <input type="text" value="{{ $user->email }}" readonly>
        </div>
        <div class="auth-field">
            <label>Phone Number</label>
            <input type="text" value="{{ $user->phone_number ?: '-' }}" readonly>
        </div>
        @if ($user->isPatient())
            <div class="auth-field">
                <label>Student ID</label>
                <input type="text" value="{{ $user->student_id ?: '-' }}" readonly>
            </div>
        @else
            <div class="auth-field">
                <label>Staff ID</label>
                <input type="text" value="{{ $user->staff_id ?: '-' }}" readonly>
            </div>
        @endif
    </div>
</section>

@if ($user->isPatient() || $user->isDoctor())
<section class="profile-panel">
    <div class="profile-panel__header">
        <h3>Medical Profile Details</h3>
        @if ($user->isPatient())
            <span class="required-tag">Required</span>
        @endif
    </div>
    <form class="auth-form profile-form" method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PUT')

        @if ($user->isPatient())
            <div class="auth-form__grid">
                <div class="auth-field">
                    <label for="blood_type" data-i18n="Blood Type">Blood Type <span class="required-tag">Required</span></label>
                    <select id="blood_type" name="blood_type" required>
                        <option value="" data-i18n="Select Blood Type">Select Blood Type</option>
                        @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $type)
                            <option value="{{ $type }}" @selected(old('blood_type', $user->blood_type) === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="auth-field">
                    <label for="emergency_contact_relationship" data-i18n="Emergency Contact Relationship">Emergency Contact Relationship <span class="required-tag">Required</span></label>
                    <input id="emergency_contact_relationship" name="emergency_contact_relationship" type="text" value="{{ old('emergency_contact_relationship', $user->emergency_contact_relationship) }}" required>
                </div>
                <div class="auth-field">
                    <label for="emergency_contact_name" data-i18n="Emergency Contact Name">Emergency Contact Name <span class="required-tag">Required</span></label>
                    <input id="emergency_contact_name" name="emergency_contact_name" type="text" value="{{ old('emergency_contact_name', $user->emergency_contact_name) }}" required>
                </div>
                <div class="auth-field">
                    <label for="emergency_contact_phone" data-i18n="Emergency Contact Phone">Emergency Contact Phone <span class="required-tag">Required</span></label>
                    <input id="emergency_contact_phone" name="emergency_contact_phone" type="text" value="{{ old('emergency_contact_phone', $user->emergency_contact_phone) }}" required>
                </div>
            </div>
            <div class="auth-field">
                <label for="allergies" data-i18n="Allergies or Medical Notes">Allergies or Medical Notes</label>
                <textarea id="allergies" name="allergies" rows="3">{{ old('allergies', $user->allergies) }}</textarea>
            </div>
        @elseif ($user->isDoctor())
            <div class="auth-field">
                <label for="specialization" data-i18n="Specialization (Doctor)">Specialization (Doctor)</label>
                <input id="specialization" name="specialization" type="text" value="{{ old('specialization', $user->specialization) }}">
            </div>
        @else
            <p class="profile-help">No additional profile fields are required for your role.</p>
        @endif

        <button type="submit" data-i18n="Save Changes">Save Profile Details</button>
    </form>
</section>
@endif

<section class="profile-panel">
    <div class="profile-panel__header">
        <h3>Change Password</h3>
    </div>
    <form class="auth-form profile-form" method="POST" action="{{ route('profile.password.update') }}">
        @csrf
        @method('PUT')

        <div class="auth-form__grid">
            <div class="auth-field">
                <label for="current_password">Current Password <span class="required-tag">Required</span></label>
                <input id="current_password" name="current_password" type="password" required>
            </div>
            <div class="auth-field"></div>
            <div class="auth-field">
                <label for="password">New Password <span class="required-tag">Required</span></label>
                <input id="password" name="password" type="password" required>
            </div>
            <div class="auth-field">
                <label for="password_confirmation">Confirm New Password <span class="required-tag">Required</span></label>
                <input id="password_confirmation" name="password_confirmation" type="password" required>
            </div>
        </div>

        <button type="submit">Update Password</button>
    </form>
</section>
@endsection