@extends('frontEnd.layouts.master')
@section('title','Order Details')
@php
    $generalsetting = \App\Models\GeneralSetting::first();
@endphp
@push('css')
<style>
    /* ১. মেইন ব্যাকগ্রাউন্ড (হালকা ধূসর - চোখের জন্য আরামদায়ক) */
    .tracking-result-section {
        background: #f8f9fa; /* একদম সফট গ্রে */
        min-height: 90vh;
        padding: 60px 0;
        font-family: 'Segoe UI', sans-serif;
    }

    /* ২. ইনভয়েস কার্ড */
    .invoice-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05); /* শ্যাডো কমিয়ে দেওয়া হয়েছে */
        overflow: hidden;
        margin-bottom: 30px;
        border: 1px solid #eef2f6; /* খুব হালকা বর্ডার */
    }

    /* ৩. হেডার সেকশন (রয়্যাল ব্লু গ্র্যাডিয়েন্ট - প্রফেশনাল লুক) */
    .invoice-header {
        background: {{$generalsetting->primary_color}}; /* ডিপ ব্লু */
        padding: 20px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #fff;
    }
    .invoice-id {
        font-size: 18px;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    .invoice-date {
        font-size: 13px;
        opacity: 0.8;
        margin-top: 2px;
    }
    .status-badge {
        background: {{$generalsetting->secodery_color}};
        backdrop-filter: blur(5px);
        padding: 6px 16px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        border: 1px solid rgba(255,255,255,0.2);
    }

    /* ৪. ইনফো গ্রিড (সিম্পল এবং ক্লিন) */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
        padding: 30px;
        background: #fff;
        border-bottom: 1px solid #f1f1f1;
    }
    .info-box {
        display: flex;
        align-items: flex-start;
        gap: 15px;
    }
    /* আইকন কালার সফট করা হয়েছে */
    .info-icon {
        width: 42px;
        height: 42px;
        background: {{$generalsetting->primary_color}}; /* খুব হালকা নীল-গ্রে */
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff; /* টেক্সট কালারের সাথে মিল রেখে */
        font-size: 18px;
    }
    .info-content h6 {
        font-size: 11px;
        text-transform: uppercase;
        color: #8898aa;
        font-weight: 700;
        margin-bottom: 6px;
        letter-spacing: 0.5px;
    }
    .info-content p {
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin: 0;
        line-height: 1.4;
    }
    .info-content small {
        color: #666;
        font-size: 13px;
    }

    /* ৫. প্রোডাক্ট লিস্ট */
    .product-list-container {
        padding: 10px 30px 30px;
    }
    .section-title {
        font-size: 14px;
        font-weight: 700;
        color: #333;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f1f1f1;
        display: inline-block;
    }

    .product-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f8f9fa;
    }
    .product-item:last-child { border-bottom: none; }
    
    .prod-img-box {
        width: 50px;
        height: 50px;
        border-radius: 6px;
        overflow: hidden;
        border: 1px solid #e1e1e1;
        margin-right: 15px;
        background: #f8f8f8;
    }
    .prod-img-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .prod-details h5 {
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin-bottom: 3px;
    }
    .prod-meta span {
        font-size: 11px;
        color: #666;
        background: #f0f0f0;
        padding: 2px 6px;
        border-radius: 3px;
        margin-right: 5px;
    }

    .prod-price { text-align: right; }
    .prod-price .amount {
        display: block;
        font-weight: 700;
        color: #333;
        font-size: 15px;
    }
    .prod-price .qty {
        font-size: 12px;
        color: #888;
    }

    /* ৬. টোটাল সেকশন */
    .summary-section {
        background: #fcfcfc;
        padding: 20px 30px;
        border-top: 1px solid #f1f1f1;
    }
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 14px;
        color: #555;
    }
    .summary-row.total {
        border-top: 1px solid #e1e1e1;
        padding-top: 12px;
        margin-top: 12px;
        font-size: 16px;
        font-weight: 700;
        color: #1e3c72; /* ডিপ ব্লু */
    }

    /* ৭. বাটন এবং এরর মেসেজ */
    .btn-print {
        background: #fff;
        color: #555;
        border: 1px solid #ddd;
        padding: 8px 20px;
        border-radius: 4px;
        font-size: 13px;
        font-weight: 600;
        transition: 0.3s;
    }
    .btn-print:hover {
        background: #1e3c72;
        color: #fff;
        border-color: #1e3c72;
    }

    .not-found-card {
        text-align: center;
        padding: 60px 20px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.03);
    }
    .btn-retry {
        background: #1e3c72;
        color: #fff;
        border: none;
        padding: 10px 30px;
        border-radius: 4px;
        font-weight: 500;
        text-decoration: none;
        display: inline-block;
        margin-top: 20px;
        transition: 0.3s;
    }
    .btn-retry:hover {
        background: #162b52;
        color: #fff;
    }

    /* রেসপন্সিভ ফিক্স */
    @media (max-width: 576px) {
        .invoice-header { flex-direction: column; align-items: flex-start; gap: 15px; }
        .status-badge { align-self: flex-start; margin-top: 5px; }
        .info-grid { grid-template-columns: 1fr; gap: 20px; }
    }
