<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h2>Available Health Services</h2>
        <p>Manage the services available for patient booking.</p>
    </div>
    <a class="button-link" href="<?php echo e(route('admin.services.create')); ?>">Add Service</a>
</div>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Duration</th>
                <th>Status</th>
                <th style="text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <div class="service-name-group">
                            <strong><?php echo e($service->name); ?></strong>
                            <span class="service-badge"><?php echo e($service->is_active ? 'Active' : 'Inactive'); ?></span>
                        </div>
                    </td>
                    <td><?php echo e($service->description ?: 'No description provided.'); ?></td>
                    <td><?php echo e($service->duration_minutes); ?> mins</td>
                    <td><?php echo e($service->is_active ? 'Visible to patients' : 'Hidden from patients'); ?></td>
                    <td>
                        <div class="admin-actions">
                            <a class="button-link secondary" href="<?php echo e(route('admin.services.edit', $service)); ?>">Edit</a>
                            <form
                                action="<?php echo e(route('admin.services.destroy', $service)); ?>"
                                method="POST"
                                data-confirm-message="Delete this health service? Existing appointments may be affected."
                            >
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button class="button-link danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5">No services created.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ekesihat/ekesihatan/resources/views/admin/services/index.blade.php ENDPATH**/ ?>