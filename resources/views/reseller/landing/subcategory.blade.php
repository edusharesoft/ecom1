@php
    $categoryRoute = fn($c) => landing_url($landing->slug, 'category/'.$c->slug);
    $subcategoryRoute = fn($s) => landing_url($landing->slug, 'subcategory/'.$s->slug);
    $currentCatId = $subcategory->category_id ?? null;
@endphp
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subcategory->subcategoryName ?? $subcategory->name }} | {{ $landing->title }}</title>
    @if(!empty($landing->favicon))<link rel="icon" type="{{ str_ends_with($landing->favicon, '.ico') ? 'image/x-icon' : 'image/png' }}" href="{{ asset($landing->favicon) }}">@endif
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>@keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } } .animate-marquee { animation: marquee 20s linear infinite; }</style>
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
        <div class="container mx-auto px-4 py-3 flex items-center">
            <div class="flex-1 min-w-0 overflow-hidden">
                <div class="flex items-center">
                    <div class="flex items-center gap-6 whitespace-nowrap text-sm md:text-base">
                        <span class="inline-block animate-marquee">{{ $landing->scrolling_text }}</span>
                        <span class="inline-block animate-marquee" aria-hidden="true">{{ $landing->scrolling_text }}</span>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    @endif

    <div class="container mx-auto px-4 py-6 flex flex-col lg:flex-row gap-6">
        {{-- Sidebar --}}
        <aside class="w-full lg:w-1/4 hidden lg:block bg-white shadow rounded-lg p-4 h-fit">
            <h3 class="font-bold text-gray-800 mb-3 border-b pb-2"><i class="fa-solid fa-border-all mr-2 text-blue-600"></i> সব ক্যাটাগরি</h3>
            <ul class="space-y-1 text-sm font-medium">
                @foreach($categories ?? [] as $cat)
                    <li class="border-b border-gray-100 last:border-0">
                        <div class="flex items-center">
                            <a href="{{ $categoryRoute($cat) }}" class="flex-1 flex items-center py-3 hover:text-blue-600">
                                <span class="w-8 h-8 rounded bg-blue-100 flex items-center justify-center mr-3 overflow-hidden p-0.5 flex-shrink-0">
                                    @if(!empty($cat->icon) || !empty($cat->image))
                                        <img src="{{ asset($cat->icon ?? $cat->image) }}" alt="" class="w-full h-full object-contain" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                        <i class="fa-solid fa-folder text-blue-600 text-sm hidden"></i>
                                    @else
                                        <i class="fa-solid fa-folder text-blue-600 text-sm"></i>
                                    @endif
                                </span>
                                {{ $cat->name }}
                            </a>
                            @if($cat->subcategories && $cat->subcategories->count() > 0)
                                <button type="button" onclick="toggleSubcat('cat-{{ $cat->id }}', this)" class="p-2 text-gray-400 hover:text-blue-600">
                                    <i class="fa-solid fa-chevron-right text-xs cat-toggle-icon" style="{{ $currentCatId == $cat->id ? 'transform:rotate(90deg)' : '' }}"></i>
                                </button>
                            @endif
                        </div>
                        @if($cat->subcategories && $cat->subcategories->count() > 0)
                            <ul id="cat-{{ $cat->id }}" class="ml-11 mb-2 space-y-0.5 {{ $currentCatId == $cat->id ? '' : 'hidden' }}">
                                @foreach($cat->subcategories as $sub)
                                    <li><a href="{{ $subcategoryRoute($sub) }}" class="block py-1.5 px-2 rounded hover:bg-blue-50 hover:text-blue-600 text-xs {{ ($subcategory->id ?? null) == $sub->id ? 'text-blue-600 font-bold' : '' }}">{{ $sub->subcategoryName ?? $sub->name }}</a></li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
            <a href="{{ landing_url($landing->slug, '') }}" class="block mt-4 text-blue-600 font-bold text-sm">হোমে ফিরে যান <i class="fa-solid fa-angle-right ml-1"></i></a>
        </aside>

        {{-- Main --}}
        <main class="flex-1">
            <div class="bg-white rounded-xl shadow p-4 md:p-6 mb-6">
                <h1 class="text-xl md:text-2xl font-bold text-gray-800 border-b-4 border-blue-600 inline-block pb-1">{{ $subcategory->subcategoryName ?? $subcategory->name }}</h1>
                <p class="text-sm text-gray-500 mt-2">{{ $products->total() }} প্রোডাক্ট</p>
            </div>

            @if($products->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($products as $product)
                        @php
                            $img = $product->image && $product->image->image ? $product->image->image : 'public/uploads/default.webp';
                            $displayPrice = (float) (($customPrices[$product->id] ?? null) ?? $product->reseller_price ?? 0);
                        @endphp
                        <div class="bg-white rounded-lg shadow hover:shadow-xl transition p-3 relative">
                            <a href="{{ landing_url($landing->slug, 'product/'.$product->slug) }}"><img src="{{ asset($img) }}" alt="" class="w-full h-40 object-cover rounded mb-3"></a>
                            <h3 class="font-semibold text-sm line-clamp-2 mb-2">{{ $product->name }}</h3>
                            <div class="bg-blue-50 p-2 rounded mb-3 border border-blue-100">
                                <div class="flex justify-between"><span class="text-xs text-gray-600">মূল্য:</span><span class="font-bold text-blue-700">৳{{ number_format($displayPrice, 0) }}</span></div>
                            </div>
                            <form action="{{ landing_url($landing->slug, 'cart/add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="qty" value="1">
                                <button type="submit" class="block w-full bg-blue-600 text-white py-2 rounded text-sm text-center hover:bg-blue-700"><i class="fa-solid fa-cart-plus mr-1"></i> অর্ডার</button>
                            </form>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6">{{ $products->withQueryString()->links() }}</div>
            @else
                <div class="bg-white rounded-xl shadow p-12 text-center text-gray-500">
                    <i class="fa-solid fa-box-open text-5xl mb-4 opacity-50"></i>
                    <p>এই সাবক্যাটাগরিতে কোন প্রোডাক্ট নেই।</p>
                    @if($subcategory->category)
                    <a href="{{ landing_url($landing->slug, 'category/'.$subcategory->category->slug) }}" class="inline-block mt-4 text-blue-600 font-semibold">ক্যাটাগরিতে ফিরে যান</a>
                @else
                    <a href="{{ landing_url($landing->slug, '') }}" class="inline-block mt-4 text-blue-600 font-semibold">হোমে ফিরে যান</a>
                @endif
                </div>
            @endif
        </main>
    </div>

    <footer class="bg-gray-900 text-gray-300 pt-8 pb-6 mt-10">
        <div class="container mx-auto px-4 text-center text-sm">
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
                        <button type="button" onclick="event.stopPropagation();toggleSubcat('mcat-{{ $cat->id }}',this)" class="p-3"><i class="fa-solid fa-chevron-right text-xs" style="{{ $currentCatId == $cat->id ? 'transform:rotate(90deg)' : '' }}"></i></button>
                    @endif</div>
                    @if($cat->subcategories && $cat->subcategories->count() > 0)
                        <ul id="mcat-{{ $cat->id }}" class="bg-gray-50 {{ $currentCatId == $cat->id ? '' : 'hidden' }}">
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
        <a href="{{ route('home') }}" class="flex flex-col items-center"><i class="fa-solid fa-store text-lg mb-1"></i>শপ</a>
        <a href="{{ route('reseller.landing.order-track', $landing->slug) }}" class="flex flex-col items-center"><i class="fa-solid fa-truck-fast text-lg mb-1"></i>অর্ডার ট্রাকিং</a>
    </div>

    <script>
    function toggleSubcat(id, btn) { var el = document.getElementById(id); var icon = btn && btn.querySelector('i'); if (el) { el.classList.toggle('hidden'); if (icon) icon.style.transform = el.classList.contains('hidden') ? '' : 'rotate(90deg)'; } }
    function toggleCategoryMenu() { var o = document.getElementById('categoryOverlay'); var d = document.getElementById('categoryDrawer'); o.classList.toggle('hidden'); d.classList.toggle('-translate-x-full'); document.body.style.overflow = o.classList.contains('hidden') ? '' : 'hidden'; }
    </script>
</body>
</html>
