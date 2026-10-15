@extends('frontEnd.layouts.master')
@section('title', 'Customer Checkout')
@php
    $generalsetting = \App\Models\GeneralSetting::first();
    $hasNewsTicker =
        $generalsetting &&
        (int) ($generalsetting->news_ticker_enabled ?? 0) === 1 &&
        trim((string) ($generalsetting->top_headline ?? '')) !== '';
    $checkoutMobilePadTop = $hasNewsTicker ? '158px' : '128px';
@endphp
@push('css')
    <link rel="stylesheet" href="{{ asset('public/frontEnd/css/select2.min.css') }}" />
    <style>
        /* ================================================================
           MODERN CHECKOUT STYLES - PROFESSIONAL E-COMMERCE LOOK
        ================================================================ */
        :root {
            --primary-color: #0f3460;
            --secondary-color: #e94560;
            --success-color: #28a745;
            --border-color: #e5e7eb;
            --bg-color: #f8f9fa;
            --text-dark: #1f2937;
            --text-light: #6b7280;
        }

        .checkout-section {
            background-color: var(--bg-color);
            padding: 32px 0 48px;
            font-family: 'Poppins', sans-serif;
        }

        .checkout-layout-row {
            align-items: flex-start !important;
        }

        .checkout-form-col,
        .checkout-summary-col {
            align-self: start !important;
        }

        @media (min-width: 992px) {

            .checkout-form-col,
            .checkout-summary-col {
                margin-top: 0 !important;
                padding-top: 0 !important;
            }
        }

        .checkout-summary-col .checkout-card {
            margin-top: 0;
        }

        /* Desktop: CSS Grid */
        @media (min-width: 992px) {
            .checkout-layout-row {
                display: grid !important;
                grid-template-columns: minmax(0, 1fr) minmax(300px, 420px);
                gap: 24px;
                align-items: stretch !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                --bs-gutter-x: 0;
            }

            .checkout-layout-row>.checkout-form-col,
            .checkout-layout-row>.checkout-summary-col {
                width: 100% !important;
                max-width: 100% !important;
                flex: none !important;
                order: unset !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            .checkout-form-col {
                grid-column: 1;
                grid-row: 1;
            }

            .checkout-summary-col {
                grid-column: 2;
                grid-row: 1;
                position: relative;
                display: flex;
                flex-direction: column;
                align-self: stretch !important;
            }
        }

        .checkout-summary-sticky {
            width: 100%;
        }

        @media (min-width: 992px) {
            .checkout-summary-sticky {
                z-index: 50;
                align-self: flex-start;
            }

            .checkout-summary-sticky.is-fixed {
                position: fixed !important;
                z-index: 50;
            }

            .checkout-summary-sticky.is-abs-bottom {
                position: absolute !important;
                left: 0;
                right: 0;
                bottom: 0;
                top: auto !important;
            }
        }

        /* checkout পেজে sticky কাজ করাতে overflow ঠিক */
        html.checkout-page,
        body.checkout-page {
            overflow-x: visible !important;
            overflow-y: visible !important;
        }

        body.checkout-page #content,
        body.checkout-page .checkout-section,
        body.checkout-page .checkout-section .container,
        body.checkout-page .checkout-section form,
        body.checkout-page .checkout-layout-row {
            overflow: visible !important;
        }

        /* --- Card Design --- */
        .checkout-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .checkout-header {
            background: #fff;
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .checkout-header i {
            color: var(--secondary-color);
            font-size: 22px;
        }

        .checkout-header h6 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: var(--primary-color);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-body-custom {
            padding: 30px;
        }

        /* --- Form Inputs --- */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label-custom {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
            display: block;
        }

        .form-control-custom {
            width: 100%;
            height: 50px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 0 16px;
            font-size: 15px;
            color: #333;
            transition: all 0.2s;
            background-color: #fff;
        }

        .form-control-custom:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(15, 52, 96, 0.08);
            outline: none;
        }

        textarea.form-control-custom {
            height: auto;
            padding: 15px;
            line-height: 1.5;
        }

        /* --- Payment Methods (Interactive Box) --- */
        .payment-option-label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 2px solid var(--border-color);
            border-radius: 10px;
            padding: 16px;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-bottom: 15px;
            background: #fff;
            position: relative;
        }

        .payment-option-label:hover {
            border-color: #9ca3af;
            background: #f9fafb;
        }

        .payment-option-label input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        /* Selected State */
        .payment-option-label:has(input:checked) {
            border-color: var(--primary-color);
            background-color: #f0f5ff;
            box-shadow: 0 0 0 1px var(--primary-color);
        }

        .payment-content {
            display: flex;
            align-items: center;
            gap: 15px;
            width: 100%;
        }

        .pay-logo {
            width: 40px;
            height: 40px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .pay-info strong {
            display: block;
            font-size: 16px;
            color: var(--text-dark);
        }

        .pay-info small {
            font-size: 13px;
            color: var(--text-light);
        }

        .check-circle {
            width: 22px;
            height: 22px;
            border: 2px solid #ccc;
            border-radius: 50%;
            position: relative;
            flex-shrink: 0;
        }

        .payment-option-label input:checked~.check-circle {
            border-color: var(--primary-color);
            background: var(--primary-color);
        }

        .payment-option-label input:checked~.check-circle::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 8px;
            height: 8px;
            background: #fff;
            border-radius: 50%;
        }

        /* --- Cart Items (Scrollable) --- */
        .cart-items-scroll {
            max-height: 320px;
            overflow-y: auto;
            padding-right: 5px;
        }

        .cart-items-scroll.is-empty {
            display: none;
            max-height: 0;
            padding: 0;
            margin: 0;
        }

        .cart-items-scroll::-webkit-scrollbar {
            width: 5px;
        }

        .cart-items-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .cart-items-scroll::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 5px;
        }

        .checkout-item {
            display: flex;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px dashed var(--border-color);
            position: relative;
            align-items: center;
        }

        .checkout-item:last-child {
            border-bottom: none;
        }

        .checkout-pro-img {
            width: 70px;
            height: 70px;
            border-radius: 8px;
            border: 1px solid #eee;
            object-fit: cover;
        }

        .checkout-pro-info h6 {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-dark);
            margin: 0 0 5px;
            line-height: 1.4;
        }

        .checkout-pro-info .meta {
            font-size: 12px;
            color: var(--text-light);
        }

        .remove-item-btn {
            color: #ef4444;
            cursor: pointer;
            font-size: 16px;
            position: absolute;
            top: 15px;
            right: 0;
            transition: 0.2s;
        }

        .remove-item-btn:hover {
            color: #dc2626;
            transform: scale(1.1);
        }

        /* Quantity Control */
        .qty-box {
            display: flex;
            align-items: center;
            background: #f3f4f6;
            border-radius: 6px;
            padding: 3px;
            margin-top: 8px;
            width: fit-content;
        }

        .qty-btn {
            width: 28px;
            height: 28px;
            border: none;
            background: #fff;
            border-radius: 4px;
            color: var(--primary-color);
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qty-btn:hover {
            background: var(--primary-color);
            color: #fff;
        }

        .qty-val {
            width: 30px;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
        }

        /* --- COUPON BOX (LARGER & MODERN) --- */
        .coupon-wrapper {
            background: #f8fafc;
            padding: 20px;
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
        }

        .coupon-group-modern {
            display: flex;
            width: 100%;
            height: 55px;
            /* Bigger Height */
            border: 2px solid #d1d5db;
            border-radius: 8px;
            overflow: hidden;
            transition: 0.3s;
            background: #fff;
        }

        .coupon-group-modern:focus-within {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(15, 52, 96, 0.05);
        }

        .coupon-input-modern {
            flex-grow: 1;
            border: none;
            padding: 0 20px;
            font-size: 15px;
            color: #333;
            outline: none;
        }

        .coupon-btn-modern {
            background: var(--text-dark);
            color: #fff;
            border: none;
            padding: 0 30px;
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            cursor: pointer;
            transition: 0.3s;
        }

        .coupon-btn-modern:hover {
            background: var(--secondary-color);
        }

        /* --- Totals Area --- */
        .summary-totals {
            padding: 24px;
            background: #fff;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 15px;
            color: var(--text-dark);
        }

        .total-row.final {
            border-top: 2px dashed #e5e7eb;
            margin-top: 15px;
            padding-top: 15px;
            font-size: 20px;
            font-weight: 800;
            color: var(--primary-color);
        }

        /* Advance/Due Alert */
        .advance-alert {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
            text-align: center;
        }

        /* --- Submit Button --- */
        .btn-place-order {
            background: var(--secondary-color);
            color: #fff;
            width: 100%;
            border: none;
            padding: 18px;
            border-radius: 10px;
            font-size: 17px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s;
            box-shadow: 0 10px 25px rgba(233, 69, 96, 0.3);
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .btn-place-order:hover {
            background: var(--primary-color);
            transform: translateY(-2px);
        }

        /* --- Responsive Fixes --- */
        @media (max-width: 991.98px) {
            body.checkout-page #content {
                padding-top: {{ $checkoutMobilePadTop }} !important;
            }

            .checkout-section {
                padding: 12px 0 40px;
            }

            .checkout-layout-row {
                display: flex !important;
                flex-direction: column;
                align-items: flex-start !important;
            }

            .checkout-summary-col {
                order: -1;
                flex: 0 0 auto !important;
                width: 100% !important;
                height: auto !important;
                min-height: 0 !important;
                display: block !important;
                margin-bottom: 12px;
            }

            .checkout-form-col {
                order: 0;
                flex: 0 0 auto !important;
                width: 100% !important;
            }

            .checkout-summary-placeholder {
                display: none !important;
                height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .checkout-summary-sticky.is-fixed,
            .checkout-summary-sticky.is-abs-bottom {
                position: static !important;
                top: auto !important;
                left: auto !important;
                width: 100% !important;
            }

            .mobile-submit-btn {
                display: block !important;
                margin-top: 25px;
            }

            .desktop-submit-btn {
                display: none !important;
            }
        }

        @media (min-width: 992px) {
            .mobile-submit-btn {
                display: none !important;
            }

            .desktop-submit-btn {
                display: block !important;
            }
        }
    </style>
@endpush

@section('content')
    <script>
        document.documentElement.classList.add('checkout-page');
        document.body.classList.add('checkout-page');
    </script>
    <section class="checkout-section">
        @php
            // ==============================================================
            //  PHP LOGIC: CART, SHIPPING, DISCOUNT, ADVANCE (UNCHANGED)
            // ==============================================================
            $subtotal = Cart::instance('shopping')->subtotal();
            $subtotal = str_replace(',', '', $subtotal);
            $subtotal = str_replace('.00', '', $subtotal);
            $subtotal = (float) $subtotal;

            // ✅ শিপিং লজিক চেক
            $requires_shipping = false;
            foreach (Cart::instance('shopping')->content() as $item) {
                $product = \App\Models\Product::find($item->id);
                if ($product && $product->is_digital != 1) {
                    $requires_shipping = true;
                    break;
                }
            }

            // ✅ শিপিং চার্জ সেট
            // ⭐ Free Delivery Check - যদি সব প্রোডাক্ট free delivery eligible হয়, shipping charge 0
            $hasAllFreeDelivery = \App\Http\Controllers\Frontend\ShoppingController::hasAllFreeDeliveryProducts();

            if ($requires_shipping && !$hasAllFreeDelivery) {
                $shipping = Session::get('shipping') ? Session::get('shipping') : 0;
            } else {
                $shipping = 0;
                Session::put('shipping', 0);
            }

            $discount = Session::get('discount', 0);
            // ⭐ Grand Total Calculation - Free delivery হলে shipping charge 0
            $grand_total = $subtotal + $shipping - $discount;

            // ✅ JS ডেটা অ্যারে
            $cartItemsForJs = [];
            $hasDigital = false;
            foreach (Cart::instance('shopping')->content() as $item) {
                $p = \App\Models\Product::find($item->id);
                if ($p && $p->is_digital == 1) {
                    $hasDigital = true;
                }
                $cartItemsForJs[] = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'qty' => $item->qty,
                    'price' => (float) $item->price,
                    'image' => asset($item->options->image ?? ''),
                    'link' => isset($item->options->slug) ? url('/product/' . $item->options->slug) : '#',
                    'is_digital' => (int) ($p->is_digital ?? 0),
                    'free_delivery' => (int) ($p->free_delivery ?? 0),
                    'color_id' => $item->options->color_id ?? null,
                    'size_id' => $item->options->size_id ?? null,
                    'variant_price_id' => $item->options->variant_price_id ?? null,
                ];
            }

            // ✅ Advance Logic
            $advance_amount = \App\Http\Controllers\Frontend\ShoppingController::getCartAdvanceAmount();
            $hasAdvance = $advance_amount > 0 ? true : false;
            $payable_now = $hasAdvance ? $advance_amount : $grand_total;
            $due_amount = $hasAdvance ? $grand_total - $advance_amount : 0;

            $__gsCheckoutOtp = \App\Models\GeneralSetting::where('status', 1)->first();
            $__custCheckoutOtpPending = session('chkotp_customer_pending');
            $__checkoutOtpDraft = session('chkotp_customer_draft', []);
            $__showCheckoutOtpModal =
                $__gsCheckoutOtp && ($__gsCheckoutOtp->checkout_otp_enabled ?? 0) == 1 && $__custCheckoutOtpPending;
            $__otpFieldValue = session('checkout_otp_cleared') ? '' : old('checkout_otp');
        @endphp

        <div class="container">
            {{-- মেইন ফর্ম --}}
            <form id="checkout-form" action="{{ route('customer.ordersave') }}" method="POST" data-parsley-validate="">
                @csrf
                <input type="hidden" name="checkout_otp" id="checkout_otp_hidden" value="{{ $__otpFieldValue }}">
                @if (!empty($__showCheckoutOtpModal) && !empty($__checkoutOtpDraft))
                    @foreach (['division_id', 'district_id', 'upazila_id', 'payment_method', 'manual_trx_id', 'manual_sender_number', 'order_note'] as $__draftKey)
                        @if (!empty($__checkoutOtpDraft[$__draftKey]))
                            <input type="hidden" name="{{ $__draftKey }}" value="{{ $__checkoutOtpDraft[$__draftKey] }}">
                        @endif
                    @endforeach
                @endif
                {{-- Traffic: সার্ভার সেশন (referrer/fbclid) + ব্রাউজার sessionStorage --}}
                <input type="hidden" name="traffic_source" id="inp_ts"
                    value="{{ old('traffic_source', session('order_traffic_source', 'direct')) }}">
                <input type="hidden" name="traffic_referrer" id="inp_tsr"
                    value="{{ old('traffic_referrer', session('order_traffic_referrer', '')) }}">
                <script>
                    try {
                        var elTs = document.getElementById('inp_ts');
                        var elTsr = document.getElementById('inp_tsr');
                        var ts = sessionStorage.getItem('_ts');
                        var tsr = sessionStorage.getItem('_tsr');
                        if (ts !== null && ts !== '') {
                            elTs.value = ts;
                        } else if (elTs.value && elTs.value !== 'direct') {
                            sessionStorage.setItem('_ts', elTs.value);
                        }
                        if (tsr !== null && tsr !== '') {
                            elTsr.value = tsr;
                        } else if (elTsr.value) {
                            sessionStorage.setItem('_tsr', elTsr.value);
                        }
                    } catch (e) {}
                </script>

                <div class="row checkout-layout-row g-4" id="checkoutLayoutRow">

                    {{-- LEFT COLUMN: Shipping & Payment --}}
                    <div class="col-lg-7 col-md-12 checkout-form-col">

                        {{-- 1. SHIPPING INFO CARD --}}
                        <div class="checkout-card">
                            <div class="checkout-header">
                                <i class="fas fa-truck-moving"></i>
                                <h6>শিপিং এবং বিলিং তথ্য</h6>
                            </div>
                            <div class="card-body-custom">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label-custom">আপনার নাম *</label>
                                            <input type="text" name="name" class="form-control-custom"
                                                value="{{ Auth::guard('customer')->user()->name ?? old('name') }}"
                                                placeholder="সম্পূর্ণ নাম লিখুন" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label-custom">মোবাইল নাম্বার *</label>
                                            <input type="text" name="phone" class="form-control-custom" minlength="11"
                                                maxlength="11" pattern="0[0-9]+"
                                                value="{{ Auth::guard('customer')->user()->phone ?? old('phone') }}"
                                                placeholder="017xxxxxxxx" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label-custom">সম্পূর্ণ ঠিকানা *</label>
                                            <input type="text" name="address" class="form-control-custom"
                                                value="{{ Auth::guard('customer')->user()->address ?? old('address') }}"
                                                placeholder="বাসা নং, রোড নং, এলাকা, জেলা" required>
                                        </div>
                                    </div>
                                    @if ($requires_shipping)
                                        <div class="col-12">
                                            <div class="row g-2 g-md-3 align-items-end checkout-location-fields">
                                                <div class="col-12 col-md-4">
                                                    <div class="form-group mb-0 mb-md-2">
                                                        <label class="form-label-custom">বিভাগ *</label>
                                                        <select name="division_id" id="checkout_division"
                                                            class="form-control-custom" required>
                                                            <option value="">বিভাগ নির্বাচন করুন</option>
                                                            @foreach ($divisions ?? collect() as $d)
                                                                <option value="{{ $d->id }}">{{ $d->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div class="form-group mb-0 mb-md-2">
                                                        <label class="form-label-custom">জেলা *</label>
                                                        <select name="district_id" id="checkout_district"
                                                            class="form-control-custom" required disabled>
                                                            <option value="">আগে বিভাগ সিলেক্ট করুন</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div class="form-group mb-0 mb-md-2">
                                                        <label class="form-label-custom">উপজেলা / থানা *</label>
                                                        <select name="upazila_id" id="checkout_upazila"
                                                            class="form-control-custom" required disabled>
                                                            <option value="">আগে জেলা সিলেক্ট করুন</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label-custom">বিভাগ *</label>
                                                <input type="text" class="form-control-custom"
                                                    value="ডিজিটাল / ফ্রি শিপিং — লোকেশন লাগবে না" readonly disabled
                                                    style="background:#f3f4f6;">
                                                <input type="hidden" name="division_id" value="">
                                                <input type="hidden" name="district_id" value="">
                                                <input type="hidden" name="upazila_id" value="">
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label-custom">অর্ডার নোট (ঐচ্ছিক)</label>
                                            <textarea name="order_note" id="order_note" class="form-control-custom" rows="2"
                                                style="height:auto; resize:none;" placeholder="ডেলিভারি সম্পর্কে বিশেষ কিছু বলার থাকলে লিখুন...">{{ $order_note ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 2. PAYMENT METHOD CARD --}}


                        {{-- 2. PAYMENT METHOD CARD --}}
                        <div class="checkout-card">
                            <div class="checkout-header">
                                <i class="fas fa-wallet"></i>
                                <h6>পেমেন্ট মেথড নির্বাচন করুন</h6>
                            </div>
                            <div class="card-body-custom">

                                @if ($hasAdvance)
                                    <div class="alert alert-warning border-0 shadow-sm mb-4"
                                        style="border-left: 5px solid #ffc107 !important; background-color: #fff8e1;">
                                        <div class="d-flex gap-3 align-items-center">
                                            <i class="fas fa-exclamation-triangle text-warning fs-4"></i>
                                            <div>
                                                <strong>অগ্রিম পেমেন্ট প্রয়োজন!</strong>
                                                <p class="mb-0 small">এই অর্ডারে <b>৳
                                                        {{ number_format($advance_amount, 2) }}</b> অগ্রিম পেমেন্ট করতে হবে।
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Payment Options List --}}
                                <div class="payment-options-list">

                                    {{-- COD Option --}}
                                    @if (!$hasDigital && !$hasAdvance)
                                        <label class="payment-option-label">
                                            <input type="radio" name="payment_method" value="cod" checked required>
                                            <div class="payment-content">
                                                <div class="text-center" style="width: 40px;"><i
                                                        class="fas fa-truck text-success fs-2"></i></div>
                                                <div class="pay-info">
                                                    <strong>Cash On Delivery</strong>
                                                    <small>পণ্য হাতে পেয়ে মূল্য পরিশোধ করুন</small>
                                                </div>
                                            </div>
                                            <div class="check-circle"></div>
                                        </label>
                                    @endif
                                    @if ($shatajpay)
                                        <label class="payment-option-label">
                                            <input type="radio" name="payment_method" value="shatajpay" required>
                                            <div class="payment-content">
                                                <img src="{{ asset('public/frontEnd/images/shatajpay.png') }}"
                                                    class="pay-logo" alt="Shataj Pay"
                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">

                                                <div class="pay-info" style="display: none;">
                                                    <i class="fas fa-credit-card text-primary fs-4"></i>
                                                </div>

                                                <div class="pay-info">
                                                    <strong>Shataj Pay</strong>
                                                    <small>কার্ড ও মোবাইল ব্যাংকিং পেমেন্ট</small>
                                                </div>
                                            </div>

                                            <div class="check-circle"></div>
                                        </label>
                                    @endif

                                    {{-- Bkash --}}
                                    @if ($bkash_gateway)
                                        <label class="payment-option-label">
                                            {{-- required যুক্ত করা হয়েছে --}}
                                            <input type="radio" name="payment_method" value="bkash" required>
                                            <div class="payment-content">
                                                <img src="{{ asset('public/frontEnd/images/bkash.svg') }}"
                                                    class="pay-logo" alt="bKash">
                                                <div class="pay-info">
                                                    <strong>bKash Payment</strong>
                                                    <small>বিকাশ অ্যাপ বা গেটওয়ে দ্বারা পেমেন্ট</small>
                                                </div>
                                            </div>
                                            <div class="check-circle"></div>
                                        </label>
                                    @endif

                                    {{-- ShurjoPay --}}
                                    @if ($shurjopay_gateway)
                                        <label class="payment-option-label">
                                            {{-- required যুক্ত করা হয়েছে --}}
                                            <input type="radio" name="payment_method" value="shurjopay" required>
                                            <div class="payment-content">
                                                <img src="{{ asset('public/frontEnd/images/shurjoPay.png') }}"
                                                    class="pay-logo" alt="ShurjoPay">
                                                <div class="pay-info">
                                                    <strong>Online Payment</strong>
                                                    <small>ShurjoPay (Card/Mobile Banking)</small>
                                                </div>
                                            </div>
                                            <div class="check-circle"></div>
                                        </label>
                                    @endif

                                    {{-- UddoktaPay --}}
                                    @if ($uddoktapay_gateway)
                                        <label class="payment-option-label">
                                            {{-- required যুক্ত করা হয়েছে --}}
                                            <input type="radio" name="payment_method" value="uddoktapay" required>
                                            <div class="payment-content">
                                                <img src="{{ asset('public/frontEnd/images/uddokta.png') }}"
                                                    class="pay-logo" alt="UddoktaPay">
                                                <div class="pay-info">
                                                    <strong>UddoktaPay</strong>
                                                    <small>মোবাইল ব্যাংকিং পেমেন্ট গেটওয়ে</small>
                                                </div>
                                            </div>
                                            <div class="check-circle"></div>
                                        </label>
                                    @endif

                                    {{-- aamarPay --}}
                                    @if ($aamarpay_gateway)
                                        <label class="payment-option-label">
                                            <input type="radio" name="payment_method" value="aamarpay" required>
                                            <div class="payment-content">
                                                <img src="{{ asset('public/frontEnd/images/aamarpay.png') }}"
                                                    class="pay-logo" alt="aamarPay"
                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="pay-info" style="display: none;">
                                                    <i class="fas fa-credit-card text-primary fs-4"></i>
                                                </div>
                                                <div class="pay-info">
                                                    <strong>aamarPay</strong>
                                                    <small>কার্ড ও মোবাইল ব্যাংকিং পেমেন্ট</small>
                                                </div>
                                            </div>
                                            <div class="check-circle"></div>
                                        </label>
                                    @endif

                                    @foreach ($manual_gateways ?? [] as $mg)
                                        <label class="payment-option-label">
                                            <input type="radio" name="payment_method"
                                                value="manual_{{ $mg->id }}" required>
                                            <div class="payment-content">
                                                @if ($mg->logo_asset_url)
                                                    <img src="{{ $mg->logo_asset_url }}" class="pay-logo"
                                                        alt="{{ $mg->title }}">
                                                @else
                                                    <div class="text-center" style="width: 40px;"><i
                                                            class="fas fa-money-check-alt text-primary fs-2"></i></div>
                                                @endif
                                                <div class="pay-info">
                                                    <strong>{{ $mg->title }}</strong>
                                                    <small>ম্যানুয়াল পেমেন্ট — নির্দেশনা অনুযায়ী টাকা পাঠিয়ে ট্রানজেকশন আইডি
                                                        দিন</small>
                                                </div>
                                            </div>
                                            <div class="check-circle"></div>
                                        </label>
                                    @endforeach

                                </div>
                                <div id="manual-payment-fields" class="mt-3 p-3 rounded-3 border border-warning bg-light"
                                    style="display:none;">
                                    <h6 class="fw-bold text-dark mb-2"><i
                                            class="fas fa-info-circle text-warning me-1"></i> ম্যানুয়াল পেমেন্ট নির্দেশনা
                                    </h6>
                                    <div id="manual-instructions-body" class="small text-secondary mb-3"
                                        style="white-space:pre-wrap;"></div>
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">ট্রানজেকশন আইডি / রেফারেন্স <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="manual_trx_id" id="manual_trx_id"
                                                class="form-control form-control-custom"
                                                value="{{ old('manual_trx_id') }}" maxlength="55" placeholder="TrxID">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">যে নম্বর থেকে পাঠিয়েছেন
                                                (ঐচ্ছিক)</label>
                                            <input type="text" name="manual_sender_number"
                                                class="form-control form-control-custom"
                                                value="{{ old('manual_sender_number') }}" maxlength="55"
                                                placeholder="01xxx">
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    window.MANUAL_GATEWAYS = @json(
                                        ($manual_gateways ?? collect())->map(fn($g) => [
                                                    'code' => 'manual_' . $g->id,
                                                    'instructions' => (string) ($g->instructions ?? ''),
                                                ])->values()->all());
                                </script>
                                {{-- Error message placeholder --}}
                                <div id="payment-error" class="text-danger fw-bold mt-2 text-center"
                                    style="display:none;">
                                    <i class="fas fa-exclamation-circle"></i> অনুগ্রহ করে একটি পেমেন্ট মেথড সিলেক্ট করুন।
                                </div>
                            </div>
                        </div>

                        {{-- MOBILE SUBMIT BUTTON (Only Visible on Mobile) --}}
                        <div class="mobile-submit-btn">
                            <button type="submit" class="btn-place-order">
                                অর্ডার নিশ্চিত করুন <i class="fas fa-arrow-right"></i>
                            </button>
                            <div class="text-center text-muted small mt-3">
                                <i class="fas fa-shield-alt"></i> ১০০% নিরাপদ এবং সিকিউর চেকআউট
                            </div>
                        </div>

                    </div>

                    {{-- RIGHT SIDE: Order Summary --}}
                    <div class="col-lg-5 col-md-12 checkout-summary-col" id="checkoutSummaryCol">
                        <div class="checkout-summary-sticky" id="checkoutSummarySticky">
                            <div class="checkout-card">
                                <div class="checkout-header">
                                    <i class="fas fa-shopping-bag"></i>
                                    <h6>অর্ডার সামারি ({{ Cart::instance('shopping')->count() }})</h6>
                                </div>

                                <div class="card-body-custom p-0">
                                    {{-- Products List (Scrollable) --}}
                                    <div class="cart-items-scroll px-4 pt-3 cartlist {{ Cart::instance('shopping')->count() ? '' : 'is-empty' }}"
                                        style="overflow-y: auto;">
                                        @foreach (Cart::instance('shopping')->content() as $value)
                                            <div class="checkout-item">
                                                {{-- Remove --}}
                                                <a class="remove-item-btn cart_remove" data-id="{{ $value->rowId }}"
                                                    title="Remove Item">
                                                    <i class="far fa-trash-alt"></i>
                                                </a>

                                                {{-- Image --}}
                                                <a href="{{ route('product', $value->options->slug) }}">
                                                    <img src="{{ asset($value->options->image) }}"
                                                        class="checkout-pro-img">
                                                </a>

                                                {{-- Info --}}
                                                <div class="checkout-pro-info flex-grow-1">
                                                    <a href="{{ route('product', $value->options->slug) }}"
                                                        class="text-dark text-decoration-none">
                                                        <h6>{{ Str::limit($value->name, 35) }}</h6>
                                                    </a>
                                                    <div class="meta text-muted small mb-1">
                                                        @if ($value->options->product_size)
                                                            Size: {{ $value->options->product_size }}
                                                        @endif
                                                        @if ($value->options->product_color)
                                                            | Color: {{ $value->options->product_color }}
                                                        @endif
                                                    </div>

                                                    {{-- Price & Qty --}}
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="qty-box checkout-qty"
                                                            data-rowid="{{ $value->rowId }}">
                                                            <button type="button" class="qty-btn minus"><i
                                                                    class="fas fa-minus"
                                                                    style="font-size:10px;"></i></button>
                                                            <span class="qty-val qty-value">{{ $value->qty }}</span>
                                                            <button type="button" class="qty-btn plus"><i
                                                                    class="fas fa-plus"
                                                                    style="font-size:10px;"></i></button>
                                                        </div>
                                                        <div class="fw-bold text-dark">৳
                                                            {{ number_format($value->price * $value->qty, 0) }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    {{-- COUPON SECTION --}}
                                    <div class="coupon-wrapper">
                                        @if (!Session::has('coupon_code'))
                                            <div class="coupon-group-modern">
                                                {{-- ভিজ্যুয়াল ইনপুট (এটি কোনো ফর্মের অংশ নয়, শুধু ডাটা নেওয়ার জন্য) --}}
                                                <input type="text" id="coupon_input" class="coupon-input-modern"
                                                    placeholder="কুপন কোড আছে? এখানে লিখুন...">
                                                <button type="button" class="coupon-btn-modern"
                                                    onclick="submitCoupon()">APPLY</button>
                                            </div>
                                        @else
                                            <div
                                                class="alert alert-success d-flex justify-content-between align-items-center m-0 py-3 px-3 border-0 rounded shadow-sm">
                                                <span><i class="fas fa-check-circle"></i> Coupon
                                                    <b>{{ Session::get('coupon_code') }}</b> Applied!</span>
                                                <a href="{{ route('coupon.remove') }}"
                                                    class="text-danger fw-bold text-decoration-none px-2">REMOVE</a>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Calculation --}}
                                    <div class="summary-totals">
                                        <div class="total-row"><span>সাবটোটাল</span> <span id="subtotalAmount">৳
                                                {{ number_format($subtotal, 2) }}</span></div>
                                        <div class="total-row"><span>ডেলিভারি চার্জ</span> <span id="shippingAmount">৳
                                                {{ number_format($shipping, 2) }}</span></div>
                                        @if ($discount > 0)
                                            <div class="total-row text-success"><span>কুপন ছাড়</span> <span
                                                    id="discountAmount">- ৳ {{ number_format($discount, 2) }}</span></div>
                                        @endif
                                        <div class="total-row final"><span>সর্বমোট</span> <span id="grandTotalAmount">৳
                                                {{ number_format($grand_total, 2) }}</span></div>

                                        @if ($hasAdvance)
                                            <div class="advance-alert">
                                                <div class="total-row text-success fw-bold"><span>অগ্রিম (পেইড):</span>
                                                    <span id="advanceAmountCell">৳
                                                        {{ number_format($advance_amount, 2) }}</span></div>
                                                <div class="total-row text-danger fw-bold mb-0"><span>বাকি (ডিউ):</span>
                                                    <span id="dueAmountCell">৳ {{ number_format($due_amount, 2) }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- DESKTOP SUBMIT BUTTON (Only Visible on Desktop) --}}
                                    <div class="desktop-submit-btn p-4">
                                        <button type="submit" class="btn-place-order">
                                            অর্ডার নিশ্চিত করুন <i class="fas fa-check-circle"></i>
                                        </button>
                                        <div class="text-center text-muted small mt-3">
                                            <i class="fas fa-lock"></i> ১০০% নিরাপদ চেকআউট প্রসেস
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>

            @if (!empty($__showCheckoutOtpModal))
                <form id="checkout_otp_resend_form" method="POST" action="{{ route('customer.ordersave') }}"
                    class="d-none">
                    @csrf
                    <input type="hidden" name="checkout_otp_resend" value="1">
                </form>
                <div class="modal fade" id="checkoutOtpModal" tabindex="-1" aria-labelledby="checkoutOtpModalLabel"
                    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg overflow-hidden">
                            <div class="modal-header text-white"
                                style="background: linear-gradient(135deg,#0f3460,#e94560);">
                                <h5 class="modal-title d-flex align-items-center gap-2 mb-0" id="checkoutOtpModalLabel">
                                    <i class="fas fa-mobile-alt"></i> OTP ভেরিফিকেশন
                                </h5>
                            </div>
                            <div class="modal-body p-4">
                                <p class="text-muted small mb-3">আপনার মোবাইল নম্বরে একটি <strong>৬ ডিজিটের OTP</strong>
                                    এসএমএসে পাঠানো হয়েছে। কোডটি লিখে নিচের বাটনে চাপ দিন।</p>
                                @error('checkout_otp')
                                    <div class="alert alert-danger py-2 small mb-3">{{ $message }}</div>
                                @enderror
                                <label class="form-label fw-semibold">OTP কোড</label>
                                <input type="text" id="checkout_otp_modal_field"
                                    class="form-control form-control-lg text-center letter-spacing-wide" maxlength="6"
                                    inputmode="numeric" autocomplete="one-time-code" placeholder="● ● ● ● ● ●"
                                    style="letter-spacing: 0.35em;" value="{{ $__otpFieldValue }}">
                                <div class="d-flex flex-wrap gap-2 mt-4 justify-content-between align-items-center">
                                    <button type="submit" form="checkout_otp_resend_form"
                                        class="btn btn-outline-secondary btn-sm" id="checkout_otp_resend_btn">OTP আবার
                                        পাঠান</button>
                                    <button type="button" class="btn btn-success px-4 fw-bold"
                                        id="checkout_otp_confirm_btn">অর্ডার সম্পূর্ণ করুন</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </section>
