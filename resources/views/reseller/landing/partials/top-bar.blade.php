{{-- Top Bar: হোম + যোগাযোগ (রিসেলার লগইন ও মূল ওয়েবসাইট রিমুভ) --}}
<div class="bg-gray-900 text-gray-300 text-xs md:text-sm py-2">
    <div class="container mx-auto px-4 flex justify-between items-center">
        <div class="flex space-x-4">
            @if($landing->phone ?? null)
                <a href="tel:{{ $landing->phone }}" class="hover:text-white transition"><i class="fa-solid fa-phone mr-1"></i> {{ $landing->phone }}</a>
            @endif
            @if($landing->email ?? null)
                <span class="hidden md:inline"><i class="fa-solid fa-envelope mr-1"></i> {{ $landing->email }}</span>
            @endif
        </div>
        <div class="flex space-x-4">
            <a href="{{ landing_url($landing->slug, '') }}" class="hover:text-white transition">হোম</a>
            <span>|</span>
            <a href="{{ route('reseller.landing.contact', $landing->slug) }}" class="hover:text-white transition">যোগাযোগ</a>
        </div>
    </div>
</div>
