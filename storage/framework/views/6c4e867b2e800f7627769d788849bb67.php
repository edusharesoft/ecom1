<?php $__env->startSection('title', 'ডেলিভারি লগইন'); ?>
<?php $__env->startSection('heading', 'ডেলিভারি পোর্টাল'); ?>
<?php $__env->startSection('subheading', 'রাইডার একাউন্টে প্রবেশ করতে মোবাইল নম্বর ও পাসওয়ার্ড লিখুন।'); ?>

<?php $__env->startSection('content'); ?>
<?php if($errors->any()): ?><div class="alert alert-danger small"><?php echo e($errors->first()); ?></div><?php endif; ?>
<form method="post" action="<?php echo e(route('delivery.login.post')); ?>" novalidate>
    <?php echo csrf_field(); ?>
    <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['au-field', 'is-invalid' => $errors->has('phone')]); ?>">
        <span class="ic"><i class="fa-solid fa-mobile-screen"></i></span>
        <input id="login-phone" type="tel" name="phone" value="<?php echo e(old('phone')); ?>" placeholder="মোবাইল নম্বর (01XXXXXXXXX)" required autocomplete="username" inputmode="tel" autofocus>
    </div>
    <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['au-field', 'is-invalid' => $errors->has('password')]); ?>">
        <span class="ic"><i class="fa-solid fa-lock"></i></span>
        <input id="login-password" type="password" name="password" required autocomplete="current-password" minlength="6" placeholder="পাসওয়ার্ড">
        <button type="button" class="eye" tabindex="-1" aria-label="পাসওয়ার্ড দেখান"><i class="fa-regular fa-eye-slash"></i></button>
    </div>
    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember" <?php if(old('remember')): echo 'checked'; endif; ?>>
        <label class="form-check-label" for="remember">আমাকে মনে রাখুন</label>
    </div>
    <button type="submit" class="au-btn">লগইন</button>
    <a href="<?php echo e(route('delivery.password.request')); ?>" class="au-btn-2">পাসওয়ার্ড ভুলে গেছেন?</a>
</form>
<div class="au-links">মোবাইল OTP দিয়ে পাসওয়ার্ড রিসেট করা যাবে।</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.mb-auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\ecom1\resources\views/delivery/auth/login.blade.php ENDPATH**/ ?>