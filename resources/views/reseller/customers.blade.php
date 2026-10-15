@extends('reseller.layouts.app')

@section('title', 'কাস্টমার লিস্ট')

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

    /* Customer Card */
    .customer-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        transition: all 0.3s ease;
        overflow: hidden;
        margin-bottom: 20px;
    }

    .customer-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.1);
        border-color: #cbd5e1;
    }

    .customer-header {
        padding: 20px;
        border-bottom: 1px solid #f1f5f9;
        background: #f8fafc;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .customer-body {
        padding: 20px;
    }

    .customer-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #e0e7ff;
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.5rem;
        overflow: hidden;
    }

    .customer-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .stat-badge {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
        text-align: center;
        min-width: 100px;
    }

    .stat-badge-label {
        font-size: 0.75rem;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .stat-badge-value {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
    }

    .profit-badge {
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

    .search-form {
        max-width: 400px;
    }
</style>
@endpush

@section('content')

    <div class="stats-card">
        <div class="stats-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="flex-grow-1">
            <h5 class="fw-bold m-0 text-dark">কাস্টমার লিস্ট</h5>
            <small class="text-muted">মোট কাস্টমার: <strong>{{ $customers->total() }}</strong> জন</small>
        </div>
        <div class="ms-auto">
             <form method="GET" class="d-flex align-items-center gap-2 search-form">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="নাম, ফোন বা ইমেইল..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
                @if(request('search'))
                    <a href="{{ route('reseller.customers') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-times"></i></a>
                @endif
             </form>
        </div>
    </div>

    <div class="row">
        @forelse($customers as $customer)
        <div class="col-12">
            <div class="customer-card">
                <div class="customer-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="customer-avatar">
                            @if($customer->image && file_exists(public_path($customer->image)))
                                <img src="{{ asset($customer->image) }}" alt="{{ $customer->name }}">
                            @else
                                {{ strtoupper(substr($customer->name ?? 'C', 0, 1)) }}
                            @endif
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">{{ $customer->name ?? 'N/A' }}</h6>
                            <p class="text-muted mb-0 small">
                                <i class="fas fa-phone-alt me-1 text-secondary"></i> {{ $customer->phone ?? 'N/A' }}
                                @if($customer->email)
                                    <span class="ms-2"><i class="fas fa-envelope me-1 text-secondary"></i> {{ $customer->email }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if($customer->last_order_date)
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i> শেষ অর্ডার: {{ \Carbon\Carbon::parse($customer->last_order_date)->format('d M, Y') }}
                            </small>
                        @endif
                    </div>
                </div>

                <div class="customer-body">
                    <div class="row g-4 align-items-center">
                        <div class="col-md-3">
                            <div class="stat-badge">
                                <div class="stat-badge-label">মোট অর্ডার</div>
                                <div class="stat-badge-value">{{ $customer->total_orders ?? 0 }} টি</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-badge">
                                <div class="stat-badge-label">মোট খরচ</div>
                                <div class="stat-badge-value">৳{{ number_format($customer->total_spent ?? 0, 0) }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-badge">
                                <div class="stat-badge-label">আপনার লাভ</div>
                                <div class="stat-badge-value text-success">৳{{ number_format($customer->total_profit ?? 0, 0) }}</div>
                            </div>
                        </div>
                        <div class="col-md-3 text-md-end">
                            <div class="profit-badge">
                                <i class="fas fa-coins"></i> লাভ: ৳{{ number_format($customer->total_profit ?? 0, 0) }}
                            </div>
                        </div>
                    </div>

                    @if($customer->address || $customer->district || $customer->area)
                    <div class="mt-3 pt-3 border-top">
                        <small class="text-muted">
                            <i class="fas fa-map-marker-alt me-1"></i> 
                            {{ $customer->address ?? '' }}
                            @if($customer->district)
                                , {{ $customer->district }}
                            @endif
                            @if($customer->area)
                                , {{ $customer->area }}
                            @endif
                        </small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
                <div class="mb-3">
                    <i class="fas fa-users fa-4x text-muted" style="opacity: 0.3;"></i>
                </div>
                <h5 class="fw-bold text-secondary">কোন কাস্টমার পাওয়া যায়নি</h5>
                <p class="text-muted">
                    @if(request('search'))
                        "{{ request('search') }}" এর জন্য কোন ফলাফল পাওয়া যায়নি।
                    @else
                        আপনার এখনও কোন কাস্টমার নেই। অর্ডার প্লেস করার পর কাস্টমার লিস্ট এখানে দেখাবে।
                    @endif
                </p>
                @if(request('search'))
                    <a href="{{ route('reseller.customers') }}" class="btn btn-primary rounded-pill px-4 mt-2">
                        <i class="fas fa-arrow-left me-2"></i> সব কাস্টমার দেখুন
                    </a>
                @else
                    <a href="{{ route('reseller.products.index') }}" class="btn btn-primary rounded-pill px-4 mt-2">
                        <i class="fas fa-shopping-cart me-2"></i> নতুন অর্ডার করুন
                    </a>
                @endif
            </div>
        </div>
        @endforelse
    </div>

    @if($customers->hasPages())
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
            @if ($customers->onFirstPage())
                <span class="page-link-circle disabled">
                    <i class="fas fa-chevron-left" style="font-size: 12px;"></i>
                </span>
            @else
                <a href="{{ $customers->previousPageUrl() }}" class="page-link-circle" title="Previous">
                    <i class="fas fa-chevron-left" style="font-size: 12px;"></i>
                </a>
            @endif

            {{-- Page Numbers --}}
            @foreach(range(1, $customers->lastPage()) as $i)
                @if($i >= $customers->currentPage() - 2 && $i <= $customers->currentPage() + 2)
                    @if ($i == $customers->currentPage())
                        <span class="page-link-circle active">{{ $i }}</span>
                    @else
                        <a href="{{ $customers->url($i) }}" class="page-link-circle">{{ $i }}</a>
                    @endif
                @endif
            @endforeach

            {{-- Next Button --}}
            @if ($customers->hasMorePages())
                <a href="{{ $customers->nextPageUrl() }}" class="page-link-circle" title="Next">
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
