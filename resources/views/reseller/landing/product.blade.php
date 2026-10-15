@php
    $categoryRoute = fn($c) => landing_url($landing->slug, 'category/'.$c->slug);
    $subcategoryRoute = fn($s) => landing_url($landing->slug, 'subcategory/'.$s->slug);
    $productRoute = fn($p) => landing_url($landing->slug, 'product/'.$p->slug);
    $mainImg = $product->image && $product->image->image ? $product->image->image : 'public/uploads/default.webp';
    $gallery = $product->images && $product->images->count() > 0 ? $product->images->pluck('image')->filter()->toArray() : [$mainImg];
    $retail = (float)($product->new_price ?? 0);
    $displayPrice = (float) (($customPrices[$product->id] ?? null) ?? $product->reseller_price ?? 0);
    $costPrice = (float)($product->reseller_price ?? 0);
    $profit = max(0, $displayPrice - $costPrice);
    $discount = $retail > 0 ? round((($retail - $displayPrice) / $retail) * 100) : 0;
@endphp
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="description" content="{{ Str::limit(strip_tags($product->description ?? ''), 160) }}">
    <title>{{ $product->name }} | {{ $landing->title }}</title>
    @if(!empty($landing->favicon))<link rel="icon" type="{{ str_ends_with($landing->favicon, '.ico') ? 'image/x-icon' : 'image/png' }}" href="{{ asset($landing->favicon) }}">@endif
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        html, body { overflow-x: hidden; max-width: 100vw; }
        .product-responsive-img { width: 100%; height: auto; max-width: 100%; object-fit: contain; }
        @media (max-width: 1023px) {
            .product-container { flex-direction: column; width: 100%; }
            .product-sidebar { width: 100%; max-width: 100%; }
            .product-main { width: 100%; min-width: 100%; }
        }
        @media (max-width: 640px) {
            .product-grid { padding: 0.75rem; gap: 0.75rem; }
            .container { padding-left: 0.75rem; padding-right: 0.75rem; }
        }
        @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
        .animate-marquee { animation: marquee 20s linear infinite; }
    </style>
    @include('reseller.landing.partials.tracking-head')
