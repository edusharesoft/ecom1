{{-- GTM, Facebook Pixel, TikTok Pixel - রিসেলার ল্যান্ডিং --}}
@if(!empty($landing->gtm_id) || !empty($landing->facebook_pixel_id) || !empty($landing->tiktok_pixel_id))
@php
    $dl_page_type = 'other';
    if (request()->routeIs('reseller.landing.public') || request()->is('r/*') && !request()->is('r/*/category/*') && !request()->is('r/*/subcategory/*') && !request()->is('r/*/product/*') && !request()->is('r/*/order*') && !request()->is('r/*/contact')) $dl_page_type = 'home';
    if (request()->is('r/*/product/*')) $dl_page_type = 'product_detail';
    if (request()->is('r/*/category/*')) $dl_page_type = 'category';
    if (request()->is('r/*/order*')) $dl_page_type = 'checkout';
    $gtm_id = !empty($landing->gtm_id) ? (preg_match('/^GTM-/i', trim($landing->gtm_id)) ? trim($landing->gtm_id) : 'GTM-' . trim($landing->gtm_id)) : null;
@endphp
<script>
    window.dataLayer = window.dataLayer || [];
    dataLayer.push({
        event: 'site_page_data',
        page_type: {{ json_encode($dl_page_type) }},
        page_url: {{ json_encode(url()->current()) }},
        currency: 'BDT',
        site_name: {{ json_encode($landing->title ?? '') }}
    });
</script>
@if($gtm_id)
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{{ $gtm_id }}');</script>
@endif
@if(!empty($landing->facebook_pixel_id))
<script>
!(function (f, b, e, v, n, t, s) {
    if (f.fbq) return;
    n = f.fbq = function () { n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments); };
    if (!f._fbq) f._fbq = n; n.push = n; n.loaded = !0; n.version = "2.0"; n.queue = [];
    t = b.createElement(e); t.async = !0; t.src = v;
    s = b.getElementsByTagName(e)[0]; s.parentNode.insertBefore(t, s);
})(window, document, "script", "https://connect.facebook.net/en_US/fbevents.js");
fbq('init', '{{ trim($landing->facebook_pixel_id) }}');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id={{ trim($landing->facebook_pixel_id) }}&ev=PageView&noscript=1" /></noscript>
@endif
@if(!empty($landing->tiktok_pixel_id))
<script>
!function (w, d, t) {
    w.TiktokAnalyticsObject=t;
    var ttq=w[t]=w[t]||[];
    ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie","holdConsent","revokeConsent","grantConsent"];
    ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};
    for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);
    ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e};
    ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";
        ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};
        var o=d.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;
        var a=d.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
}(window, document, 'ttq');
ttq.load('{{ trim($landing->tiktok_pixel_id) }}');
ttq.page();
</script>
@endif
@endif
@include('frontEnd.layouts.partials.traffic-attribution')
