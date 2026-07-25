@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h2 data-i18n="Patient Details">Patient Details</h2>
        <p data-i18n="Emergency information for clinical use.">Emergency information for clinical use.</p>
    </div>
    <a class="button-link secondary" href="{{ route('staff.patients.index') }}" data-i18n="Back to directory">Back to directory</a>
</div>

<section class="info-panel">
    <div class="info-panel__grid">
        <div>
            <strong data-i18n="Full Name">Full Name</strong>
            <p>{{ $patient->name }}</p>
        </div>
        <div>
            <strong data-i18n="Email">Email</strong>
            <p>{{ $patient->email }}</p>
        </div>
        <div>
            <strong data-i18n="Phone Number">Phone Number</strong>
            <p>{{ $patient->phone_number ?? '—' }}</p>
        </div>
        <div>
            <strong data-i18n="Student ID">Student ID</strong>
            <p>{{ $patient->student_id ?? '—' }}</p>
        </div>
        <div>
            <strong data-i18n="Blood Type">Blood Type</strong>
            <p>{{ $patient->blood_type ?? '—' }}</p>
        </div>
        <div>
            <strong data-i18n="Emergency Contact Name">Emergency Contact Name</strong>
            <p>{{ $patient->emergency_contact_name ?? '—' }}</p>
        </div>
        <div>
            <strong data-i18n="Emergency Contact Phone">Emergency Contact Phone</strong>
            <p>{{ $patient->emergency_contact_phone ?? '—' }}</p>
        </div>
        <div>
            <strong data-i18n="Emergency Contact Relationship">Emergency Contact Relationship</strong>
            <p>{{ $patient->emergency_contact_relationship ?? '—' }}</p>
        </div>
        <div class="info-panel__full">
            <strong data-i18n="Allergies or Medical Notes">Allergies or Medical Notes</strong>
            <p>{{ $patient->allergies ?? '—' }}</p>
        </div>
    </div>
</section>
@endsection
