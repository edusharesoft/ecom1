<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>অর্ডার ফর্ম | {{ $landing->title }}</title>
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
        <div class="container mx-auto px-4 py-4">
            <a href="{{ landing_url($landing->slug, '') }}" class="flex items-center">
                @if($landing->logo)<img src="{{ asset($landing->logo) }}" alt="" class="h-10 w-auto">@else<span class="text-xl font-bold text-blue-700">{{ $landing->title }}</span>@endif
            </a>
        </div>
    </header>

    <div class="container mx-auto px-4 py-6 max-w-4xl">
        @php
            $gsLandingOtpSet = \App\Models\GeneralSetting::where('status', 1)->first();
            $landingOtpChanKey = 'landing_' . md5($landing->slug);
            $landingOtpPendingKey = session('chkotp_' . $landingOtpChanKey . '_pending');
            $showLandingCheckoutOtpModal = $gsLandingOtpSet && ($gsLandingOtpSet->checkout_otp_enabled ?? 0) == 1 && $landingOtpPendingKey;
        @endphp

        <h1 class="text-xl md:text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fa-solid fa-cart-shopping mr-2 text-blue-600"></i> অর্ডার ফর্ম
        </h1>

        <form id="landing-order-form" action="{{ landing_url($landing->slug, 'order') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="checkout_otp" id="landing_checkout_otp_hidden" value="{{ old('checkout_otp') }}">
            <input type="hidden" name="traffic_source" id="inp_ts" value="{{ old('traffic_source', session('order_traffic_source', 'direct')) }}">
            <input type="hidden" name="traffic_referrer" id="inp_tsr" value="{{ old('traffic_referrer', session('order_traffic_referrer', '')) }}">
            <script>
            try {
                var elTs = document.getElementById('inp_ts');
                var elTsr = document.getElementById('inp_tsr');
                var ts = sessionStorage.getItem('_ts');
                var tsr = sessionStorage.getItem('_tsr');
                if (ts !== null && ts !== '') {
                    elTs.value = ts;
                } else if (elTs.value && elTs.value !== 'direct') {
                    sessionStorage.setItem('_ts', elTs.value);
                }
                if (tsr !== null && tsr !== '') {
                    elTsr.value = tsr;
                } else if (elTsr.value) {
                    sessionStorage.setItem('_tsr', elTsr.value);
                }
            } catch (e) {}
            </script>

            {{-- Cart Items --}}
            <div class="bg-white rounded-xl shadow p-4 sm:p-6">
                <h2 class="font-bold text-gray-800 mb-4">প্রোডাক্ট সমূহ</h2>
                <ul class="space-y-4">
                    @foreach($cartItems as $item)
                        <li class="flex items-center gap-4 pb-4 border-b border-gray-100 last:border-0">
                            @php
                                $img = $item->product->image && $item->product->image->image ? $item->product->image->image : 'public/uploads/default.webp';
                            @endphp
                            <img src="{{ asset($img) }}" alt="" class="w-16 h-16 object-cover rounded">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-800 truncate">{{ $item->product->name }}</p>
                                <p class="text-sm text-gray-500">পরিমাণ: {{ $item->qty }} × ৳{{ number_format($item->price, 0) }} = ৳{{ number_format($item->total, 0) }}</p>
                            </div>
                            <a href="{{ landing_url($landing->slug, 'cart/remove/'.$item->product->id) }}" class="text-red-500 hover:text-red-700" onclick="return confirm('রিমুভ করবেন?')">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-4 pt-4 border-t font-bold text-lg">সাবটোটাল: ৳{{ number_format($subtotal, 0) }}</div>
            </div>

            {{-- Customer Info --}}
            <div class="bg-white rounded-xl shadow p-4 sm:p-6">
                <h2 class="font-bold text-gray-800 mb-4"><i class="fa-solid fa-user mr-2 text-blue-600"></i> আপনার তথ্য</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">নাম <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="আপনার নাম">
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ফোন <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="01XXXXXXXXX">
                        @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">ঠিকানা <span class="text-red-500">*</span></label>
                    <textarea name="address" rows="3" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="বিস্তারিত ঠিকানা">{{ old('address') }}</textarea>
                    @error('address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">এলাকা / ডেলিভারি চার্জ <span class="text-red-500">*</span></label>
                    <select name="area" id="areaSelect" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">সিলেক্ট করুন</option>
                        @foreach($shippingcharge as $sh)
                            <option value="{{ $sh->id }}" data-amount="{{ $sh->amount ?? 0 }}" {{ old('area') == $sh->id ? 'selected' : '' }}>{{ $sh->name }} - ৳{{ number_format($sh->amount ?? 0, 0) }}</option>
                        @endforeach
                        <option value="free_shipping" data-amount="0">ফ্রি ডেলিভারি - ৳0</option>
                    </select>
                    @error('area')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">অর্ডার নোট (ঐচ্ছিক)</label>
                    <textarea name="order_note" rows="2" class="w-full px-4 py-2 border rounded-lg" placeholder="বিশেষ নির্দেশনা">{{ old('order_note') }}</textarea>
                </div>
            </div>

            {{-- Payment --}}
            <div class="bg-white rounded-xl shadow p-4 sm:p-6">
                <h2 class="font-bold text-gray-800 mb-4"><i class="fa-solid fa-credit-card mr-2 text-blue-600"></i> পেমেন্ট মেথড</h2>
                <div class="space-y-3">
                    <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer hover:bg-blue-50 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50">
                        <input type="radio" name="payment_method" value="cod" {{ old('payment_method', 'cod') == 'cod' ? 'checked' : '' }} required>
                        <span class="font-medium">ক্যাশ অন ডেলিভারি (COD)</span>
                    </label>
                    <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer hover:bg-blue-50 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50">
                        <input type="radio" name="payment_method" value="bkash" {{ old('payment_method') == 'bkash' ? 'checked' : '' }}>
                        <span class="font-medium">bKash</span>
                    </label>
                    <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer hover:bg-blue-50 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50">
                        <input type="radio" name="payment_method" value="shurjopay" {{ old('payment_method') == 'shurjopay' ? 'checked' : '' }}>
                        <span class="font-medium">ShurjoPay</span>
                    </label>
                </div>
            </div>

            {{-- Summary --}}
            <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-4 sm:p-6">
                <div class="flex justify-between mb-2"><span>সাবটোটাল:</span><span>৳{{ number_format($subtotal, 0) }}</span></div>
                <div class="flex justify-between mb-2"><span>ডেলিভারি:</span><span id="shippingDisplay">৳{{ number_format($defaultShippingAmount ?? 0, 0) }}</span></div>
                <div class="flex justify-between font-bold text-lg pt-2 border-t border-blue-200"><span>মোট:</span><span id="grandTotal">৳{{ number_format($subtotal + ($defaultShippingAmount ?? 0), 0) }}</span></div>
            </div>

            <button type="submit" class="w-full py-4 bg-blue-600 text-white font-bold text-lg rounded-xl hover:bg-blue-700 transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-check-circle"></i> অর্ডার কনফার্ম করুন
            </button>
        </form>

        @if(!empty($showLandingCheckoutOtpModal))
        <div id="landingCheckoutOtpBackdrop" class="fixed inset-0 z-[200] flex items-center justify-center bg-black/60 p-4" role="dialog" aria-modal="true">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 relative border-2 border-blue-500">
                <h3 class="font-bold text-lg text-gray-900 mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-mobile-screen text-blue-600"></i> OTP ভেরিফিকেশন
                </h3>
                <p class="text-sm text-gray-600 mb-4">আপনার মোবাইলে পাঠানো <strong>৬ ডিজিটের OTP</strong> লিখে নিচের বাটনে চাপ দিন।</p>
                @error('checkout_otp')
                    <p class="text-red-600 text-sm mb-3 font-medium">{{ $message }}</p>
                @enderror
                <label class="block text-sm font-medium text-gray-700 mb-1">OTP কোড</label>
                <input type="text" id="landing_otp_modal_input" maxlength="6" inputmode="numeric" autocomplete="one-time-code"
                    class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl mb-4 font-mono text-2xl text-center tracking-[0.4em]"
                    placeholder="______" value="{{ old('checkout_otp') }}">
                <div class="flex flex-wrap gap-3 justify-between items-center">
                    <button type="button" id="landing_otp_resend_btn" class="text-blue-600 font-semibold text-sm hover:underline px-2">OTP আবার পাঠান</button>
                    <button type="button" id="landing_otp_confirm_btn" class="bg-blue-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 transition">
                        অর্ডার সম্পূর্ণ করুন
                    </button>
                </div>
            </div>
        </div>
        <form id="landing_otp_resend_form" action="{{ route('reseller.landing.order.resend_otp', $landing->slug) }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="phone" id="landing_otp_resend_phone" value="">
        </form>
        @endif

        <div class="mt-6 text-center">
            <a href="{{ landing_url($landing->slug, '') }}" class="text-blue-600 font-semibold hover:underline">
                <i class="fa-solid fa-arrow-left mr-1"></i> হোমে ফিরে যান
            </a>
        </div>
    </div>

    <script>
    const sub = {{ $subtotal }};
    function updateTotals() {
        const sel = document.getElementById('areaSelect');
        const opt = sel?.options[sel.selectedIndex];
        const ship = opt ? parseFloat(opt.getAttribute('data-amount') || 0) : 0;
        document.getElementById('shippingDisplay').textContent = '৳' + Math.round(ship).toLocaleString('en-BD');
        document.getElementById('grandTotal').textContent = '৳' + Math.round(sub + ship).toLocaleString('en-BD');
    }
    document.getElementById('areaSelect')?.addEventListener('change', updateTotals);
    updateTotals();
    </script>
@if(!empty($showLandingCheckoutOtpModal))
<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalInput = document.getElementById('landing_otp_modal_input');
    if (modalInput) modalInput.focus();
    document.getElementById('landing_otp_confirm_btn').addEventListener('click', function () {
        var raw = modalInput ? modalInput.value : '';
        document.getElementById('landing_checkout_otp_hidden').value = raw.replace(/\D/g, '').slice(0, 6);
        document.getElementById('landing-order-form').submit();
    });
    document.getElementById('landing_otp_resend_btn').addEventListener('click', function () {
        var phoneIn = document.querySelector('#landing-order-form input[name="phone"]');
        document.getElementById('landing_otp_resend_phone').value = phoneIn ? phoneIn.value : '';
        document.getElementById('landing_otp_resend_form').submit();
    });
});
</script>
@endif
</body>
</html>
