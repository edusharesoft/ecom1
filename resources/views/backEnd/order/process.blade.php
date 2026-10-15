@extends('backEnd.layouts.master')
@php
    $shipRow = $data->shipping ?? null;
    $admSelDiv = (int) ($shipRow->division_id ?? old('division_id', 0));
    $admSelDist = (int) ($shipRow->district_id ?? old('district_id', 0));
    $admSelUp = (int) ($shipRow->upazila_id ?? 0);
    $processDistricts = collect($districts ?? []);
    $processUpazilas = collect($upazilas ?? []);
    $selDelBoy = (int) ($data->delivery_boy_id ?? 0);

    $isResellerOrder = !empty($data->customer_payable_amount);
    $customPrice = null;
    $totalProductValue = 0;
    if ($isResellerOrder && $data->customer_payable_amount) {
        $customPrice = $data->customer_payable_amount - ($data->shipping_charge ?? 0);
        foreach ($data->orderdetails as $od) {
            $totalProductValue += ($od->sale_price * $od->qty);
        }
    }

    if ($isResellerOrder && $data->customer_payable_amount) {
        $subtotal = $data->customer_payable_amount - ($data->shipping_charge ?? 0);
    } else {
        $subtotal = $data->orderdetails->sum(fn ($item) => $item->sale_price * $item->qty);
    }
    $shipping = $data->shipping_charge ?? 0;
    $discount = $data->discount ?? 0;
    $finalTotal = $isResellerOrder ? ($data->customer_payable_amount ?? $data->amount) : $data->amount;

    $payRelation = $data->payment;
    $payGateway = optional($payRelation)->payment_method;
    $payStatusVal = optional($payRelation)->payment_status ?? ($data->payment_status ?? 'pending');
    $currentStatusName = optional($data->status)->name ?? 'N/A';
@endphp
@section('title', 'Order Process #' . $data->invoice_id)

