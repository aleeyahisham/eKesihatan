@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h2>Appointments</h2>
        <p>Operations control center for assignment corrections, schedule interventions, and status tracking.</p>
    </div>
</div>

<div class="filter-panel">
    <form method="GET" action="{{ route('admin.appointments.report') }}" class="filter-row">
        <div class="auth-field">
            <label for="report_month">Report Month</label>
            <input id="report_month" name="month" type="month" value="{{ request('month', now()->format('Y-m')) }}" required>
        </div>
        <div class="auth-field">
            <label for="report_week">Week of Month</label>
            <select id="report_week" name="week" required>
                @for ($week = 1; $week <= 5; $week++)
                    <option value="{{ $week }}" @selected((int) request('week', 1) === $week)>Week {{ $week }}</option>
                @endfor
            </select>
        </div>
        <button type="submit" class="button-link">Download Weekly Report</button>
    </form>
</div>

<div class="stat-grid">
    <article class="stat-card">
        <span>Upcoming Queue</span>
        <strong>{{ $metrics['upcoming'] }}</strong>
    </article>
    <article class="stat-card">
        <span>Completed Cases</span>
        <strong>{{ $metrics['completed'] }}</strong>
    </article>
    <article class="stat-card">
        <span>Needs Review</span>
        <strong>{{ $metrics['requiresAction'] }}</strong>
    </article>
</div>

<div class="admin-table-wrap">
<table class="admin-table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Time</th>
            <th>Patient</th>
            <th>Doctor</th>
            <th>Service</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($appointments as $appointment)
            <tr>
                <td>{{ $appointment->scheduled_at->format('d M Y') }}</td>
                <td>{{ $appointment->scheduled_at->format('h:i A') }}</td>
                <td>{{ $appointment->patient->name }}</td>
                <td>{{ $appointment->doctor->name }}</td>
                <td>{{ $appointment->service?->name ?? 'General' }}</td>
                <td>{{ ucfirst($appointment->status) }}</td>
                <td>
                    <div class="admin-actions">
                        <a class="button-link secondary" href="{{ route('admin.appointments.show', $appointment) }}">Review</a>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="7">No appointments found.</td></tr>
        @endforelse
    </tbody>
</table>
</div>
@endsection