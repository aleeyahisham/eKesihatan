<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h2 data-i18n="Patient Directory">Patient Directory</h2>
        <p data-i18n="View patient demographic and emergency details.">View patient demographic and emergency details.</p>
    </div>
</div>

<details class="filter-panel" <?php if(!empty($query)): ?> open <?php endif; ?>>
    <summary data-i18n="Search patients">Search patients</summary>
    <form method="GET" action="<?php echo e(route('staff.patients.index')); ?>">
        <div>
            <label for="patient-search" data-i18n="Search by name or student ID">Search by name or student ID</label>
            <input id="patient-search" name="q" type="text" value="<?php echo e($query); ?>" placeholder="STU-12345">
        </div>
        <div class="quick-actions">
            <button type="submit" class="button-link" data-i18n="Search">Search</button>
            <a class="button-link secondary" href="<?php echo e(route('staff.patients.index')); ?>" data-i18n="Reset">Reset</a>
        </div>
    </form>
</details>

<section class="card-grid">
    <?php $__empty_1 = true; $__currentLoopData = $patients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $patient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <article class="info-card">
            <div class="info-card__header">
                <h3><?php echo e($patient->name); ?></h3>
                <span class="status-chip"><?php echo e($patient->blood_type ?: 'N/A'); ?></span>
            </div>
            <p><strong data-i18n="Student ID">Student ID</strong>: <?php echo e($patient->student_id ?? '—'); ?></p>
            <p><strong data-i18n="Phone Number">Phone Number</strong>: <?php echo e($patient->phone_number ?? '—'); ?></p>
            <p><strong data-i18n="Emergency Contact">Emergency Contact</strong>: <?php echo e($patient->emergency_contact_name ?? '—'); ?></p>
            <a class="card-link" href="<?php echo e(route('staff.patients.show', $patient)); ?>" data-i18n="View Details">View Details</a>
        </article>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p data-i18n="No patients found.">No patients found.</p>
    <?php endif; ?>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ekesihat/ekesihatan/resources/views/staff/patients/index.blade.php ENDPATH**/ ?>