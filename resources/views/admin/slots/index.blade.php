@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h2>Appointment Slots</h2>
        <p>Configure daily clinic slots and doctor availability windows.</p>
    </div>
    <a class="button-link" href="{{ route('admin.slots.create') }}">Add Slot</a>
</div>

<div class="filter-panel">
    <form method="GET" action="{{ route('admin.slots.index') }}" class="filter-row">
        <div class="auth-field">
            <label for="month">Month</label>
            <input id="month" name="month" type="month" value="{{ $month ?? now()->format('Y-m') }}">
        </div>
        <div class="auth-field">
            <label for="date">Exact Date</label>
            <input id="date" name="date" type="date" value="{{ $date ?? '' }}">
        </div>
        <div class="auth-field">
            <label for="doctor_id">Doctor</label>
            <select id="doctor_id" name="doctor_id">
                <option value="">All Doctors</option>
                @foreach ($doctors as $doctor)
                    <option value="{{ $doctor->id }}" @selected((string) ($doctorId ?? '') === (string) $doctor->id)>{{ $doctor->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="button-link">Search Slots</button>
        <a class="button-link secondary" href="{{ route('admin.slots.index') }}">Reset</a>
    </form>
</div>

<div class="admin-table-wrap">
<table class="admin-table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Time</th>
            <th>Doctor</th>
            <th>Capacity</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($slots as $slot)
            <tr>
                <td>{{ $slot->slot_date->format('d M Y') }}</td>
                <td>{{ $slot->start_time }} - {{ $slot->end_time }}</td>
                <td>{{ $slot->doctor->name }}</td>
                <td>{{ $slot->capacity }}</td>
                <td>{{ $slot->is_active ? 'Active' : 'Inactive' }}</td>
                <td>
                    <div class="admin-actions">
                        <a class="button-link secondary" href="{{ route('admin.slots.edit', $slot) }}">Edit</a>
                        <form
                            action="{{ route('admin.slots.destroy', $slot) }}"
                            method="POST"
                            data-confirm-message="Delete this slot? Existing bookings tied to this slot may be affected."
                        >
                            @csrf
                            @method('DELETE')
                            <button class="button-link danger" type="submit">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="6">No appointment slots created.</td></tr>
        @endforelse
    </tbody>
</table>
</div>
@endsection