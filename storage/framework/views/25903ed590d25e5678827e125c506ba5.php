<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h2 data-i18n="Clinic Bulletins">Clinic Bulletins</h2>
        <p>Publish and manage clinic announcements shown to users.</p>
    </div>
    <a class="button-link" href="<?php echo e(route('admin.bulletins.create')); ?>" data-i18n="Add Bulletin">Add Bulletin</a>
</div>

<div class="admin-table-wrap">
<table class="admin-table">
    <thead>
        <tr>
            <th data-i18n="Title">Title</th>
            <th data-i18n="Date">Date</th>
            <th data-i18n="Status">Status</th>
            <th data-i18n="Actions" style="text-align: right;">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $bulletins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bulletin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($bulletin->title); ?></td>
                <td><?php echo e($bulletin->event_date ? $bulletin->event_date->format('d M Y') : '—'); ?></td>
                <td data-i18n="<?php echo e($bulletin->is_published ? 'Published' : 'Draft'); ?>"><?php echo e($bulletin->is_published ? 'Published' : 'Draft'); ?></td>
                <td>
                    <div class="admin-actions">
                        <a class="button-link secondary" href="<?php echo e(route('admin.bulletins.edit', $bulletin)); ?>" data-i18n="Edit">Edit</a>
                        <form
                            action="<?php echo e(route('admin.bulletins.destroy', $bulletin)); ?>"
                            method="POST"
                            data-confirm-message="Delete this bulletin post? This will remove it from the landing page."
                        >
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button class="button-link danger" type="submit" data-i18n="Delete">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="4" data-i18n="No bulletins created.">No bulletins created.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ekesihat/ekesihatan/resources/views/admin/bulletins/index.blade.php ENDPATH**/ ?>