</style>
@endpush

@section('content')
<section class="tracking-result-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">

                {{-- যদি কোনো অর্ডার না পাওয়া যায় --}}
                @if($order->count() == 0)
                    <div class="not-found-card">
                        <img src="https://cdn-icons-png.flaticon.com/512/2748/2748558.png" width="80" class="mb-4 opacity-50" alt="No Data">
                        <h4 class="text-dark fw-bold mb-2">অর্ডার খুঁজে পাওয়া যায়নি</h4>
                        <p class="text-muted">আপনার ইনভয়েস আইডি অথবা ফোন নম্বরটি সঠিক কিনা যাচাই করুন।</p>
                        <a href="{{ route('customer.order_track') }}" class="btn-retry">
                            আবার চেষ্টা করুন
                        </a>
                    </div>
                @else

                    {{-- অর্ডার লুপ --}}
                    @foreach($order as $value)
                    <div class="invoice-card">
                        
                        {{-- 1. Header --}}
                        <div class="invoice-header">
                            <div>
                                <div class="invoice-id">
                                    Invoice #{{$value->invoice_id}}
                                </div>
                                <div class="invoice-date">
                                    Placed on: {{ date('d M, Y h:i A', strtotime($value->created_at)) }}
                                </div>
                            </div>
                            <div class="status-badge">
                                {{ optional(App\Models\Orderstatus::find($value->order_status))->name ?? 'Unknown' }}
                            </div>
                        </div>

                        {{-- 2. Info Grid --}}
                        <div class="info-grid">
                            <div class="info-box">
                                <div class="info-icon"><i class="fas fa-user"></i></div>
                                <div class="info-content">
                                    <h6>Customer Details</h6>
                                    <p>{{ $value->shipping->name ?? 'Guest' }}</p>
                                    <small>{{ $value->shipping->phone ?? $value->shipping_phone }}</small>
                                </div>
                            </div>
                            <div class="info-box">
                                <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                                <div class="info-content">
                                    <h6>Delivery Address</h6>
                                    <p>{{ $value->shipping->area ?? 'General' }}</p>
                                    <small>{{ Str::limit($value->shipping->address ?? '', 35) }}</small>
                                </div>
                            </div>
                            <div class="info-box">
                                <div class="info-icon"><i class="fas fa-credit-card"></i></div>
                                <div class="info-content">
                                    <h6>Payment Info</h6>
                                    <p class="text-uppercase">{{ $value->payment->payment_method ?? 'COD' }}</p>
                                    <small class="{{ $value->payment_status == 'paid' ? 'text-success' : 'text-danger' }}">
                                        Status: {{ ucfirst($value->payment_status) }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        {{-- 3. Product List --}}
                        <div class="product-list-container">
                            <span class="section-title">Order Items</span>
                            
                            @php
                                $orderdetails = App\Models\OrderDetails::where('order_id', $value->id)->get();
                                $subtotal = 0;
                            @endphp

                            @foreach($orderdetails as $product)
                            <div class="product-item">
                                <div class="d-flex align-items-center">
                                    <div class="prod-img-box">
                                        <img src="{{ asset($product->image->image ?? 'public/frontEnd/images/no-image.png') }}" alt="Product">
                                    </div>
                                    <div class="prod-details">
                                        <h5>{{ $product->product_name }}</h5>
                                        <div class="prod-meta">
                                            @if($product->product_size) <span>Size: {{ $product->product_size }}</span> @endif
                                            @if($product->product_color) <span>Color: {{ $product->product_color }}</span> @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="prod-price">
                                    <span class="amount">{{ number_format($product->sale_price * $product->qty, 0) }} ৳</span>
                                    <span class="qty">{{ $product->sale_price }} x {{ $product->qty }}</span>
                                </div>
                                @php $subtotal += ($product->sale_price * $product->qty); @endphp
                            </div>
                            @endforeach
                        </div>

                        {{-- 4. Summary & Total --}}
                        <div class="summary-section">
                            <div class="row justify-content-end">
                                <div class="col-md-5">
                                    <div class="summary-row">
                                        <span>Subtotal</span>
                                        <span>{{ number_format($subtotal, 0) }} ৳</span>
                                    </div>
                                    <div class="summary-row">
                                        <span>Delivery Charge</span>
                                        <span>(+) {{ number_format($value->shipping_charge, 0) }} ৳</span>
                                    </div>
                                    @if($value->discount > 0)
                                    <div class="summary-row text-danger">
                                        <span>Discount</span>
                                        <span>(-) {{ number_format($value->discount, 0) }} ৳</span>
                                    </div>
                                    @endif
                                    <div class="summary-row total">
                                        <span>Grand Total</span>
                                        <span>{{ number_format($value->amount, 0) }} ৳</span>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Print Button --}}
                            <div class="text-end mt-4">
                                <button onclick="window.print()" class="btn-print">
                                    <i class="fas fa-print me-1"></i> Print Invoice
                                </button>
                            </div>
                        </div>

                    </div>
                    @endforeach

                @endif

            </div>
        </div>
    </div>
</section>
@endsection