</head>
<body class="bg-gray-100 font-sans text-gray-800 pb-16 md:pb-0">
    @include('reseller.landing.partials.tracking-body')
    @include('reseller.landing.partials.top-bar')

    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center gap-4">
            <a href="{{ landing_url($landing->slug, '') }}">
                @if($landing->logo)<img src="{{ asset($landing->logo) }}" alt="{{ $landing->title }}" class="h-10 md:h-12 w-auto max-w-[180px] object-contain">
                @else<span class="text-2xl font-extrabold text-blue-700">{{ Str::limit($landing->title ?? 'স্টোর', 20) }}</span>@endif
            </a>
            <form action="{{ route('home') }}" method="GET" class="hidden md:flex max-w-xl flex-1 mx-4">
                <input type="hidden" name="search" value="1">
                <input type="text" name="q" placeholder="প্রোডাক্ট সার্চ..." class="flex-1 px-4 py-2 border rounded-l-lg outline-none">
                <button type="submit" class="bg-blue-600 text-white px-6 rounded-r-lg"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
            <div class="flex items-center space-x-4">
                @include('reseller.landing.partials.header-actions')
            </div>
        </div>
    </header>

    @if(!empty($landing->scrolling_text))
    <nav class="bg-blue-700 text-white overflow-hidden">
        <div class="container mx-auto px-3 sm:px-4 py-2 sm:py-3 flex items-center">
            <div class="flex-1 min-w-0 overflow-hidden">
                <div class="flex items-center">
                    <div class="flex items-center gap-6 whitespace-nowrap text-sm sm:text-base">
                        <span class="inline-block animate-marquee">{{ $landing->scrolling_text }}</span>
                        <span class="inline-block animate-marquee" aria-hidden="true">{{ $landing->scrolling_text }}</span>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    @endif

    <div class="container mx-auto px-3 sm:px-4 py-4 sm:py-6 flex flex-col lg:flex-row gap-4 sm:gap-6 product-container w-full max-w-[100vw] overflow-x-hidden">
        {{-- Category Sidebar (open by default, click header to toggle) --}}
        <aside class="w-full lg:w-64 flex-shrink-0 product-sidebar order-2 lg:order-1">
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <button type="button" onclick="toggleProductCat()" class="w-full flex items-center justify-between px-4 py-3 sm:py-4 bg-blue-700 text-white font-bold text-left hover:bg-blue-800 transition text-sm sm:text-base">
                    <span><i class="fa-solid fa-border-all mr-2"></i> সব ক্যাটাগরি</span>
                    <i id="productCatIcon" class="fa-solid fa-chevron-down text-sm transition-transform duration-200" style="transform: rotate(180deg);"></i>
                </button>
                <div id="productCatList" class="border-t border-gray-200 max-h-[70vh] overflow-y-auto">
                    <ul class="py-2 text-sm">
                        @foreach($categories ?? [] as $cat)
                            <li class="border-b border-gray-100 last:border-0">
                                <div class="flex items-center">
                                    <a href="{{ $categoryRoute($cat) }}" class="flex-1 flex items-center px-4 py-3 hover:bg-blue-50 hover:text-blue-600 transition min-w-0">
                                        <span class="w-8 h-8 rounded bg-blue-100 flex items-center justify-center mr-3 flex-shrink-0 overflow-hidden p-0.5">
                                            @if(!empty($cat->icon) || !empty($cat->image))
                                                <img src="{{ asset($cat->icon ?? $cat->image) }}" alt="" class="w-full h-full object-contain" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                                <i class="fa-solid fa-folder text-blue-600 text-xs hidden"></i>
                                            @else
                                                <i class="fa-solid fa-folder text-blue-600 text-xs"></i>
                                            @endif
                                        </span>
                                        <span class="truncate">{{ $cat->name }}</span>
                                    </a>
                                    @if($cat->subcategories && $cat->subcategories->count() > 0)
                                        <button type="button" onclick="toggleSubcat('pcat-{{ $cat->id }}', this)" class="p-2 text-gray-400 hover:text-blue-600 flex-shrink-0">
                                            <i class="fa-solid fa-chevron-right text-xs transition-transform duration-200"></i>
                                        </button>
                                    @endif
                                </div>
                                @if($cat->subcategories && $cat->subcategories->count() > 0)
                                    <ul id="pcat-{{ $cat->id }}" class="bg-gray-50 hidden">
                                        @foreach($cat->subcategories as $sub)
                                            <li><a href="{{ $subcategoryRoute($sub) }}" class="block py-2 px-4 pl-12 hover:bg-blue-50 hover:text-blue-600 text-xs">{{ $sub->subcategoryName ?? $sub->name }}</a></li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ landing_url($landing->slug, '') }}" class="block px-4 py-3 text-blue-600 font-semibold text-sm hover:bg-blue-50 border-t">হোমে ফিরে যান <i class="fa-solid fa-angle-right ml-1"></i></a>
                </div>
            </div>
        </aside>

        <main class="flex-1 min-w-0 product-main order-1 lg:order-2 w-full">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden w-full">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 p-4 sm:p-6 product-grid">
                    {{-- Images --}}
                    <div>
                        <div id="mainImgWrap" class="aspect-square bg-gray-100 rounded-lg overflow-hidden mb-4 w-full">
                            <img id="mainProductImg" src="{{ asset($mainImg) }}" alt="{{ $product->name }}" class="product-responsive-img w-full h-full object-contain">
                        </div>
                        @if(count($gallery) > 1)
                            <div class="flex gap-2 overflow-x-auto pb-2">
                                @foreach($gallery as $idx => $gimg)
                                    <button type="button" onclick="document.getElementById('mainProductImg').src='{{ asset($gimg) }}'" class="flex-shrink-0 w-16 h-16 rounded border-2 border-gray-200 overflow-hidden hover:border-blue-500 focus:border-blue-500">
                                        <img src="{{ asset($gimg) }}" alt="" class="w-full h-full object-cover">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div>
                        <h1 class="text-base sm:text-xl md:text-2xl font-bold text-gray-800 mb-2 break-words">{{ $product->name }}</h1>
                        @if($product->brand)
                            <p class="text-sm text-gray-500 mb-3">ব্র্যান্ড: {{ $product->brand->name }}</p>
                        @endif

                        <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-4 sm:p-5 mb-4 sm:mb-6">
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-gray-800">মূল্য:</span>
                                <span class="text-2xl font-bold text-blue-700">৳{{ number_format($displayPrice, 0) }}</span>
                            </div>
                        </div>

                        @if($product->stock !== null)
                            <p class="text-sm mb-4">
                                স্টক: <span class="font-semibold {{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">{{ $product->stock > 0 ? $product->stock . ' পিস' : 'স্টক আউট' }}</span>
                            </p>
                        @endif

                        <div class="flex flex-col sm:flex-row flex-wrap gap-2 sm:gap-3">
                            <form action="{{ landing_url($landing->slug, 'cart/add') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="qty" value="1">
                                <button type="submit" class="inline-flex items-center justify-center px-4 sm:px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition text-sm sm:text-base w-full sm:w-auto">
                                    <i class="fa-solid fa-cart-shopping mr-2"></i> অর্ডার করুন
                                </button>
                            </form>
                            @if($landing->phone)
                                <a href="tel:{{ $landing->phone }}" class="inline-flex items-center justify-center px-4 sm:px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition text-sm sm:text-base w-full sm:w-auto">
                                    <i class="fa-solid fa-phone mr-2"></i> কল করুন
                                </a>
                            @endif
                            <a href="javascript:void(0)" onclick="navigator.clipboard.writeText('{{ route('product', $product->slug) }}'); alert('লিংক কপি হয়েছে!');" class="inline-flex items-center justify-center px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm w-full sm:w-auto">
                                <i class="fa-solid fa-link mr-2"></i> লিংক কপি
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Description --}}
                @if($product->description)
                    <div class="border-t p-4 sm:p-6 overflow-x-auto">
                        <h2 class="text-lg font-bold text-gray-800 mb-3">বিস্তারিত</h2>
                        <div class="prose prose-sm max-w-none text-gray-600 break-words overflow-x-auto">
                            {!! $product->description !!}
                        </div>
                    </div>
                @endif
            </div>

            {{-- Related Products --}}
            @if($relatedProducts->count() > 0)
                <div class="mt-6 sm:mt-8">
                    <h2 class="text-base sm:text-lg font-bold text-gray-800 mb-3 sm:mb-4 border-b-4 border-blue-600 inline-block pb-1">অন্যান্য প্রোডাক্ট</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 sm:gap-4">
                        @foreach($relatedProducts as $rp)
                            @php
                                $rImg = $rp->image && $rp->image->image ? $rp->image->image : 'public/uploads/default.webp';
                                $rDisplayPrice = (float) (($customPrices[$rp->id] ?? null) ?? $rp->reseller_price ?? 0);
                                $rCostPrice = (float)($rp->reseller_price ?? 0);
                                $rProfit = max(0, $rDisplayPrice - $rCostPrice);
                            @endphp
                            <a href="{{ $productRoute($rp) }}" class="bg-white rounded-lg shadow hover:shadow-xl transition p-2 sm:p-3 block">
                                <img src="{{ asset($rImg) }}" alt="" class="w-full h-28 sm:h-36 object-cover rounded mb-2 max-w-full">
                                <h3 class="font-semibold text-sm line-clamp-2">{{ $rp->name }}</h3>
                                <div class="mt-1">
                                    <span class="font-bold text-blue-700">৳{{ number_format($rDisplayPrice, 0) }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </main>
    </div>

    <footer class="bg-gray-900 text-gray-300 pt-6 sm:pt-8 pb-6 mt-8 sm:mt-10">
        <div class="container mx-auto px-4 text-center text-xs sm:text-sm">
            &copy; {{ date('Y') }} {{ $landing->title }}. Powered by {{ config('app.name') }}.
        </div>
    </footer>

    {{-- Mobile Drawer --}}
    <div id="categoryOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-[60] hidden" onclick="toggleCategoryMenu()"></div>
    <div id="categoryDrawer" class="fixed top-0 left-0 w-3/4 max-w-[300px] h-full bg-white z-[70] transform -translate-x-full transition-transform shadow-2xl overflow-y-auto">
        <div class="bg-blue-700 text-white p-4 flex justify-between items-center"><h3 class="font-bold">সব ক্যাটাগরি</h3><button onclick="toggleCategoryMenu()" class="text-2xl">&times;</button></div>
        <ul class="py-2">
            @foreach($categories ?? [] as $cat)
                <li class="border-b">
                    <div class="flex"><a href="{{ $categoryRoute($cat) }}" onclick="toggleCategoryMenu()" class="flex-1 py-3 px-4">{{ $cat->name }}</a>
                    @if($cat->subcategories && $cat->subcategories->count() > 0)
                        <button type="button" onclick="event.stopPropagation();toggleSubcat('mcat-{{ $cat->id }}',this)" class="p-3"><i class="fa-solid fa-chevron-right text-xs" style="{{ ($product->category_id ?? null) == $cat->id ? 'transform:rotate(90deg)' : '' }}"></i></button>
                    @endif</div>
                    @if($cat->subcategories && $cat->subcategories->count() > 0)
                        <ul id="mcat-{{ $cat->id }}" class="bg-gray-50 {{ ($product->category_id ?? null) == $cat->id ? '' : 'hidden' }}">
                            @foreach($cat->subcategories as $sub)
                                <li><a href="{{ $subcategoryRoute($sub) }}" onclick="toggleCategoryMenu()" class="block py-2 px-4 pl-12 text-sm">{{ $sub->subcategoryName ?? $sub->name }}</a></li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
            <li><a href="{{ landing_url($landing->slug, '') }}" onclick="toggleCategoryMenu()" class="block px-4 py-3 text-blue-600 font-bold">হোম</a></li>
        </ul>
    </div>

    <div class="fixed bottom-0 left-0 w-full bg-white border-t z-50 md:hidden py-2 flex justify-around text-gray-500 text-xs">
        <a href="{{ landing_url($landing->slug, '') }}" class="flex flex-col items-center text-blue-600"><i class="fa-solid fa-house text-lg mb-1"></i>হোম</a>
        <a href="javascript:void(0)" onclick="toggleCategoryMenu()" class="flex flex-col items-center"><i class="fa-solid fa-border-all text-lg mb-1"></i>ক্যাটাগরি</a>
        <a href="{{ landing_url($landing->slug, 'order') }}" class="flex flex-col items-center text-green-600"><i class="fa-solid fa-cart-shopping text-lg mb-1"></i>অর্ডার</a>
        <a href="{{ route('reseller.landing.order-track', $landing->slug) }}" class="flex flex-col items-center"><i class="fa-solid fa-truck-fast text-lg mb-1"></i>অর্ডার ট্রাকিং</a>
    </div>

    <script>
    function toggleProductCat() {
        var list = document.getElementById('productCatList');
        var icon = document.getElementById('productCatIcon');
        if (list.classList.contains('hidden')) {
            list.classList.remove('hidden');
            if (icon) icon.style.transform = 'rotate(180deg)';
        } else {
            list.classList.add('hidden');
            if (icon) icon.style.transform = '';
        }
    }
    function toggleSubcat(id, btn) { var el = document.getElementById(id); var icon = btn && btn.querySelector('i'); if (el) { el.classList.toggle('hidden'); if (icon) icon.style.transform = el.classList.contains('hidden') ? '' : 'rotate(90deg)'; } }
    function toggleCategoryMenu() { var o = document.getElementById('categoryOverlay'); var d = document.getElementById('categoryDrawer'); o.classList.toggle('hidden'); d.classList.toggle('-translate-x-full'); document.body.style.overflow = o.classList.contains('hidden') ? '' : 'hidden'; }
    </script>
</body>
</html>
