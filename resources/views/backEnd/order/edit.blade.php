@extends('backEnd.layouts.master')
@php
    $editDistricts = collect($districts ?? []);
    $editUpazilas = collect($upazilas ?? []);
    $shipLoc = $shippinginfo;
    $admEdDiv = (int) ($shipLoc->division_id ?? 0);
    $admEdDist = (int) ($shipLoc->district_id ?? 0);
    $admEdUp = (int) ($shipLoc->upazila_id ?? 0);

    $subtotal = Cart::instance('pos_shopping')->subtotal();
    $subtotal = str_replace([',', '.00'], '', $subtotal);
    $shipping = Session::get('pos_shipping');
    $lineProductDiscount = 0;
    foreach (Cart::instance('pos_shopping')->content() as $cartLine) {
        $lineProductDiscount += (float) ($cartLine->options->product_discount ?? 0) * $cartLine->qty;
    }
    Session::put('product_discount', $lineProductDiscount);
    $total_discount = (float) Session::get('pos_discount', 0) + $lineProductDiscount;
    $total = ($subtotal + $shipping) - $total_discount;
    $paidAmount = \App\Models\Payment::where('order_id', $order->id)->sum('amount');
    $advancePaid = 0;
    $dueAmount = $total;
    if ($paidAmount > 0 && $paidAmount < $total) {
        $advancePaid = $paidAmount;
        $dueAmount = $total - $advancePaid;
    }
    $posPay = $order->payment;
    $posPayStatus = optional($posPay)->payment_status ?? ($order->payment_status ?? 'pending');
    $statusName = optional($order->status)->name ?? 'N/A';
@endphp
@section('title', 'Edit Order #' . $order->invoice_id)

