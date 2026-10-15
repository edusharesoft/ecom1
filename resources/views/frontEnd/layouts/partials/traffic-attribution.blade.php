{{-- শেয়ার লিঙ্ক / সোশ্যাল রিফারার / fbclid — utm ছাড়াই (সার্ভার সেশনের সাথে মিল রেখে) --}}
@php
    $serverTrafficSource = session('order_traffic_source', 'direct');
    $serverTrafficReferrer = session('order_traffic_referrer', '');
@endphp
<script>
(function () {
    try {
        var params = new URLSearchParams(window.location.search);
        var utm = (params.get('utm_source') || '').toLowerCase().trim();
        var ref = document.referrer || '';
        var host = (window.location.hostname || '').toLowerCase().replace(/^www\./, '');
        var serverTs = @json($serverTrafficSource);
        var serverTsr = @json($serverTrafficReferrer);

        function hostFromUrl(url) {
            try {
                return new URL(url).hostname.toLowerCase().replace(/^www\./, '');
            } catch (e) {
                return '';
            }
        }

        function refContains(refLower, needles) {
            for (var i = 0; i < needles.length; i++) {
                if (refLower.indexOf(needles[i]) !== -1) {
                    return true;
                }
            }
            return false;
        }

        function detectSource() {
            var r = ref.toLowerCase();
            if (r) {
                if (refContains(r, ['youtube.com', 'youtu.be', 'm.youtube.com'])) {
                    return 'youtube';
                }
                if (refContains(r, ['twitter.com', 't.co', 'x.com'])) {
                    return 'twitter';
                }
                if (refContains(r, ['tiktok.com'])) {
                    return 'tiktok';
                }
                if (refContains(r, ['instagram.com', 'l.instagram.com'])) {
                    return 'instagram';
                }
                if (refContains(r, ['facebook.com', 'fb.com', 'm.facebook.com', 'l.facebook.com', 'lm.facebook.com', 'web.facebook.com'])) {
                    return 'facebook';
                }
                if (refContains(r, ['google.', 'google.com'])) {
                    return 'google';
                }
                if (refContains(r, ['bing.com'])) {
                    return 'bing';
                }
                if (refContains(r, ['yahoo.'])) {
                    return 'yahoo';
                }
                if (refContains(r, ['whatsapp.com', 'wa.me', 'api.whatsapp.com'])) {
                    return 'whatsapp';
                }
                var refHost = hostFromUrl(ref);
                if (refHost && refHost !== host) {
                    return 'other';
                }
            }

            if (params.get('fbclid') || utm === 'fb' || utm === 'facebook' || utm.indexOf('facebook') !== -1) {
                return 'facebook';
            }
            if (utm === 'ig' || utm === 'instagram' || utm.indexOf('instagram') !== -1) {
                return 'instagram';
            }
            if (params.get('gclid') || utm.indexOf('google') !== -1) {
                return 'google';
            }
            if (params.get('ttclid') || utm.indexOf('tiktok') !== -1) {
                return 'tiktok';
            }
            if (params.get('twclid') || utm === 'x' || utm.indexOf('twitter') !== -1) {
                return 'twitter';
            }
            if (utm.indexOf('whatsapp') !== -1 || utm === 'wa') {
                return 'whatsapp';
            }
            if (utm.indexOf('youtube') !== -1 || utm === 'yt') {
                return 'youtube';
            }
            if (utm.indexOf('bing') !== -1) {
                return 'bing';
            }
            if (utm.indexOf('yahoo') !== -1) {
                return 'yahoo';
            }
            var allowed = ['facebook', 'google', 'tiktok', 'whatsapp', 'instagram', 'youtube', 'bing', 'yahoo', 'twitter'];
            if (utm && allowed.indexOf(utm) !== -1) {
                return utm;
            }
            if (utm) {
                return 'other';
            }

            return 'direct';
        }

        var source = detectSource();
        var refOut = ref || serverTsr || '';
        var existing = sessionStorage.getItem('_ts');

        // নন-ডাইরেক্ট = সর্বদা আপডেট (Twitter/YouTube লিঙ্কে আগের Facebook মুছে যাবে)
        if (source !== 'direct') {
            sessionStorage.setItem('_ts', source);
            sessionStorage.setItem('_tsr', refOut.substring(0, 490));
        } else if (!existing || existing === 'direct') {
            sessionStorage.setItem('_ts', 'direct');
            sessionStorage.setItem('_tsr', refOut.substring(0, 490));
        }

        var fbclid = params.get('fbclid');
        if (fbclid) {
            var ts = Math.floor(Date.now() / 1000);
            document.cookie = 'fbc=fb.1.' + ts + '.' + fbclid + ';path=/;max-age=' + (90 * 24 * 60 * 60) + ';SameSite=Lax';
        }
    } catch (e) {
    }
})();
</script>
