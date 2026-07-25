

<?php $__env->startSection('content'); ?>
<div class="auth-shell">
    <div class="auth-card">
        <div class="auth-card__header">
            <h2 data-i18n="Staff Login">Staff Login</h2>
        </div>

        <form method="POST" action="<?php echo e(route('staff.login.attempt')); ?>" class="auth-form">
            <?php echo csrf_field(); ?>
            <div class="auth-field">
                <label for="role" data-i18n="Role">Role</label>
                <select id="role" name="role" required>
                    <option value="doctor" <?php if(old('role') === 'doctor'): echo 'selected'; endif; ?> data-i18n="Doctor">Doctor</option>
                    <option value="staff" <?php if(old('role') === 'staff'): echo 'selected'; endif; ?> data-i18n="Staff">Staff</option>
                </select>
            </div>
            <div class="auth-field">
                <label for="email" data-i18n="Email">Email</label>
                <input id="email" name="email" type="email" value="<?php echo e(old('email')); ?>" required>
            </div>
            <div class="auth-field">
                <label for="password" data-i18n="Password">Password</label>
                <input id="password" name="password" type="password" required>
            </div>
            <button type="submit" data-i18n="Staff Login">Staff Login</button>
        </form>

        <div class="auth-footer">
            <span data-i18n="Patient?">Patient?</span>
            <a href="<?php echo e(route('login')); ?>" data-i18n="Patient Login">Patient Login</a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ekesihat/ekesihatan/resources/views/auth/staff-login.blade.php ENDPATH**/ ?>