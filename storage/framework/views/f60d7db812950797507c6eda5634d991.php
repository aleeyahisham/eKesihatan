

<?php $__env->startSection('content'); ?>
<div class="page-header appointment-page-header">
    <div>
        <h2 data-i18n="My Appointments">My Appointments</h2>
        <p data-i18n="Track your upcoming visits, reschedule when needed, or cancel with confirmation.">Track your upcoming visits, reschedule when needed, or cancel with confirmation.</p>
    </div>
    <a class="button-link" href="<?php echo e(route('patient.appointments.create')); ?>" data-i18n="Book New Appointment">Book New Appointment</a>
</div>

<div class="appointments-table-card">
    <table class="appointments-table table-card-mobile">
        <thead>
            <tr>
                <th data-i18n="Date & Time">Date & Time</th>
                <th data-i18n="Doctor">Doctor</th>
                <th data-i18n="Service">Service</th>
                <th data-i18n="Status">Status</th>
                <th class="appointments-actions-header" data-i18n="Actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $appointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $isLocked = $appointment->checked_in_at || in_array($appointment->status, ['checked-in', 'cancelled'], true);
                    $isCancelled = $appointment->status === 'cancelled';
                ?>
                <tr>
                    <td data-label="Date & Time">
                        <strong><?php echo e($appointment->scheduled_at->format('d M Y')); ?></strong>
                        <div class="appointments-time"><?php echo e($appointment->scheduled_at->format('h:i A')); ?></div>
                    </td>
                    <td data-label="Doctor"><?php echo e($appointment->doctor->name); ?></td>
                    <td data-label="Service"><?php echo e($appointment->service?->name ?? 'General'); ?></td>
                    <td data-label="Status">
                        <span class="status-chip <?php echo e($appointment->status === 'confirmed' ? 'success' : ($appointment->status === 'cancelled' ? 'danger' : 'warning')); ?>">
                            <?php echo e(ucfirst($appointment->status)); ?>

                        </span>
                    </td>
                    <td data-label="Actions">
                        <div class="appointments-actions">
                            <?php if($isCancelled): ?>
                                <span class="button-link secondary disabled" data-i18n="View">View</span>
                            <?php else: ?>
                                <a class="button-link secondary" href="<?php echo e(route('patient.appointments.show', $appointment)); ?>" data-i18n="View">View</a>
                            <?php endif; ?>

                            <?php if($isLocked): ?>
                                <span class="button-link secondary disabled" data-i18n="Reschedule">Reschedule</span>
                                <button class="button-link danger" type="button" disabled data-i18n="Cancel">Cancel</button>
                            <?php else: ?>
                                <a class="button-link secondary" href="<?php echo e(route('patient.appointments.edit', $appointment)); ?>" data-i18n="Reschedule">Reschedule</a>
                                <form action="<?php echo e(route('patient.appointments.destroy', $appointment)); ?>" method="POST" class="appointments-delete-form" data-confirm-kind="delete">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button class="button-link danger" type="submit" data-i18n="Cancel">Cancel</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td class="table-empty" colspan="5" data-i18n="No appointments found.">No appointments found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ekesihat/ekesihatan/resources/views/patient/appointments/index.blade.php ENDPATH**/ ?>