<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>অর্ডার ট্রাকিং ফলাফল | {{ $landing->title }}</title>
    @if(!empty($landing->favicon))<link rel="icon" type="{{ str_ends_with($landing->favicon, '.ico') ? 'image/x-icon' : 'image/png' }}" href="{{ asset($landing->favicon) }}">@endif
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @include('reseller.landing.partials.tracking-head')
</head>
<body class="bg-gray-100 font-sans text-gray-800 pb-20 md:pb-0">
    @include('reseller.landing.partials.tracking-body')
    <div class="bg-gray-900 text-gray-300 text-xs py-2 px-4">
        <div class="container mx-auto flex justify-between">
            @if($landing->phone)<a href="tel:{{ $landing->phone }}" class="hover:text-white"><i class="fa-solid fa-phone mr-1"></i> {{ $landing->phone }}</a>@endif
            <div class="flex gap-4">
                <a href="{{ landing_url($landing->slug, '') }}" class="hover:text-white">হোম</a>
                <span>|</span>
                <a href="{{ route('reseller.landing.contact', $landing->slug) }}" class="hover:text-white">যোগাযোগ</a>
            </div>
        </div>
    </div>

    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ landing_url($landing->slug, '') }}">
                @if($landing->logo)<img src="{{ asset($landing->logo) }}" alt="" class="h-10 w-auto">@else<span class="text-xl font-bold text-blue-700">{{ $landing->title }}</span>@endif
            </a>
            <a href="{{ route('reseller.landing.order-track', $landing->slug) }}" class="text-blue-600 font-semibold hover:text-blue-700"><i class="fa-solid fa-truck-fast mr-1"></i> আবার ট্র্যাক করুন</a>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8 max-w-3xl">
        <h1 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fa-solid fa-list-check mr-2 text-blue-600"></i> আপনার অর্ডার সমূহ
        </h1>

        @foreach($orders as $order)
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
            <div class="bg-blue-600 text-white px-4 py-3 flex flex-wrap justify-between items-center gap-2">
                <div>
                    <span class="font-bold">Invoice #{{ $order->invoice_id }}</span>
                    <span class="block text-sm opacity-90">{{ date('d M, Y h:i A', strtotime($order->created_at)) }}</span>
                </div>
                <span class="px-3 py-1 bg-white/20 rounded-lg text-sm font-semibold">
                    {{ optional(\App\Models\OrderStatus::find($order->order_status))->name ?? 'Unknown' }}
                </span>
            </div>
            <div class="p-4 sm:p-6 space-y-4">
                @if($order->shipping)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div class="flex gap-3">
                        <span class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600 shrink-0"><i class="fa-solid fa-user"></i></span>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $order->shipping->name ?? 'Guest' }}</p>
                            <p class="text-gray-600">{{ $order->shipping->phone ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <span class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600 shrink-0"><i class="fa-solid fa-location-dot"></i></span>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $order->shipping->area ?? 'General' }}</p>
                            <p class="text-gray-600">{{ Str::limit($order->shipping->address ?? '-', 50) }}</p>
                        </div>
                    </div>
                </div>
                @endif
                <div class="border-t pt-4">
                    <p class="font-semibold text-gray-800 mb-3">প্রোডাক্ট সমূহ</p>
                    <ul class="space-y-3">
                        @foreach($order->orderdetails as $item)
                        <li class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                            <span class="text-gray-700">{{ $item->product_name }} × {{ $item->qty }}</span>
                            <span class="font-bold text-blue-700">৳{{ number_format($item->sale_price * $item->qty, 0) }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="flex justify-between items-center pt-4 border-t-2 font-bold text-lg">
                    <span>মোট</span>
                    <span class="text-blue-700">৳{{ number_format($order->amount, 0) }}</span>
                </div>
            </div>
        </div>
        @endforeach

        <a href="{{ route('reseller.landing.order-track', $landing->slug) }}" class="inline-flex items-center justify-center w-full sm:w-auto px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition">
            <i class="fa-solid fa-arrow-left mr-2"></i> আরেকটি অর্ডার ট্র্যাক করুন
        </a>
    </div>

    <div class="fixed bottom-0 left-0 w-full bg-white border-t z-50 md:hidden py-3 flex justify-center gap-4">
        <a href="{{ route('reseller.landing.order-track', $landing->slug) }}" class="text-blue-600 font-semibold"><i class="fa-solid fa-truck-fast mr-1"></i> ট্র্যাক</a>
        <a href="{{ landing_url($landing->slug, '') }}" class="text-blue-600 font-semibold"><i class="fa-solid fa-house mr-1"></i> হোম</a>
    </div>
</body>
</html>
