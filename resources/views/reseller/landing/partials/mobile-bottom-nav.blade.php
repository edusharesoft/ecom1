{{-- Mobile Bottom Nav: Order Tracking instead of Login --}}
<a href="{{ landing_url($landing->slug, '') }}" class="flex flex-col items-center text-blue-600">
    <i class="fa-solid fa-house text-lg mb-1"></i>
    <span>হোম</span>
</a>
<a href="javascript:void(0)" onclick="toggleCategoryMenu()" class="flex flex-col items-center hover:text-blue-600 transition">
    <i class="fa-solid fa-border-all text-lg mb-1"></i>
    <span>ক্যাটাগরি</span>
</a>
<a href="{{ route('home') }}" class="flex flex-col items-center hover:text-blue-600 transition">
    <i class="fa-solid fa-store text-lg mb-1"></i>
    <span>শপ</span>
</a>
@if($landing->phone ?? null)
    <a href="tel:{{ $landing->phone }}" class="flex flex-col items-center hover:text-blue-600 transition">
        <i class="fa-solid fa-phone text-lg mb-1"></i>
        <span>কল</span>
    </a>
@endif
<a href="{{ route('reseller.landing.order-track', $landing->slug) }}" class="flex flex-col items-center hover:text-blue-600 transition">
    <i class="fa-solid fa-truck-fast text-lg mb-1"></i>
    <span>অর্ডার ট্রাকিং</span>
</a>
