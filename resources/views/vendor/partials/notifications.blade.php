@php
    $user = Auth::guard('admin')->user();
    $vendorId = $user->vendor_id ?? null;
    
    // Get order IDs for this vendor
    $orderIds = [];
    if ($vendorId) {
        $orderIds = \App\Models\OrderDetails::whereIn('product_id', function($query) use ($vendorId) {
                $query->select('id')
                      ->from('products')
                      ->where('vendor_id', $vendorId);
            })
            ->distinct()
            ->pluck('order_id')
            ->toArray();
    }
    
    // New Orders (Last 24 hours, pending/processing)
    $newOrders = $vendorId ? \App\Models\Order::whereIn('id', $orderIds)
        ->whereIn('order_status', ['1', '2', '3'])
        ->where('created_at', '>=', \Carbon\Carbon::now()->subDay())
        ->count() : 0;
    
    // Pending Withdrawals
    $pendingWithdrawals = $vendorId ? \App\Models\VendorWithdrawal::where('vendor_id', $vendorId)
        ->where('status', 'pending')
        ->count() : 0;
    
    // Pending Refunds
    $pendingRefunds = $vendorId ? \App\Models\Refund::where('vendor_id', $vendorId)
        ->where('status', 'pending')
        ->count() : 0;
    
    $totalNotifications = $newOrders + $pendingWithdrawals + $pendingRefunds;
    
    // Get recent notifications
    $recentOrders = $vendorId ? \App\Models\Order::whereIn('id', $orderIds)
        ->whereIn('order_status', ['1', '2', '3'])
        ->with('customer')
        ->latest()
        ->limit(3)
        ->get() : collect();
    
    $recentWithdrawals = $vendorId ? \App\Models\VendorWithdrawal::where('vendor_id', $vendorId)
        ->where('status', 'pending')
        ->latest()
        ->limit(2)
        ->get() : collect();
    
    $recentRefunds = $vendorId ? \App\Models\Refund::where('vendor_id', $vendorId)
        ->where('status', 'pending')
        ->with('order', 'customer')
        ->latest()
        ->limit(2)
        ->get() : collect();
@endphp

<!-- Header -->
<li>
    <div class="px-3 py-2 border-bottom">
        <h6 class="mb-1 fw-bold text-dark">
            <i class="fas fa-bell text-primary me-2"></i>
            নোটিফিকেশন
            @if($totalNotifications > 0)
                <span class="badge bg-danger ms-2">{{ $totalNotifications }}</span>
            @endif
        </h6>
    </div>
</li>

<!-- New Orders -->
@if($newOrders > 0)
<li>
    <a class="dropdown-item py-3" href="{{ route('vendor.orders') }}">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="fas fa-shopping-cart text-primary"></i>
                </div>
            </div>
            <div class="flex-grow-1 ms-3">
                <p class="mb-0 fw-semibold text-dark">নতুন অর্ডার</p>
                <small class="text-muted">
                    {{ $newOrders }} টি নতুন অর্ডার পেন্ডিং আছে
                </small>
            </div>
            <div class="flex-shrink-0">
                <span class="badge bg-danger rounded-pill">{{ $newOrders }}</span>
            </div>
        </div>
    </a>
</li>
@endif

<!-- Recent Orders -->
@foreach($recentOrders as $order)
<li>
    <a class="dropdown-item py-2" href="{{ route('vendor.orders') }}">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                    <i class="fas fa-box text-info"></i>
                </div>
            </div>
            <div class="flex-grow-1 ms-2">
                <p class="mb-0 small fw-semibold">Order #{{ $order->invoice_id ?? $order->id }}</p>
                <small class="text-muted">{{ $order->customer->name ?? 'Guest' }} - ৳{{ number_format($order->amount, 0) }}</small>
            </div>
            <div class="flex-shrink-0">
                <small class="text-muted">{{ $order->created_at->diffForHumans() }}</small>
            </div>
        </div>
    </a>
</li>
@endforeach

<!-- Pending Withdrawals -->
@if($pendingWithdrawals > 0)
<li>
    <hr class="dropdown-divider my-2">
</li>
<li>
    <a class="dropdown-item py-3" href="{{ route('vendor.withdrawals.index') }}">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="fas fa-wallet text-warning"></i>
                </div>
            </div>
            <div class="flex-grow-1 ms-3">
                <p class="mb-0 fw-semibold text-dark">উইথড্রল রিকোয়েস্ট</p>
                <small class="text-muted">
                    {{ $pendingWithdrawals }} টি উইথড্রল পেন্ডিং আছে
                </small>
            </div>
            <div class="flex-shrink-0">
                <span class="badge bg-warning rounded-pill">{{ $pendingWithdrawals }}</span>
            </div>
        </div>
    </a>
