@php
    use Illuminate\Support\Str;
    $customer = Auth::guard('customer')->user();
    $pendingOrdersCount = \App\Models\Order::where('customer_id', $customer->id)
        ->whereNotIn('order_status', ['6', '11'])
        ->count();
    
    // Get dark logo
    $logo = null;
    if(isset($generalsetting) && $generalsetting && !empty($generalsetting->dark_logo)) {
        $logo = $generalsetting->dark_logo;
    } else {
        $gs = \App\Models\GeneralSetting::where('status', 1)->first();
        if($gs && !empty($gs->dark_logo)) {
            $logo = $gs->dark_logo;
        }
    }
@endphp

<aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r transform -translate-x-full lg:translate-x-0 lg:static lg:inset-auto lg:flex flex-col shrink-0 h-screen transition-transform duration-300">
    <div class="p-4 sm:p-6 flex items-center justify-between lg:justify-start gap-2 border-b border-gray-100">
        @if($logo)
            <a href="{{ route('home') }}" class="flex items-center gap-2 flex-1">
                <img src="{{ asset($logo) }}" alt="{{ $generalsetting->name ?? 'Logo' }}" class="h-8 sm:h-10 w-auto max-w-full object-contain">
            </a>
        @else
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr($generalsetting->name ?? 'G', 0, 1)) }}
                </div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800 tracking-tight">
                    {{ Str::limit($generalsetting->name ?? 'GadgetShop', 8) }}
                </h1>
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