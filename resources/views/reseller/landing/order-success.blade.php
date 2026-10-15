<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>অর্ডার সফল | {{ $landing->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @include('reseller.landing.partials.tracking-head')
</head>
<body class="bg-gray-100 font-sans text-gray-800 min-h-screen flex flex-col items-center justify-center p-6">
    @include('reseller.landing.partials.tracking-body')
    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md w-full text-center">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fa-solid fa-check text-4xl text-green-600"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">অর্ডার সফল হয়েছে!</h1>
        <p class="text-gray-600 mb-6">আপনার অর্ডার নম্বর: <strong class="text-blue-600">#{{ $order->invoice_id }}</strong></p>

        <div class="text-left bg-gray-50 rounded-xl p-4 mb-6 space-y-2">
            <p><span class="text-gray-500">মোট টাকা:</span> <strong>৳{{ number_format($order->amount ?? 0, 0) }}</strong></p>
            @if($order->shipping)
                <p><span class="text-gray-500">ঠিকানা:</span> {{ $order->shipping->address ?? '-' }}</p>
                <p><span class="text-gray-500">ফোন:</span> {{ $order->shipping->phone ?? '-' }}</p>
            @endif
        </div>

        <div class="space-y-3">
            <a href="{{ landing_url($landing->slug, '') }}" class="block w-full py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700">
                হোমে ফিরে যান
            </a>
            <a href="{{ route('home') }}" class="block w-full py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50">
                মূল ওয়েবসাইট
            </a>
        </div>
    </div>

    {{-- Purchase conversion events (Facebook, TikTok) --}}
    @if(!empty($landing->facebook_pixel_id))
    <script>
        if (typeof fbq !== 'undefined') {
            fbq('track', 'Purchase', {
                value: {{ (float)($order->amount ?? 0) }},
                currency: 'BDT',
                order_id: '{{ $order->invoice_id ?? '' }}',
                content_ids: {{ json_encode($order->orderdetails->pluck('product_id')->toArray() ?? []) }},
                content_type: 'product',
                num_items: {{ $order->orderdetails->sum('qty') ?? 0 }}
            });
        }
    </script>
    @endif
    @if(!empty($landing->tiktok_pixel_id))
    <script>
        if (typeof ttq !== 'undefined') {
            ttq.track('CompletePayment', {
                value: {{ (float)($order->amount ?? 0) }},
                currency: 'BDT',
                content_id: '{{ $order->invoice_id ?? '' }}',
                contents: [{ content_id: '{{ $order->invoice_id }}', content_type: 'product', quantity: {{ $order->orderdetails->sum('qty') ?? 0 }} }]
            });
        }
    </script>
    @endif

</body>
</html>
