@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h2>Edit Doctor</h2>
        <p>Update doctor profile, specialization, and access credentials.</p>
    </div>
    <a class="button-link secondary" href="{{ route('admin.doctors.index') }}">Back to Doctors</a>
</div>

<section class="admin-page-card">
    <form class="auth-form profile-form" method="POST" action="{{ route('admin.doctors.update', $doctor) }}">
        @csrf
        @method('PUT')
        <div>
            <label for="name">Full Name</label>
            <input id="name" name="name" type="text" value="{{ old('name', $doctor->name) }}" required>
        </div>
        <div>
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $doctor->email) }}" required>
        </div>
        <div>
            <label for="staff_id">Staff ID</label>
            <input id="staff_id" name="staff_id" type="text" value="{{ old('staff_id', $doctor->staff_id) }}">
        </div>
        <div>
            <label for="phone_number">Phone Number</label>
            <input id="phone_number" name="phone_number" type="text" value="{{ old('phone_number', $doctor->phone_number) }}">
        </div>
        <div>
            <label for="specialization">Specialization</label>
            <input id="specialization" name="specialization" type="text" value="{{ old('specialization', $doctor->specialization) }}">
        </div>
        <div>
            <label for="password">New Password (optional)</label>
            <input id="password" name="password" type="password">
        </div>
        <div>
            <label for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" name="password_confirmation" type="password">
        </div>
        <button type="submit">Update Doctor</button>
    </form>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form.profile-form').forEach((form) => {
        const fields = Array.from(form.querySelectorAll('input, select, textarea'))
            .filter((field) => !field.disabled && field.type !== 'hidden');

        fields.forEach((field, index) => {
            field.addEventListener('keydown', (event) => {
                if (event.key !== 'Enter') return;
                if (index < fields.length - 1) {
                    event.preventDefault();
                    fields[index + 1].focus();
                }
            });
        });
    });
});
</script>
@endpush
