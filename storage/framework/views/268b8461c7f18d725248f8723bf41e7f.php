

<?php $__env->startSection('content'); ?>
<div class="page-header qr-page-header">
    <div>
        <h2>QR Check-In</h2>
        <p>Show this QR code at the clinic counter to record your attendance quickly.</p>
    </div>
    <a class="button-link secondary" href="<?php echo e(route('patient.appointments.show', $appointment)); ?>">Back to Appointment</a>
</div>

<section class="qr-checkin-card">
    <div class="qr-checkin-grid">
        <div class="qr-image-panel">
            <img src="<?php echo e($qrImageData ?? $qrImageUrl); ?>" alt="QR code for check-in" width="220" height="220">
            <?php if($isCheckInOpen): ?>
                <p class="qr-help">Keep this page open and let clinic staff scan your code.</p>
            <?php else: ?>
                <p class="qr-help">QR check-in is locked until <?php echo e($checkInOpensAt->format('d M Y, h:i A')); ?> (10 minutes before your appointment).</p>
            <?php endif; ?>
        </div>

        <div class="qr-detail-panel">
            <?php if($appointment->queue_number): ?>
                <p class="qr-detail"><span>Queue Number</span><strong><?php echo e($appointment->queue_number); ?></strong></p>
            <?php endif; ?>
            <p class="qr-detail"><span>Appointment Time</span><strong><?php echo e($appointment->scheduled_at->format('d M Y, h:i A')); ?></strong></p>
            <p class="qr-detail"><span>Doctor</span><strong><?php echo e($appointment->doctor?->name ?? 'Assigned Doctor'); ?></strong></p>
            <p class="qr-detail"><span>Service</span><strong><?php echo e($appointment->service?->name ?? 'Clinic Service'); ?></strong></p>

            <div class="qr-fallback-link">
                <span>Manual check-in link</span>
                <?php if($isCheckInOpen): ?>
                    <a href="<?php echo e($checkInUrl); ?>" target="_blank" rel="noopener noreferrer"><?php echo e($checkInUrl); ?></a>
                <?php else: ?>
                    <span>Available at <?php echo e($checkInOpensAt->format('d M Y, h:i A')); ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ekesihat/ekesihatan/resources/views/appointments/qr.blade.php ENDPATH**/ ?>