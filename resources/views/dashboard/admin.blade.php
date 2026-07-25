@extends('layouts.app')

@section('content')
@php
    $alertsCount = $missedAppointments->count() + $followUpAppointments->count();
    $attendanceWindowLabel = $attendanceWindowStart->format('d M Y') . ' - ' . $attendanceWindowEnd->format('d M Y');
@endphp
<div class="page-header">
    <div>
        <h2 data-i18n="Staff Dashboard">Staff Dashboard</h2>
    </div>
    @if ($alertsCount > 0)
        <div class="alert-summary-pill">
            <span class="status-chip warning"><strong>{{ $alertsCount }}</strong> <span data-i18n="items need review">items need review</span></span>
            <span class="alert-summary-pill__text" data-i18n="Review missed attendance and follow-up cases below.">Review missed attendance and follow-up cases below.</span>
        </div>
    @else
        <span class="status-chip success" data-i18n="Attendance Stable">Attendance Stable</span>
    @endif
</div>

<section class="dashboard-grid">
    <article class="profile-card admin-profile">
        <div class="profile-avatar" aria-hidden="true">{{ $profileInitials ?: 'UK' }}</div>
        <h3>{{ auth()->user()->name }}</h3>
        <p>{{ auth()->user()->email }}</p>
        <p>{{ auth()->user()->phone_number ?? '—' }}</p>
        <div class="profile-meta">
            <span data-i18n="Role">Role</span>
            <strong data-i18n="Staff">Staff</strong>
        </div>
    </article>

    <article class="info-card detail-card">
        <div class="detail-card__header">
            <h4 data-i18n="Operations Summary">Operations Summary</h4>
        </div>
        <div class="detail-grid">
            <div>
                <span data-i18n="Pending Appointments:">Pending Appointments:</span>
                <strong>{{ $pendingAppointments }}</strong>
            </div>
            <div>
                <span data-i18n="Today's Appointments:">Today's Appointments:</span>
                <strong>{{ $todayAppointments }}</strong>
            </div>
            <div>
                <span data-i18n="Active Services:">Active Services:</span>
                <strong>{{ $servicesCount }}</strong>
            </div>
            <div>
                <span data-i18n="Doctors:">Doctors:</span>
                <strong>{{ $doctorsCount }}</strong>
            </div>
        </div>
    </article>

    <article class="info-card detail-card">
        <div class="detail-card__header">
            <h4 data-i18n="Quick Actions">Quick Actions</h4>
        </div>
        <div class="action-grid">
            <a class="action-link" href="{{ route('admin.services.index') }}" data-i18n="Manage Health Services">Manage Health Services</a>
            <a class="action-link" href="{{ route('admin.doctors.index') }}" data-i18n="Manage Doctors">Manage Doctors</a>
            <a class="action-link" href="{{ route('admin.slots.index') }}" data-i18n="Manage Appointment Slots">Manage Appointment Slots</a>
            <a class="action-link" href="{{ route('admin.appointments.index') }}" data-i18n="Manage Appointments">Manage Appointments</a>
            <a class="action-link secondary" href="{{ route('staff.patients.index') }}" data-i18n="Patient Directory">Patient Directory</a>
        </div>
    </article>
</section>

<section>
    <div class="section-header">
        <div>
            <h3 data-i18n="Patient Attendance Monitor">Patient Attendance Monitor</h3>
            <p>
                <span data-i18n="Showing data for">Showing data for</span> <strong>{{ $attendanceWindowLabel }}</strong>
            </p>
        </div>
        <div class="attendance-period-toggle">
            <a
                class="tab-button {{ $attendancePeriod === 'weekly' ? 'active' : '' }}"
                href="{{ route('dashboard', ['attendance_period' => 'weekly']) }}"
                data-i18n="Weekly View"
            >Weekly View</a>
            <a
                class="tab-button {{ $attendancePeriod === 'monthly' ? 'active' : '' }}"
                href="{{ route('dashboard', ['attendance_period' => 'monthly']) }}"
                data-i18n="Monthly View"
            >Monthly View</a>
        </div>
    </div>

    <div class="stat-grid attendance-summary-grid">
        <article class="stat-card">
            <span data-i18n="Scheduled">Scheduled</span>
            <strong>{{ $attendanceSummary['totalScheduled'] }}</strong>
        </article>
        <article class="stat-card">
            <span data-i18n="Attended">Attended</span>
            <strong>{{ $attendanceSummary['attendedCount'] }}</strong>
            <small>{{ $attendanceSummary['attendanceRate'] }}% <span data-i18n="attendance rate">attendance rate</span></small>
        </article>
        <article class="stat-card">
            <span data-i18n="Could Not Attend">Could Not Attend</span>
            <strong>{{ $attendanceSummary['missedCount'] }}</strong>
            <small>{{ $attendanceSummary['missedRate'] }}% <span data-i18n="missed rate">missed rate</span></small>
        </article>
        <article class="stat-card">
            <span data-i18n="Pending Outcome">Pending Outcome</span>
            <strong>{{ $attendanceSummary['pendingCount'] }}</strong>
        </article>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th data-i18n="Period">Period</th>
                <th data-i18n="Range">Range</th>
                <th data-i18n="Scheduled">Scheduled</th>
                <th data-i18n="Attended">Attended</th>
                <th data-i18n="Could Not Attend">Could Not Attend</th>
                <th data-i18n="Attendance Rate">Attendance Rate</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($attendanceBuckets as $bucket)
                @php
                    $rateClass = $bucket['attendanceRate'] >= 80 ? 'success' : ($bucket['attendanceRate'] >= 50 ? 'warning' : 'danger');
                @endphp
                <tr>
                    <td><strong>{{ $bucket['label'] }}</strong></td>
                    <td>{{ $bucket['range'] }}</td>
                    <td>{{ $bucket['scheduledCount'] }}</td>
                    <td>{{ $bucket['attendedCount'] }}</td>
                    <td>{{ $bucket['missedCount'] }}</td>
                    <td>
                        <span class="status-chip {{ $rateClass }}">{{ $bucket['attendanceRate'] }}%</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" data-i18n="No appointments scheduled in this period.">No appointments scheduled in this period.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</section>

