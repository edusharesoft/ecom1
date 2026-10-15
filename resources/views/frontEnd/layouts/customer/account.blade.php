@php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;

$customer = Auth::guard('customer')->user();
$customerId = $customer->id;

// Statistics
$totalOrders = Order::where('customer_id', $customerId)->count();
$pendingOrders = Order::where('customer_id', $customerId)
    ->whereNotIn('order_status', ['6', '11'])
    ->count();
$completedOrders = Order::where('customer_id', $customerId)
    ->where('order_status', '6')
    ->count();

// Recent Orders (last 5)
$recentOrders = Order::where('customer_id', $customerId)
    ->with(['status', 'payment', 'orderdetails.product'])
    ->latest()
    ->limit(5)
    ->get();

// Recommended Products
$recommendedProducts = Product::where('status', 1)
    ->where('approval_status', 'approved')
    ->where('stock', '>', 0)
    ->with('image')
    ->inRandomOrder()
    ->limit(4)
    ->get();

// Total Order Amount
$totalOrderAmount = Order::where('customer_id', $customerId)->sum('amount');

// Profile Image - Use direct image path
$profileImage = $customer->image ? asset($customer->image) : asset('public/uploads/default/no-image.png');

// Pending Orders Count for Badge
$pendingOrdersCount = Order::where('customer_id', $customerId)
    ->whereNotIn('order_status', ['6', '11'])
    ->count();

