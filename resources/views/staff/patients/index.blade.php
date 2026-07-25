@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h2 data-i18n="Patient Directory">Patient Directory</h2>
        <p data-i18n="View patient demographic and emergency details.">View patient demographic and emergency details.</p>
    </div>
</div>

<details class="filter-panel" @if (!empty($query)) open @endif>
    <summary data-i18n="Search patients">Search patients</summary>
    <form method="GET" action="{{ route('staff.patients.index') }}">
        <div>
            <label for="patient-search" data-i18n="Search by name or student ID">Search by name or student ID</label>
            <input id="patient-search" name="q" type="text" value="{{ $query }}" placeholder="STU-12345">
        </div>
        <div class="quick-actions">
            <button type="submit" class="button-link" data-i18n="Search">Search</button>
            <a class="button-link secondary" href="{{ route('staff.patients.index') }}" data-i18n="Reset">Reset</a>
        </div>
    </form>
</details>

<section class="card-grid">
    @forelse ($patients as $patient)
        <article class="info-card">
            <div class="info-card__header">
                <h3>{{ $patient->name }}</h3>
                <span class="status-chip">{{ $patient->blood_type ?: 'N/A' }}</span>
            </div>
            <p><strong data-i18n="Student ID">Student ID</strong>: {{ $patient->student_id ?? '—' }}</p>
            <p><strong data-i18n="Phone Number">Phone Number</strong>: {{ $patient->phone_number ?? '—' }}</p>
            <p><strong data-i18n="Emergency Contact">Emergency Contact</strong>: {{ $patient->emergency_contact_name ?? '—' }}</p>
            <a class="card-link" href="{{ route('staff.patients.show', $patient) }}" data-i18n="View Details">View Details</a>
        </article>
    @empty
        <p data-i18n="No patients found.">No patients found.</p>
    @endforelse
</section>
@endsection
