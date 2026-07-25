<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h2 data-i18n="Doctor Dashboard">Doctor Dashboard</h2>
        <p data-i18n="Today's appointments for">Today's appointments for</p>
        <strong><?php echo e(now()->format('d M Y')); ?></strong>
    </div>
    <a class="button-link secondary" href="<?php echo e(route('staff.patients.index')); ?>" data-i18n="Patient Directory">Patient Directory</a>
</div>

<?php
    $completedCount = $appointments->where('status', 'completed')->count();
    $noShowCount = $appointments->where('status', 'no-show')->count();
?>

<section class="dashboard-grid">
    <article class="profile-card">
        <div class="profile-avatar" aria-hidden="true"><?php echo e($profileInitials ?: 'DR'); ?></div>
        <h3><?php echo e(auth()->user()->name); ?></h3>
        <p><?php echo e(auth()->user()->email); ?></p>
        <p><?php echo e(auth()->user()->phone_number ?? '—'); ?></p>
        <div class="profile-meta">
            <span data-i18n="Specialization">Specialization</span>
            <strong><?php echo e(auth()->user()->specialization ?? 'General'); ?></strong>
        </div>
    </article>

    <article class="info-card detail-card">
        <div class="detail-card__header">
            <h4 data-i18n="Today's Summary">Today's Summary</h4>
        </div>
        <div class="detail-grid">
            <div>
                <span data-i18n="Today's Appointments:">Today's Appointments:</span>
                <strong><?php echo e($appointments->count()); ?></strong>
            </div>
            <div>
                <span data-i18n="Completed">Completed</span>
                <strong><?php echo e($completedCount); ?></strong>
            </div>
            <div>
                <span data-i18n="No-show">No-show</span>
                <strong><?php echo e($noShowCount); ?></strong>
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
            <h4><?php echo e(now()->format('D, d M')); ?></h4>
            <?php $__empty_1 = true; $__currentLoopData = $appointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="calendar-event">
                    <strong><?php echo e($appointment->scheduled_at->format('h:i A')); ?></strong>
                    <div><?php echo e($appointment->patient->name); ?></div>
                    <div><?php echo e($appointment->service?->name ?? 'General'); ?></div>
                    <span class="status-chip <?php echo e($appointment->status === 'completed' ? 'success' : ($appointment->status === 'no-show' ? 'danger' : 'warning')); ?>">
                        <?php echo e(ucfirst($appointment->status)); ?>

                    </span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p data-i18n="No appointments scheduled.">No appointments scheduled.</p>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ekesihat/ekesihatan/resources/views/dashboard/doctor.blade.php ENDPATH**/ ?>