

<?php $__env->startSection('content'); ?>
<h2>Patient History</h2>
<p><?php echo e($patient->name); ?></p>

<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Doctor</th>
            <th>Service</th>
            <th>Status</th>
            <th>Attachment</th>
        </tr>
    </thead>
    <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $appointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($appointment->scheduled_at->format('d M Y')); ?></td>
                <td><?php echo e($appointment->doctor->name); ?></td>
                <td><?php echo e($appointment->service?->name ?? 'General'); ?></td>
                <td><?php echo e(ucfirst($appointment->status)); ?></td>
                <td>
                    <div class="table-actions">
                        <?php $__empty_2 = true; $__currentLoopData = $appointment->documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                            <a class="button-link secondary" href="<?php echo e(route('doctor.documents.show', $document)); ?>" target="_blank">View Attachment</a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                            <span class="button-link secondary disabled">View Attachment</span>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="5">No appointment history.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ekesihat/ekesihatan/resources/views/doctor/patients/history.blade.php ENDPATH**/ ?>