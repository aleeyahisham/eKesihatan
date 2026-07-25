
 
<?php $__env->startSection('content'); ?>
<h2 data-i18n="Appointment Details">Appointment Details</h2>
 
<p><strong data-i18n="Patient:">Patient:</strong> <?php echo e($appointment->patient->name); ?></p>
<p><strong data-i18n="Service:">Service:</strong> <?php echo e($appointment->service?->name ?? 'General'); ?></p>
<p><strong data-i18n="Scheduled At:">Scheduled At:</strong> <?php echo e($appointment->scheduled_at->format('d M Y, h:i A')); ?></p>
<p><strong data-i18n="Status:">Status:</strong> <?php echo e(ucfirst($appointment->status)); ?></p>
<?php ($canManageAppointment = $appointment->checked_in_at || $appointment->status === 'checked-in'); ?>
<?php if($appointment->checked_in_at): ?>
    <p><strong data-i18n="Checked in at:">Checked in at:</strong> <?php echo e($appointment->checked_in_at->format('h:i A')); ?></p>
<?php else: ?>
    <p data-i18n="Not checked in yet.">Not checked in yet.</p>
<?php endif; ?>
 
<form method="POST" action="<?php echo e(route('doctor.appointments.update', $appointment)); ?>">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PATCH'); ?>
    <label for="status" data-i18n="Update Status">Update Status</label>
    <select id="status" name="status" <?php if(!$canManageAppointment): echo 'disabled'; endif; ?>>
        <option value="completed" <?php if($appointment->status === 'completed'): echo 'selected'; endif; ?>>Completed</option>
        <option value="no-show" <?php if($appointment->status === 'no-show'): echo 'selected'; endif; ?>>No-show</option>
    </select>
    <button type="submit" data-i18n="Update" <?php if(!$canManageAppointment): echo 'disabled'; endif; ?>>Update</button>
    <?php if (! ($canManageAppointment)): ?>
        <p class="profile-help">Status update is disabled until the patient checks in.</p>
    <?php endif; ?>
</form>
 
<h3 data-i18n="Medical Documents">Medical Documents</h3>
<?php if($canManageAppointment): ?>
    <a class="button-link secondary" href="<?php echo e(route('doctor.documents.create', $appointment)); ?>" data-i18n="Upload Document">Upload Document</a>
<?php else: ?>
    <span class="button-link secondary disabled" data-i18n="Upload Document">Upload Document</span>
    <p class="profile-help">Document upload is disabled until the patient checks in.</p>
<?php endif; ?>
<ul>
    <?php $__empty_1 = true; $__currentLoopData = $appointment->documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <li>
            <?php echo e($document->filename); ?>

            <a href="<?php echo e(route('doctor.documents.show', $document)); ?>" target="_blank" data-i18n="View Document">View Document</a>
        </li>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <li data-i18n="No documents uploaded.">No documents uploaded.</li>
    <?php endif; ?>
</ul>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ekesihat/ekesihatan/resources/views/doctor/appointments/show.blade.php ENDPATH**/ ?>