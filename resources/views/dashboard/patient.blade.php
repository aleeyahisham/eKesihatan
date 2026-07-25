@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h2 data-i18n="Patient Profile">Patient Profile</h2>
        <p data-i18n="Manage your appointments and health services.">Manage your appointments and health services.</p>
    </div>
    <a class="button-link" href="{{ route('patient.appointments.create') }}" data-i18n="Book Appointment">Book Appointment</a>
</div>

@php
    $nextAppointment = $appointments->first();
@endphp

<div class="stat-grid">
    <div class="stat-card">
        <span data-i18n="Upcoming Appointments">Upcoming Appointments</span>
        <strong>{{ $appointments->count() }}</strong>
    </div>
    <div class="stat-card">
        <span data-i18n="Next Appointment">Next Appointment</span>
        <strong>{{ $nextAppointment ? $nextAppointment->scheduled_at->format('d M, h:i A') : '—' }}</strong>
    </div>
    <div class="stat-card">
        <span data-i18n="Assigned Doctor">Assigned Doctor</span>
        <strong>{{ $nextAppointment ? $nextAppointment->doctor->name : '—' }}</strong>
    </div>
</div>

<section class="patient-overview">
    <article class="profile-card">
        <div class="profile-avatar" aria-hidden="true">{{ $profileInitials ?: 'EK' }}</div>
        <h3>{{ $patient->name }}</h3>
        <p>{{ $patient->email }}</p>
        <p>{{ $patient->phone_number ?? '—' }}</p>
        <div class="profile-meta">
            <span data-i18n="Student ID">Student ID</span>
            <strong>{{ $patient->student_id ?? '—' }}</strong>
        </div>
        <div class="profile-meta">
            <span data-i18n="Blood Type">Blood Type</span>
            <strong>{{ $patient->blood_type ?? '—' }}</strong>
        </div>
    </article>

    <article class="info-card detail-card">
        <div class="detail-card__header">
            <h4 data-i18n="General Information">General Information</h4>
        </div>
        <div class="detail-grid">
            <div>
                <span data-i18n="Role">Role</span>
                <strong data-i18n="Patient">Patient</strong>
            </div>
            <div>
                <span data-i18n="Registration Date">Registration Date</span>
                <strong>{{ $patient->created_at->format('d M Y') }}</strong>
            </div>
            <div>
                <span data-i18n="Emergency Contact Name">Emergency Contact Name</span>
                <strong>{{ $patient->emergency_contact_name ?? '—' }}</strong>
            </div>
            <div>
                <span data-i18n="Emergency Contact Phone">Emergency Contact Phone</span>
                <strong>{{ $patient->emergency_contact_phone ?? '—' }}</strong>
            </div>
        </div>
    </article>

    <article class="info-card detail-card">
        <div class="detail-card__header">
            <h4 data-i18n="Anamnesis">Anamnesis</h4>
        </div>
        <div class="detail-grid">
            <div>
                <span data-i18n="Allergies or Medical Notes">Allergies or Medical Notes</span>
                <strong>{{ $patient->allergies ?? '—' }}</strong>
            </div>
            <div>
                <span data-i18n="Emergency Contact Relationship">Emergency Contact Relationship</span>
                <strong>{{ $patient->emergency_contact_relationship ?? '—' }}</strong>
            </div>
        </div>
    </article>

    <article class="info-card patient-files">
        <div class="detail-card__header">
            <h4 data-i18n="Medical Files">Medical Files</h4>
        </div>
        <ul class="file-list">
            @forelse ($documents as $document)
                <li>
                    <div>
                        <strong>{{ $document->filename }}</strong>
                        <span>{{ number_format($document->size_bytes / 1024, 1) }} KB</span>
                    </div>
                    <a class="card-link" href="{{ route('patient.documents.show', $document) }}" target="_blank" data-i18n="View Document">View Document</a>
                </li>
            @empty
                <li data-i18n="No documents uploaded yet.">No documents uploaded yet.</li>
            @endforelse
        </ul>
    </article>
</section>

<section class="patient-visits">
    <div class="visit-tabs">
        <button type="button" class="tab-button active" data-tab="upcoming">
            <span data-i18n="Upcoming Visits">Upcoming Visits</span>
            <span class="tab-count">{{ $appointments->count() }}</span>
        </button>
        <button type="button" class="tab-button" data-tab="past">
            <span data-i18n="Past Visits">Past Visits</span>
            <span class="tab-count">{{ $pastAppointments->count() }}</span>
        </button>
    </div>

    <div class="tab-panel is-active" data-tab-panel="upcoming">
        @forelse ($appointments as $appointment)
            <article class="visit-card">
                <div class="visit-date">
                    <strong>{{ $appointment->scheduled_at->format('d M Y') }}</strong>
                    <span>{{ $appointment->scheduled_at->format('h:i A') }}</span>
                </div>
                <div>
                    <span data-i18n="Service">Service</span>
                    <strong>{{ $appointment->service?->name ?? 'General' }}</strong>
                </div>
                <div>
                    <span data-i18n="Doctor">Doctor</span>
                    <strong>{{ $appointment->doctor->name }}</strong>
                </div>
                <div class="visit-status">
                    <span data-i18n="Status">Status</span>
                    <span class="status-chip warning">{{ ucfirst($appointment->status) }}</span>
                </div>
                <a class="card-link" href="{{ route('patient.appointments.show', $appointment) }}" data-i18n="View">View</a>
            </article>
        @empty
            <p data-i18n="No upcoming appointments.">No upcoming appointments.</p>
        @endforelse
    </div>

    <div class="tab-panel" data-tab-panel="past">
        @forelse ($pastAppointments as $appointment)
            <article class="visit-card">
                <div class="visit-date">
                    <strong>{{ $appointment->scheduled_at->format('d M Y') }}</strong>
                    <span>{{ $appointment->scheduled_at->format('h:i A') }}</span>
                </div>
                <div>
                    <span data-i18n="Service">Service</span>
                    <strong>{{ $appointment->service?->name ?? 'General' }}</strong>
                </div>
                <div>
                    <span data-i18n="Doctor">Doctor</span>
                    <strong>{{ $appointment->doctor->name }}</strong>
                </div>
                <div class="visit-status">
                    <span data-i18n="Status">Status</span>
                    <span class="status-chip">{{ ucfirst($appointment->status) }}</span>
                </div>
                <a class="card-link" href="{{ route('patient.appointments.show', $appointment) }}" data-i18n="View">View</a>
            </article>
        @empty
            <p data-i18n="No appointment history.">No appointment history.</p>
        @endforelse
    </div>
</section>

<script>
    (function () {
        const tabs = document.querySelectorAll('.tab-button');
        const panels = document.querySelectorAll('[data-tab-panel]');

        tabs.forEach((tab) => {
            tab.addEventListener('click', () => {
                const target = tab.dataset.tab;
                tabs.forEach((button) => button.classList.remove('active'));
                panels.forEach((panel) => panel.classList.remove('is-active'));
                tab.classList.add('active');
                document.querySelector(`[data-tab-panel="${target}"]`)?.classList.add('is-active');
            });
        });
    })();
</script>
@endsection