

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h2>Appointment Slots</h2>
        <p>Configure daily clinic slots and doctor availability windows.</p>
    </div>
    <a class="button-link" href="<?php echo e(route('admin.slots.create')); ?>">Add Slot</a>
</div>

<div class="filter-panel">
    <form method="GET" action="<?php echo e(route('admin.slots.index')); ?>" class="filter-row">
        <div class="auth-field">
            <label for="month">Month</label>
            <input id="month" name="month" type="month" value="<?php echo e($month ?? now()->format('Y-m')); ?>">
        </div>
        <div class="auth-field">
            <label for="date">Exact Date</label>
            <input id="date" name="date" type="date" value="<?php echo e($date ?? ''); ?>">
        </div>
        <div class="auth-field">
            <label for="doctor_id">Doctor</label>
            <select id="doctor_id" name="doctor_id">
                <option value="">All Doctors</option>
                <?php $__currentLoopData = $doctors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doctor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($doctor->id); ?>" <?php if((string) ($doctorId ?? '') === (string) $doctor->id): echo 'selected'; endif; ?>><?php echo e($doctor->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <button type="submit" class="button-link">Search Slots</button>
        <a class="button-link secondary" href="<?php echo e(route('admin.slots.index')); ?>">Reset</a>
    </form>
</div>

<div class="admin-table-wrap">
<table class="admin-table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Time</th>
            <th>Doctor</th>
            <th>Capacity</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $slots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($slot->slot_date->format('d M Y')); ?></td>
                <td><?php echo e($slot->start_time); ?> - <?php echo e($slot->end_time); ?></td>
                <td><?php echo e($slot->doctor->name); ?></td>
                <td><?php echo e($slot->capacity); ?></td>
                <td><?php echo e($slot->is_active ? 'Active' : 'Inactive'); ?></td>
                <td>
                    <div class="admin-actions">
                        <a class="button-link secondary" href="<?php echo e(route('admin.slots.edit', $slot)); ?>">Edit</a>
                        <form
                            action="<?php echo e(route('admin.slots.destroy', $slot)); ?>"
                            method="POST"
                            data-confirm-message="Delete this slot? Existing bookings tied to this slot may be affected."
                        >
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button class="button-link danger" type="submit">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="6">No appointment slots created.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ekesihat/ekesihatan/resources/views/admin/slots/index.blade.php ENDPATH**/ ?>