<?php $__env->startSection('content'); ?>
<div class="page-header service-page-header">
    <div>
        <h2>Available Health Services</h2>
        <p>Choose a care option below and book your visit in a few steps.</p>
    </div>
</div>

<p class="service-intro">These are the clinic services currently available for booking at the university health centre. Each option includes a short description so you can select the right care before confirming your appointment.</p>

<div class="service-table-card">
    <table class="service-table table-card-mobile">
        <thead>
            <tr>
                <th>Service</th>
                <th>What it covers</th>
                <th>Approx. time</th>
                <th class="service-action-header">Book</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td data-label="Service">
                        <div class="service-name-group">
                            <strong><?php echo e($service->name); ?></strong>
                            <span class="service-badge">Available now</span>
                        </div>
                    </td>
                    <td data-label="What it covers"><?php echo e($service->description); ?></td>
                    <td data-label="Approx. time"><?php echo e($service->duration_minutes); ?> mins</td>
                    <td class="service-action-cell" data-label="Book">
                        <a class="service-book-link" href="<?php echo e(route('patient.appointments.create', ['service_id' => $service->id])); ?>">Book now</a>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td class="table-empty" colspan="4">No active services.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<section class="doctor-gallery">
    <div class="section-header">
        <div>
            <h3>Meet the doctors</h3>
            <p>Each doctor supports one of our main care services so you can choose confidently.</p>
        </div>
    </div>

    <div class="doctor-grid">
        <?php $__empty_1 = true; $__currentLoopData = $doctors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doctor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <article class="doctor-card">
                <img src="<?php echo e($doctor->image_url); ?>" alt="<?php echo e($doctor->name); ?>">
                <div>
                    <h4><?php echo e($doctor->name); ?></h4>
                    <p class="doctor-specialty"><?php echo e($doctor->specialization ?? 'Clinic doctor'); ?></p>
                    <p><?php echo e($doctor->service_focus ?? 'Available for clinic care.'); ?></p>
                </div>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p>No doctors are currently listed.</p>
        <?php endif; ?>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ekesihat/ekesihatan/resources/views/patient/services/index.blade.php ENDPATH**/ ?>