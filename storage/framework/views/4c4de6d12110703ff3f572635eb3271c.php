<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h2>Appointment Review</h2>
        <p>Review assignment details and update appointment routing/status.</p>
    </div>
    <a class="button-link secondary" href="<?php echo e(route('admin.appointments.index')); ?>">Back to Appointments</a>
</div>

<section class="admin-page-card">
    <p><strong>Patient:</strong> <?php echo e($appointment->patient->name); ?></p>
    <p><strong>Email:</strong> <?php echo e($appointment->patient->email); ?></p>
    <p><strong>Service:</strong> <?php echo e($appointment->service?->name ?? 'General'); ?></p>
    <p><strong>Current Status:</strong> <?php echo e(ucfirst($appointment->status)); ?></p>
    <p><strong>Scheduled At:</strong> <?php echo e($appointment->scheduled_at->format('d M Y, h:i A')); ?></p>
</section>

<section class="admin-page-card">
<form class="auth-form profile-form" method="POST" action="<?php echo e(route('admin.appointments.update', $appointment)); ?>">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>
    <div>
        <label for="doctor_id">Doctor</label>
        <select id="doctor_id" name="doctor_id" required>
            <?php $__currentLoopData = $doctors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doctor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($doctor->id); ?>" <?php if($appointment->doctor_id === $doctor->id): echo 'selected'; endif; ?>><?php echo e($doctor->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div>
        <label for="appointment_slot_id">Slot</label>
        <select id="appointment_slot_id" name="appointment_slot_id" required>
            <?php $__currentLoopData = $slots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($slot->id); ?>" <?php if($appointment->appointment_slot_id === $slot->id): echo 'selected'; endif; ?>>
                    <?php echo e($slot->slot_date->format('d M Y')); ?> <?php echo e($slot->start_time); ?> - <?php echo e($slot->end_time); ?>

                    (Dr. <?php echo e($slot->doctor->name); ?>)
                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div>
        <label for="status">Status</label>
        <select id="status" name="status" required>
            <?php $__currentLoopData = ['approved', 'checked-in', 'completed', 'no-show', 'rescheduled', 'rejected', 'cancelled']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($status); ?>" <?php if($appointment->status === $status): echo 'selected'; endif; ?>><?php echo e(ucfirst($status)); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div>
        <label for="notes">Notes</label>
        <textarea id="notes" name="notes" rows="3"><?php echo e(old('notes', $appointment->notes)); ?></textarea>
    </div>
    <button type="submit">Update Appointment</button>
</form>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ekesihat/ekesihatan/resources/views/admin/appointments/show.blade.php ENDPATH**/ ?>