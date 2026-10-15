@extends('backEnd.layouts.master')
@section('title', 'রিফান্ড ব্যবস্থাপনা')

@section('css')
@include('backEnd.refunds.partials.refund_styles')
@endsection

@section('content')
<div class="container-fluid refund-shell refund-page">

    <div class="rf-page-header">
        <div>
            <h4>রিফান্ড ব্যবস্থাপনা <span class="rf-badge-count">{{ $data->total() }}</span></h4>
            <p class="rf-sub">সকল রিফান্ড অনুরোধ দেখুন, অনুমোদন ও প্রসেস করুন</p>
        </div>
    </div>

    @php
        $statusLabels = [
            'pending' => 'মুলতুবি',
            'approved' => 'অনুমোদিত',
            'rejected' => 'প্রত্যাখ্যান',
            'processed' => 'সম্পন্ন',
        ];
    @endphp

    <div class="rf-stat-grid">
        <a href="{{ route('admin.refunds.index') }}" class="rf-stat {{ !request('status') ? 'active' : '' }}">
            <div class="rf-stat-label">মোট</div>
            <div class="rf-stat-val">{{ $statusCounts->sum() }}</div>
        </a>
        @foreach(['pending', 'approved', 'rejected', 'processed'] as $st)
        <a href="{{ route('admin.refunds.index', ['status' => $st]) }}" class="rf-stat {{ $st }} {{ request('status') === $st ? 'active' : '' }}">
            <div class="rf-stat-label">{{ $statusLabels[$st] }}</div>
            <div class="rf-stat-val">{{ $statusCounts[$st] ?? 0 }}</div>
        </a>
        @endforeach
    </div>

    <div class="rf-card">
        <div class="rf-card-head">
            <h6><i class="fas fa-filter"></i> ফিল্টার</h6>
        </div>
        <div class="rf-card-body">
            <form method="GET" action="{{ route('admin.refunds.index') }}" class="rf-filter-grid">
                <div>
                    <label class="rf-label">ইনভয়েস খুঁজুন</label>
                    <input type="text" name="order_invoice" class="rf-input"
                           placeholder="ইনভয়েস নম্বর..." value="{{ request('order_invoice') }}">
                </div>
                <div>
                    <label class="rf-label">স্ট্যাটাস</label>
                    <select name="status" class="rf-select">
                        <option value="">সব স্ট্যাটাস</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ $statusLabels[$status] ?? ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="rf-filter-actions">
                    <button type="submit" class="btn rf-btn-primary">
                        <i class="fas fa-search me-1"></i> ফিল্টার
                    </button>
                    <a href="{{ route('admin.refunds.index') }}" class="rf-btn-ghost">
                        <i class="fas fa-redo"></i> রিসেট
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="rf-card">
        <div class="rf-card-head">
            <h6><i class="fas fa-undo-alt"></i> রিফান্ড তালিকা</h6>
        </div>
        <div class="rf-card-body">
            <p class="d-lg-none rf-scroll-hint"><i class="fas fa-arrows-alt-h me-1"></i> বাকি কলাম দেখতে স্লাইড করুন</p>

            <div class="rf-table-rail">
                <table class="table rf-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>রিফান্ড</th>
                            <th>গ্রাহক</th>
                            <th>পরিমাণ</th>
                            <th>পদ্ধতি</th>
                            <th>স্ট্যাটাস</th>
                            <th>তারিখ</th>
                            <th class="text-end">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $refund)
                        <tr>
                            <td>{{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}</td>
                            <td>
                                <div class="rf-refund-id">#{{ $refund->refund_id }}</div>
                                @if($refund->order)
                                <br><a href="{{ route('admin.order.invoice', ['invoice_id' => $refund->order->invoice_id]) }}"
                                       target="_blank" class="rf-invoice-link">INV-{{ $refund->order->invoice_id }}</a>
                                @endif
                            </td>
                            <td>
                                <span class="rf-customer-name">{{ $refund->customer->name ?? 'অতিথি' }}</span><br>
                                <span class="rf-meta-sub">{{ $refund->customer->phone ?? '—' }}</span>
                            </td>
                            <td>
                                <span class="rf-amount">৳{{ number_format($refund->amount + $refund->shipping_charge, 0) }}</span>
                                @if($refund->shipping_charge > 0)
                                <br><span class="rf-meta-sub">শিপিং সহ</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-capitalize rf-meta-sub">{{ str_replace('_', ' ', $refund->refund_method) }}</span>
                            </td>
                            <td>
                                @if($refund->status == 'pending')
                                    <span class="rf-pill rf-pill-pending">মুলতুবি</span>
                                @elseif($refund->status == 'approved')
                                    <span class="rf-pill rf-pill-approved">অনুমোদিত</span>
                                @elseif($refund->status == 'rejected')
                                    <span class="rf-pill rf-pill-rejected">প্রত্যাখ্যান</span>
                                @else
                                    <span class="rf-pill rf-pill-processed">সম্পন্ন</span>
                                @endif
                            </td>
                            <td>
                                {{ $refund->created_at->format('d M, Y') }}<br>
                                <span class="rf-meta-sub">{{ $refund->created_at->format('h:i A') }}</span>
                            </td>
                            <td class="text-end">
                                <div class="rf-row-actions">
                                    <a href="{{ route('admin.refunds.show', $refund->id) }}" class="rf-act-btn rf-act-view" title="বিস্তারিত">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($refund->status == 'pending')
                                        <button type="button" class="rf-act-btn rf-act-approve" title="অনুমোদন"
                                                data-bs-toggle="modal" data-bs-target="#approveModal{{ $refund->id }}">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="rf-act-btn rf-act-reject" title="প্রত্যাখ্যান"
                                                data-bs-toggle="modal" data-bs-target="#rejectModal{{ $refund->id }}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @elseif($refund->status == 'approved')
                                        <button type="button" class="rf-act-btn rf-act-process" title="পেমেন্ট প্রসেস"
                                                data-bs-toggle="modal" data-bs-target="#processModal{{ $refund->id }}">
                                            <i class="fas fa-credit-card"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8">
                                <div class="rf-empty">
                                    <i class="fas fa-inbox"></i>
                                    <p class="mb-0">কোনো রিফান্ড অনুরোধ নেই</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($data->hasPages())
            <div class="rf-paginate">
                {{ $data->links('pagination::bootstrap-4') }}
            </div>
            @endif
        </div>
    </div>
</div>

@foreach($data as $refund)
<div class="modal fade rf-modal" id="approveModal{{ $refund->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">রিফান্ড অনুমোদন</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.refunds.approve', $refund->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small mb-3">৳{{ number_format($refund->amount + $refund->shipping_charge, 0) }} রিফান্ড অনুমোদন করবেন?</p>
                    <label class="rf-label">অ্যাডমিন নোট (ঐচ্ছিক)</label>
                    <textarea name="admin_note" class="rf-textarea" rows="2">{{ $refund->admin_note }}</textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn rf-btn-ghost" data-bs-dismiss="modal">বাতিল</button>
                    <button type="submit" class="btn rf-btn-primary">অনুমোদন</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade rf-modal" id="rejectModal{{ $refund->id }}" tabindex="-1" aria-hidden="true">
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
                    <textarea name="admin_note" class="rf-textarea" rows="3" required>{{ $refund->admin_note }}</textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn rf-btn-ghost" data-bs-dismiss="modal">বাতিল</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">প্রত্যাখ্যান</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade rf-modal" id="processModal{{ $refund->id }}" tabindex="-1" aria-hidden="true">
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
                    <input type="text" name="transaction_id" class="rf-input mb-3" required>
                    <label class="rf-label">পদ্ধতি</label>
                    <select name="refund_method" class="rf-select mb-3">
                        <option value="bkash">bKash</option>
                        <option value="nagad">Nagad</option>
                        <option value="bank">ব্যাংক</option>
                        <option value="manual">নগদ/ম্যানুয়াল</option>
                    </select>
                    <label class="rf-label">অ্যাকাউন্ট</label>
                    <input type="text" name="refund_account" class="rf-input" value="{{ $refund->refund_account }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn rf-btn-ghost" data-bs-dismiss="modal">বাতিল</button>
                    <button type="submit" class="btn rf-btn-primary">সম্পন্ন</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection
