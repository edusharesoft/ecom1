<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>অর্ডার ট্রাকিং | {{ $landing->title }}</title>
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
            <a href="{{ landing_url($landing->slug, '') }}" class="text-blue-600 font-semibold hover:text-blue-700"><i class="fa-solid fa-arrow-left mr-1"></i> হোমে ফিরে যান</a>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8 max-w-2xl">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-blue-600 text-white p-6 sm:p-8 text-center">
                <i class="fa-solid fa-truck-fast text-5xl mb-4 opacity-90"></i>
                <h1 class="text-xl md:text-2xl font-bold">অর্ডার ট্রাকিং</h1>
                <p class="text-sm mt-2 opacity-90">ঘরে বসেই জানুন আপনার অর্ডারের অবস্থান। মোবাইল নাম্বার অথবা ইনভয়েস আইডি দিন।</p>
            </div>
            <div class="p-6 sm:p-8">
                <form action="{{ route('reseller.landing.order-track.result', $landing->slug) }}" method="GET" class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">মোবাইল নাম্বার</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="017xxxxxxxx" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition">
                    </div>
                    <div class="flex items-center gap-2 text-gray-400 text-sm">
                        <span class="flex-1 h-px bg-gray-200"></span>
                        <span>অথবা</span>
                        <span class="flex-1 h-px bg-gray-200"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">ইনভয়েস আইডি</label>
                        <input type="text" name="invoice_id" value="{{ old('invoice_id') }}" placeholder="যেমন: 54321" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition">
                    </div>
                    <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-search"></i> ট্র্যাক করুন
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="fixed bottom-0 left-0 w-full bg-white border-t z-50 md:hidden py-2 flex justify-center">
        <a href="{{ landing_url($landing->slug, '') }}" class="text-blue-600 font-semibold"><i class="fa-solid fa-house mr-1"></i> হোমে ফিরে যান</a>
    </div>
</body>
</html>
