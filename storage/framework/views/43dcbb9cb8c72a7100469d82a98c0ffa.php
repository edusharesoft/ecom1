<?php
    $generalsetting = $generalsetting ?? \App\Models\GeneralSetting::first();
?>

<?php $__env->startSection('title', 'Customer Login'); ?>
<?php $__env->startSection('heading', 'Login To Your Account'); ?>
<?php $__env->startSection('subheading', 'আপনার মোবাইল নাম্বার বা ইমেইল এবং পাসওয়ার্ড দিন'); ?>

<?php $__env->startSection('content'); ?>
<form action="<?php echo e(route('customer.signin')); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['au-field', 'is-invalid' => $errors->has('login')]); ?>">
        <span class="ic"><i class="fa-regular fa-user"></i></span>
        <input type="text" id="login" name="login" value="<?php echo e(old('login')); ?>" placeholder="মোবাইল নাম্বার (017xxxxxxxx) অথবা ইমেইল" autocomplete="username" required autofocus>
    </div>
    <?php $__errorArgs = ['login'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="au-err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

    <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['au-field', 'is-invalid' => $errors->has('password')]); ?>">
        <span class="ic"><i class="fa-solid fa-lock"></i></span>
        <input type="password" id="password" name="password" placeholder="পাসওয়ার্ড" autocomplete="current-password" required>
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

    <button type="submit" class="au-btn">লগিন করুন</button>
    <a href="<?php echo e(route('customer.forgot.password')); ?>" class="au-btn-2">পাসওয়ার্ড ভুলে গেছেন?</a>
</form>

<?php if(isset($demoMode) && $demoMode): ?>
    <div class="au-note">
        <div class="mb-1 fw-semibold"><i class="fa-solid fa-circle-info text-primary"></i> ডেমো একাউন্ট</div>
        <small class="d-block mb-1 text-muted">রিসেলার ইউজার</small>
        <div class="d-flex gap-2 align-items-center flex-wrap mb-2">
            <input type="text" class="form-control form-control-sm" value="01631843149" readonly style="flex:1;min-width:0">
            <input type="text" class="form-control form-control-sm" value="12345678" readonly style="width:100px">
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="fillDemoCreds('01631843149','12345678')">Use</button>
        </div>
        <small class="d-block mb-1 text-muted">ভেন্ডর ইউজার</small>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <input type="text" class="form-control form-control-sm" value="01870829343" readonly style="flex:1;min-width:0">
            <input type="text" class="form-control form-control-sm" value="123456789" readonly style="width:100px">
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="fillDemoCreds('01870829343','123456789')">Use</button>
        </div>
    </div>
<?php endif; ?>

<div class="au-links">একাউন্ট না থাকলে? <a href="<?php echo e(route('customer.register')); ?>">রেজিস্ট্রেশন করুন</a></div>
<?php if(!empty($generalsetting->hotline ?? null)): ?>
    <div class="au-links"><i class="fa-solid fa-phone"></i> হটলাইন: <?php echo e($generalsetting->hotline); ?></div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function fillDemoCreds(login, pass) {
    document.getElementById('login').value = login;
    document.getElementById('password').value = pass;
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.mb-auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\ecom1\resources\views/frontEnd/layouts/customer/login.blade.php ENDPATH**/ ?>