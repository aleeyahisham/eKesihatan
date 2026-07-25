

<?php $__env->startSection('content'); ?>
<div class="doctor-page">
    <div class="doctor-page__header">
        <div>
            <p class="doctor-page__eyebrow">Doctor Workspace</p>
            <h2>Daily Appointments</h2>
            <p class="doctor-page__subtitle">
                Review your assigned consultations and manage emergency schedule changes.
            </p>
        </div>

        <div class="doctor-page__date-badge">
            <?php echo e(\Carbon\Carbon::parse($date)->format('d M Y')); ?>

        </div>
    </div>

    <section class="doctor-toolbar">
        <form
            method="GET"
            action="<?php echo e(route('doctor.appointments.index')); ?>"
            class="doctor-date-form"
        >
            <div class="field-group">
                <label for="date">Select Date</label>
                <input
                    id="date"
                    name="date"
                    type="date"
                    value="<?php echo e($date); ?>"
                    required
                >
            </div>

            <button type="submit" class="button-link">
                View Schedule
            </button>
        </form>
    </section>

    <section class="emergency-reschedule-card">
        <div class="emergency-reschedule-card__header">
            <div>
                <p class="doctor-page__eyebrow">Emergency Workflow</p>
                <h3>Emergency Reschedule</h3>
                <p>
                    Reassign eligible General Consultation appointments to Dr. Fadzli
                    using the earliest available matching slots.
                </p>
            </div>
        </div>

        <form
            method="POST"
            action="<?php echo e(route('doctor.appointments.emergency-reschedule')); ?>"
            class="emergency-reschedule-form"
            data-confirm-kind="emergency-reschedule"
            data-confirm-message="Trigger emergency rescheduling for all eligible upcoming General Consultation appointments on this date?"
        >
            <?php echo csrf_field(); ?>

            <input type="hidden" name="date" value="<?php echo e($date); ?>">

            <div class="field-group">
                <label for="reason">Reason</label>
                <input
                    id="reason"
                    name="reason"
                    type="text"
                    maxlength="500"
                    placeholder="Emergency leave, urgent duty, medical emergency, etc."
                    value="<?php echo e(old('reason')); ?>"
                >
            </div>

            <button type="submit" class="emergency-reschedule-button">
                Trigger Emergency Reschedule
            </button>
        </form>
    </section>

    <section class="doctor-appointment-list">
        <div class="doctor-appointment-list__header">
            <div>
                <h3>Appointments for <?php echo e(\Carbon\Carbon::parse($date)->format('d M Y')); ?></h3>
                <p><?php echo e($appointments->count()); ?> appointment(s)</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="doctor-appointment-table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Patient</th>
                        <th>Service</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $appointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td data-label="Time">
                                <?php echo e(\Carbon\Carbon::parse($appointment->scheduled_at)->format('h:i A')); ?>

                            </td>

                            <td data-label="Patient">
                                <?php echo e($appointment->patient?->name ?? 'Patient unavailable'); ?>

                            </td>

                            <td data-label="Service">
                                <?php echo e($appointment->service?->name ?? 'General Consultation'); ?>

                            </td>

                            <td data-label="Status">
                                <span class="status-pill status-pill--<?php echo e(\Illuminate\Support\Str::slug($appointment->status)); ?>">
                                    <?php echo e(ucfirst(str_replace('-', ' ', $appointment->status))); ?>

                                </span>
                            </td>

                            <td data-label="Actions">
                                <div class="doctor-actions">
                                    <a
                                        class="button-link secondary"
                                        href="<?php echo e(route(
                                            'doctor.appointments.show',
                                            $appointment
                                        )); ?>"
                                    >
                                        View
                                    </a>

                                    <?php if($appointment->patient): ?>
                                        <a
                                            class="button-link secondary"
                                            href="<?php echo e(route(
                                                'doctor.patients.history',
                                                $appointment->patient
                                            )); ?>"
                                        >
                                            History
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="empty-table-state">
                                No appointments were found for the selected date.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ekesihat/ekesihatan/resources/views/doctor/appointments/index.blade.php ENDPATH**/ ?>