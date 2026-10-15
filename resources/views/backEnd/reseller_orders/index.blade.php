@extends('backEnd.layouts.master')
@section('title', 'রিসেলার অর্ডার')

@section('css')
@include('backEnd.reseller_orders.partials.index_styles')
@endsection

@section('content')
<div class="container-fluid reseller-orders-shell reseller-orders-page">

    <div class="ro-page-header">
        <div>
            <h4>রিসেলার অর্ডার <span class="ro-badge-count">{{ $orders->total() }}</span></h4>
            <div class="ro-sub">অসম্পূর্ণ / পেন্ডিং রিসেলার অর্ডার — সম্পন্ন অর্ডার মূল অর্ডার তালিকায় দেখা যাবে</div>
            <div class="ro-info-pill">
                <i class="fas fa-info-circle"></i>
                শুধুমাত্র অসম্পন্ন অর্ডার এখানে দেখানো হয়
            </div>
        </div>
    </div>

    <div class="ro-card">
        <div class="ro-card-head">
            <h6><i class="fas fa-filter"></i> ফিল্টার ও অনুসন্ধান</h6>
        </div>
        <div class="ro-card-body">
            <form method="GET" action="{{ route('admin.reseller-orders.index') }}" class="ro-filter-grid">
                <div>
                    <label class="ro-label">খুঁজুন</label>
                    <input type="text" name="search" class="form-control ro-input"
                        placeholder="ইনভয়েস, গ্রাহক, রিসেলার..." value="{{ request('search') }}">
                </div>
                <div>
                    <label class="ro-label">স্ট্যাটাস</label>
                    <select name="status" class="form-select ro-select">
                        <option value="">সব স্ট্যাটাস</option>
                        @foreach($orderStatuses as $status)
                            <option value="{{ $status->id }}" {{ request('status') == $status->id ? 'selected' : '' }}>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="ro-label">রিসেলার</label>
                    <select name="reseller_id" class="form-select ro-select">
                        <option value="">সব রিসেলার</option>
                        @foreach($resellers as $reseller)
                            <option value="{{ $reseller->id }}" {{ request('reseller_id') == $reseller->id ? 'selected' : '' }}>
                                {{ $reseller->name }} ({{ $reseller->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="ro-filter-actions">
                    <button type="submit" class="btn ro-btn-primary">
                        <i class="fas fa-search me-1"></i> ফিল্টার
                    </button>
                    <a href="{{ route('admin.reseller-orders.index') }}" class="ro-btn-ghost">রিসেট</a>
                </div>
            </form>
        </div>
    </div>

    <div class="ro-card">
        <div class="ro-card-head">
            <h6><i class="fas fa-list-alt"></i> অর্ডার তালিকা</h6>
        </div>
        <div class="ro-card-body">

            <form id="bulkStatusForm" method="POST" action="{{ route('admin.reseller-orders.bulk-update-status') }}">
                @csrf
                <div class="ro-bulk-bar">
                    <div>
                        <label class="ro-label mb-1">বাল্ক স্ট্যাটাস</label>
                        <select name="order_status" class="form-select ro-select" required>
                            <option value="">স্ট্যাটাস বেছে নিন</option>
                            @foreach($orderStatuses as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn ro-btn-bulk" id="bulkUpdateBtn" disabled>
                        <i class="fas fa-flag me-1"></i> সিলেক্টেড আপডেট
                    </button>
                    <span class="ro-selected-count" id="selectedCount">০টি সিলেক্ট</span>
                    <input type="hidden" name="order_ids" id="selectedOrderIds">
                </div>

                <p class="d-lg-none ro-scroll-hint"><i class="fas fa-arrows-alt-h me-1"></i> বাকি কলাম দেখতে বাম–ডানে স্লাইড করুন</p>

                <div class="ro-table-rail">
                    <table class="table ro-table mb-0">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll" class="form-check-input" aria-label="সব সিলেক্ট"></th>
                                <th>#</th>
                                <th>ইনভয়েস</th>
                                <th>তারিখ</th>
                                <th>রিসেলার</th>
                                <th>গ্রাহক</th>
                                <th>পণ্য</th>
                                <th>পরিমাণ</th>
                                <th>লাভ</th>
                                <th>স্ট্যাটাস</th>
                                <th>অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input order-checkbox" value="{{ $order->id }}">
                                </td>
                                <td>{{ $loop->iteration + ($orders->currentPage() - 1) * $orders->perPage() }}</td>
                                <td>
                                    <a href="{{ route('admin.order.process', ['invoice_id' => $order->invoice_id]) }}" class="ro-invoice-link">
                                        #{{ $order->invoice_id }}
                                    </a>
                                </td>
                                <td>
                                    {{ $order->created_at->format('d M, Y') }}<br>
                                    <span class="ro-meta-sub">{{ $order->created_at->format('h:i A') }}</span>
                                </td>
                                <td>
                                    @if($order->user)
                                        <span class="ro-meta-name">{{ $order->user->name }}</span><br>
                                        <span class="ro-meta-sub">{{ $order->user->email }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $custName = $order->shipping->name ?? $order->customer->name ?? '—';
                                        $custPhone = $order->shipping->phone ?? $order->customer->phone ?? '—';
                                    @endphp
                                    <span class="ro-meta-name">{{ $custName }}</span><br>
                                    <span class="ro-meta-sub">{{ $custPhone }}</span>
                                </td>
                                <td>
                                    @if($order->orderdetails && $order->orderdetails->count() > 0)
                                        <div class="ro-product-thumbs">
                                            @foreach($order->orderdetails->take(3) as $detail)
                                                @php
                                                    $productImage = null;
                                                    if ($detail->product && $detail->product->image) {
                                                        $productImage = $detail->product->image->image;
                                                    } elseif ($detail->image) {
                                                        $productImage = $detail->image->image;
                                                    }
                                                @endphp
                                                @if($productImage)
                                                    <img src="{{ asset($productImage) }}" alt="" class="ro-product-thumb"
                                                        title="{{ $detail->product_name }} (×{{ $detail->qty }})">
                                                @else
                                                    <span class="ro-product-more"><i class="fas fa-box"></i></span>
                                                @endif
                                            @endforeach
                                            @if($order->orderdetails->count() > 3)
                                                <span class="ro-product-more">+{{ $order->orderdetails->count() - 3 }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="ro-amount">৳{{ number_format($order->customer_payable_amount ?? $order->amount, 0) }}</span>
                                </td>
                                <td>
                                    <span class="ro-profit-badge">৳{{ number_format($order->reseller_profit ?? 0, 0) }}</span>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.reseller-orders.update-status') }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                        <select name="order_status" class="form-select ro-status-select" onchange="this.form.submit()">
                                            @foreach($orderStatuses as $status)
                                                <option value="{{ $status->id }}" {{ $order->order_status == $status->id ? 'selected' : '' }}>
                                                    {{ $status->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <div class="ro-row-actions">
                                        <a href="{{ route('admin.order.invoice', ['invoice_id' => $order->invoice_id]) }}"
                                           class="ro-act-btn ro-act-view" title="ইনভয়েস" target="_blank">
                                            <i class="fas fa-file-invoice"></i>
                                        </a>
                                        <a href="{{ route('admin.order.process', ['invoice_id' => $order->invoice_id]) }}"
                                           class="ro-act-btn ro-act-process" title="প্রসেস">
                                            <i class="fas fa-cog"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11">
                                    <div class="ro-empty">
                                        <i class="fas fa-inbox"></i>
                                        <p class="mb-0">কোনো রিসেলার অর্ডার পাওয়া যায়নি</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($orders->hasPages())
                <div class="ro-paginate">
                    {{ $orders->withQueryString()->links('pagination::bootstrap-4') }}
                </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
(function () {
    var selectAll = document.getElementById('selectAll');
    var bulkBtn = document.getElementById('bulkUpdateBtn');
    var countEl = document.getElementById('selectedCount');
    var idsEl = document.getElementById('selectedOrderIds');
    var bulkForm = document.getElementById('bulkStatusForm');

    if (!selectAll || !bulkForm) return;

    function updateSelectedOrders() {
        var selected = Array.from(document.querySelectorAll('.order-checkbox:checked')).map(function (cb) {
            return cb.value;
        });
        var count = selected.length;
        if (countEl) countEl.textContent = count + 'টি সিলেক্ট';
        if (idsEl) idsEl.value = JSON.stringify(selected);
        if (bulkBtn) bulkBtn.disabled = count === 0;
    }

    selectAll.addEventListener('change', function () {
        document.querySelectorAll('.order-checkbox').forEach(function (checkbox) {
            checkbox.checked = selectAll.checked;
        });
        updateSelectedOrders();
    });

    document.querySelectorAll('.order-checkbox').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            var all = document.querySelectorAll('.order-checkbox');
            var checked = document.querySelectorAll('.order-checkbox:checked');
            selectAll.checked = all.length > 0 && checked.length === all.length;
            updateSelectedOrders();
        });
    });

    bulkForm.addEventListener('submit', function (e) {
        var selected = JSON.parse(idsEl.value || '[]');
        if (selected.length === 0) {
            e.preventDefault();
            alert('অন্তত একটি অর্ডার সিলেক্ট করুন');
            return false;
        }
        bulkForm.querySelectorAll('input[name="order_ids[]"]').forEach(function (el) { el.remove(); });
        selected.forEach(function (id) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'order_ids[]';
            input.value = id;
            bulkForm.appendChild(input);
        });
    });

    updateSelectedOrders();
})();
</script>
@endsection
