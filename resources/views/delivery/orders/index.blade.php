@extends('delivery.layouts.app')
@section('title', 'অর্ডার')
@section('header_title', 'অসম্পূর্ণ ডেলিভারি')

@section('page_subtitle')
    <p>আপনাকে অ্যাসাইন করা অর্ডারের তালিকা।</p>
@endsection

@section('content')
<div class="d-link-top">
    <a href="{{ route('delivery.orders.history') }}">সম্পন্ন ডেলিভারি দেখুন →</a>
</div>

@forelse($orders as $o)
    @php
        $collectAmount = !empty($o->customer_payable_amount)
            ? (float) $o->customer_payable_amount
            : (float) $o->amount;
        $customerPhone = $o->shipping->phone ?? '';
    @endphp
    <div class="d-card d-card--flush mb-3">
        <a href="{{ route('delivery.orders.show', $o->id) }}" class="d-list-link" style="padding:16px 16px 10px;">
            <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;">
                <span class="d-inv">#{{ $o->invoice_id }}</span>
                <span class="d-badge">নতুন</span>
            </div>
            <div class="d-order-list-amount">
                <span>কাস্টমার থেকে কালেক্ট</span>
                <strong>৳{{ number_format($collectAmount, 2) }}</strong>
            </div>
            <small class="d-muted-small" style="display:block;margin-top:6px;line-height:1.45;">{{ \Illuminate\Support\Str::limit($o->shipping->address ?? '—', 120) }}</small>
        </a>
        @if($customerPhone)
            <div class="d-order-list-actions">
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $customerPhone) }}" class="d-call-btn">
                    <span class="d-call-icon" aria-hidden="true">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498A1 1 0 0121 16.72V20a2 2 0 01-2 2h-1C9.716 22 2 14.284 2 6V5z"/>
                        </svg>
                    </span>
                    <span class="d-call-text">
                        <span>কাস্টমারকে কল করুন</span>
                        <strong>{{ $customerPhone }}</strong>
                    </span>
                </a>
            </div>
        @endif
    </div>
@empty
    <div class="d-card"><p class="d-empty" style="padding:0;margin:0;">কোনো অর্ডার নেই</p></div>
@endforelse

<div class="d-pagination">{{ $orders->links() }}</div>
@endsection

@push('css')
<style>
    .d-order-list-amount {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-top: 10px;
        padding: 9px 10px;
        border-radius: 10px;
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
        font-size: 0.86rem;
    }
    .d-order-list-amount span { font-weight: 600; }
    .d-order-list-amount strong {
        font-size: 1rem;
        white-space: nowrap;
    }
    .d-order-list-actions {
        padding: 0 16px 16px;
    }
    .d-call-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        width: 100%;
        padding: 12px 14px;
        border-radius: 14px;
        background: linear-gradient(135deg, #16a34a 0%, #059669 100%);
        color: #fff;
        border: 0;
        text-decoration: none;
        box-shadow: 0 8px 20px rgba(5, 150, 105, 0.22);
        transition: transform .12s ease, box-shadow .12s ease;
    }
    .d-call-btn:active {
        transform: scale(0.99);
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.18);
    }
    .d-call-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255,255,255,.18);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .d-call-icon svg {
        width: 19px;
        height: 19px;
    }
    .d-call-text {
        display: flex;
        flex-direction: column;
        gap: 2px;
        min-width: 0;
    }
    .d-call-text span {
        font-size: 0.88rem;
        font-weight: 800;
    }
    .d-call-text strong {
        color: rgba(255,255,255,.88);
        font-size: 0.8rem;
        font-weight: 700;
        white-space: nowrap;
    }
    @media (min-width: 768px) {
        .d-card.d-card--flush { border-radius: 14px; }
    }
</style>
@endpush
