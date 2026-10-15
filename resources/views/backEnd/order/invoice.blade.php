@extends('backEnd.layouts.master')
@section('title', 'ইনভয়েস #' . $order->invoice_id)

@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<style>
    body { background: #eef1f8; }
    .invoice-view-shell { padding: 8px 0 32px; }

    .inv-page-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }
    .inv-page-header h4 { margin: 0; font-weight: 700; color: #0f172a; font-size: 1.35rem; }
    .inv-page-header .inv-sub { font-size: 13px; color: #64748b; margin-top: 4px; }
    .inv-header-actions { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }

    .inv-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid rgba(148, 163, 184, 0.28);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 16px;
        overflow: hidden;
    }
    .inv-card-head {
        padding: 14px 18px;
        border-bottom: 1px solid #e2e8f0;
        background: linear-gradient(180deg, #fafbff 0%, #fff 100%);
    }
    .inv-card-head h6 {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
    }
    .inv-card-head h6 i { color: #6366f1; margin-right: 6px; }
    .inv-card-body { padding: 16px 18px; }

    .inv-badge-invoice {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 999px;
    }
    .inv-badge-status {
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 999px;
        background: #fef3c7;
        color: #b45309;
    }
    .inv-badge-reseller {
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 999px;
        background: #fff7ed;
        color: #c2410c;
    }

    .inv-form-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #94a3b8;
        font-weight: 700;
        margin-bottom: 4px;
    }
    .inv-info-line {
        font-size: 13px;
        color: #334155;
        margin-bottom: 6px;
    }
    .inv-info-line strong { color: #0f172a; }

    .inv-panel-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 14px;
        margin-bottom: 14px;
    }
    .inv-panel-box:last-child { margin-bottom: 0; }

    .inv-btn-primary {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        border: none;
        color: #fff;
        font-weight: 600;
        padding: 8px 18px;
        border-radius: 999px;
        font-size: 13px;
    }
    .inv-btn-primary:hover { color: #fff; opacity: .95; }
    .inv-btn-outline {
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
        padding: 8px 16px;
    }

    /* ইনভয়েস ডকুমেন্ট */
    .inv-document {
        background: #fff;
        border-radius: 14px;
        border: 1px solid rgba(148, 163, 184, 0.28);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }
    .inv-doc-top {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 20px;
        padding: 24px 28px;
        border-bottom: 1px solid #e2e8f0;
        background: linear-gradient(135deg, #fafbff 0%, #fff 50%, #f0fdf4 100%);
    }
    .inv-doc-logo img { max-height: 56px; max-width: 200px; object-fit: contain; }
    .inv-doc-title {
        text-align: right;
        flex: 1 1 200px;
    }
    .inv-doc-title .inv-doc-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: #64748b;
        font-weight: 700;
    }
    .inv-doc-title h2 {
        margin: 4px 0 0;
        font-size: 1.75rem;
        font-weight: 800;
        color: #4f46e5;
        letter-spacing: -0.02em;
    }
    .inv-doc-meta {
        font-size: 13px;
        color: #475569;
        margin-top: 6px;
    }

    .inv-parties {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        padding: 20px 28px;
        border-bottom: 1px solid #e2e8f0;
    }
    @media (max-width: 575.98px) {
        .inv-parties { grid-template-columns: 1fr; }
    }
    .inv-party h6 {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #6366f1;
        font-weight: 700;
        margin-bottom: 10px;
    }
    .inv-party p {
        margin: 0 0 4px;
        font-size: 14px;
        color: #334155;
        line-height: 1.5;
    }

    .inv-items-table { margin: 0; }
    .inv-items-table thead { background: #f8fafc; }
    .inv-items-table th {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        font-weight: 600;
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }
    .inv-items-table td {
        padding: 12px 16px;
        font-size: 13px;
        color: #334155;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    .inv-items-table tbody tr:hover { background: #fafbff; }
    .inv-item-variant { font-size: 11px; color: #64748b; margin-top: 2px; }

    .inv-doc-footer {
        display: flex;
        flex-wrap: wrap;
        gap: 24px;
        justify-content: space-between;
        padding: 20px 28px 24px;
        align-items: flex-start;
    }
    .inv-summary {
        min-width: 280px;
        max-width: 340px;
        margin-left: auto;
    }
    .inv-summary-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        font-size: 14px;
        color: #475569;
        border-bottom: 1px dashed #e2e8f0;
    }
    .inv-summary-row strong { color: #0f172a; }
    .inv-summary-row.inv-total {
        border: none;
        margin-top: 8px;
        padding: 12px 14px;
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: #fff;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 700;
    }
    .inv-summary-row.inv-total strong { color: #fff; }
    .inv-summary-row.inv-reseller {
        background: #fff7ed;
        border-radius: 8px;
        padding: 8px 12px;
        margin-bottom: 8px;
        border: 1px solid #fed7aa;
        color: #9a3412;
    }

    .inv-terms {
        flex: 1 1 100%;
        text-align: center;
        padding-top: 16px;
        margin-top: 8px;
        border-top: 1px solid #e2e8f0;
        font-size: 13px;
        color: #64748b;
    }
    .inv-terms a { color: #4f46e5; font-weight: 600; }

    .pos-receipt { display: none; }

    @page { size: 80mm auto; margin: 3mm 4mm; }

    @media print {
        .navbar-custom, .left-side-menu, .right-bar,
        .invoice-view-shell, .inv-screen-wrap, .no-print,
        header, footer { display: none !important; }
        body { background: #fff !important; }
        #wrapper, .content-page, .content-page > .content {
            padding: 0 !important; margin: 0 !important;
        }
        .pos-receipt { display: block !important; }
        .pos-receipt * { font-family: 'Courier New', Courier, monospace; }
        .pos-receipt .rh { text-align: center; border-bottom: 1px solid #000; padding-bottom: 5px; margin-bottom: 5px; }
        .pos-receipt .rh .shop { font-size: 15px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        .pos-receipt .rh p { font-size: 10px; margin-top: 2px; }
        .pos-receipt .rt { text-align: center; font-size: 12px; font-weight: 700; border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 3px 0; margin: 4px 0; letter-spacing: 3px; }
        .pos-receipt .rm { font-size: 11px; margin-bottom: 3px; }
        .pos-receipt .fl { display: flex; justify-content: space-between; margin-bottom: 2px; }
        .pos-receipt table { width: 100%; border-collapse: collapse; font-size: 10px; margin: 4px 0; }
        .pos-receipt table thead th { border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 2px; font-weight: 700; text-align: left; }
        .pos-receipt table thead th.r { text-align: right; }
        .pos-receipt table tbody td { padding: 3px 2px; vertical-align: top; }
        .pos-receipt table tbody tr:last-child td { border-bottom: 1px solid #000; }
        .pos-receipt .rs { display: flex; justify-content: space-between; font-size: 11px; padding: 2px 0; }
        .pos-receipt .rtotal { display: flex; justify-content: space-between; font-size: 13px; font-weight: 700; border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 4px 0; margin: 3px 0; }
        .pos-receipt .rp { font-size: 11px; margin-top: 3px; }
        .pos-receipt .rp .fl { padding: 2px 0; margin-bottom: 0; }
        .pos-receipt .ptotal { display: flex; justify-content: space-between; font-size: 13px; font-weight: 700; border-top: 1px solid #000; padding-top: 4px; margin-top: 3px; }
        .pos-receipt .dash { border: none; border-top: 1px dashed #555; margin: 5px 0; }
        .pos-receipt .rf { text-align: center; border-top: 1px dashed #666; margin-top: 10px; padding-top: 7px; font-size: 12px; }
        .pos-receipt .rf .ty { font-size: 14px; font-weight: 700; }
        .pos-receipt .rf small { font-size: 9px; font-style: italic; margin-top: 3px; display: block; }
    }
</style>
@endsection

@section('content')
@php
    $invPay = $order->payment;
    $invPayStatus = optional($invPay)->payment_status ?? ($order->payment_status ?? 'pending');
    $payGateway = optional($invPay)->payment_method ?? ($order->payment_gateway ?? 'N/A');
    $statusName = optional($order->status)->name ?? 'N/A';
    $statusBadge = $order->order_status == 6 ? 'success' : ($order->order_status == 11 ? 'danger' : 'warning');

    $isResellerOrder = !empty($order->customer_payable_amount);
    $customPrice = null;
    $totalProductValue = 0;
    if ($isResellerOrder && $order->customer_payable_amount) {
        $customPrice = $order->customer_payable_amount - $order->shipping_charge;
        foreach ($order->orderdetails as $od) {
            $totalProductValue += ($od->sale_price * $od->qty);
        }
    }
    if ($isResellerOrder && $order->customer_payable_amount) {
        $subtotal = $order->customer_payable_amount - $order->shipping_charge;
    } else {
        $subtotal = 0;
        foreach ($order->orderdetails as $item) {
            $subtotal += ($item->sale_price * $item->qty);
        }
    }
    $shipping = $order->shipping_charge ?? 0;
    $discount = $order->discount ?? 0;
    $finalTotal = $isResellerOrder ? $order->customer_payable_amount : $order->amount;
    $advancePaid = \App\Models\Payment::where('order_id', $order->id)->sum('amount');
    $dueAmount = $finalTotal - $advancePaid;
    $orderNoteText = $order->order_note ?? $order->note ?? '';
@endphp

<div class="container-fluid invoice-view-shell inv-screen-wrap">

    <div class="inv-page-header no-print">
        <div>
            <h4>অর্ডার ইনভয়েস</h4>
            <div class="inv-sub">
                ইনভয়েস <strong>#{{ $order->invoice_id }}</strong>
                · স্ট্যাটাস: <span class="inv-badge-status">{{ $statusName }}</span>
                @if($isResellerOrder)
                    · <span class="inv-badge-reseller">রিসেলার অর্ডার</span>
                @endif
            </div>
        </div>
        <div class="inv-header-actions">
            <a href="{{ route('admin.orders', 'all') }}" class="btn btn-sm btn-light inv-btn-outline no-print">
                <i class="fas fa-arrow-left me-1"></i> অর্ডার তালিকা
            </a>
            <a href="{{ route('admin.order.process', $order->invoice_id) }}" class="btn btn-sm btn-outline-primary inv-btn-outline no-print">
                <i class="fas fa-cog me-1"></i> প্রসেস
            </a>
            <a href="{{ route('admin.order.edit', $order->invoice_id) }}" class="btn btn-sm btn-outline-secondary inv-btn-outline no-print">
                <i class="fas fa-edit me-1"></i> এডিট
            </a>
            <button type="button" onclick="printFunction()" class="btn btn-sm inv-btn-primary no-print">
                <i class="fas fa-print me-1"></i> প্রিন্ট (POS)
            </button>
        </div>
    </div>

    <div class="row g-3">
        {{-- বাম: কন্ট্রোল --}}
        <div class="col-lg-4 no-print">
            <div class="inv-card">
                <div class="inv-card-head">
                    <h6><i class="fas fa-credit-card"></i> পেমেন্ট</h6>
                </div>
                <div class="inv-card-body">
                    <div class="inv-panel-box">
                        <div class="inv-form-label">গেটওয়ে</div>
                        <p class="inv-info-line mb-2">{{ ucfirst($payGateway) }}</p>
                        <div class="inv-form-label">পেমেন্ট স্ট্যাটাস</div>
                        <div class="d-flex gap-2 flex-wrap align-items-center mt-1">
                            <select id="payment_status_{{ $order->id }}" class="form-select form-select-sm" style="max-width:140px;">
                                <option value="pending" {{ $invPayStatus == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ $invPayStatus == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="unpaid" {{ $invPayStatus == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                <option value="failed" {{ $invPayStatus == 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                            <button type="button" class="btn btn-sm btn-success" onclick="updatePaymentStatus({{ $order->id }})">
                                <i class="fas fa-check"></i> আপডেট
                            </button>
                        </div>
                        <div class="mt-2">
                            @include('backEnd.order.partials.manual_payment_verify_box', ['payment' => $invPay])
                        </div>
                    </div>

                    <div class="inv-panel-box">
                        <div class="inv-form-label">অর্ডার স্ট্যাটাস</div>
                        <p class="mb-2">
                            <span class="badge bg-{{ $statusBadge }}">{{ $statusName }}</span>
                        </p>
                        @if(isset($orderstatus) && $orderstatus->count())
                        <div class="d-flex gap-2 flex-wrap align-items-center">
                            <select id="order_status_{{ $order->id }}" class="form-select form-select-sm" style="min-width:140px;">
                                @foreach($orderstatus as $status)
                                    <option value="{{ $status->id }}" {{ $order->order_status == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-sm inv-btn-primary" onclick="updateOrderStatus({{ $order->id }})">
                                <i class="fas fa-save me-1"></i> আপডেট
                            </button>
                        </div>
                        @endif
                        @if($order->courier_type)
                        <small class="text-muted d-block mt-2">
                            <i class="fas fa-truck"></i> {{ ucfirst($order->courier_type) }}
                            @if($order->courier_tracking_id) · {{ $order->courier_tracking_id }}@endif
                        </small>
                        @endif
                    </div>
                </div>
            </div>

            <div class="inv-card">
                <div class="inv-card-head">
                    <h6><i class="fas fa-store"></i> দোকানের তথ্য</h6>
                </div>
                <div class="inv-card-body">
                    <p class="inv-info-line"><strong>{{ $generalsetting->name }}</strong></p>
                    @if($contact->phone)<p class="inv-info-line"><i class="fas fa-phone text-muted me-1"></i> {{ $contact->phone }}</p>@endif
                    @if($contact->email)<p class="inv-info-line"><i class="fas fa-envelope text-muted me-1"></i> {{ $contact->email }}</p>@endif
                    @if($orderNoteText)
                    <div class="inv-panel-box mt-3 mb-0">
                        <div class="inv-form-label">অর্ডার নোট</div>
                        <p class="inv-info-line mb-0">{{ $orderNoteText }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ডান: ইনভয়েস --}}
        <div class="col-lg-8">
            <div class="inv-document">
                <div class="inv-doc-top">
                    <div class="inv-doc-logo">
                        <img src="{{ asset($generalsetting->white_logo) }}" alt="{{ $generalsetting->name }}">
                    </div>
                    <div class="inv-doc-title">
                        <div class="inv-doc-label">Invoice</div>
                        <h2>ইনভয়েস</h2>
                        <div class="inv-doc-meta">
                            <span class="inv-badge-invoice">#{{ $order->invoice_id }}</span>
                            · {{ $order->created_at->format('d M Y, h:i A') }}
                        </div>
                    </div>
                </div>

                <div class="inv-parties">
                    <div class="inv-party">
                        <h6>ইনভয়েস থেকে</h6>
                        <p><strong>{{ $generalsetting->name }}</strong></p>
                        @if($contact->phone)<p>{{ $contact->phone }}</p>@endif
                        @if($contact->email)<p>{{ $contact->email }}</p>@endif
                        <p class="mt-2"><span class="inv-form-label d-block">পেমেন্ট</span>{{ ucfirst($payGateway) }} · {{ ucfirst($invPayStatus) }}</p>
                    </div>
                    <div class="inv-party" style="text-align:right;">
                        <h6>ইনভয়েস প্রাপক</h6>
                        <p><strong>{{ $order->shipping ? $order->shipping->name : '—' }}</strong></p>
                        @if($order->shipping && $order->shipping->phone)<p>{{ $order->shipping->phone }}</p>@endif
                        @if($order->shipping && $order->shipping->address)<p>{{ $order->shipping->address }}</p>@endif
                        @if($order->shipping && $order->shipping->area)<p>{{ $order->shipping->area }}</p>@endif
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table inv-items-table mb-0">
                        <thead>
                            <tr>
                                <th style="width:40px;">#</th>
                                <th>পণ্য</th>
                                <th class="text-end">দাম</th>
                                <th class="text-center">পরিমাণ</th>
                                <th class="text-end">সাবটোটাল</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderdetails as $value)
                            @php
                                if ($isResellerOrder && $customPrice && $totalProductValue > 0) {
                                    $thisProductValue = $value->sale_price * $value->qty;
                                    $thisProductShare = ($thisProductValue / $totalProductValue) * $customPrice;
                                    $displayPrice = $thisProductShare / $value->qty;
                                } else {
                                    $displayPrice = $value->sale_price;
                                }
                                $sizeDisplay = null;
                                if ($value->size) {
                                    $sizeDisplay = $value->size->sizeName ?? $value->size->size_name ?? $value->size->name ?? null;
                                } elseif ($value->product_size) {
                                    $s = \App\Models\Size::find($value->product_size);
                                    $sizeDisplay = $s ? ($s->sizeName ?? $s->size_name ?? null) : (!is_numeric($value->product_size) ? $value->product_size : null);
                                }
                                $displayColor = ($value->color && $value->color->name) ? $value->color->name : ($value->product_color ?: null);
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $value->product_name }}</strong>
                                    @if($sizeDisplay || $displayColor)
                                    <div class="inv-item-variant">
                                        @if($sizeDisplay) সাইজ: {{ $sizeDisplay }} @endif
                                        @if($sizeDisplay && $displayColor) · @endif
                                        @if($displayColor) রঙ: {{ $displayColor }} @endif
                                    </div>
                                    @endif
                                </td>
                                <td class="text-end">৳{{ number_format($displayPrice, 2) }}</td>
                                <td class="text-center">{{ $value->qty }}</td>
                                <td class="text-end fw-semibold">৳{{ number_format($displayPrice * $value->qty, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="inv-doc-footer">
                    <div class="inv-summary">
                        @if($isResellerOrder)
                        <div class="inv-summary-row inv-reseller">
                            <span><i class="fas fa-user-tag me-1"></i> রিসেলার অর্ডার</span>
                            <span></span>
                        </div>
                        @endif
                        <div class="inv-summary-row">
                            <span>সাবটোটাল</span>
                            <strong>৳{{ number_format($subtotal, 2) }}</strong>
                        </div>
                        <div class="inv-summary-row">
                            <span>ডেলিভারি (+)</span>
                            <strong>৳{{ number_format($shipping, 2) }}</strong>
                        </div>
                        <div class="inv-summary-row">
                            <span>ছাড় (−)</span>
                            <strong>৳{{ number_format($discount, 2) }}</strong>
                        </div>
                        <div class="inv-summary-row inv-total">
                            <span>{{ $isResellerOrder ? 'গ্রাহক প্রদেয়' : 'মোট পরিশোধ' }}</span>
                            <strong>৳{{ number_format($finalTotal, 2) }}</strong>
                        </div>
                        @if($advancePaid > 0 && $advancePaid < $finalTotal)
                        <div class="inv-summary-row">
                            <span>অগ্রিম পরিশোধ</span>
                            <strong>৳{{ number_format($advancePaid, 2) }}</strong>
                        </div>
                        <div class="inv-summary-row">
                            <span>বাকি</span>
                            <strong class="text-danger">৳{{ number_format($dueAmount, 2) }}</strong>
                        </div>
                        @endif
                    </div>
                    <div class="inv-terms">
                        <p class="mb-1"><a href="{{ route('page', ['slug' => 'terms-condition']) }}">Terms & Conditions</a></p>
                        <p class="mb-0 fst-italic">* কম্পিউটার জেনারেটেড ইনভয়েস — স্বাক্ষরের প্রয়োজন নেই।</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- POS RECEIPT (print only) --}}
@php
    $isRes   = !empty($order->customer_payable_amount);
    $sub     = 0;
    foreach ($order->orderdetails as $od) { $sub += ($od->sale_price * $od->qty); }
    if ($isRes && $order->customer_payable_amount) { $sub = $order->customer_payable_amount - $order->shipping_charge; }
    $ftotal  = $isRes ? $order->customer_payable_amount : $order->amount;
    $tqty    = $order->orderdetails->sum('qty');
    $pmethod = strtoupper(optional($order->payment)->payment_method ?? ($order->payment_gateway ?? 'N/A'));
    $pstatus = optional($order->payment)->payment_status ?? ($order->payment_status ?? 'pending');
    $adv     = \App\Models\Payment::where('order_id', $order->id)->sum('amount');
    $due     = $ftotal - $adv;
    $trkId   = $order->courier_tracking_id ?? $order->consignment_id ?? null;
    $courier = $order->courier_type ?? ($trkId ? 'steadfast' : null);
@endphp
<div class="pos-receipt">
    <div class="rh">
        <div class="shop">{{ $generalsetting->name }}</div>
        @if($contact->address)<p>{{ $contact->address }}</p>@endif
        @if($contact->phone)<p>Phone: {{ $contact->phone }}</p>@endif
        @if($contact->email)<p>{{ $contact->email }}</p>@endif
    </div>
    <div class="rt">POS Invoice</div>
    <div class="rm">
        <div class="fl">
            <span>Bill No. : <strong>{{ $order->invoice_id }}</strong></span>
            <span>{{ $order->created_at->format('H:i') }} hrs</span>
        </div>
        <div class="fl"><span>Date &nbsp;&nbsp;: <strong>{{ $order->created_at->format('d-m-Y') }}</strong></span></div>
        @if($order->shipping && $order->shipping->name)
        <div class="fl"><span>Buyer &nbsp;&nbsp;: <strong>{{ $order->shipping->name }}</strong></span></div>
        @endif
        @if($order->shipping && $order->shipping->phone)
        <div class="fl"><span>Phone &nbsp;&nbsp;: {{ $order->shipping->phone }}</span></div>
        @endif
        @if($order->shipping && ($order->shipping->address || $order->shipping->area))
        <div class="fl"><span>Address : {{ $order->shipping->address }}{{ $order->shipping->area ? ', '.$order->shipping->area : '' }}</span></div>
        @endif
    </div>
    <table>
        <thead>
            <tr>
                <th style="width:14px;">#</th>
                <th>Product</th>
                <th style="width:22px;text-align:center;">Qty</th>
                <th style="width:44px;" class="r">Rate</th>
                <th style="width:48px;" class="r">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderdetails as $key => $value)
            @php
                if ($isRes && $order->customer_payable_amount && $sub > 0) {
                    $tv = $value->sale_price * $value->qty;
                    $dp = (($tv / ($sub + $order->discount)) * $sub) / $value->qty;
                } else { $dp = $value->sale_price; }
                $szd = null;
                if ($value->size) { $szd = $value->size->sizeName ?? null; }
                elseif ($value->product_size) {
                    $sm  = \App\Models\Size::find($value->product_size);
                    $szd = $sm ? ($sm->sizeName ?? null) : (is_numeric($value->product_size) ? null : $value->product_size);
                }
                $cld = ($value->color && $value->color->colorName) ? $value->color->colorName : ((!is_numeric($value->product_color) && $value->product_color) ? $value->product_color : null);
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    <strong>{{ $value->product_name }}</strong>
                    @if($szd || $cld)<br><small>@if($szd)Sz:{{ $szd }}@endif @if($szd&&$cld)| @endif @if($cld){{ $cld }}@endif</small>@endif
                </td>
                <td style="text-align:center;">{{ $value->qty }}</td>
                <td style="text-align:right;">{{ number_format($dp,2) }}</td>
                <td style="text-align:right;">{{ number_format($dp*$value->qty,2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="rs"><span>Subtotal</span><span>{{ number_format($sub,2) }}</span></div>
    @if($order->discount > 0)<div class="rs"><span>Discount (–)</span><span>{{ number_format($order->discount,2) }}</span></div>@endif
    @if($order->shipping_charge > 0)<div class="rs"><span>Delivery (+)</span><span>{{ number_format($order->shipping_charge,2) }}</span></div>@endif
    <div class="rtotal">
        <span>Total &nbsp; {{ $tqty }} {{ $tqty>1?'Nos':'No' }}</span>
        <span>&#2547; {{ number_format($ftotal,2) }}</span>
    </div>
    <div class="rp">
        <div class="fl"><span>Method &nbsp;&nbsp;:</span><span><strong>{{ $pmethod }}</strong></span></div>
        <div class="fl"><span>Pay Status :</span><span><strong>{{ strtoupper($pstatus) }}</strong></span></div>
        @if($adv > 0 && $adv < $ftotal)
        <div class="fl"><span>Advance &nbsp;&nbsp;:</span><span>&#2547; {{ number_format($adv,2) }}</span></div>
        <div class="fl"><span>Due &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</span><span><strong>&#2547; {{ number_format($due,2) }}</strong></span></div>
        @endif
        @if($courier)
        <hr class="dash">
        <div class="fl"><span>Courier &nbsp;&nbsp;:</span><span><strong>{{ ucfirst($courier) }}</strong></span></div>
        @if($trkId)<div class="fl"><span>Tracking &nbsp;:</span><span>{{ $trkId }}</span></div>@endif
        @if($order->courier_sent_at)<div class="fl"><span>Sent &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</span><span>{{ \Carbon\Carbon::parse($order->courier_sent_at)->format('d M Y') }}</span></div>@endif
        @endif
        <div class="ptotal"><span>Total Paid</span><span>&#2547; {{ number_format($ftotal,2) }}</span></div>
    </div>
    <hr class="dash">
    <div class="rs"><span>Order Status :</span><span><strong>{{ $order->status ? $order->status->name : 'Processing' }}</strong></span></div>
    <div class="rf">
        <div class="ty">Thank You!</div>
        <div>Visit Again!</div>
        <small>* Computer generated invoice. No signature required.</small>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
function printFunction() {
    window.print();
}

function updatePaymentStatus(orderId) {
    var status = document.getElementById('payment_status_' + orderId).value;
    fetch('{{ route("admin.order.updatePaymentStatus") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ order_id: orderId, payment_status: status })
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
        if (data.status === 'success') {
            if (typeof toastr !== 'undefined') toastr.success(data.message, 'সফল');
        } else {
            if (typeof toastr !== 'undefined') toastr.error(data.message, 'ত্রুটি');
        }
    })
    .catch(function () {
        if (typeof toastr !== 'undefined') toastr.error('কিছু একটা ভুল হয়েছে!', 'ত্রুটি');
    });
}

function updateOrderStatus(orderId) {
    var status = document.getElementById('order_status_' + orderId).value;
    if (!status) {
        if (typeof toastr !== 'undefined') toastr.warning('স্ট্যাটাস সিলেক্ট করুন', 'সতর্কতা');
        return;
    }
    if (!confirm('অর্ডার স্ট্যাটাস পরিবর্তন করতে চান? কুরিয়ার অটো-আপডেট ওভাররাইড হতে পারে।')) {
        return;
    }
    fetch('{{ route("admin.order.updateSingleStatus") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ order_id: orderId, order_status: status })
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
        if (data.status === 'success') {
            if (typeof toastr !== 'undefined') toastr.success(data.message, 'সফল');
            setTimeout(function () { location.reload(); }, 1000);
        } else {
            if (typeof toastr !== 'undefined') toastr.error(data.message, 'ত্রুটি');
        }
    })
    .catch(function () {
        if (typeof toastr !== 'undefined') toastr.error('কিছু একটা ভুল হয়েছে!', 'ত্রুটি');
    });
}
</script>
@endsection
