

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h2>Appointments</h2>
        <p>Operations control center for assignment corrections, schedule interventions, and status tracking.</p>
    </div>
</div>

<div class="filter-panel">
    <form method="GET" action="<?php echo e(route('admin.appointments.report')); ?>" class="filter-row">
        <div class="auth-field">
            <label for="report_month">Report Month</label>
            <input id="report_month" name="month" type="month" value="<?php echo e(request('month', now()->format('Y-m'))); ?>" required>
        </div>
        <div class="auth-field">
            <label for="report_week">Week of Month</label>
            <select id="report_week" name="week" required>
                <?php for($week = 1; $week <= 5; $week++): ?>
                    <option value="<?php echo e($week); ?>" <?php if((int) request('week', 1) === $week): echo 'selected'; endif; ?>>Week <?php echo e($week); ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <button type="submit" class="button-link">Download Weekly Report</button>
    </form>
</div>

<div class="stat-grid">
    <article class="stat-card">
        <span>Upcoming Queue</span>
        <strong><?php echo e($metrics['upcoming']); ?></strong>
    </article>
    <article class="stat-card">
        <span>Completed Cases</span>
        <strong><?php echo e($metrics['completed']); ?></strong>
    </article>
    <article class="stat-card">
        <span>Needs Review</span>
        <strong><?php echo e($metrics['requiresAction']); ?></strong>
    </article>
</div>

<div class="admin-table-wrap">
<table class="admin-table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Time</th>
            <th>Patient</th>
            <th>Doctor</th>
            <th>Service</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $appointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($appointment->scheduled_at->format('d M Y')); ?></td>
                <td><?php echo e($appointment->scheduled_at->format('h:i A')); ?></td>
                <td><?php echo e($appointment->patient->name); ?></td>
                <td><?php echo e($appointment->doctor->name); ?></td>
                <td><?php echo e($appointment->service?->name ?? 'General'); ?></td>
                <td><?php echo e(ucfirst($appointment->status)); ?></td>
                <td>
                    <div class="admin-actions">
                        <a class="button-link secondary" href="<?php echo e(route('admin.appointments.show', $appointment)); ?>">Review</a>
                    </div>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="7">No appointments found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ekesihat/ekesihatan/resources/views/admin/appointments/index.blade.php ENDPATH**/ ?>