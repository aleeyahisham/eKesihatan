@extends('layouts.app')

@section('content')
<div class="page-header appointment-page-header">
    <div>
        <h2 data-i18n="My Appointments">My Appointments</h2>
        <p data-i18n="Track your upcoming visits, reschedule when needed, or cancel with confirmation.">Track your upcoming visits, reschedule when needed, or cancel with confirmation.</p>
    </div>
    <a class="button-link" href="{{ route('patient.appointments.create') }}" data-i18n="Book New Appointment">Book New Appointment</a>
</div>

<div class="appointments-table-card">
    <table class="appointments-table table-card-mobile">
        <thead>
            <tr>
                <th data-i18n="Date & Time">Date & Time</th>
                <th data-i18n="Doctor">Doctor</th>
                <th data-i18n="Service">Service</th>
                <th data-i18n="Status">Status</th>
                <th class="appointments-actions-header" data-i18n="Actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($appointments as $appointment)
                @php
                    $isLocked = $appointment->checked_in_at || in_array($appointment->status, ['checked-in', 'cancelled'], true);
                    $isCancelled = $appointment->status === 'cancelled';
                @endphp
                <tr>
                    <td data-label="Date & Time">
                        <strong>{{ $appointment->scheduled_at->format('d M Y') }}</strong>
                        <div class="appointments-time">{{ $appointment->scheduled_at->format('h:i A') }}</div>
                    </td>
                    <td data-label="Doctor">{{ $appointment->doctor->name }}</td>
                    <td data-label="Service">{{ $appointment->service?->name ?? 'General' }}</td>
                    <td data-label="Status">
                        <span class="status-chip {{ $appointment->status === 'confirmed' ? 'success' : ($appointment->status === 'cancelled' ? 'danger' : 'warning') }}">
                            {{ ucfirst($appointment->status) }}
                        </span>
                    </td>
                    <td data-label="Actions">
                        <div class="appointments-actions">
                            @if ($isCancelled)
                                <span class="button-link secondary disabled" data-i18n="View">View</span>
                            @else
                                <a class="button-link secondary" href="{{ route('patient.appointments.show', $appointment) }}" data-i18n="View">View</a>
                            @endif

                            @if ($isLocked)
                                <span class="button-link secondary disabled" data-i18n="Reschedule">Reschedule</span>
                                <button class="button-link danger" type="button" disabled data-i18n="Cancel">Cancel</button>
                            @else
                                <a class="button-link secondary" href="{{ route('patient.appointments.edit', $appointment) }}" data-i18n="Reschedule">Reschedule</a>
                                <form action="{{ route('patient.appointments.destroy', $appointment) }}" method="POST" class="appointments-delete-form" data-confirm-kind="delete">
                                    @csrf
                                    @method('DELETE')
                                    <button class="button-link danger" type="submit" data-i18n="Cancel">Cancel</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="table-empty" colspan="5" data-i18n="No appointments found.">No appointments found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection