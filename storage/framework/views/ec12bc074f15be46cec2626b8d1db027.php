<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h2 data-i18n="Patient Profile">Patient Profile</h2>
        <p data-i18n="Manage your appointments and health services.">Manage your appointments and health services.</p>
    </div>
    <a class="button-link" href="<?php echo e(route('patient.appointments.create')); ?>" data-i18n="Book Appointment">Book Appointment</a>
</div>

<?php
    $nextAppointment = $appointments->first();
?>

<div class="stat-grid">
    <div class="stat-card">
        <span data-i18n="Upcoming Appointments">Upcoming Appointments</span>
        <strong><?php echo e($appointments->count()); ?></strong>
    </div>
    <div class="stat-card">
        <span data-i18n="Next Appointment">Next Appointment</span>
        <strong><?php echo e($nextAppointment ? $nextAppointment->scheduled_at->format('d M, h:i A') : '—'); ?></strong>
    </div>
    <div class="stat-card">
        <span data-i18n="Assigned Doctor">Assigned Doctor</span>
        <strong><?php echo e($nextAppointment ? $nextAppointment->doctor->name : '—'); ?></strong>
    </div>
</div>

<section class="patient-overview">
    <article class="profile-card">
        <div class="profile-avatar" aria-hidden="true"><?php echo e($profileInitials ?: 'EK'); ?></div>
        <h3><?php echo e($patient->name); ?></h3>
        <p><?php echo e($patient->email); ?></p>
        <p><?php echo e($patient->phone_number ?? '—'); ?></p>
        <div class="profile-meta">
            <span data-i18n="Student ID">Student ID</span>
            <strong><?php echo e($patient->student_id ?? '—'); ?></strong>
        </div>
        <div class="profile-meta">
            <span data-i18n="Blood Type">Blood Type</span>
            <strong><?php echo e($patient->blood_type ?? '—'); ?></strong>
        </div>
    </article>

    <article class="info-card detail-card">
        <div class="detail-card__header">
            <h4 data-i18n="General Information">General Information</h4>
        </div>
        <div class="detail-grid">
            <div>
                <span data-i18n="Role">Role</span>
                <strong data-i18n="Patient">Patient</strong>
            </div>
            <div>
                <span data-i18n="Registration Date">Registration Date</span>
                <strong><?php echo e($patient->created_at->format('d M Y')); ?></strong>
            </div>
            <div>
                <span data-i18n="Emergency Contact Name">Emergency Contact Name</span>
                <strong><?php echo e($patient->emergency_contact_name ?? '—'); ?></strong>
            </div>
            <div>
                <span data-i18n="Emergency Contact Phone">Emergency Contact Phone</span>
                <strong><?php echo e($patient->emergency_contact_phone ?? '—'); ?></strong>
            </div>
        </div>
    </article>

    <article class="info-card detail-card">
        <div class="detail-card__header">
            <h4 data-i18n="Anamnesis">Anamnesis</h4>
        </div>
        <div class="detail-grid">
            <div>
                <span data-i18n="Allergies or Medical Notes">Allergies or Medical Notes</span>
                <strong><?php echo e($patient->allergies ?? '—'); ?></strong>
            </div>
            <div>
                <span data-i18n="Emergency Contact Relationship">Emergency Contact Relationship</span>
                <strong><?php echo e($patient->emergency_contact_relationship ?? '—'); ?></strong>
            </div>
        </div>
    </article>

    <article class="info-card patient-files">
        <div class="detail-card__header">
            <h4 data-i18n="Medical Files">Medical Files</h4>
        </div>
        <ul class="file-list">
            <?php $__empty_1 = true; $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <li>
                    <div>
                        <strong><?php echo e($document->filename); ?></strong>
                        <span><?php echo e(number_format($document->size_bytes / 1024, 1)); ?> KB</span>
                    </div>
                    <a class="card-link" href="<?php echo e(route('patient.documents.show', $document)); ?>" target="_blank" data-i18n="View Document">View Document</a>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <li data-i18n="No documents uploaded yet.">No documents uploaded yet.</li>
            <?php endif; ?>
        </ul>
    </article>
</section>

<section class="patient-visits">
    <div class="visit-tabs">
        <button type="button" class="tab-button active" data-tab="upcoming">
            <span data-i18n="Upcoming Visits">Upcoming Visits</span>
            <span class="tab-count"><?php echo e($appointments->count()); ?></span>
        </button>
        <button type="button" class="tab-button" data-tab="past">
            <span data-i18n="Past Visits">Past Visits</span>
            <span class="tab-count"><?php echo e($pastAppointments->count()); ?></span>
        </button>
    </div>

    <div class="tab-panel is-active" data-tab-panel="upcoming">
        <?php $__empty_1 = true; $__currentLoopData = $appointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <article class="visit-card">
                <div class="visit-date">
                    <strong><?php echo e($appointment->scheduled_at->format('d M Y')); ?></strong>
                    <span><?php echo e($appointment->scheduled_at->format('h:i A')); ?></span>
                </div>
                <div>
                    <span data-i18n="Service">Service</span>
                    <strong><?php echo e($appointment->service?->name ?? 'General'); ?></strong>
                </div>
                <div>
                    <span data-i18n="Doctor">Doctor</span>
                    <strong><?php echo e($appointment->doctor->name); ?></strong>
                </div>
                <div class="visit-status">
                    <span data-i18n="Status">Status</span>
                    <span class="status-chip warning"><?php echo e(ucfirst($appointment->status)); ?></span>
                </div>
                <a class="card-link" href="<?php echo e(route('patient.appointments.show', $appointment)); ?>" data-i18n="View">View</a>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p data-i18n="No upcoming appointments.">No upcoming appointments.</p>
        <?php endif; ?>
    </div>

    <div class="tab-panel" data-tab-panel="past">
        <?php $__empty_1 = true; $__currentLoopData = $pastAppointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <article class="visit-card">
                <div class="visit-date">
                    <strong><?php echo e($appointment->scheduled_at->format('d M Y')); ?></strong>
                    <span><?php echo e($appointment->scheduled_at->format('h:i A')); ?></span>
                </div>
                <div>
                    <span data-i18n="Service">Service</span>
                    <strong><?php echo e($appointment->service?->name ?? 'General'); ?></strong>
                </div>
                <div>
                    <span data-i18n="Doctor">Doctor</span>
                    <strong><?php echo e($appointment->doctor->name); ?></strong>
                </div>
                <div class="visit-status">
                    <span data-i18n="Status">Status</span>
                    <span class="status-chip"><?php echo e(ucfirst($appointment->status)); ?></span>
                </div>
                <a class="card-link" href="<?php echo e(route('patient.appointments.show', $appointment)); ?>" data-i18n="View">View</a>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p data-i18n="No appointment history.">No appointment history.</p>
        <?php endif; ?>
    </div>
</section>

<script>
    (function () {
        const tabs = document.querySelectorAll('.tab-button');
        const panels = document.querySelectorAll('[data-tab-panel]');

        tabs.forEach((tab) => {
            tab.addEventListener('click', () => {
                const target = tab.dataset.tab;
                tabs.forEach((button) => button.classList.remove('active'));
                panels.forEach((panel) => panel.classList.remove('is-active'));
                tab.classList.add('active');
                document.querySelector(`[data-tab-panel="${target}"]`)?.classList.add('is-active');
            });
        });
    })();
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ekesihat/ekesihatan/resources/views/dashboard/patient.blade.php ENDPATH**/ ?>