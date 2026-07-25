@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h2>Appointment Review</h2>
        <p>Review assignment details and update appointment routing/status.</p>
    </div>
    <a class="button-link secondary" href="{{ route('admin.appointments.index') }}">Back to Appointments</a>
</div>

<section class="admin-page-card">
    <p><strong>Patient:</strong> {{ $appointment->patient->name }}</p>
    <p><strong>Email:</strong> {{ $appointment->patient->email }}</p>
    <p><strong>Service:</strong> {{ $appointment->service?->name ?? 'General' }}</p>
    <p><strong>Current Status:</strong> {{ ucfirst($appointment->status) }}</p>
    <p><strong>Scheduled At:</strong> {{ $appointment->scheduled_at->format('d M Y, h:i A') }}</p>
</section>

<section class="admin-page-card">
<form class="auth-form profile-form" method="POST" action="{{ route('admin.appointments.update', $appointment) }}">
    @csrf
    @method('PUT')
    <div>
        <label for="doctor_id">Doctor</label>
        <select id="doctor_id" name="doctor_id" required>
            @foreach ($doctors as $doctor)
                <option value="{{ $doctor->id }}" @selected($appointment->doctor_id === $doctor->id)>{{ $doctor->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="appointment_slot_id">Slot</label>
        <select id="appointment_slot_id" name="appointment_slot_id" required>
            @foreach ($slots as $slot)
                <option value="{{ $slot->id }}" @selected($appointment->appointment_slot_id === $slot->id)>
                    {{ $slot->slot_date->format('d M Y') }} {{ $slot->start_time }} - {{ $slot->end_time }}
                    (Dr. {{ $slot->doctor->name }})
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="status">Status</label>
        <select id="status" name="status" required>
            @foreach (['approved', 'checked-in', 'completed', 'no-show', 'rescheduled', 'rejected', 'cancelled'] as $status)
                <option value="{{ $status }}" @selected($appointment->status === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="notes">Notes</label>
        <textarea id="notes" name="notes" rows="3">{{ old('notes', $appointment->notes) }}</textarea>
    </div>
    <button type="submit">Update Appointment</button>
</form>
</section>
@endsection