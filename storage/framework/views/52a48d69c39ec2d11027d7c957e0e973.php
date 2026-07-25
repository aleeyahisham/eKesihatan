

<?php $__env->startSection('content'); ?>
<div class="page-header checkin-page-header">
    <div>
        <h2>
            <?php echo e(!empty($isTooEarly)
                ? 'Check-In Not Open Yet'
                : (!empty($isDuplicate) ? 'Check-In Already Recorded' : 'Check-In Successful')); ?>

        </h2>
        <p>
            <?php echo e(!empty($isTooEarly)
                ? 'This QR can only be used 10 minutes before your appointment. Please try again at ' . $checkInOpensAt->format('h:i A') . '.'
                : (!empty($isDuplicate)
                    ? 'This appointment has already been checked in. Duplicate check-in is not allowed.'
                    : 'Your attendance has been recorded successfully.')); ?>

        </p>
    </div>
    <a class="button-link secondary" href="<?php echo e(auth()->check() ? route('dashboard') : route('landing')); ?>">
        <?php echo e(auth()->check() ? 'Back to Dashboard' : 'Back to Home'); ?>

    </a>
</div>

<section class="checkin-result-card <?php echo e(!empty($isTooEarly) ? 'is-duplicate' : (!empty($isDuplicate) ? 'is-duplicate' : 'is-success')); ?>">
    <p class="checkin-result-banner">
        <?php echo e(!empty($isTooEarly)
            ? 'Check-in is currently locked. Please wait until the check-in window opens.'
            : (!empty($isDuplicate)
                ? 'Duplicate scan detected: existing check-in details are shown below.'
                : 'Check-in accepted: clinic staff can continue with your queue.')); ?>

    </p>

    <div class="checkin-result-grid">
        <p class="checkin-result-item"><span>Patient</span><strong><?php echo e($appointment->patient->name); ?></strong></p>
        <p class="checkin-result-item"><span>Appointment</span><strong><?php echo e($appointment->scheduled_at->format('d M Y, h:i A')); ?></strong></p>

        <?php if($appointment->queue_number): ?>
            <p class="checkin-result-item"><span>Queue Number</span><strong><?php echo e($appointment->queue_number); ?></strong></p>
        <?php endif; ?>

        <?php if($appointment->checked_in_at): ?>
            <p class="checkin-result-item"><span>Checked in at</span><strong><?php echo e($appointment->checked_in_at->format('h:i A')); ?></strong></p>
        <?php endif; ?>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ekesihat/ekesihatan/resources/views/queue/checked-in.blade.php ENDPATH**/ ?>