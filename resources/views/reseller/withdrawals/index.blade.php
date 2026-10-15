@extends('reseller.layouts.app')

@section('title', 'টাকা উত্তোলন')
@section('page-title', 'টাকা উত্তোলন')

@push('styles')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
        --card-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.1);
        --input-bg: #f8fafc;
        --border-color: #e2e8f0;
    }

    /* Layout Cards */
    .withdraw-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        overflow: hidden;
    }

    .card-header-custom {
        background: #fff;
        padding: 20px 25px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Balance Highlight */
    .balance-box {
        background: var(--primary-gradient);
        color: white;
        border-radius: 16px;
        padding: 25px;
        position: relative;
        overflow: hidden;
        margin-bottom: 25px;
    }
    .balance-box::after {
        content: '';
        position: absolute;
        top: -20px; right: -20px;
        width: 100px; height: 100px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }
    
    /* Form Styling */
    .form-label {
        font-weight: 600;
        font-size: 0.85rem;
        color: #475569;
        margin-bottom: 8px;
    }
    
    .form-control, .form-select {
        background-color: var(--input-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 12px 15px;
        font-size: 0.95rem;
        transition: all 0.2s;
    }
    .form-control:focus, .form-select:focus {
        background-color: #fff;
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .input-group-text {
        background-color: var(--input-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px 0 0 12px;
        color: #64748b;
    }
    .input-group .form-control { border-left: none; }

    /* Button */
    .btn-gradient {
        background: var(--primary-gradient);
        border: none;
        color: white;
        padding: 12px;
        border-radius: 12px;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: transform 0.2s;
    }
    .btn-gradient:hover {
        transform: translateY(-2px);
        color: white;
        box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
    }

    /* Table Styling */
    .table-modern thead th {
        background: #f8fafc;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        padding: 15px;
        border-bottom: 1px solid var(--border-color);
    }
    .table-modern tbody td {
        padding: 15px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }
    .table-modern tbody tr:last-child td { border-bottom: none; }

    /* Status Badges */
    .badge-soft { padding: 6px 12px; border-radius: 30px; font-weight: 600; font-size: 0.75rem; }
    .badge-success { background: #ecfdf5; color: #059669; }
    .badge-danger { background: #fef2f2; color: #dc2626; }
    .badge-warning { background: #fffbeb; color: #d97706; }
    .badge-secondary { background: #f1f5f9; color: #475569; }
</style>
@endpush

@section('content')

    <div class="mb-4">
        <h4 class="fw-bold text-dark mb-1">টাকা উত্তোলন (Withdraw)</h4>
        <p class="text-muted small">আপনার আয়ের টাকা নিরাপদে উত্তোলন করুন</p>
    </div>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4 mb-5">
        
        <div class="col-lg-8">
            <div class="withdraw-card h-100">
                <div class="card-header-custom">
                    <h6 class="fw-bold m-0 text-dark"><i class="fas fa-paper-plane me-2 text-primary"></i> নতুন উত্তোলনের আবেদন</h6>
                </div>
                <div class="card-body p-4">
                    
                    <div class="balance-box d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-white-50 text-uppercase small fw-bold">বর্তমান ব্যালেন্স</span>
                            <h2 class="text-white fw-bold mb-0">৳{{ number_format($user->wallet_balance ?? 0, 2) }}</h2>
                            @if(($minBalance ?? 0) > 0)
                                <small class="text-white-50 d-block mt-1">উত্তোলনযোগ্য: ৳{{ number_format($maxWithdrawable ?? 0, 0) }} (সর্বনিম্ন ৳{{ number_format($minBalance ?? 0, 0) }} ব্যালেন্স রাখতে হবে)</small>
                            @endif
                        </div>
                        <div class="bg-white bg-opacity-25 p-3 rounded-circle">
                            <i class="fas fa-wallet fa-2x text-white"></i>
                        </div>
                    </div>

                    @php $canWithdraw = ($maxWithdrawable ?? 0) >= ($minWithdraw ?? 100); @endphp

                    <form action="{{ route('reseller.withdrawals.store') }}" method="POST" {{ !$canWithdraw ? 'onsubmit="return false;"' : '' }}>
                        @csrf
                        
                        @if(!$canWithdraw)
                        <div class="alert alert-warning mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            উত্তোলনযোগ্য ব্যালেন্স (৳{{ number_format($maxWithdrawable ?? 0, 0) }}) সর্বনিম্ন উত্তোলন পরিমাণের (৳{{ number_format($minWithdraw ?? 100, 0) }}) কম। নতুন আয় হলে আবার চেষ্টা করুন।
                        </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">উত্তোলনের পরিমাণ <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-coins"></i></span>
                                    <input type="number" step="0.01" min="{{ $minWithdraw ?? 100 }}" max="{{ max($minWithdraw ?? 100, $maxWithdrawable ?? 0) }}" name="amount" class="form-control" placeholder="কমপক্ষে ৳{{ $minWithdraw ?? 100 }}" required {{ !$canWithdraw ? 'disabled' : '' }}>
                                </div>
                                <small class="text-muted">সর্বোচ্চ উত্তোলনযোগ্য: ৳{{ number_format($maxWithdrawable ?? 0, 0) }}</small>
                                @error('amount') <small class="text-danger d-block">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">পেমেন্ট মেথড <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-university"></i></span>
                                    <select name="payout_method" class="form-select" required>
                                        <option value="">সিলেক্ট করুন</option>
                                        <option value="bkash">বিকাশ (Bkash)</option>
                                        <option value="nagad">নগদ (Nagad)</option>
                                        <option value="rocket">রকেট (Rocket)</option>
                                        <option value="bank">ব্যাংক ট্রান্সফার</option>
                                    </select>
                                </div>
                                @error('payout_method') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">অ্যাকাউন্ট নাম্বার <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                    <input type="text" name="account_number" class="form-control" placeholder="017xxxxxxxx / Acc No." required>
                                </div>
                                @error('account_number') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">অ্যাকাউন্ট নাম (ঐচ্ছিক)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" name="account_name" class="form-control" placeholder="অ্যাকাউন্ট হোল্ডারের নাম">
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">নোট (ঐচ্ছিক)</label>
                                <textarea name="note" rows="2" class="form-control" placeholder="কোনো বিশেষ নির্দেশনা থাকলে লিখুন..."></textarea>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-gradient w-100 shadow-sm" {{ !$canWithdraw ? 'disabled' : '' }}>
                                <i class="fas fa-check-circle me-2"></i> রিকোয়েস্ট সাবমিট করুন
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="withdraw-card h-100">
                <div class="card-header-custom">
                    <h6 class="fw-bold m-0 text-dark"><i class="fas fa-info-circle me-2 text-primary"></i> নিয়মাবলী</h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-start mb-4">
                        <div class="bg-light p-2 rounded-circle text-primary me-3">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">সর্বনিম্ন উত্তোলন</h6>
                            <p class="text-muted small mb-0">আপনি সর্বনিম্ন <strong>৳{{ number_format($minWithdraw ?? 100, 0) }}</strong> টাকা উত্তোলন করতে পারবেন।</p>
                        </div>
                    </div>

                    @if(($minBalance ?? 0) > 0)
                    <div class="d-flex align-items-start mb-4">
                        <div class="bg-light p-2 rounded-circle text-primary me-3">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">সর্বনিম্ন ব্যালেন্স (রিজার্ভ)</h6>
                            <p class="text-muted small mb-0">আপনার অ্যাকাউন্টে সর্বনিম্ন <strong>৳{{ number_format($minBalance ?? 0, 0) }}</strong> রাখতে হবে যা উত্তোলন করা যাবে না।</p>
                        </div>
                    </div>
                    @endif

                    <div class="d-flex align-items-start mb-4">
                        <div class="bg-light p-2 rounded-circle text-primary me-3">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">প্রসেসিং সময়</h6>
                            <p class="text-muted small mb-0">রিকোয়েস্ট করার পর ২৪-৭২ ঘন্টার মধ্যে টাকা পাঠানো হবে।</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start">
                        <div class="bg-light p-2 rounded-circle text-primary me-3">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">সিকিউরিটি</h6>
                            <p class="text-muted small mb-0">ভুল নাম্বারে টাকা গেলে কর্তৃপক্ষ দায়ী থাকবে না। নাম্বার চেক করে দিন।</p>
                        </div>
                    </div>

                    <div class="alert alert-warning border-0 mt-4 mb-0 rounded-3">
                        <small><i class="fas fa-exclamation-triangle me-1"></i> ছুটির দিনে পেমেন্ট প্রসেসিং দেরি হতে পারে।</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="withdraw-card">
                <div class="card-header-custom">
                    <h6 class="fw-bold m-0 text-dark"><i class="fas fa-history me-2 text-primary"></i> উত্তোলনের ইতিহাস</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th>তারিখ</th>
                                <th>পরিমাণ</th>
                                <th>মেথড</th>
                                <th>অ্যাকাউন্ট</th>
                                <th>স্ট্যাটাস</th>
                                <th>নোট</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($withdrawals as $row)
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-dark">{{ $row->created_at->format('d M, Y') }}</span>
                                            <small class="text-muted">{{ $row->created_at->format('h:i A') }}</small>
                                        </div>
                                    </td>
                                    <td><strong class="text-primary fs-6">৳{{ number_format($row->amount ?? 0, 2) }}</strong></td>
                                    <td>
                                        <span class="badge badge-soft badge-secondary text-uppercase">{{ $row->payout_method ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-dark small">{{ $row->account_number ?? '-' }}</span>
                                            <small class="text-muted" style="font-size: 11px;">{{ $row->account_name ?? '' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $status = $row->status ?? 'pending';
                                            $badgeClass = 'badge-warning';
                                            $statusLabel = 'Pending';
                                            
                                            if ($status === 'approved') {
                                                $badgeClass = 'badge-success';
                                                $statusLabel = 'Approved';
                                            } elseif ($status === 'rejected') {
                                                $badgeClass = 'badge-danger';
                                                $statusLabel = 'Rejected';
                                            }
                                        @endphp
                                        <span class="badge badge-soft {{ $badgeClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($row->note)
                                            <span class="text-muted small" data-bs-toggle="tooltip" title="{{ $row->note }}">
                                                {{ Str::limit($row->note, 20) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="opacity-50">
                                            <i class="fas fa-folder-open fa-3x mb-3 text-secondary"></i>
                                            <h6 class="text-muted">কোন উত্তোলনের রেকর্ড নেই</h6>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($withdrawals->hasPages())
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
                        @if ($withdrawals->onFirstPage())
                            <span class="page-link-circle disabled">
                                <i class="fas fa-chevron-left" style="font-size: 12px;"></i>
                            </span>
                        @else
                            <a href="{{ $withdrawals->previousPageUrl() }}" class="page-link-circle" title="Previous">
                                <i class="fas fa-chevron-left" style="font-size: 12px;"></i>
                            </a>
                        @endif

                        {{-- Page Numbers --}}
                        @foreach(range(1, $withdrawals->lastPage()) as $i)
                            @if($i >= $withdrawals->currentPage() - 2 && $i <= $withdrawals->currentPage() + 2)
                                @if ($i == $withdrawals->currentPage())
                                    <span class="page-link-circle active">{{ $i }}</span>
                                @else
                                    <a href="{{ $withdrawals->url($i) }}" class="page-link-circle">{{ $i }}</a>
                                @endif
                            @endif
                        @endforeach

                        {{-- Next Button --}}
                        @if ($withdrawals->hasMorePages())
                            <a href="{{ $withdrawals->nextPageUrl() }}" class="page-link-circle" title="Next">
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

@endsection

@push('scripts')
<script>
    // Tooltip init
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush