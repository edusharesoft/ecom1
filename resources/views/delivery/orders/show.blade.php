@extends('delivery.layouts.app')
@section('title', '#'.$order->invoice_id)
@section('header_title', 'অর্ডার #'.$order->invoice_id)

@section('page_subtitle')
    <p>কাস্টমার, অর্ডার সামারি, প্রোডাক্ট ও ডেলিভারি তথ্য।</p>
@endsection

@section('content')
@php
    $collectAmount = !empty($order->customer_payable_amount)
        ? (float) $order->customer_payable_amount
        : (float) $order->amount;

    $paymentMethod = optional($order->payment)->payment_method ?? ($order->payment_method ?? '—');
    $paymentStatus = optional($order->payment)->payment_status ?? ($order->payment_status ?? 'pending');
    $deliveryOtpPending = !empty($order->delivery_otp_hash)
        && $order->delivery_otp_expires_at
        && $order->delivery_otp_expires_at->isFuture();
    $isCancelled = (int) $order->order_status === 11;
    $customerPhone = $order->shipping->phone ?? '';
@endphp

<div class="d-card">
    <div class="d-muted-label" style="margin-bottom:6px;">কাস্টমার</div>
    <div class="d-cust-name">{{ $order->shipping->name ?? '—' }}</div>
    <div class="d-phone">{{ $order->shipping->phone ?? '' }}</div>
    <div class="d-addr">{{ $order->shipping->address ?? '' }}</div>
    @if($customerPhone)
        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $customerPhone) }}" class="d-detail-call-btn">
            <span class="d-detail-call-icon" aria-hidden="true">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498A1 1 0 0121 16.72V20a2 2 0 01-2 2h-1C9.716 22 2 14.284 2 6V5z"/>
                </svg>
            </span>
            <span class="d-detail-call-text">
                <span>কাস্টমারকে কল করুন</span>
                <strong>{{ $customerPhone }}</strong>
            </span>
        </a>
    @endif
</div>

<div class="d-card">
    <div class="d-section-title">অর্ডার ডিটেইলস</div>
    <div class="d-order-summary">
        <div class="d-summary-item">
            <span>ইনভয়েস</span>
            <strong>#{{ $order->invoice_id }}</strong>
        </div>
        <div class="d-summary-item">
            <span>স্ট্যাটাস</span>
            <strong>{{ $order->status->name ?? '—' }}</strong>
        </div>
        <div class="d-summary-item">
            <span>পেমেন্ট</span>
            <strong>{{ strtoupper($paymentMethod) }} · {{ ucfirst($paymentStatus) }}</strong>
        </div>
        <div class="d-summary-item">
            <span>শিপিং চার্জ</span>
            <strong>৳{{ number_format((float) ($order->shipping_charge ?? 0), 2) }}</strong>
        </div>
        <div class="d-summary-item d-summary-item--highlight">
            <span>কাস্টমার থেকে কালেক্ট</span>
            <strong>৳{{ number_format($collectAmount, 2) }}</strong>
        </div>
    </div>
</div>

<div class="d-card d-card--flush">
    <div class="d-section-bar">প্রোডাক্ট ডিটেইলস</div>
    @forelse($order->orderdetails as $item)
        @php
            $imagePath = $item->image->image ?? 'public/no-image.png';
            $lineTotal = (float) $item->sale_price * (int) $item->qty;

            $colorName = 'N/A';
            if ($item->color) {
                $colorName = $item->color->name ?? 'N/A';
            } elseif (!empty($item->product_color) && !is_numeric($item->product_color)) {
                $colorName = $item->product_color;
            }

            $sizeName = 'N/A';
            if ($item->size) {
                $sizeName = $item->size->sizeName ?? $item->size->size_name ?? $item->size->name ?? 'N/A';
            } elseif (!empty($item->product_size) && !is_numeric($item->product_size)) {
                $sizeName = $item->product_size;
            }
        @endphp
        <div class="d-product-row">
            <img src="{{ asset($imagePath) }}" alt="" class="d-product-img" width="64" height="64">
            <div class="d-product-info">
                <div class="d-product-name">{{ $item->product_name }}</div>
                <div class="d-product-meta">
                    <span>Qty: {{ $item->qty }}</span>
                    <span>Size: {{ $sizeName }}</span>
                    <span>Color: {{ $colorName }}</span>
                </div>
                <div class="d-product-price">
                    <span>৳{{ number_format((float) $item->sale_price, 2) }} × {{ $item->qty }}</span>
                    <strong>৳{{ number_format($lineTotal, 2) }}</strong>
                </div>
            </div>
        </div>
    @empty
        <p class="d-empty" style="margin:0;">প্রোডাক্ট ডিটেইলস পাওয়া যায়নি</p>
    @endforelse
