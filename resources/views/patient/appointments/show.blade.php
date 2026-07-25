@extends('layouts.app')
 
@section('content')
<div class="page-header appointment-detail-header">
    <div>
        <h2 data-i18n="Appointment Details">Appointment Details</h2>
        <p>Review your booking information, reminders, and supporting documents.</p>
    </div>
    <a class="button-link secondary" href="{{ route('patient.appointments.index') }}">Back to My Appointments</a>
</div>

<section class="appointment-detail-card">
    <div class="appointment-detail-grid">
        <div>
            <span>Date</span>
            <strong>{{ $appointment->scheduled_at->format('d M Y') }}</strong>
        </div>
        <div>
            <span>Time</span>
            <strong>{{ $appointment->scheduled_at->format('h:i A') }}</strong>
        </div>
        <div>
            <span>Doctor</span>
            <strong>{{ $appointment->doctor->name }}</strong>
        </div>
        <div>
            <span>Service</span>
            <strong>{{ $appointment->service?->name ?? 'General' }}</strong>
        </div>
        <div>
            <span>Status</span>
            <strong>
                <span class="status-chip {{ $appointment->status === 'confirmed' || $appointment->status === 'approved' ? 'success' : ($appointment->status === 'cancelled' ? 'danger' : 'warning') }}">
                    {{ ucfirst($appointment->status) }}
                </span>
            </strong>
        </div>
        <div>
            <span>Queue Number</span>
            <strong>{{ $appointment->queue_number ?: 'Pending confirmation' }}</strong>
        </div>
    </div>

    <div class="appointment-detail-note">
        <h3>Clinical Notes</h3>
        <p>{{ $appointment->notes ?: 'No additional notes were provided for this appointment.' }}</p>
    </div>

    @if ($appointment->checked_in_at)
        <p class="appointment-checkin-state">Attendance recorded.</p>
    @else
        <a class="button-link" href="{{ route('patient.appointments.qr', $appointment) }}" data-i18n="Show QR Check-In">Show QR Check-In</a>
    @endif
</section>

<section class="appointment-detail-card">
    <h3>Email Notifications</h3>
    <p>You will receive a confirmation email and reminder emails 1 hour and 15 minutes before your appointment.</p>
</section>

<section class="appointment-detail-card">
    <h3 data-i18n="Medical Documents">Medical Documents</h3>
    <ul class="appointment-doc-list">
        @forelse ($appointment->documents as $document)
            <li>
                <span>{{ $document->filename }}</span>
                <a class="button-link secondary" href="{{ route('patient.documents.show', $document) }}" target="_blank" data-i18n="View Document">View Document</a>
            </li>
        @empty
            <li data-i18n="No documents uploaded yet.">No documents uploaded yet.</li>
        @endforelse
    </ul>
</section>
@endsection