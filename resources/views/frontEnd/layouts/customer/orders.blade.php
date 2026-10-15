@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

$customer = Auth::guard('customer')->user();
$customerId = $customer->id;

// Site Name & Logo
$siteName = \App\Models\GeneralSetting::first();
$siteInitial = strtoupper(substr($siteName->name ?? 'G', 0, 1));
$siteDisplayName = Str::limit($siteName->name ?? 'GadgetShop', 8);
$generalsetting = $siteName;
$darkLogo = $siteName->dark_logo ?? null;

// Pending Orders Count for Badge
$pendingOrdersCount = \App\Models\Order::where('customer_id', $customerId)
    ->whereNotIn('order_status', ['6', '11'])
    ->count();

// Profile Image - Use direct image path
$profileImage = $customer->image ? asset($customer->image) : asset('public/uploads/default/no-image.png');

// Total Order Amount
$totalOrderAmount = \App\Models\Order::where('customer_id', $customerId)->sum('amount');
@endphp

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Order History | {{ $siteName->name ?? 'Gadget Style' }}</title>
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
                <h2 class="text-xl font-bold text-gray-800">আমার অর্ডার</h2>
                <p class="text-xs text-gray-400 mt-0.5 hidden sm:block">আপনার অর্ডার ইতিহাস</p>
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

        <div class="p-4 lg:p-8 max-w-7xl mx-auto">
            
            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500 uppercase mb-1">মোট অর্ডার</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $orders->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shopping-bag text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500 uppercase mb-1">চলমান</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $pendingOrdersCount }}</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-50 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-orange-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500 uppercase mb-1">মোট টাকা</p>
                            <p class="text-2xl font-bold text-gray-800">৳{{ number_format($totalOrderAmount, 0) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                            <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Orders List --}}
            <div class="space-y-4">
                @forelse($orders as $value)
                                @php
                                    // Payment Logic
                                    $payment_record = \App\Models\Payment::where('order_id', $value->id)->orderBy('id', 'desc')->first();
                                    
                                    $gateway_status = $payment_record ? strtolower(trim($payment_record->payment_status)) : '';
                                    $payment_method = $payment_record ? strtolower(trim($payment_record->payment_method)) : '';
                                    
                                    $admin_status   = strtolower(trim($value->payment_status ?? ''));
                                    $order_status   = strtolower(trim($value->status->slug ?? $value->status->name ?? ''));

                                    $grand_total = $value->amount;
                                    $paid_amount = 0;

                                    // Payment calculation logic
                                    if ($payment_record && !in_array($gateway_status, ['failed', 'cancel', 'cancelled', 'rejected'])) {
                                        $paid_amount = $payment_record->amount;
                                    }

                                    $is_cod = in_array($payment_method, ['cod', 'cash', 'cash_on_delivery']);
                                    $is_order_completed = in_array($order_status, ['completed', 'delivered']) || in_array($admin_status, ['completed', 'delivered']);

                                    if ($is_cod && !$is_order_completed) {
                                        if ($paid_amount >= $grand_total) {
                                            $paid_amount = 0;
                                        }
                                    }

                                    if ($is_order_completed) {
                                        $paid_amount = $grand_total;
                                    } 
                                    elseif (($paid_amount == 0 || !$payment_record) && in_array($admin_status, ['paid', 'success', 'approved'])) {
                                        $paid_amount = $grand_total;
                                    }

                                    $due_amount = max(0, $grand_total - $paid_amount);

                                    $is_failed = false;
                                    if ($paid_amount == 0 && in_array($gateway_status, ['failed', 'cancel', 'cancelled'])) {
                                        $is_failed = true;
                                    }

                                    $show_download = ($paid_amount >= $grand_total) || ($paid_amount > 0 && !$is_failed);

                                    $digitalDownloads = \App\Models\DigitalDownload::where('order_id', $value->id)->get();
                                    $hasDigitalProduct = $digitalDownloads->count() > 0;

                                    // Order Status Badge
                                    $statusClass = '';
                                    $statusText = $value->status ? $value->status->name : 'Pending';
                                    
                                    if($value->order_status == '6') {
                                        $statusClass = 'bg-green-50 text-green-600';
                                        $statusText = 'Completed';
                                    } elseif($value->order_status == '11') {
                                        $statusClass = 'bg-red-50 text-red-600';
                                        $statusText = 'Cancelled';
                                    } elseif(in_array($value->order_status, ['3', '4', '5'])) {
                                        $statusClass = 'bg-orange-50 text-orange-600';
                                        $statusText = 'Shipped';
                                    } else {
                                        $statusClass = 'bg-gray-50 text-gray-600';
                                        $statusText = 'Pending';
                                    }

                                    // Refund Logic
                                    $canRefund = false;
                                    $hasPendingRefund = method_exists($value, 'hasPendingRefund') ? $value->hasPendingRefund() : false;
                                    
                                    if ($value->order_status != 11 && $paid_amount > 0 && !$hasPendingRefund) {
                                        $canRefund = true;
                                    }
                                    
                                    $existingRefund = \App\Models\Refund::where('order_id', $value->id)
                                        ->whereIn('status', ['pending', 'approved'])
                                        ->first();
                                @endphp

                                {{-- Order Card - Simplified --}}
                                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                                    <div class="p-4">
                                        {{-- Header Row --}}
                                        <div class="flex items-start justify-between gap-4 mb-4">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <h4 class="text-base font-bold text-gray-800">#{{ $value->invoice_id ?? $value->id }}</h4>
                                                    <span class="{{ $statusClass }} px-2 py-0.5 rounded text-xs font-semibold">{{ $statusText }}</span>
                                                </div>
                                                <p class="text-xs text-gray-500">{{ $value->created_at->format('d M, Y - h:i A') }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-lg font-bold text-gray-800">৳{{ number_format($grand_total, 2) }}</p>
                                            </div>
                                        </div>

                                        {{-- Product Images --}}
                                        @if($value->orderdetails && $value->orderdetails->count() > 0)
                                            <div class="flex gap-2 mb-4 pb-4 border-b border-gray-100">
                                                @foreach($value->orderdetails->take(5) as $detail)
                                                    @php
                                                        $productImage = null;
                                                        if ($detail->product && $detail->product->image) {
                                                            $productImage = $detail->product->image->image;
                                                        } elseif ($detail->image) {
                                                            $productImage = $detail->image->image;
                                                        }
                                                    @endphp
                                                    <img src="{{ $productImage ? asset($productImage) : asset('public/uploads/default/no-image.png') }}" 
                                                         onerror="this.src='{{ asset('public/uploads/default/no-image.png') }}'"
                                                         class="w-12 h-12 rounded object-cover border border-gray-200"
                                                         alt="{{ $detail->product_name ?? 'Product' }}">
                                                @endforeach
                                                @if($value->orderdetails->count() > 5)
                                                    <div class="w-12 h-12 rounded bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-500 text-xs font-bold">
                                                        +{{ $value->orderdetails->count() - 5 }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        {{-- Payment & Actions Row --}}
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                            {{-- Payment Status --}}
                                            <div class="flex items-center gap-3 text-sm">
                                                <div>
                                                    <span class="text-gray-500">Paid: </span>
                                                    <span class="font-semibold {{ $paid_amount > 0 ? 'text-green-600' : 'text-gray-400' }}">৳{{ number_format($paid_amount, 2) }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-500">Due: </span>
                                                    <span class="font-semibold {{ $due_amount > 0 ? 'text-red-600' : 'text-gray-400' }}">৳{{ number_format($due_amount, 2) }}</span>
                                                </div>
                                                <div>
                                                    @if($paid_amount >= $grand_total)
                                                        <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-semibold">Paid</span>
                                                    @elseif($is_failed)
                                                        <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded text-xs font-semibold">Failed</span>
                                                    @elseif($paid_amount > 0)
                                                        <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-semibold">Partial</span>
                                                    @else
                                                        @if($is_cod)
                                                            <span class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded text-xs font-semibold">COD</span>
                                                        @else
                                                            <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded text-xs font-semibold">Unpaid</span>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Action Buttons --}}
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('customer.invoice',['id'=>$value->id]) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs px-4 py-2 rounded-lg transition font-semibold flex items-center gap-1">
                                                    <i class="fas fa-eye"></i>
                                                    <span class="hidden sm:inline">দেখুন</span>
                                                </a>
                                                @if($value->admin_note)
                                                    <a href="{{ route('customer.order_note',['id'=>$value->id]) }}" class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-2 rounded-lg transition" title="Admin Note">
                                                        <i class="fas fa-sticky-note"></i>
                                                    </a>
                                                @endif
                                                @if($hasDigitalProduct && $show_download)
                                                    @foreach($digitalDownloads as $dl)
                                                        <a href="{{ route('digital.download', $dl->token) }}" 
                                                           class="bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-2 rounded-lg transition" target="_blank" title="Download">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    @endforeach
                                                @endif
                                                @if($canRefund)
                                                    <a href="{{ route('customer.refunds.create', $value->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white text-xs px-3 py-2 rounded-lg transition" title="Request Refund">
                                                        <i class="fas fa-undo"></i>
                                                    </a>
                                                @elseif($existingRefund)
                                                    <a href="{{ route('customer.refunds.show', $existingRefund->id) }}" class="bg-purple-500 hover:bg-purple-600 text-white text-xs px-3 py-2 rounded-lg transition" title="View Refund">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                                        <i class="fas fa-box-open text-4xl text-gray-300"></i>
                                    </div>
                                    <h5 class="text-lg font-bold text-gray-800 mb-2">কোনো অর্ডার পাওয়া যায়নি</h5>
                                    <p class="text-gray-500">আপনি এখনো কোনো অর্ডার করেননি।</p>
                                </div>
                            @endforelse
            </div>

            {{-- Pagination --}}
            @if(method_exists($orders, 'hasPages') && $orders->hasPages())
                <div class="mt-6 flex justify-center">
                    <div class="flex items-center gap-2 flex-wrap justify-center">
                        {{-- Previous Page Link --}}
                        @if ($orders->onFirstPage())
                            <span class="px-4 py-2 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed">
                                <i class="fas fa-chevron-left"></i>
                            </span>
                        @else
                            <a href="{{ $orders->previousPageUrl() }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        @endif

                        {{-- Pagination Elements --}}
                        @php
                            $currentPage = $orders->currentPage();
                            $lastPage = $orders->lastPage();
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($lastPage, $currentPage + 2);
                        @endphp

                        @if($startPage > 1)
                            <a href="{{ $orders->url(1) }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">1</a>
                            @if($startPage > 2)
                                <span class="px-2 text-gray-400">...</span>
                            @endif
                        @endif

                        @for($page = $startPage; $page <= $endPage; $page++)
                            @if ($page == $currentPage)
                                <span class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-semibold">{{ $page }}</span>
                            @else
                                <a href="{{ $orders->url($page) }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">{{ $page }}</a>
                            @endif
                        @endfor

                        @if($endPage < $lastPage)
                            @if($endPage < $lastPage - 1)
                                <span class="px-2 text-gray-400">...</span>
                            @endif
                            <a href="{{ $orders->url($lastPage) }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">{{ $lastPage }}</a>
                        @endif

                        {{-- Next Page Link --}}
                        @if ($orders->hasMorePages())
                            <a href="{{ $orders->nextPageUrl() }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        @else
                            <span class="px-4 py-2 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed">
                                <i class="fas fa-chevron-right"></i>
                            </span>
                        @endif
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
    </script>
</body>
</html>