</div>

@if(!$order->rider_delivered_at && !$isCancelled)
    <div class="d-card d-otp-card">
        <div class="d-section-title">ডেলিভারি OTP</div>
        @if($deliveryOtpPending)
            <p class="d-otp-text">
                কাস্টমারের ফোনে OTP পাঠানো হয়েছে। কোডটি নিয়ে নিচে বসান।
                <br>
                <small>মেয়াদ: {{ $order->delivery_otp_expires_at->format('h:i A') }}</small>
            </p>
        @else
            <p class="d-otp-text">ডেলিভারি সম্পন্ন করার আগে কাস্টমারের ফোনে OTP পাঠাতে হবে।</p>
        @endif

        <form method="post" action="{{ route('delivery.orders.deliver', $order->id) }}" class="d-form">
            @csrf
            @if($deliveryOtpPending)
                <label class="d-label" for="delivery_otp">কাস্টমার OTP</label>
                <input
                    id="delivery_otp"
                    type="text"
                    name="delivery_otp"
                    inputmode="numeric"
                    pattern="[0-9]{6}"
                    maxlength="6"
                    placeholder="৬ ডিজিট OTP"
                    value="{{ old('delivery_otp') }}"
                    required
                >
                <button type="submit" class="d-btn d-btn--success" style="margin-top:12px;" onclick="return confirm('OTP যাচাই করে ডেলিভারি সম্পন্ন করবেন?');">
                    OTP যাচাই করে ডেলিভারি সম্পন্ন করুন
                </button>
            @else
                <button type="submit" class="d-btn d-btn--primary">কাস্টমারের ফোনে OTP পাঠান</button>
            @endif
        </form>

        @if($deliveryOtpPending)
            <form method="post" action="{{ route('delivery.orders.deliver', $order->id) }}" style="margin-top:10px;">
                @csrf
                <input type="hidden" name="resend_otp" value="1">
                <button type="submit" class="d-btn d-btn--outline">আবার OTP পাঠান</button>
            </form>
        @endif
    </div>

    <form method="post" action="{{ route('delivery.orders.deliver', $order->id) }}" onsubmit="return confirm('ডেলিভারি সম্পন্ন হিসেবে চিহ্নিত করবেন?');" style="display:none;">
        @csrf
        <button type="submit" class="d-btn d-btn--success">ডেলিভারি সম্পন্ন করুন</button>
    </form>
    <p class="d-hint">সঠিক OTP দিলে অর্ডার ডেলিভারি সম্পন্ন হবে এবং কমিশন (যদি সেট থাকে) ওয়ালেটে যোগ হবে।</p>
    <div class="d-card d-cancel-card">
        <div class="d-section-title">অর্ডার ক্যানসেল</div>
        <p class="d-cancel-text">কাস্টমার অর্ডার নিতে না চাইলে বা ডেলিভারি সম্ভব না হলে অর্ডার ক্যানসেল করুন।</p>
        <form method="post" action="{{ route('delivery.orders.cancel', $order->id) }}" onsubmit="return confirm('এই অর্ডার ক্যানসেল করবেন?');">
            @csrf
            <label class="d-label" for="cancel_note">কারণ (ঐচ্ছিক)</label>
            <textarea id="cancel_note" name="cancel_note" rows="2" placeholder="যেমন: কাস্টমার ফোন ধরেনি / নিতে চায়নি">{{ old('cancel_note') }}</textarea>
            <button type="submit" class="d-btn d-btn--danger" style="margin-top:12px;">অর্ডার ক্যানসেল করুন</button>
        </form>
    </div>
@elseif($isCancelled)
    <div class="d-card d-cancelled-banner">
        এই অর্ডার ক্যানসেল করা হয়েছে।
    </div>