@section('css')
<style>
    body { background: #eef1f8; }
    .order-edit-shell { padding: 8px 0 28px; }

    .oe-page-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }
    .oe-page-header h4 {
        margin: 0;
        font-weight: 700;
        color: #0f172a;
        font-size: 1.35rem;
    }
    .oe-page-header .oe-sub {
        font-size: 13px;
        color: #64748b;
        margin-top: 2px;
    }
    .oe-header-actions { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }

    .oe-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid rgba(148, 163, 184, 0.28);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        margin-bottom: 16px;
        overflow: hidden;
    }
    .oe-card-head {
        padding: 14px 18px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        background: linear-gradient(180deg, #fafbff 0%, #fff 100%);
    }
    .oe-card-head h6 {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
        letter-spacing: 0.02em;
    }
    .oe-card-head h6 i {
        color: #6366f1;
        margin-right: 6px;
    }
    .oe-card-body { padding: 16px 18px; }

    .oe-badge-invoice {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 999px;
    }
    .oe-badge-status {
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 999px;
        background: #fef3c7;
        color: #b45309;
    }

    .oe-section-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #94a3b8;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .oe-cart-table thead { background: #f8fafc; }
    .oe-cart-table th {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        font-weight: 600;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
        padding: 10px 12px;
    }
    .oe-cart-table td {
        vertical-align: middle;
        padding: 10px 12px;
        font-size: 13px;
        color: #334155;
    }
    .oe-cart-table .oe-product-img {
        width: 44px;
        height: 44px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }
    .oe-cart-table .product_discount {
        width: 72px;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        padding: 4px 8px;
        font-size: 13px;
    }
    .oe-cart-table .oe-variant-select {
        min-width: 110px;
        max-width: 140px;
        font-size: 12px;
        padding: 4px 8px;
    }
    .oe-cart-table .btn-remove {
        width: 32px;
        height: 32px;
        padding: 0;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .oe-qty-control {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }
    .oe-qty-btn {
        width: 30px;
        height: 30px;
        border: 1px solid #cbd5e1;
        background: #eef2ff;
        color: #4f46e5;
        border-radius: 8px;
        font-weight: 700;
        line-height: 1;
        padding: 0;
        cursor: pointer;
    }
    .oe-qty-btn:hover {
        background: #e0e7ff;
        border-color: #a5b4fc;
    }
    .oe-qty-input {
        width: 38px;
        text-align: center;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-weight: 600;
        font-size: 13px;
        background: #fff;
    }

    .oe-form-label {
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 5px;
    }
    .oe-form-label i {
        color: #94a3b8;
        width: 16px;
        margin-right: 4px;
    }
    .oe-input-group .form-control,
    .oe-input-group .form-select {
        border-radius: 8px;
        border-color: #cbd5e1;
        font-size: 13px;
    }
    .oe-input-group .form-control:focus,
    .oe-input-group .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }

    .oe-summary-table { margin: 0; }
    .oe-summary-table td {
        padding: 8px 0;
        font-size: 14px;
        color: #475569;
        border: none;
    }
    .oe-summary-table tr.oe-summary-total td {
        border-top: 2px dashed #e2e8f0;
        padding-top: 12px;
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
    }
    .oe-summary-table tr.oe-summary-total td:last-child { color: #16a34a; }
    .oe-summary-table tr.oe-summary-due td:last-child { color: #dc2626; }

    .oe-payment-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px;
    }

    .btn-oe-primary {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        border: none;
        color: #fff;
        font-weight: 600;
        padding: 10px 28px;
        border-radius: 999px;
        box-shadow: 0 10px 22px rgba(79, 70, 229, 0.35);
        transition: all 0.2s ease;
    }
    .btn-oe-primary:hover {
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 14px 28px rgba(79, 70, 229, 0.45);
    }
    .btn-oe-outline {
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
    }

    .oe-product-add .select2-container { width: 100% !important; }
    .oe-product-add .select2-container .select2-selection--single {
        height: 42px;
        border-radius: 10px;
        border-color: #cbd5e1;
        padding-top: 6px;
    }

    @media (min-width: 992px) {
        .oe-sidebar-sticky {
            position: sticky;
            top: 80px;
        }
    }
</style>
<link href="{{ asset('public/backEnd') }}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
@endsection

@section('content')
<div class="container-fluid order-edit-shell">

    <div class="oe-page-header">
        <div>
            <h4>অর্ডার এডিট</h4>
            <div class="oe-sub">
                ইনভয়েস <strong>#{{ $order->invoice_id }}</strong>
                · স্ট্যাটাস: <span class="oe-badge-status">{{ $statusName }}</span>
            </div>
        </div>
        <div class="oe-header-actions">
            <a href="{{ route('admin.order.process', $order->invoice_id) }}" class="btn btn-sm btn-light btn-oe-outline">
                <i class="fas fa-arrow-left me-1"></i> প্রসেস পেজ
            </a>
            <a href="{{ route('admin.order.invoice', $order->invoice_id) }}" class="btn btn-sm btn-outline-primary btn-oe-outline" target="_blank">
                <i class="fas fa-file-invoice me-1"></i> ইনভয়েস
            </a>
            <form method="get" action="{{ route('admin.order.cart_clear') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger btn-oe-outline delete-confirm">
                    <i class="fas fa-trash-alt me-1"></i> কার্ট ক্লিয়ার
                </button>
            </form>
        </div>
    </div>

    <form action="{{ route('admin.order.update') }}" method="POST" class="pos_form" id="order_edit_form" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="order_id" value="{{ $order->id }}">

        <div class="row g-3">
            {{-- বাম: পণ্য ও কার্ট --}}
            <div class="col-lg-8">
                <div class="oe-card">
                    <div class="oe-card-head">
                        <h6><i class="fas fa-shopping-cart"></i> অর্ডার আইটেম</h6>
                        <span class="oe-badge-invoice">#{{ $order->invoice_id }}</span>
                    </div>
                    <div class="oe-card-body">
                        <div class="oe-product-add mb-3">
                            <label class="oe-form-label"><i class="fas fa-plus-circle"></i> পণ্য যোগ করুন</label>
                            <select id="cart_add" class="form-control select2">
                                <option value="">পণ্য খুঁজুন ও সিলেক্ট করুন...</option>
                                @foreach($products as $value)
                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover oe-cart-table mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:56px;"></th>
                                        <th>পণ্য</th>
                                        <th>রঙ</th>
                                        <th>সাইজ</th>
                                        <th class="text-center">পরিমাণ</th>
                                        <th class="text-end">দাম</th>
                                        <th class="text-center">ছাড়</th>
                                        <th class="text-end">সাবটোটাল</th>
                                        <th style="width:48px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="cartTable">
                                    @include('backEnd.order.cart_table_rows_edit', ['cartinfo' => $cartinfo])
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- পেমেন্ট --}}
                <div class="oe-card">
                    <div class="oe-card-head">
                        <h6><i class="fas fa-credit-card"></i> পেমেন্ট তথ্য</h6>
                    </div>
                    <div class="oe-card-body oe-payment-box">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="oe-form-label">পেমেন্ট গেটওয়ে</label>
                                <input type="text" class="form-control" value="{{ ucfirst(optional($posPay)->payment_method ?? $order->payment_gateway ?? 'N/A') }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="oe-form-label">পেমেন্ট স্ট্যাটাস</label>
                                <div class="input-group">
                                    <select id="payment_status_{{ $order->id }}" class="form-select">
                                        <option value="pending" {{ $posPayStatus == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="paid" {{ $posPayStatus == 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="unpaid" {{ $posPayStatus == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                        <option value="failed" {{ $posPayStatus == 'failed' ? 'selected' : '' }}>Failed</option>
                                    </select>
                                    <button type="button" class="btn btn-success" onclick="updatePaymentStatus({{ $order->id }})">
                                        <i class="fa fa-check"></i> আপডেট
                                    </button>
                                </div>
                            </div>
                        </div>
                        @include('backEnd.order.partials.manual_payment_verify_box', ['payment' => $posPay])
                    </div>
                </div>
            </div>

            {{-- ডান: গ্রাহক + সারাংশ --}}
            <div class="col-lg-4">
                <div class="oe-sidebar-sticky">
                    <div class="oe-card">
                        <div class="oe-card-head">
                            <h6><i class="fas fa-user"></i> গ্রাহক ও ডেলিভারি</h6>
                        </div>
                        <div class="oe-card-body">
                            <div class="oe-input-group mb-3">
                                <label class="oe-form-label" for="name"><i class="fas fa-user"></i> নাম</label>
                                <input type="text" id="name" class="form-control" placeholder="গ্রাহকের নাম" name="name" value="{{ $shippinginfo->name }}" required>
                            </div>
                            <div class="oe-input-group mb-3">
                                <label class="oe-form-label" for="phone"><i class="fas fa-phone"></i> মোবাইল</label>
                                <input type="number" id="phone" class="form-control" placeholder="01XXXXXXXXX" name="phone" value="{{ $shippinginfo->phone }}" required>
                            </div>
                            <div class="oe-input-group mb-3">
                                <label class="oe-form-label" for="address"><i class="fas fa-map-marker-alt"></i> ঠিকানা</label>
                                <textarea id="address" class="form-control" rows="2" placeholder="বিস্তারিত ঠিকানা" name="address" required>{{ $shippinginfo->address }}</textarea>
                            </div>

                            <div class="oe-section-label mt-2">ডেলিভারি লোকেশন</div>
                            <div class="oe-input-group mb-3">
                                <label class="oe-form-label" for="adm_edit_division">বিভাগ</label>
                                <select id="adm_edit_division" class="form-select" name="division_id" required>
                                    <option value="">বিভাগ নির্বাচন করুন</option>
                                    @foreach(($divisions ?? []) as $d)
                                        <option value="{{ $d->id }}" {{ $admEdDiv === (int) $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row g-2">
                                <div class="col-12 col-md-6 oe-input-group mb-3 mb-md-0">
                                    <label class="oe-form-label" for="adm_edit_district">জেলা</label>
                                    <select id="adm_edit_district" class="form-select" name="district_id" required>
                                        <option value="">{{ $admEdDiv ? 'জেলা নির্বাচন করুন' : 'আগে বিভাগ সিলেক্ট করুন' }}</option>
                                        @foreach($editDistricts->where('division_id', $admEdDiv) as $district)
                                            <option value="{{ $district->id }}" {{ $admEdDist === (int) $district->id ? 'selected' : '' }}>
                                                {{ $district->name }} (৳{{ $district->delivery_charge }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-6 oe-input-group">
                                    <label class="oe-form-label" for="adm_edit_upazila">উপজেলা</label>
                                    <select id="adm_edit_upazila" class="form-select" name="upazila_id" required>
                                        <option value="">{{ $admEdDist ? 'উপজেলা নির্বাচন করুন' : 'আগে জেলা সিলেক্ট করুন' }}</option>
                                        @foreach($editUpazilas->where('district_id', $admEdDist) as $upazila)
                                            <option value="{{ $upazila->id }}" {{ $admEdUp === (int) $upazila->id ? 'selected' : '' }}>
                                                {{ $upazila->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="oe-card">
                        <div class="oe-card-head">
                            <h6><i class="fas fa-receipt"></i> অর্ডার সারাংশ</h6>
                        </div>
                        <div class="oe-card-body">
                            <table class="table table-borderless oe-summary-table w-100">
                                <tbody id="cart_details">
                                    <tr>
                                        <td>সাবটোটাল</td>
                                        <td class="text-end">৳{{ number_format((float) $subtotal, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>ডেলিভারি চার্জ</td>
                                        <td class="text-end">৳{{ number_format((float) $shipping, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>মোট ছাড়</td>
                                        <td class="text-end text-danger">−৳{{ number_format((float) $total_discount, 2) }}</td>
                                    </tr>
                                    <tr class="oe-summary-total">
                                        <td>মোট পরিশোধ</td>
                                        <td class="text-end">৳{{ number_format((float) $total, 2) }}</td>
                                    </tr>
                                    @if($advancePaid > 0)
                                    <tr>
                                        <td>অগ্রিম পরিশোধ</td>
                                        <td class="text-end text-success">৳{{ number_format($advancePaid, 2) }}</td>
                                    </tr>
                                    <tr class="oe-summary-due">
                                        <td>বাকি</td>
                                        <td class="text-end">৳{{ number_format($dueAmount, 2) }}</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>

                            <div class="d-grid gap-2 mt-3">
                                <button type="submit" class="btn btn-oe-primary">
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
<script src="{{ asset('public/backEnd/') }}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{ asset('public/backEnd/') }}/assets/js/pages/form-validation.init.js"></script>
<script src="{{ asset('public/backEnd/') }}/assets/libs/select2/js/select2.min.js"></script>
<script src="{{ asset('public/backEnd/') }}/assets/js/pages/form-advanced.init.js"></script>

<script>
function updatePaymentStatus(orderId) {
    var status = document.getElementById('payment_status_' + orderId).value;
    fetch('{{ route("admin.order.updatePaymentStatus") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ order_id: orderId, payment_status: status })
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
        if (data.status === 'success') {
            toastr.success(data.message, 'সফল');
        } else {
            toastr.error(data.message, 'ত্রুটি');
        }
    })
    .catch(function () {
        toastr.error('কিছু একটা ভুল হয়েছে!', 'ত্রুটি');
    });
}

$(document).ready(function () {
    $('.select2').select2({ placeholder: 'পণ্য খুঁজুন...', allowClear: true });
});

var oeEditQuery = 'layout=edit&order_id={{ $order->id }}';

function cart_details() {
    $.ajax({
        type: 'GET',
        url: '{{ route("admin.order.cart_details") }}?' + oeEditQuery,
        dataType: 'html',
        success: function (cartinfo) { $('#cart_details').html(cartinfo); }
    });
}
function cart_content() {
    $.ajax({
        type: 'GET',
        url: '{{ route("admin.order.cart_content") }}?' + oeEditQuery,
        dataType: 'html',
        success: function (cartinfo) {
            $('#cartTable').html(cartinfo);
            cart_details();
        }
    });
}
function refreshCart() {
    cart_content();
}

$('#cart_add').on('change', function () {
    var id = $(this).val();
    if (id) {
        $.ajax({
            cache: 'false',
            type: 'GET',
            data: { id: id },
            url: '{{ route("admin.order.cart_add") }}',
            dataType: 'json',
            success: function () {
                refreshCart();
                $('#cart_add').val(null).trigger('change');
            }
        });
    }
});

$(document).on('click', '.cart_remove', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    if (id) {
        $.ajax({
            cache: false,
            type: 'GET',
            data: { id: id },
            url: '{{ route("admin.order.cart_remove") }}',
            dataType: 'json',
            success: function () { refreshCart(); }
        });
    }
});

$(document).on('click', '.cart_increment', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    var qty = $(this).val();
    if (!id) return;
    $.ajax({
        cache: false,
        type: 'GET',
        data: { id: id, qty: qty },
        url: '{{ route("admin.order.cart_increment") }}',
        dataType: 'json',
        success: function () { refreshCart(); }
    });
});

$(document).on('click', '.cart_decrement', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    var qty = $(this).val();
    if (!id) return;
    $.ajax({
        cache: false,
        type: 'GET',
        data: { id: id, qty: qty },
        url: '{{ route("admin.order.cart_decrement") }}',
        dataType: 'json',
        success: function () { refreshCart(); }
    });
});

function syncLineDiscountHidden($input) {
    var key = $input.data('line-key');
    if (!key) return;
    $input.closest('td').find('.line-discount-hidden').val($input.val() || 0);
}

function applyProductDiscount($input) {
    syncLineDiscountHidden($input);
    var id = $input.data('id');
    var discount = $input.val();
    if (!id) return;
    $.ajax({
        cache: false,
        type: 'GET',
        data: { id: id, discount: discount },
        url: '{{ route("admin.order.product_discount") }}',
        dataType: 'json',
        success: function () { refreshCart(); }
    });
}

$(document).on('input change', '.product_discount', function () {
    syncLineDiscountHidden($(this));
});

$(document).on('change', '.product_discount', function () {
    applyProductDiscount($(this));
});
$(document).on('keyup', '.product_discount', function () {
    syncLineDiscountHidden($(this));
    clearTimeout($(this).data('discountTimer'));
    var $input = $(this);
    $input.data('discountTimer', setTimeout(function () {
        applyProductDiscount($input);
    }, 400));
});

$('#order_edit_form').on('submit', function () {
    $('#cartTable .product_discount').each(function () {
        syncLineDiscountHidden($(this));
    });
});

var admEditAllDistricts = @json($editDistricts->values());
var admEditAllUpazilas = @json($editUpazilas->values());

function admEditFillDistricts(divId, preselect, cb) {
    var $d = $('#adm_edit_district');
    var $u = $('#adm_edit_upazila');
    divId = divId || '';
    $d.prop('disabled', !divId).html(divId ? '<option value="">লোড...</option>' : '<option value="">আগে বিভাগ সিলেক্ট করুন</option>');
    $u.html('<option value="">আগে জেলা সিলেক্ট করুন</option>').prop('disabled', false);
    if (!divId) { if (cb) cb(); return; }
    divId = parseInt(divId, 10);
    var opts = '<option value="">জেলা নির্বাচন করুন</option>';
    admEditAllDistricts
        .filter(function (r) { return parseInt(r.division_id, 10) === divId; })
        .forEach(function (r) {
            opts += '<option value="' + r.id + '">' + r.name + ' (৳' + r.delivery_charge + ')</option>';
        });
    $d.html(opts).prop('disabled', false);
    if (preselect) $d.val(String(preselect));
    if (cb) cb();
}

function admEditFillUpazilas(distId, preselect, cb) {
    var $u = $('#adm_edit_upazila');
    distId = distId || '';
    $u.prop('disabled', !distId).html(distId ? '<option value="">লোড...</option>' : '<option value="">আগে জেলা সিলেক্ট করুন</option>');
    if (!distId) { if (cb) cb(); return; }
    distId = parseInt(distId, 10);
    var opts = '<option value="">উপজেলা নির্বাচন করুন</option>';
    admEditAllUpazilas
        .filter(function (r) { return parseInt(r.district_id, 10) === distId; })
        .forEach(function (r) { opts += '<option value="' + r.id + '">' + r.name + '</option>'; });
    $u.html(opts).prop('disabled', false);
    if (preselect) $u.val(String(preselect));
    if (cb) cb();
}

$('#adm_edit_division').on('change', function () {
    admEditFillDistricts($(this).val(), null, function () {
        refreshCart();
    });
});

$('#adm_edit_district').on('change', function () {
    var id = $(this).val();
    if (id) {
        $.ajax({
            type: 'GET',
            data: { id: id },
            url: '{{ route("admin.order.cart_shipping") }}',
            dataType: 'json',
            complete: function () { refreshCart(); }
        });
    }
    admEditFillUpazilas(id, null, function () {
        refreshCart();
    });
});

function admEditUpdateVariant(rowId, productId, sizeId, colorId) {
    $.ajax({
        cache: false,
        type: 'GET',
        data: {
            id: rowId,
            product_id: productId,
            size_id: sizeId !== undefined ? sizeId : '',
            color_id: colorId !== undefined ? colorId : ''
        },
        url: '{{ route("admin.order.cart.update") }}',
        dataType: 'json',
        success: function () { refreshCart(); }
    });
}

$(document).on('change', '.cart-size-selector', function () {
    admEditUpdateVariant($(this).data('id'), $(this).data('product-id'), $(this).val(), undefined);
});

$(document).on('change', '.cart-color-selector', function () {
    admEditUpdateVariant($(this).data('id'), $(this).data('product-id'), undefined, $(this).val());
});
</script>
@endsection
