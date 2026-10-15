<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>যোগাযোগ | {{ $landing->title }}</title>
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
            @if($landing->email)<a href="mailto:{{ $landing->email }}" class="hover:text-white"><i class="fa-solid fa-envelope mr-1"></i> {{ $landing->email }}</a>@endif
            <div class="flex gap-4">
                <a href="{{ landing_url($landing->slug, '') }}" class="hover:text-white transition">হোম</a>
                <span>|</span>
                <a href="{{ route('reseller.landing.contact', $landing->slug) }}" class="hover:text-white transition font-semibold">যোগাযোগ</a>
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

    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <h1 class="text-xl md:text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fa-solid fa-address-card mr-2 text-blue-600"></i> যোগাযোগ
        </h1>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Contact Info --}}
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="font-bold text-gray-800 mb-4">আমাদের সাথে যোগাযোগ করুন</h2>
                <div class="space-y-4">
                    @if($landing->phone)
                    <div class="flex items-start gap-4">
                        <span class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600 shrink-0"><i class="fa-solid fa-phone"></i></span>
                        <div>
                            <p class="font-semibold text-gray-800">ফোন</p>
                            <a href="tel:{{ $landing->phone }}" class="text-gray-600 hover:text-blue-600">{{ $landing->phone }}</a>
                        </div>
                    </div>
                    @endif
                    @if($landing->email)
                    <div class="flex items-start gap-4">
                        <span class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600 shrink-0"><i class="fa-solid fa-envelope"></i></span>
                        <div>
                            <p class="font-semibold text-gray-800">ইমেইল</p>
                            <a href="mailto:{{ $landing->email }}" class="text-gray-600 hover:text-blue-600">{{ $landing->email }}</a>
                        </div>
                    </div>
                    @endif
                    @if($landing->address)
                    <div class="flex items-start gap-4">
                        <span class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600 shrink-0"><i class="fa-solid fa-location-dot"></i></span>
                        <div>
                            <p class="font-semibold text-gray-800">ঠিকানা</p>
                            <p class="text-gray-600">{{ $landing->address }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Contact Form --}}
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="font-bold text-gray-800 mb-4">মেসেজ পাঠান</h2>
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
                @endif
                <form action="{{ route('reseller.landing.contact.store', $landing->slug) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">নাম <span class="text-red-500">*</span></label>
                        <input type="text" name="full_name" value="{{ old('full_name') }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="আপনার নাম">
                        @error('full_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">মোবাইল <span class="text-red-500">*</span></label>
                        <input type="text" name="mobile" value="{{ old('mobile') }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="01XXXXXXXXX">
                        @error('mobile')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ইমেইল</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="email@example.com">
                        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">বিষয়</label>
                        <input type="text" name="subject" value="{{ old('subject') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="বিষয়">
                        @error('subject')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">মেসেজ <span class="text-red-500">*</span></label>
                        <textarea name="details" rows="4" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="আপনার মেসেজ লিখুন">{{ old('details') }}</textarea>
                        @error('details')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                        <i class="fa-solid fa-paper-plane mr-2"></i> পাঠান
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="fixed bottom-0 left-0 w-full bg-white border-t z-50 md:hidden py-3 flex justify-center">
        <a href="{{ landing_url($landing->slug, '') }}" class="text-blue-600 font-semibold"><i class="fa-solid fa-house mr-1"></i> হোমে ফিরে যান</a>
    </div>
</body>
</html>
