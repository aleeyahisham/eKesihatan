@extends('layouts.app')

@section('content')
<div class="page-header qr-page-header">
    <div>
        <h2>QR Check-In</h2>
        <p>Show this QR code at the clinic counter to record your attendance quickly.</p>
    </div>
    <a class="button-link secondary" href="{{ route('patient.appointments.show', $appointment) }}">Back to Appointment</a>
</div>

<section class="qr-checkin-card">
    <div class="qr-checkin-grid">
        <div class="qr-image-panel">
            <img src="{{ $qrImageData ?? $qrImageUrl }}" alt="QR code for check-in" width="220" height="220">
            @if ($isCheckInOpen)
                <p class="qr-help">Keep this page open and let clinic staff scan your code.</p>
            @else
                <p class="qr-help">QR check-in is locked until {{ $checkInOpensAt->format('d M Y, h:i A') }} (10 minutes before your appointment).</p>
            @endif
        </div>

        <div class="qr-detail-panel">
            @if ($appointment->queue_number)
                <p class="qr-detail"><span>Queue Number</span><strong>{{ $appointment->queue_number }}</strong></p>
            @endif
            <p class="qr-detail"><span>Appointment Time</span><strong>{{ $appointment->scheduled_at->format('d M Y, h:i A') }}</strong></p>
            <p class="qr-detail"><span>Doctor</span><strong>{{ $appointment->doctor?->name ?? 'Assigned Doctor' }}</strong></p>
            <p class="qr-detail"><span>Service</span><strong>{{ $appointment->service?->name ?? 'Clinic Service' }}</strong></p>

            <div class="qr-fallback-link">
                <span>Manual check-in link</span>
                @if ($isCheckInOpen)
                    <a href="{{ $checkInUrl }}" target="_blank" rel="noopener noreferrer">{{ $checkInUrl }}</a>
                @else
                    <span>Available at {{ $checkInOpensAt->format('d M Y, h:i A') }}</span>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection