<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h2 data-i18n="Forms & Downloads">Forms & Downloads</h2>
        <p>Manage downloadable forms, publication status, and display priority.</p>
    </div>
    <a class="button-link" href="<?php echo e(route('admin.forms.create')); ?>" data-i18n="Add Form">Add Form</a>
</div>

<?php if(!empty($downloadableFormsTableMissing)): ?>
    <div class="alert alert-danger" data-i18n="Forms table is missing. Run php artisan migrate first.">
        Forms table is missing. Run php artisan migrate first.
    </div>
<?php endif; ?>

<div class="admin-table-wrap">
<table class="admin-table">
    <thead>
        <tr>
            <th data-i18n="Title">Title</th>
            <th data-i18n="Display Order">Display Order</th>
            <th data-i18n="Status">Status</th>
            <th data-i18n="Actions" style="text-align: right;">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $forms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $form): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($form->title); ?></td>
                <td><?php echo e($form->sort_order); ?></td>
                <td data-i18n="<?php echo e($form->is_published ? 'Published' : 'Draft'); ?>"><?php echo e($form->is_published ? 'Published' : 'Draft'); ?></td>
                <td>
                    <div class="admin-actions">
                        <a class="button-link secondary" href="<?php echo e(asset($form->file_path)); ?>" target="_blank" rel="noopener noreferrer" data-i18n="Open File">Open File</a>
                        <a class="button-link secondary" href="<?php echo e(route('admin.forms.edit', $form)); ?>" data-i18n="Edit">Edit</a>
                        <form
                            action="<?php echo e(route('admin.forms.destroy', $form)); ?>"
                            method="POST"
                            data-confirm-message="Delete this form and remove its download link from users?"
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
                <td colspan="4" data-i18n="No forms created.">No forms created.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ekesihat/ekesihatan/resources/views/admin/forms/index.blade.php ENDPATH**/ ?>