{{-- GTM noscript - রিসেলার ল্যান্ডিং --}}
@if(!empty($landing->gtm_id))
@php $gtm_noscript_id = preg_match('/^GTM-/i', trim($landing->gtm_id)) ? trim($landing->gtm_id) : 'GTM-'.trim($landing->gtm_id); @endphp
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtm_noscript_id }}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif
