@extends('layouts.app')
 
@section('content')
<div class="page-header">
    <div>
        <h2>Edit Appointment Slot</h2>
        <p>Adjust time, capacity, and visibility for this doctor slot.</p>
    </div>
    <a class="button-link secondary" href="{{ route('admin.slots.index') }}">Back to Slots</a>
</div>

<section class="admin-page-card">
    <form class="auth-form profile-form" method="POST" action="{{ route('admin.slots.update', $slot) }}">
        @csrf
        @method('PUT')
        <div>
            <label for="doctor_id">Doctor</label>
            <select id="doctor_id" name="doctor_id" required>
                @foreach ($doctors as $doctor)
                    <option value="{{ $doctor->id }}" @selected($slot->doctor_id === $doctor->id)>{{ $doctor->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="slot_date">Date</label>
            <input id="slot_date" name="slot_date" type="date" value="{{ old('slot_date', $slot->slot_date->format('Y-m-d')) }}" required>
        </div>
        <div>
            <label for="start_time">Start Time</label>
            <input id="start_time" name="start_time" type="time" value="{{ old('start_time', $slot->start_time) }}" required>
        </div>
        <div>
            <label for="end_time">End Time</label>
            <input id="end_time" name="end_time" type="time" value="{{ old('end_time', $slot->end_time) }}" required>
        </div>
        <div>
            <label for="capacity">Capacity</label>
            <input id="capacity" name="capacity" type="number" min="1" max="20" value="{{ old('capacity', $slot->capacity) }}" required>
        </div>
        <div>
            <label for="location">Location</label>
            <input id="location" name="location" type="text" value="{{ old('location', $slot->location) }}">
        </div>
        <div>
            <input type="hidden" name="is_active" value="0">
            <label for="is_active">
                <input id="is_active" name="is_active" type="checkbox" value="1" @checked($slot->is_active)>
                Active
            </label>
        </div>
        <button type="submit">Update Slot</button>
    </form>
</section>
@endsection