@section('css')
<style>
    body { background: #eef1f8; }
    .order-process-shell { padding: 8px 0 28px; }

    .op-page-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }
    .op-page-header h4 { margin: 0; font-weight: 700; color: #0f172a; font-size: 1.35rem; }
    .op-page-header .op-sub { font-size: 13px; color: #64748b; margin-top: 2px; }
    .op-header-actions { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }

    .op-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid rgba(148, 163, 184, 0.28);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 16px;
        overflow: hidden;
    }
    .op-card-head {
        padding: 14px 18px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        background: linear-gradient(180deg, #fafbff 0%, #fff 100%);
    }
    .op-card-head h6 {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
    }
    .op-card-head h6 i { color: #6366f1; margin-right: 6px; }
    .op-card-body { padding: 16px 18px; }

    .op-badge-invoice {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 999px;
    }
    .op-badge-status {
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 999px;
        background: #fef3c7;
        color: #b45309;
    }
    .op-badge-reseller {
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 999px;
        background: #fff7ed;
        color: #c2410c;
    }

    .op-items-table thead { background: #f8fafc; }
    .op-items-table th {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        font-weight: 600;
        border-bottom: 1px solid #e2e8f0;
        padding: 10px 12px;
        white-space: nowrap;
    }
    .op-items-table td {
        vertical-align: middle;
        padding: 10px 12px;
        font-size: 13px;
        color: #334155;
    }
    .op-product-img {
        width: 48px;
        height: 48px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }
    .op-sl-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border-radius: 8px;
        background: #eef2ff;
        color: #4f46e5;
        font-size: 12px;
        font-weight: 700;
    }

    .op-summary-grid {
        display: grid;
        gap: 10px;
    }
    .op-summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 14px;
        color: #475569;
        padding: 4px 0;
    }
    .op-summary-row.op-total {
        border-top: 2px dashed #e2e8f0;
        margin-top: 8px;
        padding-top: 12px;
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
    }
    .op-summary-row.op-total .op-val { color: #16a34a; font-size: 18px; }
    .op-summary-row .op-val-discount { color: #dc2626; }

    .op-form-label {
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 5px;
    }
    .op-form-label i { color: #94a3b8; width: 16px; margin-right: 4px; }
    .op-input-group .form-control,
    .op-input-group .form-select {
        border-radius: 8px;
        border-color: #cbd5e1;
        font-size: 13px;
    }
    .op-input-group .form-control:focus,
    .op-input-group .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }

    .op-section-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #94a3b8;
        font-weight: 700;
        margin-bottom: 10px;
        margin-top: 4px;
    }

    .op-payment-panel {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 16px;
    }
    .op-gateway-pill {
        display: inline-block;
        background: #e0e7ff;
        color: #3730a3;
        font-weight: 600;
        font-size: 12px;
        padding: 4px 12px;
        border-radius: 999px;
        text-transform: uppercase;
    }

    .btn-op-primary {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        border: none;
        color: #fff;
        font-weight: 600;
        padding: 10px 28px;
        border-radius: 999px;
        box-shadow: 0 10px 22px rgba(79, 70, 229, 0.35);
    }
    .btn-op-primary:hover { color: #fff; transform: translateY(-1px); box-shadow: 0 14px 28px rgba(79, 70, 229, 0.45); }
    .btn-op-outline { border-radius: 999px; font-size: 13px; font-weight: 600; }

    .op-alert-reseller {
        background: #fff7ed;
        border: 1px solid #fed7aa;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 13px;
        color: #9a3412;
        margin-bottom: 12px;
    }

    .op-card:last-child { margin-bottom: 0; }

    .op-items-table-wrap {
        margin: 0;
        padding: 0;
    }
    .op-items-table-wrap .table-responsive {
        min-height: 0 !important;
        height: auto !important;
    }
    .order-process-shell form > .row.align-items-start > [class*="col-"] {
        align-self: flex-start;
    }

    @media (min-width: 992px) {
        .op-sidebar-sticky {
            position: sticky;
            top: 80px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .op-sidebar-sticky .op-card { margin-bottom: 0; }
    }
</style>
<link href="{{ asset('public/backEnd') }}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
@endsection

@section('content')
<div class="container-fluid order-process-shell">

    <div class="op-page-header">
        <div>
            <h4>অর্ডার প্রসেসিং</h4>
            <div class="op-sub">
                ইনভয়েস <strong>#{{ $data->invoice_id }}</strong>
                · স্ট্যাটাস: <span class="op-badge-status">{{ $currentStatusName }}</span>
                @if($isResellerOrder)
                    · <span class="op-badge-reseller">রিসেলার অর্ডার</span>
                @endif
            </div>
        </div>
        <div class="op-header-actions">
            <a href="{{ route('admin.order.edit', $data->invoice_id) }}" class="btn btn-sm btn-outline-primary btn-op-outline">
                <i class="fas fa-edit me-1"></i> এডিট
            </a>
            <a href="{{ route('admin.order.invoice', $data->invoice_id) }}" class="btn btn-sm btn-light btn-op-outline" target="_blank">
                <i class="fas fa-file-invoice me-1"></i> ইনভয়েস
            </a>
            <a href="{{ route('admin.orders', 'pending') }}" class="btn btn-sm btn-outline-secondary btn-op-outline">
                <i class="fas fa-arrow-left me-1"></i> অর্ডার তালিকা
            </a>
        </div>
    </div>

    <div class="op-card op-card-items mb-3">
        <div class="op-card-head">
                    <h6><i class="fas fa-box-open"></i> অর্ডার আইটেম</h6>
                    <span class="op-badge-invoice">{{ $data->orderdetails->count() }} টি পণ্য</span>
                </div>
                <div class="op-card-body p-0 op-items-table-wrap">
                    <div class="table-responsive">
                        <table class="table table-hover op-items-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width:48px;">#</th>
                                    <th style="width:60px;"></th>
                                    <th>পণ্য</th>
                                    <th>রঙ</th>
                                    <th>সাইজ</th>
                                    <th class="text-end">দাম</th>
                                    <th class="text-center">পরিমাণ</th>
                                    <th class="text-end">সাবটোটাল</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data->orderdetails as $key => $product)
                                @php
                                    if ($isResellerOrder && $customPrice && $totalProductValue > 0) {
                                        $thisProductValue = $product->sale_price * $product->qty;
                                        $thisProductShare = ($thisProductValue / $totalProductValue) * $customPrice;
                                        $displayPrice = $thisProductShare / $product->qty;
                                    } else {
                                        $displayPrice = $product->sale_price;
                                    }
                                    $sizeDisplay = 'N/A';
                                    if ($product->size) {
                                        $sizeDisplay = $product->size->sizeName ?? $product->size->size_name ?? $product->size->name ?? 'N/A';
                                    } elseif ($product->product_size) {
                                        $s = \App\Models\Size::find($product->product_size);
                                        if ($s) {
                                            $sizeDisplay = $s->sizeName ?? $s->size_name ?? 'N/A';
                                        } elseif (!is_numeric($product->product_size)) {
                                            $sizeDisplay = $product->product_size;
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td><span class="op-sl-badge">{{ $key + 1 }}</span></td>
                                    <td>
                                        <img class="op-product-img" src="{{ asset($product->image->image ?? 'public/no-image.png') }}" alt="">
                                    </td>
                                    <td><strong>{{ $product->product_name }}</strong></td>
                                    <td>{{ ($product->color && $product->color->name) ? $product->color->name : ($product->product_color ?: 'N/A') }}</td>
                                    <td>{{ $sizeDisplay }}</td>
                                    <td class="text-end">৳{{ number_format($displayPrice, 2) }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border">{{ $product->qty }}</span>
                                    </td>
                                    <td class="text-end fw-semibold">৳{{ number_format($displayPrice * $product->qty, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
    </div>

    {{-- প্রসেস ফর্ম --}}
    <form action="{{ route('admin.order_change') }}" method="POST" data-parsley-validate name="editForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{ $data->id }}">

        <div class="row g-3 align-items-start">
            <div class="col-lg-8">
                <div class="op-card">
                    <div class="op-card-head">
                        <h6><i class="fas fa-user"></i> গ্রাহক ও ডেলিভারি</h6>
                    </div>
                    <div class="op-card-body">
                        <div class="row g-3">
                            <div class="col-md-6 op-input-group">
                                <label class="op-form-label" for="name"><i class="fas fa-user"></i> গ্রাহকের নাম</label>
                                <input type="text" id="name" class="form-control" name="name" value="{{ $shipRow->name ?? '' }}" placeholder="নাম" required>
                            </div>
                            <div class="col-md-6 op-input-group">
                                <label class="op-form-label" for="phone"><i class="fas fa-phone"></i> মোবাইল</label>
                                <input type="text" id="phone" class="form-control" name="phone" value="{{ $shipRow->phone ?? '' }}" placeholder="01XXXXXXXXX" required>
                            </div>
                            <div class="col-12 op-input-group">
                                <label class="op-form-label" for="address"><i class="fas fa-map-marker-alt"></i> ঠিকানা</label>
                                <textarea id="address" name="address" class="form-control" rows="2" placeholder="বিস্তারিত ঠিকানা" required>{{ $shipRow->address ?? '' }}</textarea>
                            </div>
                        </div>

                        <div class="op-section-label mt-2">ডেলিভারি লোকেশন</div>
                        <div class="row g-3">
                            <div class="col-12 op-input-group">
                                <label class="op-form-label" for="adm_process_division">বিভাগ</label>
                                <select name="division_id" id="adm_process_division" class="form-select" required>
                                    <option value="">বিভাগ নির্বাচন করুন</option>
                                    @foreach(($divisions ?? []) as $d)
                                        <option value="{{ $d->id }}" {{ $admSelDiv === (int) $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 op-input-group">
                                <label class="op-form-label" for="adm_process_district">জেলা</label>
                                <select name="district_id" id="adm_process_district" class="form-select" required>
                                    <option value="">{{ $admSelDiv ? 'জেলা নির্বাচন করুন' : 'আগে বিভাগ সিলেক্ট করুন' }}</option>
                                    @foreach($processDistricts->where('division_id', $admSelDiv) as $district)
                                        <option value="{{ $district->id }}" {{ $admSelDist === (int) $district->id ? 'selected' : '' }}>
                                            {{ $district->name }} (৳{{ $district->delivery_charge }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 op-input-group">
                                <label class="op-form-label" for="adm_process_upazila">উপজেলা</label>
                                <select name="upazila_id" id="adm_process_upazila" class="form-select" required>
                                    <option value="">{{ $admSelDist ? 'উপজেলা নির্বাচন করুন' : 'আগে জেলা সিলেক্ট করুন' }}</option>
                                    @foreach($processUpazilas->where('district_id', $admSelDist) as $upazila)
                                        <option value="{{ $upazila->id }}" {{ $admSelUp === (int) $upazila->id ? 'selected' : '' }}>
                                            {{ $upazila->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="op-section-label mt-3">ডেলিভারি ম্যান</div>
                        <div class="op-input-group">
                            <label class="op-form-label" for="delivery_boy_id"><i class="fas fa-motorcycle"></i> রাইডার অ্যাসাইন</label>
                            <select name="delivery_boy_id" id="delivery_boy_id" class="form-select">
                                <option value="">— অ্যাসাইন করা হয়নি —</option>
                                @foreach(($deliveryBoys ?? []) as $b)
                                    <option value="{{ $b->id }}" @selected($selDelBoy === (int) $b->id)>
                                        {{ $b->name }} — {{ $b->phone }} (৳{{ number_format($b->commission_per_delivery, 0) }}/ডেলিভারি)
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">রাইডার অ্যাপ: <code>/delivery/login</code></small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="op-sidebar-sticky">
                    <div class="op-card">
                        <div class="op-card-head">
                            <h6><i class="fas fa-receipt"></i> অর্ডার সারাংশ</h6>
                        </div>
                        <div class="op-card-body">
                            @if($isResellerOrder)
                            <div class="op-alert-reseller">
                                <i class="fas fa-user-tag me-1"></i> <strong>রিসেলার অর্ডার</strong>
                            </div>
                            @endif
                            <div class="op-summary-grid">
                                <div class="op-summary-row">
                                    <span>সাবটোটাল</span>
                                    <span class="op-val">৳{{ number_format($subtotal, 2) }}</span>
                                </div>
                                <div class="op-summary-row">
                                    <span>ডেলিভারি</span>
                                    <span class="op-val">৳{{ number_format($shipping, 2) }}</span>
                                </div>
                                <div class="op-summary-row">
                                    <span>ছাড়</span>
                                    <span class="op-val op-val-discount">−৳{{ number_format($discount, 2) }}</span>
                                </div>
                                <div class="op-summary-row op-total">
                                    <span>{{ $isResellerOrder ? 'গ্রাহক প্রদেয়' : 'মোট' }}</span>
                                    <span class="op-val">৳{{ number_format($finalTotal, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="op-card">
                        <div class="op-card-head">
                            <h6><i class="fas fa-credit-card"></i> পেমেন্ট</h6>
                        </div>
                        <div class="op-card-body op-payment-panel">
                            <div class="mb-3">
                                <div class="op-form-label">গেটওয়ে</div>
                                @if(!empty($payGateway))
                                    <span class="op-gateway-pill">{{ strtoupper($payGateway) }}</span>
                                @else
                                    <span class="text-danger small">পাওয়া যায়নি</span>
                                @endif
                            </div>
                            <div>
                                <label class="op-form-label">স্ট্যাটাস</label>
                                <div class="d-flex gap-2 flex-wrap">
                                    <select id="payment_status_{{ $data->id }}" class="form-select form-select-sm">
                                        <option value="pending" {{ $payStatusVal == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="paid" {{ $payStatusVal == 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="unpaid" {{ $payStatusVal == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                        <option value="failed" {{ $payStatusVal == 'failed' ? 'selected' : '' }}>Failed</option>
                                    </select>
                                    <button type="button" class="btn btn-success btn-sm" onclick="updatePaymentStatus({{ $data->id }})">
                                        <i class="fa fa-check"></i> আপডেট
                                    </button>
                                </div>
                            </div>
                            @include('backEnd.order.partials.manual_payment_verify_box', ['payment' => $payRelation])
                        </div>
                    </div>

                    <div class="op-card">
                        <div class="op-card-head">
                            <h6><i class="fas fa-tasks"></i> অর্ডার স্ট্যাটাস</h6>
                        </div>
                        <div class="op-card-body">
                            <div class="op-input-group mb-3">
                                <label class="op-form-label" for="order_status_select"><i class="fas fa-flag"></i> স্ট্যাটাস</label>
                                <select id="order_status_select" class="form-select select2-multiple" name="status" data-toggle="select2" required>
                                    <option value="">নির্বাচন করুন</option>
                                    @foreach($orderstatus as $value)
                                        <option value="{{ $value->id }}" @if($data->order_status == $value->id) selected @endif>{{ $value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-op-primary">
                                    <i class="fas fa-save me-2"></i> অর্ডার আপডেট করুন
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="{{ asset('public/backEnd') }}/assets/libs/select2/js/select2.min.js"></script>
<script>
(function () {
    var admSelDiv = {{ $admSelDiv }};
    var admSelDist = {{ $admSelDist }};
    var admSelUp = {{ $admSelUp }};
    var allDistricts = @json($processDistricts->values());
    var allUpazilas = @json($processUpazilas->values());

    function esc(value) {
        return String(value == null ? '' : value)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    function fillDistricts(divId, preselect, then) {
        var $d = $('#adm_process_district');
        divId = parseInt(divId || 0, 10);
        $('#adm_process_upazila').html('<option value="">আগে জেলা সিলেক্ট করুন</option>').prop('disabled', false);
        if (!divId) {
            $d.html('<option value="">আগে বিভাগ সিলেক্ট করুন</option>').prop('disabled', false);
            if (then) then();
            return;
        }
        var opts = '<option value="">জেলা নির্বাচন করুন</option>';
        allDistricts.filter(function (r) { return parseInt(r.division_id, 10) === divId; })
            .forEach(function (r) {
                opts += '<option value="' + esc(r.id) + '">' + esc(r.name) + ' (৳' + esc(r.delivery_charge) + ')</option>';
            });
        $d.html(opts).prop('disabled', false);
        if (preselect) $d.val(String(preselect));
        if (then) then();
        if ($d.find('option').length <= 1) {
            $d.html('<option value="">এই বিভাগের কোনো জেলা নেই</option>');
        }
    }

    function fillUpazilas(distId, preselect, then) {
        var $u = $('#adm_process_upazila');
        distId = parseInt(distId || 0, 10);
        if (!distId) {
            $u.html('<option value="">আগে জেলা সিলেক্ট করুন</option>').prop('disabled', false);
            if (then) then();
            return;
        }
        var opts = '<option value="">উপজেলা নির্বাচন করুন</option>';
        allUpazilas.filter(function (r) { return parseInt(r.district_id, 10) === distId; })
            .forEach(function (r) {
                opts += '<option value="' + esc(r.id) + '">' + esc(r.name) + '</option>';
            });
        $u.html(opts).prop('disabled', false);
        if (preselect) $u.val(String(preselect));
        if (then) then();
        if ($u.find('option').length <= 1) {
            $u.html('<option value="">এই জেলার কোনো উপজেলা নেই</option>');
        }
    }

    $(function () {
        if ($.fn.select2) {
            $('.select2-multiple').select2({ width: '100%' });
        }
        $('#adm_process_division').on('change', function () {
            fillDistricts($(this).val(), null, null);
        });
        $('#adm_process_district').on('change', function () {
            fillUpazilas($(this).val(), null, null);
        });
        if (admSelDiv) {
            fillDistricts(admSelDiv, admSelDist || null, function () {
                if (admSelDist) fillUpazilas(admSelDist, admSelUp || null, null);
            });
        }
    });
})();

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
</script>
@endsection
