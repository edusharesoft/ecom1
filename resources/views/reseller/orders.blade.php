@extends('reseller.layouts.app')

@section('title', 'আমার অর্ডারসমূহ')

@push('styles')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        --success-soft: #ecfdf5;
        --success-text: #059669;
        --danger-soft: #fef2f2;
        --danger-text: #dc2626;
        --warning-soft: #fffbeb;
        --warning-text: #d97706;
        --info-soft: #eff6ff;
        --info-text: #2563eb;
        --border-color: #e2e8f0;
    }

    /* Stats Card */
    .stats-card {
        background: #fff;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 24px;
    }
    .stats-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: var(--primary-gradient);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    /* Order Card */
    .order-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        transition: all 0.3s ease;
        overflow: hidden;
        margin-bottom: 20px;
    }

    .order-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.1);
        border-color: #cbd5e1;
    }

    .order-header {
        padding: 20px;
        border-bottom: 1px solid #f1f5f9;
        background: #f8fafc;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .order-body {
        padding: 20px;
    }

    .customer-info {
        display: flex;
        gap: 15px;
        align-items: flex-start;
    }

    .customer-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: #e0e7ff;
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .product-list {
        background: #f8fafc;
        border-radius: 12px;
        padding: 15px;
        margin-top: 15px;
        border: 1px solid #f1f5f9;
    }

    .product-item {
        display: flex;
        justify-content: space-between;
        font-size: 0.9rem;
        padding: 8px 0;
        border-bottom: 1px dashed #e2e8f0;
    }
    .product-item:last-child { border-bottom: none; }
    
    .product-thumb-small {
        flex-shrink: 0;
    }

    .profit-pill {
        background: var(--success-soft);
        color: var(--success-text);
        padding: 6px 12px;
        border-radius: 30px;
        font-weight: 700;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    /* Status Badges */
    .badge-soft {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .badge-pending { background: var(--warning-soft); color: var(--warning-text); }
    .badge-confirmed { background: var(--info-soft); color: var(--info-text); }
    .badge-completed { background: var(--success-soft); color: var(--success-text); }
    .badge-cancelled { background: var(--danger-soft); color: var(--danger-text); }

    /* Action Button */
    .btn-view {
        background: white;
        border: 1px solid #e2e8f0;
        color: #64748b;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.85rem;
        transition: all 0.2s;
    }
    .btn-view:hover {
        background: #4f46e5;
        color: white;
        border-color: #4f46e5;
    }

    /* Tracking Button */
    .btn-track {
        background: #ecfdf5;
        border: 1px solid #bbf7d0;
        color: #047857;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .btn-track i {
        font-size: 0.9rem;
    }
    .btn-track:hover {
        background: #22c55e;
        color: #ffffff;
        border-color: #16a34a;
        text-decoration: none;
    }

    /* Pagination */
    .custom-pagination .page-link {
        border-radius: 8px;
        margin: 0 3px;
        border: none;
        color: #64748b;
    }
    .custom-pagination .page-item.active .page-link {
        background: #4f46e5;
        color: white;
    }
</style>
@endpush

@section('content')

    <div class="stats-card">
        <div class="stats-icon">
            <i class="fas fa-shopping-bag"></i>
        </div>
        <div>
            <h5 class="fw-bold m-0 text-dark">আমার অর্ডারসমূহ</h5>
            <small class="text-muted">মোট অর্ডার: <strong>{{ $orders->total() }}</strong> টি</small>
        </div>
        <div class="ms-auto">
             <form method="GET" class="d-flex align-items-center gap-2">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="অর্ডার আইডি বা ফোন..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
             </form>
        </div>
    </div>

    <div class="row">
        @forelse($orders as $order)
        <div class="col-12">
            <div class="order-card">
                <div class="order-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex flex-column">
                            <span class="text-secondary small fw-bold text-uppercase">অর্ডার আইডি</span>
                            <span class="fw-bold text-dark fs-5">#{{ $order->invoice_id ?? $order->id }}</span>
                        </div>
                        <span class="text-muted small">|</span>
                        <div class="d-flex flex-column">
                            <span class="text-secondary small fw-bold text-uppercase">তারিখ</span>
                            <span class="text-dark small"><i class="far fa-clock me-1"></i> {{ $order->created_at->format('d M, Y h:i A') }}</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        @php
                            $statusClass = 'badge-pending';
                            if ($order->order_status == 6) $statusClass = 'badge-completed'; // Delivered
                            elseif ($order->order_status == 5) $statusClass = 'badge-confirmed'; // Processing
                            elseif ($order->order_status == 11) $statusClass = 'badge-cancelled'; // Cancelled
                        @endphp
                        <span class="badge-soft {{ $statusClass }}">
                            {{ $order->status->name ?? 'Pending' }}
                        </span>

                        {{-- Courier Live Tracking (exact same logic as admin order index) --}}
                        @php
                            // Priority: courier_tracking_id > consignment_id (if exists for backward compatibility)
                            $trackingId  = $order->courier_tracking_id ?? ($order->consignment_id ?? null);
                            $courierType = $order->courier_type ?? null;

                            // If no courier_type but has consignment_id, assume it's steadfast (backward compatibility)
                            if (!$courierType && !empty($order->consignment_id ?? null)) {
                                $courierType = 'steadfast';
                            }
                        @endphp

                        @if(!empty($trackingId))
                            @php
                                $ct = strtolower($courierType ?? '');
                            @endphp
                            @if($ct === 'pathao')
                                <a href="https://merchant.pathao.com/public-tracking?consignment_id={{ $trackingId }}" target="_blank" rel="noopener noreferrer" class="btn-track">
                                    <i class="fas fa-location-arrow"></i>
                                    <span>লাইভ ট্র্যাকিং</span>
                                </a>
                            @elseif($ct === 'steadfast' || (!$courierType && $trackingId))
                                @php
                                    $sfSlug = isset($order->courier_tracking_code) && trim((string) $order->courier_tracking_code) !== ''
                                        ? trim((string) $order->courier_tracking_code)
                                        : trim((string) $trackingId);
                                    $sfHref = sprintf(config('services.steadfast.public_track_url_pattern'), $sfSlug);
                                @endphp
                                <a href="{{ $sfHref }}" target="_blank" rel="noopener noreferrer" class="btn-track">
                                    <i class="fas fa-location-arrow"></i>
                                    <span>লাইভ ট্র্যাকিং</span>
                                </a>
                            @elseif($ct === 'redx')
                                <a href="https://redx.com.bd/track/{{ $trackingId }}" target="_blank" rel="noopener noreferrer" class="btn-track">
                                    <i class="fas fa-location-arrow"></i>
                                    <span>লাইভ ট্র্যাকিং</span>
                                </a>
                            @endif
                        @endif
                        
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm rounded-circle shadow-sm" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v text-muted"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3 p-2">
                                <li><a class="dropdown-item rounded" href="#"><i class="fas fa-eye me-2 text-primary"></i> বিস্তারিত দেখুন</a></li>
                                <li><a class="dropdown-item rounded" href="#"><i class="fas fa-file-invoice me-2 text-secondary"></i> ইনভয়েস</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="order-body">
                    <div class="row g-4 align-items-center">
                        
                        <div class="col-md-5 border-end-md">
                            <div class="customer-info">
                                <div class="customer-avatar">
                                    {{ substr($order->customer->name ?? 'G', 0, 1) }}
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">{{ $order->customer->name ?? 'Guest Customer' }}</h6>
                                    <p class="text-muted mb-1 small"><i class="fas fa-phone-alt me-1 text-secondary"></i> {{ $order->customer->phone ?? 'N/A' }}</p>
                                    <p class="text-muted mb-0 small">
                                        <i class="fas fa-map-marker-alt me-1 text-secondary"></i> 
                                        @if($order->shipping && $order->shipping->address)
                                            {{ Str::limit($order->shipping->address, 50) }}
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 border-end-md">
                            @if($order->orderdetails && $order->orderdetails->count() > 0)
                                <div class="product-list">
                                    @foreach($order->orderdetails->take(3) as $detail)
                                        @php
                                            $productSlug = null;
                                            if ($detail->product && $detail->product->slug) {
                                                $productSlug = $detail->product->slug;
                                            }
                                            $productImage = null;
                                            // First try to get image from product relationship
                                            if ($detail->product && $detail->product->image) {
                                                $productImage = $detail->product->image->image;
                                            } 
                                            // Fallback: try to get from orderdetails image relationship
                                            elseif ($detail->image) {
                                                $productImage = $detail->image->image;
                                            }
                                        @endphp
                                        <div class="product-item d-flex align-items-center gap-2">
                                            <div class="flex-shrink-0">
                                                @if($productImage)
                                                    @if($productSlug)
                                                        <a href="{{ route('reseller.products.show', $productSlug) }}" class="text-decoration-none">
                                                            <img src="{{ asset($productImage) }}" 
                                                                 alt="{{ $detail->product_name }}" 
                                                                 class="product-thumb-small"
                                                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 10px; border: 1px solid #e2e8f0; transition: all 0.2s; cursor: pointer;"
                                                                 onmouseover="this.style.borderColor='#4f46e5'; this.style.transform='scale(1.05)'"
                                                                 onmouseout="this.style.borderColor='#e2e8f0'; this.style.transform='scale(1)'">
                                                        </a>
                                                    @else
                                                        <img src="{{ asset($productImage) }}" 
                                                             alt="{{ $detail->product_name }}" 
                                                             class="product-thumb-small"
                                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 10px; border: 1px solid #e2e8f0;">
                                                    @endif
                                                @else
                                                    <div class="product-thumb-small d-flex align-items-center justify-content-center bg-light text-muted" 
                                                         style="width: 50px; height: 50px; border-radius: 10px; border: 1px solid #e2e8f0;">
                                                        <i class="fas fa-box" style="font-size: 0.75rem;"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        @if($productSlug)
                                                            <a href="{{ route('reseller.products.show', $productSlug) }}" 
                                                               class="text-dark fw-semibold d-block text-decoration-none" 
                                                               style="font-size: 0.85rem; line-height: 1.3; transition: color 0.2s;"
                                                               onmouseover="this.style.color='#4f46e5'"
                                                               onmouseout="this.style.color='#1f2937'">
                                                                {{ Str::limit($detail->product_name, 22) }}
                                                            </a>
                                                        @else
                                                            <span class="text-dark fw-semibold d-block" style="font-size: 0.85rem; line-height: 1.3;">{{ Str::limit($detail->product_name, 22) }}</span>
                                                        @endif
                                                        <span class="text-muted small">x {{ $detail->qty }}</span>
                                                    </div>
                                                    <span class="fw-bold text-secondary" style="font-size: 0.9rem;">৳{{ number_format($detail->sale_price * $detail->qty) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if($order->orderdetails->count() > 3)
                                        <div class="text-center mt-2 pt-2 border-top">
                                            <small class="text-primary cursor-pointer">+ আরও {{ $order->orderdetails->count() - 3 }} টি পণ্য</small>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="text-center text-muted py-3">
                                    <small>কোন পণ্যের তথ্য পাওয়া যায়নি</small>
                                </div>
                            @endif
                        </div>

                        <div class="col-md-3 text-md-end">
                            <div class="mb-3">
                                <small class="text-uppercase text-secondary fw-bold" style="font-size: 10px;">কাস্টমার পেমেন্ট</small>
                                <h5 class="fw-bold text-dark mb-0">৳{{ number_format($order->customer_payable_amount ?? 0) }}</h5>
                            </div>
                            
                            <div>
                                <div class="profit-pill">
                                    <i class="fas fa-coins"></i> লাভ: ৳{{ number_format($order->reseller_profit ?? 0) }}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
                <div class="mb-3">
                    <img src="https://cdn-icons-png.flaticon.com/512/2038/2038854.png" width="80" alt="No Orders" style="opacity: 0.5;">
                </div>
                <h5 class="fw-bold text-secondary">কোন অর্ডার পাওয়া যায়নি</h5>
                <p class="text-muted">আপনি এখনও কোনো অর্ডার প্লেস করেননি।</p>
                <a href="{{ route('reseller.products.index') }}" class="btn btn-primary rounded-pill px-4 mt-2">
                    <i class="fas fa-plus me-2"></i> নতুন অর্ডার করুন
                </a>
            </div>
        </div>
        @endforelse
    </div>

   @if($orders->hasPages())
<div class="d-flex justify-content-center mt-5 mb-4">
    <style>
        /* ফ্লোটিং পিল কন্টেইনার */
        .pagination-pill {
            background: #ffffff;
            padding: 5px 8px;
            border-radius: 50px; /* সম্পূর্ণ রাউন্ড শেপ */
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05); /* সফট শ্যাডো */
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border: 1px solid #f1f5f9;
        }

        /* গোল বাটন স্টাইল */
        .page-link-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%; /* একদম গোল */
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        /* হোভার ইফেক্ট */
        .page-link-circle:hover {
            background-color: #f1f5f9;
            color: #1e293b;
            transform: translateY(-2px);
        }

        /* একটিভ বা সিলেক্টেড পেজ */
        .page-link-circle.active {
            background: #4f46e5; /* আপনার ব্র্যান্ড কালার */
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3); /* গ্লো ইফেক্ট */
        }

        /* ডিজেবল বাটন */
        .page-link-circle.disabled {
            color: #cbd5e1;
            cursor: default;
            pointer-events: none;
        }
    </style>

    <div class="pagination-pill">
        
        {{-- Previous Button --}}
        @if ($orders->onFirstPage())
            <span class="page-link-circle disabled">
                <i class="fas fa-chevron-left" style="font-size: 12px;"></i>
            </span>
        @else
            <a href="{{ $orders->previousPageUrl() }}" class="page-link-circle" title="Previous">
                <i class="fas fa-chevron-left" style="font-size: 12px;"></i>
            </a>
        @endif

        {{-- Page Numbers --}}
        @foreach(range(1, $orders->lastPage()) as $i)
            @if($i >= $orders->currentPage() - 2 && $i <= $orders->currentPage() + 2)
                @if ($i == $orders->currentPage())
                    <span class="page-link-circle active">{{ $i }}</span>
                @else
                    <a href="{{ $orders->url($i) }}" class="page-link-circle">{{ $i }}</a>
                @endif
            @endif
        @endforeach

        {{-- Next Button --}}
        @if ($orders->hasMorePages())
            <a href="{{ $orders->nextPageUrl() }}" class="page-link-circle" title="Next">
                <i class="fas fa-chevron-right" style="font-size: 12px;"></i>
            </a>
        @else
            <span class="page-link-circle disabled">
                <i class="fas fa-chevron-right" style="font-size: 12px;"></i>
            </span>
        @endif

    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    // Optional: Add tooltip initialization if using Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush