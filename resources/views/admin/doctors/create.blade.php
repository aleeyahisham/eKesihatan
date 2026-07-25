@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h2>Add Doctor</h2>
        <p>Create a doctor profile with contact and specialization details.</p>
    </div>
    <a class="button-link secondary" href="{{ route('admin.doctors.index') }}">Back to Doctors</a>
</div>

<section class="admin-page-card">
    <form class="auth-form profile-form" method="POST" action="{{ route('admin.doctors.store') }}">
        @csrf
        <div>
            <label for="name">Full Name</label>
            <input id="name" name="name" type="text" required>
        </div>
        <div>
            <label for="email">Email</label>
            <input id="email" name="email" type="email" required>
        </div>
        <div>
            <label for="staff_id">Staff ID</label>
            <input id="staff_id" name="staff_id" type="text">
        </div>
        <div>
            <label for="phone_number">Phone Number</label>
            <input id="phone_number" name="phone_number" type="text">
        </div>
        <div>
            <label for="specialization">Specialization</label>
            <input id="specialization" name="specialization" type="text">
        </div>
        <div>
            <label for="password">Temporary Password</label>
            <input id="password" name="password" type="password" required>
        </div>
        <div>
            <label for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required>
        </div>
        <button type="submit">Create Doctor</button>
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
