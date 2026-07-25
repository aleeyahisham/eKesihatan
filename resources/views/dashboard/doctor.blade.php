@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h2 data-i18n="Doctor Dashboard">Doctor Dashboard</h2>
        <p data-i18n="Today's appointments for">Today's appointments for</p>
        <strong>{{ now()->format('d M Y') }}</strong>
    </div>
    <a class="button-link secondary" href="{{ route('staff.patients.index') }}" data-i18n="Patient Directory">Patient Directory</a>
</div>

@php
    $completedCount = $appointments->where('status', 'completed')->count();
    $noShowCount = $appointments->where('status', 'no-show')->count();
@endphp

<section class="dashboard-grid">
    <article class="profile-card">
        <div class="profile-avatar" aria-hidden="true">{{ $profileInitials ?: 'DR' }}</div>
        <h3>{{ auth()->user()->name }}</h3>
        <p>{{ auth()->user()->email }}</p>
        <p>{{ auth()->user()->phone_number ?? '—' }}</p>
        <div class="profile-meta">
            <span data-i18n="Specialization">Specialization</span>
            <strong>{{ auth()->user()->specialization ?? 'General' }}</strong>
        </div>
    </article>

    <article class="info-card detail-card">
        <div class="detail-card__header">
            <h4 data-i18n="Today's Summary">Today's Summary</h4>
        </div>
        <div class="detail-grid">
            <div>
                <span data-i18n="Today's Appointments:">Today's Appointments:</span>
                <strong>{{ $appointments->count() }}</strong>
            </div>
            <div>
                <span data-i18n="Completed">Completed</span>
                <strong>{{ $completedCount }}</strong>
            </div>
            <div>
                <span data-i18n="No-show">No-show</span>
                <strong>{{ $noShowCount }}</strong>
            </div>
        </div>
    </article>

    <article class="info-card detail-card">
        <div class="detail-card__header">
            <h4 data-i18n="Clinic Notes">Clinic Notes</h4>
        </div>
        <div class="detail-grid">
            <div>
                <span data-i18n="Clinic Focus">Clinic Focus</span>
                <strong data-i18n="Verify student or staff IDs before each consultation.">Verify student or staff IDs before each consultation.</strong>
            </div>
            <div>
                <span data-i18n="Documentation">Documentation</span>
                <strong data-i18n="Upload medical certificates and notes after each visit.">Upload medical certificates and notes after each visit.</strong>
            </div>
            <div>
                <span data-i18n="Queue Management">Queue Management</span>
                <strong data-i18n="Mark no-shows promptly to keep queues accurate.">Mark no-shows promptly to keep queues accurate.</strong>
            </div>
        </div>
    </article>
</section>

<section>
    <h3 data-i18n="Daily Schedule">Daily Schedule</h3>
    <div class="calendar-grid">
        <div class="calendar-day">
            <h4>{{ now()->format('D, d M') }}</h4>
            @forelse ($appointments as $appointment)
                <div class="calendar-event">
                    <strong>{{ $appointment->scheduled_at->format('h:i A') }}</strong>
                    <div>{{ $appointment->patient->name }}</div>
                    <div>{{ $appointment->service?->name ?? 'General' }}</div>
                    <span class="status-chip {{ $appointment->status === 'completed' ? 'success' : ($appointment->status === 'no-show' ? 'danger' : 'warning') }}">
                        {{ ucfirst($appointment->status) }}
                    </span>
                </div>
            @empty
                <p data-i18n="No appointments scheduled.">No appointments scheduled.</p>
            @endforelse
        </div>
    </div>
</section>
@endsection