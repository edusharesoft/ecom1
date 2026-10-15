<?php $__env->startSection('title', 'Admin Login'); ?>
<?php $__env->startSection('heading', 'Login To Your Account'); ?>
<?php $__env->startSection('subheading', 'Sign in to manage your store.'); ?>

<?php $__env->startSection('content'); ?>
<?php if($errors->any() && ! $errors->has('email') && ! $errors->has('password')): ?>
    <div class="alert alert-danger small"><?php echo e($errors->first()); ?></div>
<?php endif; ?>
<form method="POST" action="<?php echo e(route('login')); ?>">
    <?php echo csrf_field(); ?>
    <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['au-field', 'is-invalid' => $errors->has('email')]); ?>">
        <span class="ic"><i class="fa-regular fa-envelope"></i></span>
        <input id="email" type="email" name="email" value="<?php echo e(old('email')); ?>" placeholder="Enter your Email" autocomplete="username" required autofocus>
    </div>
    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="au-err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['au-field', 'is-invalid' => $errors->has('password')]); ?>">
        <span class="ic"><i class="fa-solid fa-lock"></i></span>
        <input id="password" type="password" name="password" placeholder="Enter Password" autocomplete="current-password" required>
        <button type="button" class="eye" tabindex="-1" aria-label="Show password"><i class="fa-regular fa-eye-slash"></i></button>
    </div>
    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="au-err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember">
        <label class="form-check-label" for="remember">Remember me</label>
    </div>
    <button type="submit" class="au-btn">Login</button>
    <a href="<?php echo e(route('admin.password.request')); ?>" class="au-btn-2">Forgot your password?</a>
</form>

<?php if(isset($demoMode) && $demoMode): ?>
    <div class="au-note">
        <div class="mb-1"><i class="fa-solid fa-circle-info text-primary"></i> ডেমো একাউন্ট</div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <input type="text" class="form-control form-control-sm" id="demo-email" value="info@creativedesign.com.bd" readonly style="flex:1;min-width:0;">
            <input type="text" class="form-control form-control-sm" id="demo-password" value="12345678" readonly style="width:100px;">
            <button type="button" class="btn btn-outline-primary btn-sm" id="demoFill">Use</button>
        </div>
    </div>
    <?php $__env->startPush('scripts'); ?>
    <script>
    document.getElementById('demoFill').addEventListener('click', function () {
        document.getElementById('email').value = document.getElementById('demo-email').value;
        document.getElementById('password').value = document.getElementById('demo-password').value;
        this.textContent = 'Filled!';
        setTimeout(() => { this.textContent = 'Use'; }, 1200);
    });
    </script>
    <?php $__env->stopPush(); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.mb-auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\ecom1\resources\views/backEnd/auth/login.blade.php ENDPATH**/ ?>