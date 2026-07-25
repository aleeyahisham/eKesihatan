@extends('layouts.app')

@section('content')
<div class="page-header checkin-page-header">
    <div>
        <h2>
            {{ !empty($isTooEarly)
                ? 'Check-In Not Open Yet'
                : (!empty($isDuplicate) ? 'Check-In Already Recorded' : 'Check-In Successful') }}
        </h2>
        <p>
            {{ !empty($isTooEarly)
                ? 'This QR can only be used 10 minutes before your appointment. Please try again at ' . $checkInOpensAt->format('h:i A') . '.'
                : (!empty($isDuplicate)
                    ? 'This appointment has already been checked in. Duplicate check-in is not allowed.'
                    : 'Your attendance has been recorded successfully.') }}
        </p>
    </div>
    <a class="button-link secondary" href="{{ auth()->check() ? route('dashboard') : route('landing') }}">
        {{ auth()->check() ? 'Back to Dashboard' : 'Back to Home' }}
    </a>
</div>

<section class="checkin-result-card {{ !empty($isTooEarly) ? 'is-duplicate' : (!empty($isDuplicate) ? 'is-duplicate' : 'is-success') }}">
    <p class="checkin-result-banner">
        {{ !empty($isTooEarly)
            ? 'Check-in is currently locked. Please wait until the check-in window opens.'
            : (!empty($isDuplicate)
                ? 'Duplicate scan detected: existing check-in details are shown below.'
                : 'Check-in accepted: clinic staff can continue with your queue.') }}
    </p>

    <div class="checkin-result-grid">
        <p class="checkin-result-item"><span>Patient</span><strong>{{ $appointment->patient->name }}</strong></p>
        <p class="checkin-result-item"><span>Appointment</span><strong>{{ $appointment->scheduled_at->format('d M Y, h:i A') }}</strong></p>

        @if ($appointment->queue_number)
            <p class="checkin-result-item"><span>Queue Number</span><strong>{{ $appointment->queue_number }}</strong></p>
        @endif

        @if ($appointment->checked_in_at)
            <p class="checkin-result-item"><span>Checked in at</span><strong>{{ $appointment->checked_in_at->format('h:i A') }}</strong></p>
        @endif
    </div>
</section>
@endsection