</li>
@endif

@foreach($recentWithdrawals as $withdrawal)
<li>
    <a class="dropdown-item py-2" href="{{ route('vendor.withdrawals.index') }}">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                    <i class="fas fa-money-bill-wave text-warning"></i>
                </div>
            </div>
            <div class="flex-grow-1 ms-2">
                <p class="mb-0 small fw-semibold">৳{{ number_format($withdrawal->amount, 0) }} উইথড্রল</p>
                <small class="text-muted">{{ $withdrawal->payout_method ?? 'Manual' }}</small>
            </div>
            <div class="flex-shrink-0">
                <small class="text-muted">{{ $withdrawal->created_at->diffForHumans() }}</small>
            </div>
        </div>
    </a>
</li>
@endforeach

<!-- Pending Refunds -->
@if($pendingRefunds > 0)
<li>
    <hr class="dropdown-divider my-2">
</li>
<li>
    <a class="dropdown-item py-3" href="{{ route('vendor.refunds.index') }}">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                <div class="rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="fas fa-undo text-danger"></i>
                </div>
            </div>
            <div class="flex-grow-1 ms-3">
                <p class="mb-0 fw-semibold text-dark">রিফান্ড রিকোয়েস্ট</p>
                <small class="text-muted">
                    {{ $pendingRefunds }} টি রিফান্ড পেন্ডিং আছে
                </small>
            </div>
            <div class="flex-shrink-0">
                <span class="badge bg-danger rounded-pill">{{ $pendingRefunds }}</span>
            </div>
        </div>
    </a>
</li>
@endif

@foreach($recentRefunds as $refund)
<li>
    <a class="dropdown-item py-2" href="{{ route('vendor.refunds.show', $refund->id) }}">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                <div class="rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                    <i class="fas fa-undo-alt text-danger"></i>
                </div>
            </div>
            <div class="flex-grow-1 ms-2">
                <p class="mb-0 small fw-semibold">Order #{{ $refund->order->invoice_id ?? 'N/A' }}</p>
                <small class="text-muted">{{ $refund->customer->name ?? 'Guest' }} - ৳{{ number_format($refund->amount ?? 0, 0) }}</small>
            </div>
            <div class="flex-shrink-0">
                <small class="text-muted">{{ $refund->created_at->diffForHumans() }}</small>
            </div>
        </div>
    </a>
</li>
@endforeach

<!-- Verification Status -->
@if($vendor->verification_status != 'approved')
<li>
    <hr class="dropdown-divider my-2">
</li>
<li>
    <a class="dropdown-item py-3" href="{{ route('vendor.verification.index') }}">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                @if($vendor->verification_status == 'rejected')
                    <div class="rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-times-circle text-danger"></i>
                    </div>
                @else
                    <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-clock text-warning"></i>
                    </div>
                @endif
            </div>
            <div class="flex-grow-1 ms-3">
                <p class="mb-0 fw-semibold">
                    @if($vendor->verification_status == 'rejected')
                        <span class="text-danger">ভেরিফিকেশন রিজেক্ট</span>
                    @else
                        <span class="text-warning">ভেরিফিকেশন পেন্ডিং</span>
                    @endif
                </p>
                <small class="text-muted">
                    @if($vendor->verification_status == 'rejected')
                        @if($vendor->verification_note)
                            {{ \Illuminate\Support\Str::limit($vendor->verification_note, 40) }}
                        @else
                            ভেরিফিকেশন রিজেক্ট হয়েছে
                        @endif
                    @else
                        ভেরিফিকেশন সম্পন্ন করুন
                    @endif
                </small>
            </div>
            <div class="flex-shrink-0">
                <i class="fas fa-chevron-right text-muted"></i>
            </div>
        </div>
    </a>
</li>
@endif

<!-- Empty State -->
@if($totalNotifications == 0 && $vendor->verification_status == 'approved')
<li>
    <div class="px-3 py-4 text-center text-muted">
        <i class="fas fa-check-circle fa-2x mb-2 opacity-25"></i>
        <p class="mb-0 small">কোন নোটিফিকেশন নেই</p>
    </div>
</li>
@endif