@else
    <div class="d-card d-done-banner">
        ডেলিভারি সম্পন্ন — {{ $order->rider_delivered_at->format('d M Y, h:i A') }}
    </div>
@endif
@endsection

@push('css')
<style>
    .d-cust-name { font-weight: 800; font-size: 1.1rem; }
    .d-phone { margin-top: 4px; font-size: 0.95rem; color: var(--d-text); }
    .d-addr { margin-top: 12px; font-size: 0.92rem; line-height: 1.55; color: #334155; }
    .d-detail-call-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 14px;
        padding: 13px 14px;
        border-radius: 14px;
        background: linear-gradient(135deg, #16a34a 0%, #059669 100%);
        color: #fff;
        border: 0;
        text-decoration: none;
        box-shadow: 0 8px 20px rgba(5, 150, 105, 0.22);
        transition: transform .12s ease, box-shadow .12s ease;
    }
    .d-detail-call-btn:active {
        transform: scale(0.99);
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.18);
    }
    .d-detail-call-icon {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: rgba(255,255,255,.18);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .d-detail-call-icon svg {
        width: 20px;
        height: 20px;
    }
    .d-detail-call-text {
        display: flex;
        flex-direction: column;
        gap: 2px;
        min-width: 0;
    }
    .d-detail-call-text span {
        font-size: 0.9rem;
        font-weight: 800;
    }
    .d-detail-call-text strong {
        color: rgba(255,255,255,.88);
        font-size: 0.8rem;
        font-weight: 700;
        white-space: nowrap;
    }
    .d-section-title {
        font-weight: 800;
        font-size: 0.95rem;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid #f1f5f9;
    }
    .d-order-summary {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
    }
    @media (min-width: 768px) {
        .d-order-summary { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    .d-summary-item {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 10px 12px;
        border-radius: 10px;
        background: #f8fafc;
        font-size: 0.88rem;
    }
    .d-summary-item span { color: var(--d-muted); }
    .d-summary-item strong { text-align: right; }
    .d-summary-item--highlight {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }
    .d-section-bar {
        padding: 14px 18px;
        font-weight: 800;
        border-bottom: 1px solid var(--d-border);
        background: #fafbfc;
        font-size: 0.95rem;
    }
    .d-product-row {
        display: flex;
        gap: 12px;
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
    }
    .d-product-row:last-child { border-bottom: 0; }
    .d-product-img {
        width: 64px;
        height: 64px;
        border-radius: 12px;
        object-fit: cover;
        border: 1px solid var(--d-border);
        background: #f8fafc;
        flex-shrink: 0;
    }
    .d-product-info { min-width: 0; flex: 1; }
    .d-product-name {
        font-weight: 800;
        font-size: 0.93rem;
        line-height: 1.35;
        color: var(--d-text);
    }
    .d-product-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 6px 10px;
        margin-top: 6px;
        color: var(--d-muted);
        font-size: 0.78rem;
    }
    .d-product-price {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        margin-top: 8px;
        font-size: 0.86rem;
    }
    .d-product-price span { color: var(--d-muted); }
    .d-product-price strong { color: var(--d-text); white-space: nowrap; }
    .d-btn--danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: #fff;
        box-shadow: 0 4px 14px rgba(220, 38, 38, 0.24);
    }
    .d-otp-card { border-color: #bfdbfe !important; background: #eff6ff !important; }
    .d-otp-text {
        margin: 0 0 12px;
        color: #1e3a8a;
        font-size: 0.9rem;
        line-height: 1.5;
    }
    .d-otp-text small { color: #2563eb; font-weight: 700; }
    .d-cancel-card { border-color: #fecaca !important; background: #fffafa !important; margin-top: 14px; }
    .d-cancel-text {
        margin: 0 0 12px;
        color: #991b1b;
        font-size: 0.88rem;
        line-height: 1.5;
    }
    .d-hint { font-size: 0.8rem; color: var(--d-muted); margin: 12px 0 0; text-align: center; }
    .d-done-banner { background: var(--d-success-soft) !important; color: #047857; font-weight: 700; text-align: center; border-color: #a7f3d0 !important; }
    .d-cancelled-banner { background: #fee2e2 !important; color: #991b1b; font-weight: 700; text-align: center; border-color: #fecaca !important; }
</style>
@endpush
