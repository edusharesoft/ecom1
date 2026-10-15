@extends('backEnd.layouts.master')
@section('title', 'রিফান্ড বিস্তারিত')

@section('css')
@include('backEnd.refunds.partials.refund_styles')
@endsection

@section('content')
@php
    $statusLabels = [
        'pending' => ['মুলতুবি', 'rf-pill-pending'],
        'approved' => ['অনুমোদিত', 'rf-pill-approved'],
        'rejected' => ['প্রত্যাখ্যান', 'rf-pill-rejected'],
        'processed' => ['সম্পন্ন', 'rf-pill-processed'],
    ];
    $st = $statusLabels[$refund->status] ?? ['—', 'rf-pill-pending'];
    $totalRefund = $refund->amount + $refund->shipping_charge;
@endphp
<div class="container-fluid refund-shell refund-show-page">

    <div class="rf-page-header">
        <div>
            <h4>রিফান্ড #{{ $refund->refund_id }}</h4>
            <p class="rf-sub">অনুরোধের তারিখ: {{ $refund->created_at->format('d M, Y h:i A') }}</p>
        </div>
        <a href="{{ route('admin.refunds.index') }}" class="rf-btn-ghost">
            <i class="fas fa-arrow-left"></i> তালিকায় ফিরুন
        </a>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="rf-card">
                <div class="rf-card-body">
                    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                        <div>
                            <span class="rf-label">বর্তমান স্ট্যাটাস</span>
                            <span class="rf-pill {{ $st[1] }}">{{ $st[0] }}</span>
                            @if($refund->processed_at)
                            <p class="rf-meta-sub mt-2 mb-0"><i class="fas fa-check-circle me-1"></i> প্রসেস: {{ $refund->processed_at->format('d M, Y h:i A') }}</p>
                            @endif
                        </div>
                        <div class="text-end">
                            <span class="rf-label">মোট রিফান্ড</span>
                            <div class="rf-hero-amount">৳{{ number_format($totalRefund, 0) }}</div>
                            @if($refund->shipping_charge > 0)
                            <p class="rf-meta-sub mb-0">(শিপিং ৳{{ number_format($refund->shipping_charge, 0) }} সহ)</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="rf-card">
                <div class="rf-card-head">
                    <h6><i class="fas fa-comment-alt"></i> রিটার্নের কারণ</h6>
                </div>
                <div class="rf-card-body">
                    <div class="rf-reason-box">{{ $refund->reason }}</div>
                </div>
            </div>

            <div class="rf-card">
                <div class="rf-card-head">
                    <h6><i class="fas fa-shopping-bag"></i> অর্ডার বিবরণ</h6>
                    @if($refund->order)
                    <a href="{{ route('admin.order.invoice', ['invoice_id' => $refund->order->invoice_id]) }}" target="_blank" class="rf-btn-ghost" style="padding: 6px 12px; font-size: 12px;">
                        ইনভয়েস <i class="fas fa-external-link-alt ms-1"></i>
                    </a>
                    @endif
                </div>
                <div class="rf-card-body p-0">
                    <div class="rf-info-grid p-3 border-bottom">
                        <div class="rf-info-box">
                            <span class="rf-label">ইনভয়েস</span>
                            <span class="rf-val">#{{ $refund->order->invoice_id ?? '—' }}</span>
                        </div>
                        <div class="rf-info-box">
                            <span class="rf-label">অর্ডার তারিখ</span>
                            <span class="rf-val">{{ optional($refund->order->created_at)->format('d M, Y') ?? '—' }}</span>
                        </div>
                        <div class="rf-info-box">
                            <span class="rf-label">অর্ডার মোট</span>
                            <span class="rf-val">৳{{ number_format(($refund->order->amount ?? 0) + ($refund->order->shipping_charge ?? 0), 0) }}</span>
                        </div>
                    </div>
                    <div class="rf-table-rail border-0 rounded-0">
                        <table class="table rf-table mb-0">
                            <thead>
                                <tr>
                                    <th width="52"></th>
                                    <th>পণ্য</th>
                                    <th class="text-center">পরিমাণ</th>
                                    <th class="text-end">দাম</th>
                                    <th class="text-end">মোট</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($refund->order->orderdetails ?? [] as $item)
                                @php
                                    $img = $item->product->image->image ?? $item->image->image ?? null;
                                @endphp
                                <tr>
                                    <td>
                                        <img src="{{ $img ? asset($img) : asset('public/no-image.png') }}" alt="" class="rf-product-thumb"
                                             onerror="this.src='{{ asset('public/no-image.png') }}'">
                                    </td>
                                    <td><span class="fw-semibold">{{ $item->product_name }}</span></td>
                                    <td class="text-center">{{ $item->qty }}</td>
                                    <td class="text-end">৳{{ number_format($item->sale_price, 0) }}</td>
                                    <td class="text-end fw-bold">৳{{ number_format($item->sale_price * $item->qty, 0) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($refund->admin_note)
            <div class="rf-card">
                <div class="rf-card-body">
                    <h6 class="fw-bold mb-2"><i class="fas fa-sticky-note me-1 text-muted"></i> অ্যাডমিন নোট</h6>
                    <p class="mb-0 text-secondary">{{ $refund->admin_note }}</p>
                    @if($refund->processedBy)
                    <p class="rf-meta-sub mt-2 mb-0">— {{ $refund->processedBy->name }}</p>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="rf-card rf-sidebar-card">
                <div class="rf-card-body">
                    <span class="rf-label d-block mb-3">অ্যাকশন</span>
                    @if($refund->status == 'pending')
                        <button type="button" class="rf-btn-action rf-btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                            <i class="fas fa-check"></i> অনুমোদন করুন
                        </button>
                        <button type="button" class="rf-btn-action rf-btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="fas fa-times"></i> প্রত্যাখ্যান
                        </button>
                    @elseif($refund->status == 'approved')
                        <p class="small text-muted mb-3">অনুমোদিত — পেমেন্ট প্রসেস করুন</p>
                        <button type="button" class="rf-btn-action rf-btn-primary-block" data-bs-toggle="modal" data-bs-target="#processModal">
                            <i class="fas fa-credit-card"></i> পেমেন্ট প্রসেস
                        </button>
                    @else
                        <button class="rf-btn-action rf-btn-ghost" disabled style="width:100%;cursor:not-allowed;opacity:.6">
                            <i class="fas fa-lock"></i> কোনো অ্যাকশন নেই
                        </button>
                    @endif
                </div>
            </div>

            <div class="rf-card">
                <div class="rf-card-head">
                    <h6><i class="fas fa-wallet"></i> পেমেন্ট তথ্য</h6>
                </div>
                <div class="rf-card-body">
                    <div class="mb-3">
                        <span class="rf-label">পদ্ধতি</span>
                        <span class="rf-val text-capitalize">
                            @if($refund->refund_method == 'original_payment') মূল পেমেন্ট মাধ্যম
                            @else {{ str_replace('_', ' ', $refund->refund_method) }} @endif
                        </span>
                    </div>
                    <div class="mb-3">
                        <span class="rf-label">অ্যাকাউন্ট</span>
                        <span class="rf-val font-monospace bg-light px-2 py-1 rounded d-inline-block">{{ $refund->refund_account ?? '—' }}</span>
                    </div>
                    @if($refund->refund_account_name)
                    <div class="mb-3">
                        <span class="rf-label">অ্যাকাউন্ট হোল্ডার</span>
                        <span class="rf-val">{{ $refund->refund_account_name }}</span>
                    </div>
                    @endif
                    @if($refund->transaction_id)
                    <div class="rf-info-box" style="background:#ecfdf5;border-color:#a7f3d0">
                        <span class="rf-label text-success">ট্রানজেকশন আইডি</span>
                        <span class="rf-val text-success">{{ $refund->transaction_id }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="rf-card">
                <div class="rf-card-body rf-customer-card">
                    <div class="rf-avatar">{{ mb_substr($refund->customer->name ?? 'G', 0, 1) }}</div>
                    <h5 class="fw-bold mb-1">{{ $refund->customer->name ?? 'অতিথি' }}</h5>
                    <p class="rf-meta-sub mb-3">{{ $refund->customer->phone ?? '—' }}</p>
                    <div class="text-start border-top pt-3 small text-muted">
                        @if($refund->customer->email ?? null)
                        <div class="mb-2"><i class="fas fa-envelope me-2"></i>{{ $refund->customer->email }}</div>
                        @endif
                        @if($refund->customer->address ?? null)
                        <div><i class="fas fa-map-marker-alt me-2"></i>{{ $refund->customer->address }}{{ $refund->customer->district ? ', '.$refund->customer->district : '' }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade rf-modal" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">রিফান্ড অনুমোদন</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.refunds.approve', $refund->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small mb-3">৳{{ number_format($totalRefund, 0) }} রিফান্ড অনুমোদন করবেন?</p>
                    <label class="rf-label">অ্যাডমিন নোট (ঐচ্ছিক)</label>
                    <textarea name="admin_note" class="rf-textarea" rows="3" placeholder="অভ্যন্তরীণ নোট...">{{ $refund->admin_note }}</textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn rf-btn-ghost" data-bs-dismiss="modal">বাতিল</button>
                    <button type="submit" class="btn rf-btn-primary">অনুমোদন</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade rf-modal" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">রিফান্ড প্রত্যাখ্যান</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.refunds.reject', $refund->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <label class="rf-label">প্রত্যাখ্যানের কারণ <span class="text-danger">*</span></label>
                    <textarea name="admin_note" class="rf-textarea" rows="3" required placeholder="কেন প্রত্যাখ্যান করছেন?">{{ $refund->admin_note }}</textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn rf-btn-ghost" data-bs-dismiss="modal">বাতিল</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">প্রত্যাখ্যান</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade rf-modal" id="processModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">পেমেন্ট প্রসেস</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.refunds.process', $refund->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <label class="rf-label">ট্রানজেকশন আইডি <span class="text-danger">*</span></label>
                    <input type="text" name="transaction_id" class="rf-input mb-3" required placeholder="TRX-12345678" value="{{ $refund->transaction_id }}">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="rf-label">পদ্ধতি <span class="text-danger">*</span></label>
                            <select name="refund_method" class="rf-select" required>
                                <option value="bkash" {{ $refund->refund_method == 'bkash' ? 'selected' : '' }}>bKash</option>
                                <option value="nagad" {{ $refund->refund_method == 'nagad' ? 'selected' : '' }}>Nagad</option>
                                <option value="bank" {{ $refund->refund_method == 'bank' ? 'selected' : '' }}>ব্যাংক</option>
                                <option value="manual" {{ $refund->refund_method == 'manual' ? 'selected' : '' }}>নগদ</option>
                                <option value="original_payment" {{ $refund->refund_method == 'original_payment' ? 'selected' : '' }}>মূল পেমেন্ট</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="rf-label">পরিমাণ</label>
                            <input type="text" class="rf-input bg-light" readonly value="৳{{ number_format($totalRefund, 0) }}">
                        </div>
                    </div>
                    <label class="rf-label mt-3">পাঠানো অ্যাকাউন্ট</label>
                    <input type="text" name="refund_account" class="rf-input" required value="{{ $refund->refund_account }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn rf-btn-ghost" data-bs-dismiss="modal">বাতিল</button>
                    <button type="submit" class="btn rf-btn-primary">সম্পন্ন করুন</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
