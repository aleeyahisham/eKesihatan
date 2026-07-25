@extends('layouts.app')

@section('content')
<div class="doctor-page">
    <div class="doctor-page__header">
        <div>
            <p class="doctor-page__eyebrow">Doctor Workspace</p>
            <h2>Daily Appointments</h2>
            <p class="doctor-page__subtitle">
                Review your assigned consultations and manage emergency schedule changes.
            </p>
        </div>

        <div class="doctor-page__date-badge">
            {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
        </div>
    </div>

    <section class="doctor-toolbar">
        <form
            method="GET"
            action="{{ route('doctor.appointments.index') }}"
            class="doctor-date-form"
        >
            <div class="field-group">
                <label for="date">Select Date</label>
                <input
                    id="date"
                    name="date"
                    type="date"
                    value="{{ $date }}"
                    required
                >
            </div>

            <button type="submit" class="button-link">
                View Schedule
            </button>
        </form>
    </section>

    <section class="emergency-reschedule-card">
        <div class="emergency-reschedule-card__header">
            <div>
                <p class="doctor-page__eyebrow">Emergency Workflow</p>
                <h3>Emergency Reschedule</h3>
                <p>
                    Reassign eligible General Consultation appointments to Dr. Fadzli
                    using the earliest available matching slots.
                </p>
            </div>
        </div>

        <form
            method="POST"
            action="{{ route('doctor.appointments.emergency-reschedule') }}"
            class="emergency-reschedule-form"
            data-confirm-kind="emergency-reschedule"
            data-confirm-message="Trigger emergency rescheduling for all eligible upcoming General Consultation appointments on this date?"
        >
            @csrf

            <input type="hidden" name="date" value="{{ $date }}">

            <div class="field-group">
                <label for="reason">Reason</label>
                <input
                    id="reason"
                    name="reason"
                    type="text"
                    maxlength="500"
                    placeholder="Emergency leave, urgent duty, medical emergency, etc."
                    value="{{ old('reason') }}"
                >
            </div>

            <button type="submit" class="emergency-reschedule-button">
                Trigger Emergency Reschedule
            </button>
        </form>
    </section>

    <section class="doctor-appointment-list">
        <div class="doctor-appointment-list__header">
            <div>
                <h3>Appointments for {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h3>
                <p>{{ $appointments->count() }} appointment(s)</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="doctor-appointment-table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Patient</th>
                        <th>Service</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($appointments as $appointment)
                        <tr>
                            <td data-label="Time">
                                {{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('h:i A') }}
                            </td>

                            <td data-label="Patient">
                                {{ $appointment->patient?->name ?? 'Patient unavailable' }}
                            </td>

                            <td data-label="Service">
                                {{ $appointment->service?->name ?? 'General Consultation' }}
                            </td>

                            <td data-label="Status">
                                <span class="status-pill status-pill--{{ \Illuminate\Support\Str::slug($appointment->status) }}">
                                    {{ ucfirst(str_replace('-', ' ', $appointment->status)) }}
                                </span>
                            </td>

                            <td data-label="Actions">
                                <div class="doctor-actions">
                                    <a
                                        class="button-link secondary"
                                        href="{{ route(
                                            'doctor.appointments.show',
                                            $appointment
                                        ) }}"
                                    >
                                        View
                                    </a>

                                    @if($appointment->patient)
                                        <a
                                            class="button-link secondary"
                                            href="{{ route(
                                                'doctor.patients.history',
                                                $appointment->patient
                                            ) }}"
                                        >
                                            History
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-table-state">
                                No appointments were found for the selected date.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
