
<?php
    $gs = $generalsetting ?? (class_exists(\App\Models\GeneralSetting::class) ? \App\Models\GeneralSetting::first() : null);
    $brandName = $gs->name ?? config('app.name');
    $logo = !empty($gs?->dark_logo) ? asset($gs->dark_logo) : (!empty($gs?->white_logo) ? asset($gs->white_logo) : null);
    $size = trim($__env->yieldContent('auth_size'));
    $kind = trim($__env->yieldContent('auth_kind')) === 'register' ? 'register' : 'login';
    $bg = \App\Support\AuthTheme::background($kind);
    $video = trim($__env->yieldContent('auth_video')) === 'off' ? null : \App\Support\AuthTheme::video($kind);
    $home = trim($__env->yieldContent('auth_home')) ?: url('/');
    $footer = 'Developed By : Shataj Soft';
    [$fPre, $fName] = array_map('trim', explode(':', $footer, 2));
?>
<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Poppins:wght@400;500;600;700;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('public/backEnd/assets/css/toastr.min.css')); ?>">
    <?php echo $__env->make('partials.mb.auth-css', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php if(!empty($gs?->favicon)): ?><link rel="shortcut icon" href="<?php echo e(asset($gs->favicon)); ?>"><?php endif; ?>
    <title><?php echo $__env->yieldContent('title'); ?> | <?php echo e($brandName); ?></title>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="auth-ui">
<div class="au-bg" <?php if($bg): ?> style="--au-img:url('<?php echo e($bg); ?>')" <?php endif; ?>></div>
<div class="au-top">
    <a href="<?php echo e($home); ?>"><i class="fa-solid fa-house"></i> Home</a>
</div>

<main class="au-wrap">
    <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['au-card', 'wide' => $size === 'wide' && $video, 'solo' => ! $video && $size !== 'wide']); ?>">
        <div class="au-head">
            <?php if($logo): ?>
                <img src="<?php echo e($logo); ?>" alt="<?php echo e($brandName); ?>" class="au-logo">
            <?php else: ?>
                <div class="au-name"><i class="fa-solid fa-store" style="color:#0ea5e9;-webkit-text-fill-color:#0ea5e9"></i> <?php echo e($brandName); ?></div>
            <?php endif; ?>
            <div class="au-title"><?php echo $__env->yieldContent('heading'); ?></div>
            <?php if (! empty(trim($__env->yieldContent('subheading')))): ?><div class="au-sub"><?php echo $__env->yieldContent('subheading'); ?></div><?php endif; ?>
        </div>

        <?php $__currentLoopData = ['success' => 'success', 'status' => 'success', 'message' => 'info', 'warning' => 'warning', 'error' => 'danger']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(session($key) && is_string(session($key))): ?>
                <div class="alert alert-<?php echo e($type); ?> alert-dismissible fade show small" role="alert">
                    <?php echo e(session($key)); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php if($video): ?>
            <div class="row g-3 align-items-stretch">
                <div class="col-md-6 order-2 order-md-1">
                    <button type="button" class="au-media" data-yt="<?php echo e($video); ?>" aria-label="Play video">
                        <img src="https://i.ytimg.com/vi/<?php echo e($video); ?>/hqdefault.jpg" alt="" loading="lazy">
                        <span class="play"></span>
                    </button>
                </div>
                <div class="col-md-6 order-1 order-md-2 au-form"><?php echo $__env->yieldContent('content'); ?></div>
            </div>
        <?php else: ?>
            <div class="au-form"><?php echo $__env->yieldContent('content'); ?></div>
        <?php endif; ?>
    </div>
    <div class="au-foot"><?php echo e($fPre); ?> : <b><?php echo e($fName); ?></b></div>
</main>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo e(asset('public/backEnd/assets/js/toastr.min.js')); ?>"></script>
<?php if(class_exists('Toastr')): ?>
    <?php echo Toastr::message(); ?>

<?php endif; ?>
<script>
(() => {
    // show / hide password
    document.querySelectorAll('.au-field .eye').forEach(b => b.addEventListener('click', () => {
        const i = b.parentElement.querySelector('input');
        const show = i.type === 'password';
        i.type = show ? 'text' : 'password';
        b.innerHTML = show ? '<i class="fa-regular fa-eye"></i>' : '<i class="fa-regular fa-eye-slash"></i>';
    }));
    // YouTube: the player is loaded only when clicked (fast page, no tracking before that)
    document.querySelectorAll('.au-media[data-yt]').forEach(b => b.addEventListener('click', () => {
        const id = b.dataset.yt;
        if (!/^[A-Za-z0-9_-]{11}$/.test(id)) return;
        b.innerHTML = '<iframe src="https://www.youtube-nocookie.com/embed/' + id + '?autoplay=1&rel=0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
        b.style.cursor = 'default';
    }, {once: true}));
})();
</script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\laragon\www\ecom1\resources\views/layouts/mb-auth.blade.php ENDPATH**/ ?>