<section>
    <div class="section-header">
        <div>
            <h3 data-i18n="Attendance Alerts">Attendance Alerts</h3>
            <p data-i18n="Track patients who could not attend and appointments needing follow-up action.">
                Track patients who could not attend and appointments needing follow-up action.
            </p>
        </div>
        @if ($alertsCount > 0)
            <div class="alert-summary-pill alert-summary-pill--compact">
                <span class="status-chip warning"><strong>{{ $alertsCount }}</strong> <span data-i18n="items to review">items to review</span></span>
            </div>
        @else
            <span class="status-chip success" data-i18n="No alert cases">No alert cases</span>
        @endif
    </div>

    <div class="dashboard-grid admin-alert-grid">
        <article class="info-card detail-card">
            <div class="detail-card__header">
                <h4 data-i18n="Patients Could Not Attend">Patients Could Not Attend</h4>
            </div>
            <ul class="alert-list">
                @forelse ($missedAppointments as $appointment)
                    <li>
                        <div>
                            <strong>{{ $appointment->patient?->name ?? 'Unknown patient' }}</strong>
                            <span>{{ $appointment->scheduled_at->format('d M Y, h:i A') }}</span>
                            <span>{{ $appointment->doctor?->name ?? 'Unassigned doctor' }}</span>
                        </div>
                        <span class="status-chip danger">{{ ucfirst($appointment->status) }}</span>
                    </li>
                @empty
                    <li class="alert-list__empty" data-i18n="No missed appointments in the selected period.">No missed appointments in the selected period.</li>
                @endforelse
            </ul>
        </article>

        <article class="info-card detail-card">
            <div class="detail-card__header">
                <h4 data-i18n="Follow-up Needed">Follow-up Needed</h4>
            </div>
            <ul class="alert-list">
                @forelse ($followUpAppointments as $appointment)
                    <li>
                        <div>
                            <strong>{{ $appointment->patient?->name ?? 'Unknown patient' }}</strong>
                            <span>{{ $appointment->scheduled_at->format('d M Y, h:i A') }}</span>
                            <span>{{ $appointment->service?->name ?? 'General' }}</span>
                        </div>
                        <span class="status-chip warning">{{ ucfirst($appointment->status) }}</span>
                    </li>
                @empty
                    <li class="alert-list__empty" data-i18n="No follow-up appointments currently overdue.">No follow-up appointments currently overdue.</li>
                @endforelse
            </ul>
        </article>
    </div>
</section>

<section>
    <div class="section-header">
        <div>
            <h3 data-i18n="Doctor Availability Calendar">Doctor Availability Calendar</h3>
            <p data-i18n="Review weekly slot coverage and spot leave or off-campus duties.">
                Review weekly slot coverage and spot leave or off-campus duties.
            </p>
        </div>
        <span class="status-chip warning" data-i18n="No slots = Unavailable">No slots = Unavailable</span>
    </div>
    <div class="calendar-board">
        <div class="calendar-board__row calendar-board__header">
            <div class="calendar-board__doctor" data-i18n="Doctor">Doctor</div>
            @foreach ($calendarDays as $day)
                <div class="calendar-board__cell">
                    <span class="calendar-board__weekday">{{ $day->format('D') }}</span>
                    <span class="calendar-board__date">{{ $day->format('d M') }}</span>
                </div>
            @endforeach
        </div>
        @foreach ($calendarDoctors as $doctor)
            <div class="calendar-board__row">
                <div class="calendar-board__doctor">
                    <strong>{{ $doctor->name }}</strong>
                    <span>{{ $doctor->specialization ?? 'General' }}</span>
                </div>
                @foreach ($calendarDays as $day)
                    @php
                        $key = $doctor->id . '|' . $day->format('Y-m-d');
                        $slots = $slotMap->get($key, collect());
                    @endphp
                    <div class="calendar-board__cell">
                        @if ($slots->isEmpty())
                            <span class="status-chip danger" data-i18n="No slots">No slots</span>
                            <div class="calendar-board__note" data-i18n="Unavailable">Unavailable</div>
                        @else
                            <span class="status-chip success">{{ $slots->count() }} <span data-i18n="slots">slots</span></span>
                            <div class="calendar-board__times">
                                @foreach ($slots as $slot)
                                    <span>{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</section>
@endsection