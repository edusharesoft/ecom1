<?php
    $generalsetting = $generalsetting ?? \App\Models\GeneralSetting::first();
    $resellerOn = ($generalsetting?->reseller_enabled ?? 1) == 1;
    $vendorOn = ($generalsetting?->vendor_enabled ?? 1) == 1;
    $bad = fn ($name) => $errors->has($name);
?>

<?php $__env->startSection('title', 'Customer Register'); ?>
<?php $__env->startSection('auth_kind', 'register'); ?>
<?php $__env->startSection('heading', 'Create Your Account'); ?>
<?php $__env->startSection('subheading', 'আপনার তথ্য দিয়ে ফর্মটি পূরণ করুন'); ?>

<?php $__env->startPush('styles'); ?>
<style>
.acc-type { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 8px; margin-bottom: 14px; }
.acc-type label { display: flex; align-items: center; gap: 8px; border: 1px solid #dfe3ea; border-radius: 6px; padding: 9px 12px; cursor: pointer; font-size: 13.5px; background: #fff; margin: 0; transition: .15s; }
.acc-type label:has(input:checked) { border-color: #8b87e0; background: #f5f3ff; box-shadow: 0 0 0 3px rgba(139, 135, 224, .15); }
.acc-type input { width: 17px; height: 17px; }
.au-form .au-field input[type=file] { padding: 7px 10px; font-size: 13px; }
.au-sec { font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: .6px; margin: 4px 0 8px; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<form action="<?php echo e(route('customer.store')); ?>" method="POST" enctype="multipart/form-data" novalidate>
    <?php echo csrf_field(); ?>

    <?php if($resellerOn || $vendorOn): ?>
        <div class="au-sec">একাউন্টের ধরন</div>
        <div class="acc-type">
            <label><input type="radio" name="_acc" value="customer" checked> <i class="fa-regular fa-user text-primary"></i> কাস্টমার</label>
            <?php if($resellerOn): ?>
                <label><input type="checkbox" id="is_reseller" name="is_reseller" value="1" <?php if(old('is_reseller')): echo 'checked'; endif; ?> onchange="toggleResellerFields()"> <i class="fa-solid fa-user-tie text-success"></i> রিসেলার একাউন্ট</label>
            <?php endif; ?>
            <?php if($vendorOn): ?>
                <label><input type="checkbox" id="is_seller" name="is_seller" value="1" <?php if(old('is_seller')): echo 'checked'; endif; ?> onchange="toggleSellerFields()"> <i class="fa-solid fa-store text-warning"></i> সেলার (ভেন্ডর) একাউন্ট</label>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="row g-2">
        <div class="col-sm-6">
            <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['au-field', 'is-invalid' => $bad('name')]); ?>"><span class="ic"><i class="fa-regular fa-user"></i></span><input type="text" id="name" name="name" value="<?php echo e(old('name')); ?>" placeholder="আপনার নাম *" required></div>
            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="au-err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <div class="au-help" id="owner_name_label" style="display:none">দোকান মালিকের নাম লিখুন</div>
        </div>
        <div class="col-sm-6">
            <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['au-field', 'is-invalid' => $bad('phone')]); ?>"><span class="ic"><i class="fa-solid fa-mobile-screen"></i></span><input type="text" id="phone" name="phone" value="<?php echo e(old('phone')); ?>" placeholder="মোবাইল নাম্বার (01XXXXXXXXX) *" inputmode="tel" required></div>
            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="au-err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>

    <div id="email_field" style="display:none">
        <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['au-field', 'is-invalid' => $bad('email')]); ?>"><span class="ic"><i class="fa-regular fa-envelope"></i></span><input type="email" id="email" name="email" value="<?php echo e(old('email')); ?>" placeholder="ইমেইল *"></div>
        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="au-err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    
    <div id="reseller_shop_name_field" style="display:none">
        <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['au-field', 'is-invalid' => $bad('reseller_shop_name')]); ?>"><span class="ic"><i class="fa-solid fa-store"></i></span><input type="text" id="reseller_shop_name" name="reseller_shop_name" value="<?php echo e(old('reseller_shop_name')); ?>" placeholder="Shop Name *"></div>
        <?php $__errorArgs = ['reseller_shop_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="au-err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div id="reseller_verification_note" style="display:none" class="au-note mb-3 mt-0">
        <i class="fa-solid fa-circle-info text-primary"></i> রেজিস্ট্রেশনের পর লগইন করে <b>রিসেলার ড্যাশবোর্ড → ভেরিফিকেশন</b> পেজ থেকে ভোটার আইডি (উভয় পাশ) ও আপনার ছবি আপলোড করুন।
    </div>

    
    <div id="shop_name_field" style="display:none">
        <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['au-field', 'is-invalid' => $bad('shop_name')]); ?>"><span class="ic"><i class="fa-solid fa-store"></i></span><input type="text" id="shop_name" name="shop_name" value="<?php echo e(old('shop_name')); ?>" placeholder="শপের নাম *"></div>
        <?php $__errorArgs = ['shop_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="au-err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div id="slug_field" style="display:none">
        <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['au-field', 'is-invalid' => $bad('slug')]); ?>"><span class="ic"><i class="fa-solid fa-globe"></i></span><span class="pre"><?php echo e(preg_replace('#^https?://#', '', url('/'))); ?>/</span><input type="text" id="slug" name="slug" value="<?php echo e(old('slug')); ?>" placeholder="my-shop-name *" autocomplete="off" autocapitalize="off" spellcheck="false"></div>
        <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="au-err"><?php echo e($message); ?></div><?php else: ?><div class="au-help">শুধুমাত্র ইংরেজি অক্ষর, সংখ্যা এবং হাইফেন (-) ব্যবহার করুন</div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div id="address_field" style="display:none">
        <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['au-field', 'is-invalid' => $bad('address')]); ?>"><span class="ic"><i class="fa-solid fa-location-dot"></i></span><textarea id="address" name="address" rows="2" placeholder="আপনার শপের ঠিকানা"><?php echo e(old('address')); ?></textarea></div>
        <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="au-err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="row g-2">
        <div class="col-sm-6" id="logo_field" style="display:none">
            <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['au-field', 'is-invalid' => $bad('logo')]); ?>"><span class="ic"><i class="fa-regular fa-image"></i></span><input type="file" id="logo" name="logo" accept="image/*"></div>
            <?php $__errorArgs = ['logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="au-err"><?php echo e($message); ?></div><?php else: ?><div class="au-help">শপ লোগো (সর্বোচ্চ 2MB)</div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="col-sm-6" id="banner_field" style="display:none">
            <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['au-field', 'is-invalid' => $bad('banner')]); ?>"><span class="ic"><i class="fa-regular fa-images"></i></span><input type="file" id="banner" name="banner" accept="image/*"></div>
            <?php $__errorArgs = ['banner'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="au-err"><?php echo e($message); ?></div><?php else: ?><div class="au-help">শপ ব্যানার (সর্বোচ্চ 3MB)</div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>
    <div id="seller_verification_note" style="display:none" class="au-note mb-3 mt-0">
        <i class="fa-solid fa-circle-info text-primary"></i> লগইন করার পর <b>ভেন্ডর ড্যাশবোর্ড → ভেরিফিকেশন</b> থেকে এনআইডি ও আপনার ছবি আপলোড করুন। (লোগো ও ব্যানার এখানে ঐচ্ছিক।)
    </div>

    <div class="row g-2">
        <div class="col-sm-6" id="password_col">
            <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['au-field', 'is-invalid' => $bad('password')]); ?>"><span class="ic"><i class="fa-solid fa-lock"></i></span><input type="password" id="password" name="password" placeholder="পাসওয়ার্ড *" autocomplete="new-password" required><button type="button" class="eye" tabindex="-1"><i class="fa-regular fa-eye-slash"></i></button></div>
            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="au-err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="col-sm-6" id="password_confirmation_field" style="display:none">
            <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['au-field', 'is-invalid' => $bad('password_confirmation')]); ?>"><span class="ic"><i class="fa-solid fa-lock"></i></span><input type="password" id="password_confirmation" name="password_confirmation" placeholder="পাসওয়ার্ড নিশ্চিত করুন *" autocomplete="new-password"><button type="button" class="eye" tabindex="-1"><i class="fa-regular fa-eye-slash"></i></button></div>
            <?php $__errorArgs = ['password_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="au-err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>

    <button type="submit" class="au-btn mt-1"><i class="fa-solid fa-user-plus"></i> রেজিস্ট্রেশন করুন</button>
    <a href="<?php echo e(route('customer.login')); ?>" class="au-btn-2">আগেই একাউন্ট আছে? লগিন করুন</a>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(() => {
    const $ = id => document.getElementById(id);
    const show = (id, on) => { const el = $(id); if (el) el.style.display = on ? '' : 'none'; };
    const req = (id, on) => { const el = $(id); if (el) on ? el.setAttribute('required', 'required') : el.removeAttribute('required'); };
    const isReseller = () => !!($('is_reseller') && $('is_reseller').checked);
    const isSeller = () => !!($('is_seller') && $('is_seller').checked);
    const customerRadio = document.querySelector('input[name="_acc"]');

    function refresh() {
        const r = isReseller(), s = isSeller();
        if (customerRadio) customerRadio.checked = !r && !s;
        // reseller only
        show('reseller_shop_name_field', r); req('reseller_shop_name', r);
        show('reseller_verification_note', r);
        // seller only
        ['shop_name_field', 'slug_field', 'address_field', 'logo_field', 'banner_field', 'seller_verification_note'].forEach(id => show(id, s));
        req('shop_name', s); req('slug', s);
        show('owner_name_label', s);
        // both
        show('email_field', r || s); req('email', r || s);
        show('password_confirmation_field', r || s); req('password_confirmation', r || s);
        const pc = $('password_col'); if (pc) pc.className = (r || s) ? 'col-sm-6' : 'col-12';
    }
    window.toggleResellerFields = () => { if (isReseller() && $('is_seller')) $('is_seller').checked = false; refresh(); };
    window.toggleSellerFields = () => { if (isSeller() && $('is_reseller')) $('is_reseller').checked = false; refresh(); };
    if (customerRadio) customerRadio.addEventListener('change', () => {
        if ($('is_reseller')) $('is_reseller').checked = false;
        if ($('is_seller')) $('is_seller').checked = false;
        refresh();
    });

    // auto slug from shop name
    const shop = $('shop_name'), slug = $('slug');
    if (shop && slug) shop.addEventListener('input', function () {
        slug.value = this.value.toLowerCase().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-').trim();
    });
    refresh();
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.mb-auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\ecom1\resources\views/frontEnd/layouts/customer/register.blade.php ENDPATH**/ ?>