<?php $__env->startSection('content'); ?>
<?php
    $alertsCount = $missedAppointments->count() + $followUpAppointments->count();
    $attendanceWindowLabel = $attendanceWindowStart->format('d M Y') . ' - ' . $attendanceWindowEnd->format('d M Y');
?>
<div class="page-header">
    <div>
        <h2 data-i18n="Staff Dashboard">Staff Dashboard</h2>
    </div>
    <?php if($alertsCount > 0): ?>
        <div class="alert-summary-pill">
            <span class="status-chip warning"><strong><?php echo e($alertsCount); ?></strong> <span data-i18n="items need review">items need review</span></span>
            <span class="alert-summary-pill__text" data-i18n="Review missed attendance and follow-up cases below.">Review missed attendance and follow-up cases below.</span>
        </div>
    <?php else: ?>
        <span class="status-chip success" data-i18n="Attendance Stable">Attendance Stable</span>
    <?php endif; ?>
</div>

<section class="dashboard-grid">
    <article class="profile-card admin-profile">
        <div class="profile-avatar" aria-hidden="true"><?php echo e($profileInitials ?: 'UK'); ?></div>
        <h3><?php echo e(auth()->user()->name); ?></h3>
        <p><?php echo e(auth()->user()->email); ?></p>
        <p><?php echo e(auth()->user()->phone_number ?? '—'); ?></p>
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
                <strong><?php echo e($pendingAppointments); ?></strong>
            </div>
            <div>
                <span data-i18n="Today's Appointments:">Today's Appointments:</span>
                <strong><?php echo e($todayAppointments); ?></strong>
            </div>
            <div>
                <span data-i18n="Active Services:">Active Services:</span>
                <strong><?php echo e($servicesCount); ?></strong>
            </div>
            <div>
                <span data-i18n="Doctors:">Doctors:</span>
                <strong><?php echo e($doctorsCount); ?></strong>
            </div>
        </div>
    </article>

    <article class="info-card detail-card">
        <div class="detail-card__header">
            <h4 data-i18n="Quick Actions">Quick Actions</h4>
        </div>
        <div class="action-grid">
            <a class="action-link" href="<?php echo e(route('admin.services.index')); ?>" data-i18n="Manage Health Services">Manage Health Services</a>
            <a class="action-link" href="<?php echo e(route('admin.doctors.index')); ?>" data-i18n="Manage Doctors">Manage Doctors</a>
            <a class="action-link" href="<?php echo e(route('admin.slots.index')); ?>" data-i18n="Manage Appointment Slots">Manage Appointment Slots</a>
            <a class="action-link" href="<?php echo e(route('admin.appointments.index')); ?>" data-i18n="Manage Appointments">Manage Appointments</a>
            <a class="action-link secondary" href="<?php echo e(route('staff.patients.index')); ?>" data-i18n="Patient Directory">Patient Directory</a>
        </div>
    </article>
</section>

