 
<?php $__env->startSection('content'); ?>
<div class="page-header appointment-detail-header">
    <div>
        <h2 data-i18n="Appointment Details">Appointment Details</h2>
        <p>Review your booking information, reminders, and supporting documents.</p>
    </div>
    <a class="button-link secondary" href="<?php echo e(route('patient.appointments.index')); ?>">Back to My Appointments</a>
</div>

<section class="appointment-detail-card">
    <div class="appointment-detail-grid">
        <div>
            <span>Date</span>
            <strong><?php echo e($appointment->scheduled_at->format('d M Y')); ?></strong>
        </div>
        <div>
            <span>Time</span>
            <strong><?php echo e($appointment->scheduled_at->format('h:i A')); ?></strong>
        </div>
        <div>
            <span>Doctor</span>
            <strong><?php echo e($appointment->doctor->name); ?></strong>
        </div>
        <div>
            <span>Service</span>
            <strong><?php echo e($appointment->service?->name ?? 'General'); ?></strong>
        </div>
        <div>
            <span>Status</span>
            <strong>
                <span class="status-chip <?php echo e($appointment->status === 'confirmed' || $appointment->status === 'approved' ? 'success' : ($appointment->status === 'cancelled' ? 'danger' : 'warning')); ?>">
                    <?php echo e(ucfirst($appointment->status)); ?>

                </span>
            </strong>
        </div>
        <div>
            <span>Queue Number</span>
            <strong><?php echo e($appointment->queue_number ?: 'Pending confirmation'); ?></strong>
        </div>
    </div>

    <div class="appointment-detail-note">
        <h3>Clinical Notes</h3>
        <p><?php echo e($appointment->notes ?: 'No additional notes were provided for this appointment.'); ?></p>
    </div>

    <?php if($appointment->checked_in_at): ?>
        <p class="appointment-checkin-state">Attendance recorded.</p>
    <?php else: ?>
        <a class="button-link" href="<?php echo e(route('patient.appointments.qr', $appointment)); ?>" data-i18n="Show QR Check-In">Show QR Check-In</a>
    <?php endif; ?>
</section>

<section class="appointment-detail-card">
    <h3>Email Notifications</h3>
    <p>You will receive a confirmation email and reminder emails 1 hour and 15 minutes before your appointment.</p>
</section>

<section class="appointment-detail-card">
    <h3 data-i18n="Medical Documents">Medical Documents</h3>
    <ul class="appointment-doc-list">
        <?php $__empty_1 = true; $__currentLoopData = $appointment->documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <li>
                <span><?php echo e($document->filename); ?></span>
                <a class="button-link secondary" href="<?php echo e(route('patient.documents.show', $document)); ?>" target="_blank" data-i18n="View Document">View Document</a>
            </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <li data-i18n="No documents uploaded yet.">No documents uploaded yet.</li>
        <?php endif; ?>
    </ul>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ekesihat/ekesihatan/resources/views/patient/appointments/show.blade.php ENDPATH**/ ?>