<?php
    $dl_page_type = Request::is('/') ? 'home'
        : (Request::is('product/*') ? 'product_detail'
        : (Request::is('category/*') ? 'category'
        : (Request::is('cart') ? 'cart'
        : (Request::is('checkout') ? 'checkout'
        : (Request::is('customer/*') ? 'customer'
        : 'other')))));
?>
<script>
(function () {
    function bootTracking() {
        window.dataLayer = window.dataLayer || [];
        dataLayer.push({
            event: 'site_page_data',
            page_type: <?php echo json_encode($dl_page_type, 15, 512) ?>,
            page_url: <?php echo json_encode(url()->current(), 15, 512) ?>,
            currency: 'BDT',
            site_name: <?php echo json_encode(optional($generalsetting)->name ?? '', 15, 512) ?>
        });

        <?php $__currentLoopData = $gtm_code ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gtm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $gtm_id = preg_match('/^GTM-/i', trim($gtm->code)) ? trim($gtm->code) : 'GTM-'.trim($gtm->code); ?>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?php echo e($gtm_id); ?>');
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php if(isset($pixels) && $pixels->count() > 0): ?>
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
        <?php
            $fbInitUser = [];
            if (\Illuminate\Support\Facades\Auth::guard('customer')->check()) {
                $fbInitUser = \App\Support\EcommerceTrackingUser::forBrowserPixel(
                    \App\Support\EcommerceTrackingUser::fromCustomer(\Illuminate\Support\Facades\Auth::guard('customer')->user())
                );
            }
        ?>
        <?php $__currentLoopData = $pixels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pixel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        fbq('init', '<?php echo e($pixel->code); ?>', <?php echo json_encode($fbInitUser ?: new \stdClass(), 15, 512) ?>);
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        fbq('track', 'PageView');
        <?php endif; ?>

        <?php if(isset($tiktok_pixels) && $tiktok_pixels->count() > 0): ?>
        !function(w,d,t){w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie","holdConsent","revokeConsent","grantConsent"];ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e};ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=d.createElement("script");o.type="text/javascript";o.async=!0;o.src=i+"?sdkid="+e+"&lib="+t;var a=d.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)}}(window,document,'ttq');
        <?php $__currentLoopData = $tiktok_pixels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        ttq.load('<?php echo e($tp->code); ?>');
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        ttq.page();
        <?php endif; ?>
    }

    if ('requestIdleCallback' in window) {
        requestIdleCallback(bootTracking, { timeout: 3000 });
    } else {
        window.addEventListener('load', bootTracking, { once: true });
    }
})();
</script>
<?php echo $__env->make('frontEnd.layouts.partials.ecom-tracking-lib', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('frontEnd.layouts.partials.traffic-attribution', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php if(isset($pixels) && $pixels->count() > 0): ?>
    <?php $__currentLoopData = $pixels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pixel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=<?php echo e($pixel->code); ?>&ev=PageView&noscript=1"/></noscript>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php /**PATH D:\laragon\www\ecom1\resources\views/frontEnd/layouts/partials/deferred-tracking.blade.php ENDPATH**/ ?>