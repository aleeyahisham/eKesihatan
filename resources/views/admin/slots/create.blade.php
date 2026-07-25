@extends('layouts.app')
 
@section('content')
<div class="page-header">
    <div>
        <h2 data-i18n="Add Appointment Slot">Add Appointment Slot</h2>
        <p>Create a new availability slot for doctor appointment assignment.</p>
    </div>
    <a class="button-link secondary" href="{{ route('admin.slots.index') }}">Back to Slots</a>
</div>

<section class="admin-page-card">
    <form class="auth-form profile-form" method="POST" action="{{ route('admin.slots.store') }}">
        @csrf
        <div>
            <label for="doctor_id" data-i18n="Doctor">Doctor</label>
            <select id="doctor_id" name="doctor_id" required>
                @foreach ($doctors as $doctor)
                    <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="slot_date" data-i18n="Date">Date</label>
            <input id="slot_date" name="slot_date" type="date" required>
        </div>
        <div>
            <label for="start_time" data-i18n="Start Time">Start Time</label>
            <input id="start_time" name="start_time" type="time" required>
        </div>
        <div>
            <label for="end_time" data-i18n="End Time">End Time</label>
            <input id="end_time" name="end_time" type="time" required>
        </div>
        <div>
            <label for="capacity" data-i18n="Capacity">Capacity</label>
            <input id="capacity" name="capacity" type="number" min="1" max="20" value="1" required>
        </div>
        <div>
            <label for="location" data-i18n="Location">Location</label>
            <input id="location" name="location" type="text">
        </div>
        <div>
            <input type="hidden" name="is_active" value="0">
            <label for="is_active">
                <input id="is_active" name="is_active" type="checkbox" value="1" checked>
                <span data-i18n="Active">Active</span>
            </label>
        </div>
        <button type="submit" data-i18n="Create Slot">Create Slot</button>
    </form>
</section>
@endsection