// Site Name & Logo
$siteName = \App\Models\GeneralSetting::first();
$siteInitial = strtoupper(substr($siteName->name ?? 'G', 0, 1));
$siteDisplayName = Str::limit($siteName->name ?? 'GadgetShop', 8);
$generalsetting = $siteName; // For sidebar compatibility
$darkLogo = $siteName->dark_logo ?? null;
@endphp

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Customer Panel | {{ $siteName->name ?? 'Gadget Style' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Hind Siliguri', sans-serif; background-color: #F0F2F5; }
        .sidebar-item:hover { background-color: #f3f4f6; color: #4f46e5; }
        .active-menu { background-color: #EEF2FF; color: #4f46e5; border-right: 3px solid #4f46e5; }
        
        /* Table Style */
        .custom-table th { background-color: #F9FAFB; color: #6B7280; font-weight: 600; font-size: 0.85rem; }
        .custom-table td { border-bottom: 1px solid #F3F4F6; padding: 16px; font-size: 0.9rem; }
        
        /* Mobile Menu Transition */
        #sidebar { transition: transform 0.3s ease-in-out; }
        
        /* Product Image Responsive */
        .product-image-container {
            position: relative;
            width: 100%;
            padding-bottom: 100%; /* 1:1 aspect ratio */
            background-color: #f3f4f6;
            overflow: hidden;
        }
        
        @media (min-width: 640px) {
            .product-image-container {
                padding-bottom: 75%; /* 4:3 aspect ratio for tablet */
            }
        }
        
        @media (min-width: 1024px) {
            .product-image-container {
                padding-bottom: 100%; /* 1:1 aspect ratio for desktop */
            }
        }
        
        .product-image-container img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        /* Line Clamp Utility */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        /* Product Card Hover Effect */
        .product-card-hover {
            transition: all 0.3s ease;
        }
        
        .product-card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="flex min-h-screen relative">

    <div id="overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden lg:hidden"></div>

    <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r transform -translate-x-full lg:translate-x-0 lg:static lg:inset-auto lg:flex flex-col shrink-0 h-screen transition-transform duration-300">
        <div class="p-4 sm:p-6 flex items-center justify-between lg:justify-start gap-2 border-b border-gray-100">
            @if($darkLogo)
                <a href="{{ route('home') }}" class="flex items-center gap-2 flex-1">
                    <img src="{{ asset($darkLogo) }}" alt="{{ $siteName->name ?? 'Logo' }}" class="h-8 sm:h-10 w-auto max-w-full object-contain">
                </a>
            @else
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold">{{ $siteInitial }}</div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-800 tracking-tight">{{ $siteDisplayName }}</h1>
                </div>
            @endif
            <button onclick="toggleSidebar()" class="lg:hidden text-gray-500 hover:text-red-500">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <nav class="flex-1 px-0 text-gray-500 font-medium space-y-1 mt-2 overflow-y-auto">
            <a href="{{route('customer.account')}}" class="{{request()->is('customer/account')?'active-menu':'sidebar-item'}} flex items-center px-6 py-3.5 transition-colors">
                <i class="fas fa-home w-6"></i> ড্যাশবোর্ড
            </a>
            <a href="{{route('customer.orders')}}" class="{{request()->is('customer/orders')?'active-menu':'sidebar-item'}} flex items-center px-6 py-3.5 transition-colors">
                <i class="fas fa-box-open w-6"></i> আমার অর্ডার 
                @if($pendingOrdersCount > 0)
                    <span class="ml-auto bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-full">{{ $pendingOrdersCount }}</span>
                @endif
            </a>
            <a href="{{route('customer.order_track')}}" class="{{request()->is('customer/order-track*')?'active-menu':'sidebar-item'}} flex items-center px-6 py-3.5 transition-colors">
                <i class="fas fa-truck w-6"></i> ট্র্যাক অর্ডার
            </a>
            <a href="{{route('customer.refunds')}}" class="{{request()->is('customer/refunds*')?'active-menu':'sidebar-item'}} flex items-center px-6 py-3.5 transition-colors">
                <i class="fas fa-undo w-6"></i> রিফান্ড রিকোয়েস্ট
            </a>
            <a href="{{ route('complaint') }}" class="{{ request()->is('complaint') ? 'active-menu' : 'sidebar-item' }} flex items-center px-6 py-3.5 transition-colors">
                <i class="fas fa-headset w-6"></i> সাপোর্ট টিকেট
            </a>
            <a href="{{route('customer.profile_edit')}}" class="{{request()->is('customer/profile-edit')?'active-menu':'sidebar-item'}} flex items-center px-6 py-3.5 transition-colors">
                <i class="fas fa-user-cog w-6"></i> সেটিংস
            </a>
        </nav>

        <div class="p-6 border-t">
            <a href="{{ route('customer.logout') }}" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="w-full flex items-center justify-center px-4 py-2.5 text-red-500 bg-red-50 hover:bg-red-100 rounded-lg font-bold transition">
                <i class="fas fa-sign-out-alt mr-2"></i> লগআউট
            </a>
            <form id="logout-form" action="{{ route('customer.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto h-screen w-full">
        
        <header class="bg-white px-6 lg:px-8 py-4 flex justify-between items-center sticky top-0 z-20 shadow-sm border-b">
            <div class="lg:hidden mr-4">
                <button onclick="toggleSidebar()" class="text-gray-600 text-xl p-2"><i class="fas fa-bars"></i></button>
            </div>

            <div class="flex-1">
                <h2 class="text-xl font-bold text-gray-800">স্বাগতম, {{ $customer->name }}! 👋</h2>
                <p class="text-xs text-gray-400 mt-0.5 hidden sm:block">আপনার কেনাকাটার সংক্ষিপ্ত বিবরণ</p>
            </div>

            <div class="flex items-center gap-4">
                <div class="hidden sm:flex bg-green-50 text-green-700 px-4 py-2 rounded-full items-center font-bold text-sm border border-green-100">
                    <i class="fas fa-wallet mr-2"></i> মোট: ৳{{ number_format($totalOrderAmount, 0) }}
                </div>
                
                <div class="relative cursor-pointer w-10 h-10 bg-gray-50 rounded-full flex items-center justify-center hover:bg-gray-100 transition">
                    <i class="far fa-bell text-gray-600"></i>
                </div>

                <img src="{{ $profileImage }}" onerror="this.src='{{ asset('public/uploads/default/no-image.png') }}'" class="w-10 h-10 rounded-full border-2 border-white shadow-sm cursor-pointer" alt="Profile">
            </div>
        </header>

        <div class="p-3 sm:p-4 lg:p-8 max-w-7xl mx-auto space-y-6 sm:space-y-8">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center group hover:shadow-md transition">
                    <div>
                        <p class="text-gray-400 text-xs font-medium uppercase tracking-wider">মোট অর্ডার</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalOrders }} টি</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl group-hover:scale-110 transition">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center group hover:shadow-md transition">
                    <div>
                        <p class="text-gray-400 text-xs font-medium uppercase tracking-wider">চলমান অর্ডার</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $pendingOrders }} টি</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-50 text-orange-500 rounded-xl flex items-center justify-center text-xl group-hover:scale-110 transition">
                        <i class="fas fa-truck-moving"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center group hover:shadow-md transition">
                    <div>
                        <p class="text-gray-400 text-xs font-medium uppercase tracking-wider">কমপ্লিট অর্ডার</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $completedOrders }} টি</p>
                    </div>
                    <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center text-xl group-hover:scale-110 transition">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center group hover:shadow-md transition">
                    <div>
                        <p class="text-gray-400 text-xs font-medium uppercase tracking-wider">মোট টাকা</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">৳{{ number_format($totalOrderAmount, 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl group-hover:scale-110 transition">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">📦 সাম্প্রতিক অর্ডারসমূহ</h3>
                    <a href="{{ route('customer.orders') }}" class="text-sm text-indigo-600 font-semibold hover:underline">সবগুলো দেখুন</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left custom-table">
                        <thead>
                            <tr>
                                <th class="pl-6 py-4">অর্ডার আইডি</th>
                                <th class="py-4">তারিখ</th>
                                <th class="py-4">পণ্যের নাম</th>
                                <th class="py-4">মোট টাকা</th>
                                <th class="py-4">পেমেন্ট</th>
                                <th class="py-4">স্ট্যাটাস</th>
                                <th class="pr-6 py-4 text-right">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                @php
                                    $firstProduct = $order->orderdetails->first();
                                    $productName = $firstProduct && $firstProduct->product ? Str::limit($firstProduct->product->name, 30) : 'N/A';
                                    
                                    $payment = $order->payment;
                                    $paymentStatus = $payment ? strtolower($payment->payment_status) : 'pending';
                                    $isPaid = $paymentStatus === 'paid' || $paymentStatus === 'success';
                                    
                                    $statusClass = '';
                                    $statusText = '';
                                    
                                    if($order->order_status == '6') {
                                        $statusClass = 'bg-green-50 text-green-600';
                                        $statusText = 'Delivered';
                                    } elseif($order->order_status == '11') {
                                        $statusClass = 'bg-red-50 text-red-600';
                                        $statusText = 'Cancelled';
                                    } elseif(in_array($order->order_status, ['3', '4', '5'])) {
                                        $statusClass = 'bg-orange-50 text-orange-600';
                                        $statusText = 'Shipped';
                                    } else {
                                        $statusClass = 'bg-blue-50 text-blue-600';
                                        $statusText = 'Processing';
                                    }
                                @endphp
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="pl-6 font-bold text-indigo-600">#{{ $order->invoice_id ?? $order->id }}</td>
                                    <td class="text-gray-500">{{ $order->created_at->format('d M, Y') }}</td>
                                    <td class="font-medium text-gray-700">{{ $productName }}</td>
                                    <td class="font-bold text-gray-800">৳{{ number_format($order->amount, 0) }}</td>
                                    <td>
                                        @if($isPaid)
                                            <span class="bg-green-50 text-green-600 px-2.5 py-1 rounded text-xs font-bold">Paid</span>
                                        @else
                                            <span class="bg-red-50 text-red-600 px-2.5 py-1 rounded text-xs font-bold">Unpaid</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="{{ $statusClass }} px-2.5 py-1 rounded text-xs font-bold">{{ $statusText }}</span>
                                    </td>
                                    <td class="pr-6 text-right">
                                        <a href="{{ route('customer.invoice', ['id' => $order->id]) }}" class="text-gray-400 hover:text-indigo-600"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-8 text-gray-500">কোনো অর্ডার পাওয়া যায়নি</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($recommendedProducts->count() > 0)
            <div>
                <div class="flex justify-between items-center mb-5 px-2 sm:px-0">
                    <h3 class="text-lg font-bold text-gray-800">🔥 আপনার জন্য সেরা (Recommended)</h3>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-5">
                    @foreach($recommendedProducts as $product)
                        @php
                            $discount = 0;
                            if($product->old_price && $product->new_price && $product->old_price > $product->new_price) {
                                $discount = round((($product->old_price - $product->new_price) / $product->old_price) * 100);
                            }
                        @endphp
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group hover:shadow-lg transition-all duration-300 product-card-hover">
                            <div class="product-image-container relative">
                                <a href="{{ route('product', $product->slug ?? $product->id) }}" class="block w-full h-full">
                                    <img src="{{ asset($product->image->image ?? 'public/uploads/default/no-image.png') }}" onerror="this.src='{{ asset('public/uploads/default/no-image.png') }}'" class="group-hover:scale-105 transition duration-500" alt="{{ $product->name }}">
                                </a>
                                @if($discount > 0)
                                    <span class="absolute top-1.5 left-1.5 sm:top-2 sm:left-2 bg-indigo-600 text-white text-[9px] sm:text-xs px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full shadow-md font-bold z-10">{{ $discount }}% OFF</span>
                                @elseif($product->feature_product)
                                    <span class="absolute top-1.5 left-1.5 sm:top-2 sm:left-2 bg-green-500 text-white text-[9px] sm:text-xs px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full shadow-md font-bold z-10">New</span>
                                @endif
                                @if($product->stock <= 0)
                                    <span class="absolute top-1.5 right-1.5 sm:top-2 sm:right-2 bg-red-500 text-white text-[9px] sm:text-xs px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full shadow-md font-bold z-10">স্টকে নেই</span>
                                @endif
                            </div>
                            <div class="p-3 sm:p-4">
                                <h4 class="font-bold text-gray-800 text-xs sm:text-sm mb-2 line-clamp-2 min-h-[2rem] sm:min-h-[2.5rem]">
                                    <a href="{{ route('product', $product->slug ?? $product->id) }}" class="hover:text-indigo-600 transition">{{ $product->name }}</a>
                                </h4>
                                
                                <div class="flex items-center gap-1 sm:gap-2 mb-2 sm:mb-3 flex-wrap">
                                    @if($product->old_price && $product->old_price > $product->new_price)
                                        <span class="text-gray-400 line-through text-[10px] sm:text-xs">৳{{ number_format($product->old_price, 0) }}</span>
                                    @endif
                                    <span class="text-indigo-600 font-bold text-base sm:text-lg">৳{{ number_format($product->new_price ?? 0, 0) }}</span>
                                </div>
                                
                                @if($product->stock > 0)
                                <div class="flex flex-col sm:flex-row gap-2">
                                    <a href="{{ route('product', $product->slug ?? $product->id) }}" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-xs sm:text-sm font-semibold py-2 sm:py-2.5 px-3 sm:px-4 rounded-lg text-center transition duration-200 flex items-center justify-center gap-1 sm:gap-2 shadow-sm hover:shadow-md">
                                        <i class="fas fa-shopping-cart text-[10px] sm:text-xs"></i>
                                        <span class="whitespace-nowrap">অর্ডার করুন</span>
                                    </a>
                                    <button onclick="addToCart({{ $product->id }})" class="w-full sm:w-auto bg-gray-100 hover:bg-indigo-600 hover:text-white text-gray-600 sm:w-11 h-9 sm:h-11 rounded-lg flex items-center justify-center transition duration-200 border border-gray-200 hover:border-indigo-600" title="কার্টে যোগ করুন">
                                        <i class="fas fa-cart-plus text-xs sm:text-sm"></i>
                                    </button>
                                </div>
                                @else
                                <div class="w-full bg-gray-100 text-gray-500 text-xs sm:text-sm font-semibold py-2 sm:py-2.5 px-3 sm:px-4 rounded-lg text-center">
                                    <i class="fas fa-ban mr-1 sm:mr-2"></i>স্টকে নেই
                                </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }

        function addToCart(productId) {
            // Add to cart functionality
            window.location.href = '{{ url("add-to-cart") }}/' + productId + '/1';
        }
        
        function orderNow(productId) {
            // Direct order functionality - redirect to product details page
            window.location.href = '{{ url("product") }}/' + productId;
        }
    </script>
</body>
</html>
