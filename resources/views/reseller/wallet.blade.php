@extends('reseller.layouts.app')

@section('title', 'Wallet Balance')
@section('page-title', 'Wallet Balance')

@push('styles')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); /* Indigo */
        --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%); /* Emerald */
        --info-gradient: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);    /* Blue */
        --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    }

    /* --- Wallet Cards --- */
    .wallet-card {
        border-radius: 20px;
        border: none;
        color: white;
        position: relative;
        overflow: hidden;
        min-height: 180px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: var(--card-shadow);
        transition: transform 0.3s ease;
    }
    
    .wallet-card:hover { transform: translateY(-5px); }

    /* Decorative Circle Background */
    .wallet-card::after {
        content: '';
        position: absolute;
        top: -30px; right: -30px;
        width: 150px; height: 150px;
        background: rgba(255,255,255,0.15);
        border-radius: 50%;
        pointer-events: none;
    }

    .bg-gradient-purple { background: var(--primary-gradient); }
    .bg-gradient-green { background: var(--success-gradient); }
    .bg-gradient-blue { background: var(--info-gradient); }

    .icon-box-glass {
        width: 50px; height: 50px;
        background: rgba(255,255,255,0.25);
        backdrop-filter: blur(4px);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 15px;
    }

    .amount-text { font-size: 2.2rem; font-weight: 800; line-height: 1.2; }
    .label-text { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9; }

    /* --- Transaction Table --- */
    .table-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .custom-table thead th {
        background: #f8fafc;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 16px;
        border-bottom: 1px solid #e2e8f0;
    }

    .custom-table tbody td {
        padding: 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
    }
    
    .custom-table tbody tr:last-child td { border-bottom: none; }
    .custom-table tbody tr:hover { background-color: #f8fafc; }

    /* Badges */
    .badge-soft {
        padding: 6px 12px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.75rem;
    }
    .badge-completed { background: #ecfdf5; color: #059669; }
    .badge-pending { background: #fffbeb; color: #d97706; }
    .badge-cancelled { background: #fef2f2; color: #dc2626; }
    .badge-processing { background: #eff6ff; color: #2563eb; }

    /* Avatar */
    .avatar-circle {
        width: 35px; height: 35px;
        background: #e0e7ff; color: #4338ca;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: bold;
        font-size: 0.9rem;
    }
</style>
@endpush

@section('content')

    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4 class="fw-bold text-dark mb-1">My Wallet</h4>
            <p class="text-muted small mb-0">Manage your earnings and transactions</p>
        </div>
        <a href="{{ route('reseller.deposit') }}" class="btn btn-success">
            <i class="fas fa-plus-circle me-2"></i> ডিপোজিট করুন
        </a>
    </div>

    @if(session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle me-2"></i> {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ডিপোজিট প্রয়োজন পপআপ --}}
    @if(session('deposit_required'))
    <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center" role="alert">
        <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
        <div class="flex-grow-1">
            <strong>অর্ডার করতে ডিপোজিট প্রয়োজন!</strong><br>
            প্রোডাক্ট অর্ডার করার আগে আপনাকে ওয়ালেটে টাকা ডিপোজিট করতে হবে। উদ্যোক্তা পে দিয়ে ডিপোজিট করুন।
        </div>
        <a href="{{ route('reseller.deposit') }}" class="btn btn-warning ms-3">ডিপোজিট করুন</a>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row g-4 mb-5">
        <div class="col-lg-3 col-md-6">
            <div class="wallet-card bg-gradient-purple p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="icon-box-glass">
                        <i class="fas fa-wallet fa-lg"></i>
                    </div>
                    </div>
                <div>
                    <div class="label-text">Current Balance</div>
                    <div class="amount-text">৳ {{ number_format($walletBalance ?? 0, 2) }}</div>
                    <small class="opacity-75"><i class="fas fa-check-circle me-1"></i> Available for withdrawal</small>
                    @if(($walletBalance ?? 0) <= 0)
                    <div class="mt-2">
                        <a href="{{ route('reseller.deposit') }}" class="btn btn-sm btn-light">ডিপোজিট করুন</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="wallet-card bg-gradient-green p-4">
                <div class="icon-box-glass">
                    <i class="fas fa-chart-line fa-lg"></i>
                </div>
                <div>
                    <div class="label-text">Total Earned</div>
                    <div class="amount-text">৳ {{ number_format($totalEarned ?? 0, 2) }}</div>
                    <small class="opacity-75"><i class="fas fa-coins me-1"></i> Lifetime earnings</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="wallet-card bg-gradient-blue p-4">
                <div class="icon-box-glass">
                    <i class="fas fa-shopping-cart fa-lg"></i>
                </div>
                <div>
                    <div class="label-text">Total Orders</div>
                    <div class="amount-text">{{ number_format($totalOrders ?? 0) }}</div>
                    <small class="opacity-75"><i class="fas fa-box me-1"></i> Orders with profit</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="wallet-card p-4" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);">
                <div class="icon-box-glass">
                    <i class="fas fa-truck-loading fa-lg"></i>
                </div>
                <div>
                    <div class="label-text">ডেলিভারি চার্জ (ক্যান্সেল)</div>
                    <div class="amount-text">৳ {{ number_format($totalDeliveryChargeDeducted ?? 0, 2) }}</div>
                    <small class="opacity-75"><i class="fas fa-times-circle me-1"></i> ক্যান্সেলে কাটা হয়েছে</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="table-card">
                <div class="d-flex justify-content-between align-items-center p-4 border-bottom bg-white">
                    <h6 class="fw-bold m-0 text-dark"><i class="fas fa-history text-primary me-2"></i> Transaction History</h6>
                </div>
                
                <div class="table-responsive">
                    <table class="table custom-table mb-0">
                        <thead>
                            <tr>
                                <th>তারিখ</th>
                                <th>ধরণ</th>
                                <th>বিবরণ</th>
                                <th>পরিমাণ</th>
                                <th>ব্যালেন্স</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions ?? [] as $tx)
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-dark">{{ $tx->created_at->format('d M Y') }}</span>
                                            <small class="text-muted">{{ $tx->created_at->format('h:i A') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-soft {{ $tx->amount >= 0 ? 'badge-completed' : 'badge-cancelled' }}">
                                            {{ $tx->type_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-dark small">{{ $tx->description ?? '-' }}</span>
                                    </td>
                                    <td>
                                        @if($tx->amount >= 0)
                                            <span class="fw-bold text-success">+৳{{ number_format($tx->amount, 2) }}</span>
                                        @else
                                            <span class="fw-bold text-danger">-৳{{ number_format(abs($tx->amount), 2) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted">৳{{ number_format($tx->balance_after ?? 0, 2) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center justify-content-center opacity-50">
                                            <i class="fas fa-folder-open fa-3x mb-3 text-secondary"></i>
                                            <h6 class="text-muted">কোনো ট্রাঞ্জেকশন নেই</h6>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(isset($transactions) && $transactions->hasPages())
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
                        @if ($transactions->onFirstPage())
                            <span class="page-link-circle disabled">
                                <i class="fas fa-chevron-left" style="font-size: 12px;"></i>
                            </span>
                        @else
                            <a href="{{ $transactions->previousPageUrl() }}" class="page-link-circle" title="Previous">
                                <i class="fas fa-chevron-left" style="font-size: 12px;"></i>
                            </a>
                        @endif

                        {{-- Page Numbers --}}
                        @foreach(range(1, $transactions->lastPage()) as $i)
                            @if($i >= $transactions->currentPage() - 2 && $i <= $transactions->currentPage() + 2)
                                @if ($i == $transactions->currentPage())
                                    <span class="page-link-circle active">{{ $i }}</span>
                                @else
                                    <a href="{{ $transactions->url($i) }}" class="page-link-circle">{{ $i }}</a>
                                @endif
                            @endif
                        @endforeach

                        {{-- Next Button --}}
                        @if ($transactions->hasMorePages())
                            <a href="{{ $transactions->nextPageUrl() }}" class="page-link-circle" title="Next">
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
            </div>
        </div>
    </div>

@if(session('deposit_required'))
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'warning',
            title: 'ডিপোজিট প্রয়োজন!',
            html: 'প্রোডাক্ট অর্ডার করার আগে আপনাকে ওয়ালেটে টাকা ডিপোজিট করতে হবে। উদ্যোক্তা পে দিয়ে ডিপোজিট করুন।',
            confirmButtonText: 'ডিপোজিট করুন',
            confirmButtonColor: '#10b981',
            showCancelButton: true,
            cancelButtonText: 'বন্ধ করুন'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '{{ route("reseller.deposit") }}';
            }
        });
    });
</script>
@endpush
@endif
@endsection