@endsection

@push('script')
    <script src="{{ asset('public/frontEnd/js/select2.min.js') }}"></script>

    <script>
        (function($) {
            var stickyEl, summaryCol, layoutRow, placeholder, ticking = false;
            var stickyDocTop = 0;

            function headerOffset() {
                var header = document.querySelector('header');
                return (header ? header.offsetHeight : 120) + 12;
            }

            function scrollY() {
                return window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
            }

            function measure() {
                if (!stickyEl) return;
                stickyEl.classList.remove('is-fixed', 'is-abs-bottom');
                stickyEl.style.top = '';
                stickyEl.style.left = '';
                stickyEl.style.width = '';
                var rect = stickyEl.getBoundingClientRect();
                stickyDocTop = rect.top + scrollY();
            }

            function resetSticky() {
                if (!stickyEl) return;
                stickyEl.classList.remove('is-fixed', 'is-abs-bottom');
                stickyEl.style.top = '';
                stickyEl.style.left = '';
                stickyEl.style.width = '';
                if (placeholder) placeholder.style.display = 'none';
            }

            function updateSticky() {
                ticking = false;
                if (!stickyEl || !summaryCol || !layoutRow || window.innerWidth < 992) {
                    resetSticky();
                    return;
                }

                var top = headerOffset();
                var sy = scrollY();
                var stickyH = stickyEl.offsetHeight;
                var rowRect = layoutRow.getBoundingClientRect();
                var rowDocTop = rowRect.top + sy;
                var rowDocBottom = rowDocTop + layoutRow.offsetHeight;
                var colRect = summaryCol.getBoundingClientRect();
                var fixStart = stickyDocTop - top;
                var fixEnd = rowDocBottom - stickyH - top;

                if (sy < fixStart) {
                    resetSticky();
                    return;
                }

                if (placeholder) {
                    placeholder.style.display = 'block';
                    placeholder.style.height = stickyH + 'px';
                }

                if (sy >= fixEnd) {
                    stickyEl.classList.remove('is-fixed');
                    stickyEl.classList.add('is-abs-bottom');
                    stickyEl.style.top = '';
                    stickyEl.style.left = '';
                    stickyEl.style.width = '';
                    return;
                }

                stickyEl.classList.remove('is-abs-bottom');
                stickyEl.classList.add('is-fixed');
                stickyEl.style.top = top + 'px';
                stickyEl.style.left = colRect.left + 'px';
                stickyEl.style.width = colRect.width + 'px';
            }

            function onScrollOrResize() {
                if (!ticking) {
                    ticking = true;
                    window.requestAnimationFrame(updateSticky);
                }
            }

            function initCheckoutSticky() {
                stickyEl = document.getElementById('checkoutSummarySticky');
                summaryCol = document.getElementById('checkoutSummaryCol');
                layoutRow = document.getElementById('checkoutLayoutRow');
                if (!stickyEl || !summaryCol || !layoutRow) return;

                if (window.innerWidth >= 992 && !placeholder) {
                    placeholder = document.createElement('div');
                    placeholder.className = 'checkout-summary-placeholder';
                    stickyEl.parentNode.insertBefore(placeholder, stickyEl.nextSibling);
                }

                measure();
                updateSticky();
            }

            function bindEvents() {
                $(window).on('scroll.checkoutSticky', onScrollOrResize);
                $(window).on('resize.checkoutSticky', function() {
                    if (window.innerWidth < 992 && placeholder) {
                        placeholder.style.display = 'none';
                    }
                    measure();
                    onScrollOrResize();
                });
                $(window).on('load.checkoutSticky', function() {
                    measure();
                    updateSticky();
                });
            }

            $(function() {
                initCheckoutSticky();
                bindEvents();
            });
        })(jQuery);
    </script>

    @if (!empty($__showCheckoutOtpModal))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var modalEl = document.getElementById('checkoutOtpModal');
                if (!modalEl) return;
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(modalEl, {
                        backdrop: 'static',
                        keyboard: false
                    }).show();
                } else if (window.jQuery && jQuery.fn.modal) {
                    jQuery(modalEl).modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    jQuery(modalEl).modal('show');
                }
                var modalInput = document.getElementById('checkout_otp_modal_field');
                if (modalInput) {
                    setTimeout(function() {
                        modalInput.focus();
                    }, 400);
                }
                var checkoutForm = document.getElementById('checkout-form');
                if (checkoutForm) {
                    checkoutForm.querySelectorAll('input[name="payment_method"]').forEach(function(el) {
                        el.disabled = true;
                    });
                    ['checkout_division', 'checkout_district', 'checkout_upazila'].forEach(function(id) {
                        var el = document.getElementById(id);
                        if (el) el.disabled = true;
                    });
                }
                document.getElementById('checkout_otp_confirm_btn').addEventListener('click', function() {
                    window.__checkoutOtpSkipCancel = true;
                    var raw = modalInput ? modalInput.value : '';
                    var otp = raw.replace(/\D/g, '').slice(0, 6);
                    if (otp.length !== 6) {
                        if (typeof toastr !== 'undefined') {
                            toastr.error('৬ ডিজিটের OTP কোড লিখুন।', 'Error');
                        } else {
                            alert('৬ ডিজিটের OTP কোড লিখুন।');
                        }
                        if (modalInput) modalInput.focus();
                        return;
                    }
                    document.getElementById('checkout_otp_hidden').value = otp;
                    var form = document.getElementById('checkout-form');
                    if (!form) return;
                    if (typeof isSubmitting !== 'undefined') isSubmitting = true;
                    if (typeof checkoutOtpPending !== 'undefined') checkoutOtpPending = false;
                    form.submit();
                });
            });
        </script>
    @endif

    {{-- ============================================================== --}}
    {{--  JAVASCRIPT LOGIC (EXACT COPY - NO FUNCTIONALITY REMOVED)  --}}
    {{-- ============================================================== --}}

    {{-- ========================================================= --}}
    {{--  🔴 এই অংশটুকু আপনার কোডে মিসিং ছিল, তাই কাজ করছিল না   --}}
    {{-- ========================================================= --}}

    {{-- হিডেন কুপন ফর্ম (এটি অবশ্যই মেইন ফর্মের বাইরে থাকতে হবে) --}}
    <form id="coupon-form" action="{{ route('coupon.apply') }}" method="POST" style="display:none;">
        @csrf
        <input type="hidden" name="coupon_code" id="hidden_coupon_code">
    </form>

    {{-- কুপন সাবমিট করার জাভাস্ক্রিপ্ট --}}
    <script>
        function submitCoupon() {
            var code = document.getElementById('coupon_input').value;
            if (code) {
                document.getElementById('hidden_coupon_code').value = code;
                document.getElementById('coupon-form').submit();
            } else {
                // টোস্টার থাকলে টোস্টার, নাহলে এলার্ট
                if (typeof toastr !== 'undefined') {
                    toastr.error('Please enter a coupon code');
                } else {
                    alert('Please enter a coupon code');
                }
            }
        }
    </script>
    <script>
        // গ্লোবাল ভেরিয়েবল (Global Variables)
        let incompleteOrderTimer;
        let isSubmitting = false; // অর্ডার সাবমিট হচ্ছে কিনা তা চেক করার জন্য
        let checkoutOtpPending = @json((bool) ($__custCheckoutOtpPending ?? false));

        $(document).ready(function() {
            // Select2 Initialize
            $(".select2").select2({
                width: '100%'
            });

            // ==========================================
            // 1. CART LOGIC (REMOVE, INCREASE, DECREASE)
            // ==========================================

            // Remove Item
            $(document).on('click', '.cart_remove', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                var id = $(this).data("id");
                if (id) {
                    $("#loading").show();
                    $.ajax({
                        type: "GET",
                        url: "{{ route('cart.remove') }}",
                        data: {
                            id: id
                        },
                        success: function() {
                            toastr.success('Success', 'Item removed');
                            window.location.reload();
                        },
                        error: function() {
                            window.location.reload();
                        }
                    });
                }
            });

            // Quantity Increment
            $('.checkout-qty .plus').on('click', function() {
                var rowId = $(this).closest('.checkout-qty').data('rowid');
                $("#loading").show();
                $.get("{{ route('cart.increment') }}", {
                    id: rowId
                }, function() {
                    window.location.reload();
                });
            });

            // Quantity Decrement
            $('.checkout-qty .minus').on('click', function() {
                var rowId = $(this).closest('.checkout-qty').data('rowid');
                $("#loading").show();
                $.get("{{ route('cart.decrement') }}", {
                    id: rowId
                }, function() {
                    window.location.reload();
                });
            });

            // ==========================================
            // 2. SHIPPING & TOTAL CALCULATION
            // ==========================================

            const baseSubtotal = parseFloat("{{ $subtotal ?? 0 }}");
            const baseDiscount = parseFloat("{{ $discount ?? 0 }}");
            const advanceAmount = parseFloat("{{ $advance_amount ?? 0 }}");
            const hasAdvance = @json($hasAdvance ?? false);
            const requiresShipping = @json($requires_shipping ?? false);
            const cartItems = @json($cartItemsForJs ?? []);
            const hasAllFreeDelivery = @json($hasAllFreeDelivery ?? false);

            // ⭐ Free Delivery Check Function
            function checkFreeDelivery() {
                // Check if all physical products have free_delivery = 1
                let allFreeDelivery = true;
                for (let i = 0; i < cartItems.length; i++) {
                    let item = cartItems[i];
                    // Skip digital products
                    if (item.is_digital == 1) {
                        continue;
                    }
                    // If any physical product doesn't have free_delivery, return false
                    if (item.free_delivery != 1) {
                        allFreeDelivery = false;
                        break;
                    }
                }
                return allFreeDelivery;
            }

            function districtChargeFromSelect() {
                if (!$('#checkout_district').length || !$('#checkout_district').val()) {
                    return 0;
                }
                return parseFloat($('#checkout_district option:selected').attr('data-charge')) || 0;
            }

            function applyShippingToDomAndSession() {
                var isFreeDelivery = checkFreeDelivery();
                var shippingCharge = isFreeDelivery ? 0 : districtChargeFromSelect();

                var grandTotal = baseSubtotal + shippingCharge - baseDiscount;
                var dueAmount = hasAdvance ? (grandTotal - advanceAmount) : 0;

                $('#shippingAmount').text('৳ ' + shippingCharge.toFixed(2));
                $('#grandTotalAmount').text('৳ ' + grandTotal.toFixed(2));

                if (hasAdvance) {
                    $('#dueAmountCell').text('৳ ' + dueAmount.toFixed(2));
                    $('#dueAmountText').text(dueAmount.toFixed(2));
                }

                if (!requiresShipping) {
                    return;
                }

                if (isFreeDelivery) {
                    $.get('{{ route('shipping.charge') }}', {
                        id: 'free_delivery'
                    });
                } else {
                    var did = $('#checkout_district').val();
                    if (did) {
                        $.get('{{ route('shipping.charge') }}', {
                            id: did
                        });
                    }
                }
            }

            $('#checkout_division').on('change', function() {
                var divId = $(this).val();
                $('#checkout_district').prop('disabled', !divId).html(divId ?
                    '<option value="">লোড হচ্ছে...</option>' :
                    '<option value="">আগে বিভাগ সিলেক্ট করুন</option>');
                $('#checkout_upazila').prop('disabled', true).html(
                    '<option value="">আগে জেলা সিলেক্ট করুন</option>');
                if (!divId) {
                    applyShippingToDomAndSession();
                    saveIncompleteOrder();
                    return;
                }
                $.get('{{ url('/ajax/delivery/districts') }}/' + divId, function(res) {
                    var opts = '<option value="">জেলা নির্বাচন করুন</option>';
                    (res.data || []).forEach(function(r) {
                        opts += '<option value="' + r.id + '" data-charge="' + r
                            .delivery_charge + '">' + r.name + ' (৳' + r.delivery_charge +
                            ')</option>';
                    });
                    $('#checkout_district').html(opts).prop('disabled', false);
                }).fail(function() {
                    $('#checkout_district').html('<option value="">লোড ব্যর্থ</option>');
                });
                applyShippingToDomAndSession();
                saveIncompleteOrder();
            });

            $('#checkout_district').on('change', function() {
                var distId = $(this).val();
                $('#checkout_upazila').prop('disabled', !distId).html(distId ?
                    '<option value="">লোড হচ্ছে...</option>' :
                    '<option value="">আগে জেলা সিলেক্ট করুন</option>');
                if (!distId) {
                    applyShippingToDomAndSession();
                    saveIncompleteOrder();
                    return;
                }
                applyShippingToDomAndSession();
                $.get('{{ url('/ajax/delivery/upazilas') }}/' + distId, function(res) {
                    var opts = '<option value="">উপজেলা নির্বাচন করুন</option>';
                    (res.data || []).forEach(function(r) {
                        opts += '<option value="' + r.id + '">' + r.name + '</option>';
                    });
                    $('#checkout_upazila').html(opts).prop('disabled', false);
                }).fail(function() {
                    $('#checkout_upazila').html('<option value="">লোড ব্যর্থ</option>');
                });
                saveIncompleteOrder();
            });

            $('#checkout_upazila').on('change', function() {
                saveIncompleteOrder();
            });

            // ⭐ পেজ লোড — ফ্রি ডেলিভারি / শিপিং সার্ফেস
            $(document).ready(function() {
                var isFreeDeliveryOnLoad = hasAllFreeDelivery || checkFreeDelivery();

                if (!requiresShipping) {
                    return;
                }

                if (isFreeDeliveryOnLoad) {
                    applyShippingToDomAndSession();
                } else {
                    var currentShipping = parseFloat($('#shippingAmount').text().replace(/[৳,\s]/g, '')
                        .trim()) || 0;
                    var grandTotal = baseSubtotal + currentShipping - baseDiscount;
                    var dueAmount = hasAdvance ? (grandTotal - advanceAmount) : 0;

                    $('#grandTotalAmount').text('৳ ' + grandTotal.toFixed(2));

                    if (hasAdvance) {
                        $('#dueAmountCell').text('৳ ' + dueAmount.toFixed(2));
                        $('#dueAmountText').text(dueAmount.toFixed(2));
                    }

                    var did = $('#checkout_district').val();
                    if (did) {
                        $.get('{{ route('shipping.charge') }}', {
                            id: did
                        });
                    }
                }
            });

            // ==========================================
            // 3. INCOMPLETE ORDER LOGIC (MAIN REQUEST)
            // ==========================================

            function selectedLocationText($sel) {
                if (!$sel.length || !$sel.val()) return '';
                return ($sel.find('option:selected').text() || '').replace(/\s*\(৳[^)]*\)\s*/g, '').trim();
            }

            function buildCheckoutAddress() {
                var street = ($('input[name="address"]').val() || '').trim();
                var parts = [];
                if (requiresShipping) {
                    var div = selectedLocationText($('#checkout_division'));
                    var dist = selectedLocationText($('#checkout_district'));
                    var upa = selectedLocationText($('#checkout_upazila'));
                    if (div) parts.push(div);
                    if (dist) parts.push(dist);
                    if (upa) parts.push(upa);
                }
                if (street) parts.unshift(street);
                return parts.join(', ');
            }

            function buildCheckoutMeta(shippingCharge) {
                var meta = {
                    subtotal: baseSubtotal,
                    discount: baseDiscount,
                    shipping_charge: shippingCharge,
                    order_note: ($('#order_note').val() || '').trim()
                };
                if (requiresShipping) {
                    meta.division_id = $('#checkout_division').val() || null;
                    meta.district_id = $('#checkout_district').val() || null;
                    meta.upazila_id = $('#checkout_upazila').val() || null;
                    var loc = [];
                    var div = selectedLocationText($('#checkout_division'));
                    var dist = selectedLocationText($('#checkout_district'));
                    var upa = selectedLocationText($('#checkout_upazila'));
                    if (upa) loc.push(upa);
                    if (dist) loc.push(dist);
                    if (div) loc.push(div);
                    meta.location_label = loc.join(', ');
                }
                return meta;
            }

            function saveIncompleteOrder() {
                if (isSubmitting || checkoutOtpPending) return;
                if (incompleteOrderTimer) clearTimeout(incompleteOrderTimer);

                incompleteOrderTimer = setTimeout(function() {
                    var name = ($('input[name="name"]').val() || '').trim();
                    var phone = ($('input[name="phone"]').val() || '').replace(/\D/g, '');
                    var address = buildCheckoutAddress();

                    if (!name || phone.length < 11) {
                        return;
                    }

                    if (!cartItems || !cartItems.length) {
                        return;
                    }

                    var isFreeDelivery = checkFreeDelivery();
                    var shippingCharge = isFreeDelivery ? 0 : districtChargeFromSelect();
                    var total = (baseSubtotal + shippingCharge - baseDiscount).toFixed(2);
                    var meta = buildCheckoutMeta(shippingCharge);

                    $.ajax({
                        url: '{{ route('incomplete.order.store') }}',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json'
                        },
                        data: {
                            name: name,
                            phone: phone,
                            address: address,
                            items: cartItems,
                            checkout_meta: meta,
                            total_amount: total,
                            product_image: cartItems[0] && cartItems[0].image ? cartItems[0].image :
                                '',
                            product_link: cartItems[0] && cartItems[0].link ? cartItems[0].link : ''
                        }
                    });
                }, 2000);
            }

            // ফর্মের যেকোনো ইনপুট চেঞ্জ হলে এই ফাংশন কল হবে
            $('#checkout-form input, #checkout-form select, #checkout-form textarea').on('input change',
        function() {
                if ($(this).attr('name') !== 'payment_method') {
                    saveIncompleteOrder();
                }
            });

            // ==========================================
            // 4. FORM SUBMISSION & VALIDATION
            // ==========================================

            $('#checkout-form').on('submit', function(e) {
                // পেমেন্ট মেথড চেক
                var paymentMethod = $('input[name="payment_method"]:checked').val();

                if (!paymentMethod) {
                    e.preventDefault();
                    toastr.error('অর্ডার সম্পন্ন করতে পেমেন্ট মেথড নির্বাচন করুন।', 'Error');
                    $('#payment-error').show();
                    $('html, body').animate({
                        scrollTop: $(".checkout-card .fa-wallet").offset().top - 150
                    }, 500);
                    $('.btn-place-order').prop('disabled', false);
                    return false;
                } else {
                    $('#payment-error').hide();

                    var pm = $('input[name="payment_method"]:checked').val() || '';
                    if (pm.indexOf('manual_') === 0) {
                        var trx = $('input[name="manual_trx_id"]').val();
                        if (!trx || !String(trx).trim()) {
                            e.preventDefault();
                            toastr.error('ম্যানুয়াল পেমেন্টের জন্য ট্রানজেকশন আইডি লিখুন।', 'Error');
                            $('#manual-payment-fields').show();
                            $('html, body').animate({
                                scrollTop: $('#manual-payment-fields').offset().top - 120
                            }, 400);
                            $('.btn-place-order').prop('disabled', false);
                            return false;
                        }
                    }

                    // ৩. অর্ডার সাবমিট হচ্ছে, তাই ইনকমপ্লিট টাইমার বন্ধ করে দেওয়া হলো
                    window.__checkoutOtpSkipCancel = true;
                    isSubmitting = true;
                    checkoutOtpPending = true;
                    if (incompleteOrderTimer) {
                        clearTimeout(incompleteOrderTimer);
                    }

                    // ফর্ম সাবমিট হতে দিন...
                }
            });

            // পেমেন্ট সিলেক্ট করলে এরর হাইড হবে
            function syncManualPaymentUi() {
                var v = $('input[name="payment_method"]:checked').val() || '';
                if (v.indexOf('manual_') === 0) {
                    $('#manual-payment-fields').show();
                    var inst = '';
                    (window.MANUAL_GATEWAYS || []).forEach(function(g) {
                        if (g.code === v) {
                            inst = g.instructions || '';
                        }
                    });
                    $('#manual-instructions-body').html($('<div/>').text(inst).html().replace(/\n/g, '<br>'));
                    $('#manual_trx_id').prop('required', true);
                } else {
                    $('#manual-payment-fields').hide();
                    $('#manual_trx_id').prop('required', false);
                }
            }

            $('input[name="payment_method"]').on('change', function() {
                $('#payment-error').hide();
                syncManualPaymentUi();
            });
            if (!$('input[name="payment_method"]:checked').length && $('input[name="payment_method"]').length) {
                $('input[name="payment_method"]:first').prop('checked', true);
            }
            syncManualPaymentUi();

            // চেকআউটে আগে থেকে তথ্য থাকলে একবার ইনকমপ্লিট সেভ ট্রিগার
            setTimeout(function() {
                saveIncompleteOrder();
            }, 2500);
        });
    </script>
    {{-- GTM + Facebook + TikTok — checkout funnel --}}
    <script type="text/javascript">
        (function() {
            if (typeof window.EcomTracking === 'undefined') return;

            var items = @json($cartItemsForJs);
            var hasAdvance = @json($hasAdvance);
            var advanceAmount = parseFloat("{{ $advance_amount }}") || 0;
            var grandTotal = parseFloat("{{ $grand_total }}") || 0;
            var payableNow = hasAdvance ? advanceAmount : grandTotal;
            var coupon = @json(Session::get('coupon_code', null));

            function checkoutUserFromForm() {
                return {
                    name: ($('input[name="name"]').val() || '').trim(),
                    phone: ($('input[name="phone"]').val() || '').trim(),
                    address: ($('input[name="address"]').val() || '').trim(),
                    city: ($('#checkout_district option:selected').text() || '').replace(/\s*\(৳[^)]*\)\s*/g, '').trim()
                };
            }

            if (items.length) {
                EcomTracking.initiateCheckout({
                    items: items,
                    value: payableNow,
                    coupon: coupon
                });
            }

            var identifyTimer;
            $('#checkout-form input[name="name"], #checkout-form input[name="phone"]').on('input blur', function() {
                clearTimeout(identifyTimer);
                identifyTimer = setTimeout(function() {
                    var u = checkoutUserFromForm();
                    if (u.phone && String(u.phone).replace(/\D/g, '').length >= 11) {
                        EcomTracking.identify(u);
                    }
                }, 800);
            });

            @auth('customer')
                EcomTracking.identify(@json(\App\Support\EcommerceTrackingUser::fromCustomer(auth('customer')->user())));
            @endauth

            var form = document.getElementById('checkout-form');
            if (form) {
                form.addEventListener('submit', function() {
                    var pm = form.querySelector('input[name="payment_method"]:checked');
                    EcomTracking.identify(checkoutUserFromForm());
                    EcomTracking.addPaymentInfo({
                        items: items,
                        value: payableNow,
                        coupon: coupon,
                        payment_method: pm ? pm.value : ''
                    });
                });
            }
        })();
    </script>
@endpush