<section>
    <div class="section-header">
        <div>
            <h3 data-i18n="Patient Attendance Monitor">Patient Attendance Monitor</h3>
            <p>
                <span data-i18n="Showing data for">Showing data for</span> <strong><?php echo e($attendanceWindowLabel); ?></strong>
            </p>
        </div>
        <div class="attendance-period-toggle">
            <a
                class="tab-button <?php echo e($attendancePeriod === 'weekly' ? 'active' : ''); ?>"
                href="<?php echo e(route('dashboard', ['attendance_period' => 'weekly'])); ?>"
                data-i18n="Weekly View"
            >Weekly View</a>
            <a
                class="tab-button <?php echo e($attendancePeriod === 'monthly' ? 'active' : ''); ?>"
                href="<?php echo e(route('dashboard', ['attendance_period' => 'monthly'])); ?>"
                data-i18n="Monthly View"
            >Monthly View</a>
        </div>
    </div>

    <div class="stat-grid attendance-summary-grid">
        <article class="stat-card">
            <span data-i18n="Scheduled">Scheduled</span>
            <strong><?php echo e($attendanceSummary['totalScheduled']); ?></strong>
        </article>
        <article class="stat-card">
            <span data-i18n="Attended">Attended</span>
            <strong><?php echo e($attendanceSummary['attendedCount']); ?></strong>
            <small><?php echo e($attendanceSummary['attendanceRate']); ?>% <span data-i18n="attendance rate">attendance rate</span></small>
        </article>
        <article class="stat-card">
            <span data-i18n="Could Not Attend">Could Not Attend</span>
            <strong><?php echo e($attendanceSummary['missedCount']); ?></strong>
            <small><?php echo e($attendanceSummary['missedRate']); ?>% <span data-i18n="missed rate">missed rate</span></small>
        </article>
        <article class="stat-card">
            <span data-i18n="Pending Outcome">Pending Outcome</span>
            <strong><?php echo e($attendanceSummary['pendingCount']); ?></strong>
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
            <?php $__empty_1 = true; $__currentLoopData = $attendanceBuckets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bucket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $rateClass = $bucket['attendanceRate'] >= 80 ? 'success' : ($bucket['attendanceRate'] >= 50 ? 'warning' : 'danger');
                ?>
                <tr>
                    <td><strong><?php echo e($bucket['label']); ?></strong></td>
                    <td><?php echo e($bucket['range']); ?></td>
                    <td><?php echo e($bucket['scheduledCount']); ?></td>
                    <td><?php echo e($bucket['attendedCount']); ?></td>
                    <td><?php echo e($bucket['missedCount']); ?></td>
                    <td>
                        <span class="status-chip <?php echo e($rateClass); ?>"><?php echo e($bucket['attendanceRate']); ?>%</span>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" data-i18n="No appointments scheduled in this period.">No appointments scheduled in this period.</td>
                </tr>
            <?php endif; ?>
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
        <?php if($alertsCount > 0): ?>
            <div class="alert-summary-pill alert-summary-pill--compact">
                <span class="status-chip warning"><strong><?php echo e($alertsCount); ?></strong> <span data-i18n="items to review">items to review</span></span>
            </div>
        <?php else: ?>
            <span class="status-chip success" data-i18n="No alert cases">No alert cases</span>
        <?php endif; ?>
    </div>

    <div class="dashboard-grid admin-alert-grid">
        <article class="info-card detail-card">
            <div class="detail-card__header">
                <h4 data-i18n="Patients Could Not Attend">Patients Could Not Attend</h4>
            </div>
            <ul class="alert-list">
                <?php $__empty_1 = true; $__currentLoopData = $missedAppointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li>
                        <div>
                            <strong><?php echo e($appointment->patient?->name ?? 'Unknown patient'); ?></strong>
                            <span><?php echo e($appointment->scheduled_at->format('d M Y, h:i A')); ?></span>
                            <span><?php echo e($appointment->doctor?->name ?? 'Unassigned doctor'); ?></span>
                        </div>
                        <span class="status-chip danger"><?php echo e(ucfirst($appointment->status)); ?></span>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li class="alert-list__empty" data-i18n="No missed appointments in the selected period.">No missed appointments in the selected period.</li>
                <?php endif; ?>
            </ul>
        </article>

        <article class="info-card detail-card">
            <div class="detail-card__header">
                <h4 data-i18n="Follow-up Needed">Follow-up Needed</h4>
            </div>
            <ul class="alert-list">
                <?php $__empty_1 = true; $__currentLoopData = $followUpAppointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li>
                        <div>
                            <strong><?php echo e($appointment->patient?->name ?? 'Unknown patient'); ?></strong>
                            <span><?php echo e($appointment->scheduled_at->format('d M Y, h:i A')); ?></span>
                            <span><?php echo e($appointment->service?->name ?? 'General'); ?></span>
                        </div>
                        <span class="status-chip warning"><?php echo e(ucfirst($appointment->status)); ?></span>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li class="alert-list__empty" data-i18n="No follow-up appointments currently overdue.">No follow-up appointments currently overdue.</li>
                <?php endif; ?>
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
            <?php $__currentLoopData = $calendarDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="calendar-board__cell">
                    <span class="calendar-board__weekday"><?php echo e($day->format('D')); ?></span>
                    <span class="calendar-board__date"><?php echo e($day->format('d M')); ?></span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php $__currentLoopData = $calendarDoctors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doctor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="calendar-board__row">
                <div class="calendar-board__doctor">
                    <strong><?php echo e($doctor->name); ?></strong>
                    <span><?php echo e($doctor->specialization ?? 'General'); ?></span>
                </div>
                <?php $__currentLoopData = $calendarDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $key = $doctor->id . '|' . $day->format('Y-m-d');
                        $slots = $slotMap->get($key, collect());
                    ?>
                    <div class="calendar-board__cell">
                        <?php if($slots->isEmpty()): ?>
                            <span class="status-chip danger" data-i18n="No slots">No slots</span>
                            <div class="calendar-board__note" data-i18n="Unavailable">Unavailable</div>
                        <?php else: ?>
                            <span class="status-chip success"><?php echo e($slots->count()); ?> <span data-i18n="slots">slots</span></span>
                            <div class="calendar-board__times">
                                <?php $__currentLoopData = $slots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span><?php echo e(\Carbon\Carbon::parse($slot->start_time)->format('H:i')); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ekesihat/ekesihatan/resources/views/dashboard/admin.blade.php ENDPATH**/ ?>