@extends('delivery.layouts.app')
@section('title', 'ইতিহাস')
@section('header_title', 'ডেলিভারি ইতিহাস')

@section('page_subtitle')
    <p>সম্পন্ন করা ডেলিভারির রেকর্ড।</p>
@endsection

@section('content')
<div class="d-link-top">
    <a href="{{ route('delivery.orders.index') }}">← অসম্পূর্ণ অর্ডার</a>
</div>

@forelse($orders as $o)
    @php
        $collectAmount = !empty($o->customer_payable_amount)
            ? (float) $o->customer_payable_amount
            : (float) $o->amount;
    @endphp
    <div class="d-card d-card--flush mb-3">
        <div class="d-list-link" style="cursor:default;padding:16px;">
            <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;">
                <div class="d-inv">#{{ $o->invoice_id }}</div>
                <strong class="d-history-amount">৳{{ number_format($collectAmount, 2) }}</strong>
            </div>
            <small class="d-muted-small" style="display:block;margin-top:6px;">{{ $o->rider_delivered_at ? $o->rider_delivered_at->format('d M Y, h:i A') : '' }}</small>
        </div>
    </div>
@empty
    <div class="d-card"><p class="d-empty" style="padding:0;margin:0;">এখনো কিছু নেই</p></div>
@endforelse

<div class="d-pagination">{{ $orders->links() }}</div>
@endsection

@push('css')
<style>
    .d-history-amount {
        color: #047857;
        font-size: 0.98rem;
        white-space: nowrap;
    }
</style>
@endpush

