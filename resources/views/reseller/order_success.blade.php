@extends('reseller.layouts.app')

@section('title', 'অর্ডার সফল - #' . $order->invoice_id)
@section('page-title', 'অর্ডার সফল')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');

    .invoice-wrapper { 
        background: #f8fafc; 
        padding: 30px 15px; 
        font-family: 'Plus Jakarta Sans', sans-serif; 
    }
    #invoice-pdf-area { 
        background: #fff; 
        max-width: 850px; 
        margin: 0 auto; 
        border-radius: 12px; 
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); 
        overflow: hidden; 
    }
    .inv-container { padding: 40px; }
    .inv-header { 
        display: flex; 
        justify-content: space-between; 
        align-items: flex-start; 
        flex-wrap: wrap; 
        gap: 20px; 
        margin-bottom: 40px; 
    }
    .inv-logo img { width: 150px; height: auto; margin-bottom: 15px; }
    .inv-title h1 { font-size: 28px; font-weight: 800; color: #0f172a; margin: 0; }
    .inv-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
    .info-label { 
        font-size: 11px; 
        font-weight: 700; 
        color: #64748b; 
        text-transform: uppercase; 
        letter-spacing: 0.5px; 
        display: block; 
        margin-bottom: 5px; 
    }
    .info-val { font-size: 14px; color: #1e293b; line-height: 1.5; }
    .table-responsive { margin: 30px 0; }
    .inv-table { width: 100%; border-collapse: collapse; }
    .inv-table th { 
        background: #f1f5f9; 
        padding: 12px; 
        font-size: 12px; 
        font-weight: 700; 
        text-transform: uppercase; 
        color: #475569; 
    }
    .inv-table td { padding: 15px 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
    .sum-wrapper { display: flex; justify-content: flex-end; }
    .sum-box { width: 100%; max-width: 320px; }
    .sum-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 14px; }
    .total-row { 
        border-top: 2px solid #0f172a; 
        margin-top: 10px; 
        padding-top: 15px; 
        font-weight: 800; 
        font-size: 18px; 
        color: #000; 
    }
    .payment-badge-box { 
        background: #0f172a; 
        color: #fff; 
        padding: 15px; 
        border-radius: 8px; 
        margin-top: 15px; 
    }
    .status-tag { 
        display: inline-block; 
        padding: 4px 12px; 
        border-radius: 50px; 
        font-size: 11px; 
        font-weight: 700; 
        margin-top: 8px; 
    }
    .bg-paid-light { background: #dcfce7; color: #15803d; }
    .bg-due-light { background: #fee2e2; color: #b91c1c; }
    .success-banner {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #fff;
        padding: 30px;
        border-radius: 12px;
        text-align: center;
        margin-bottom: 30px;
    }
    .success-icon {
        font-size: 64px;
        margin-bottom: 15px;
    }

    @media print {
        .no-print { display: none !important; }
        body { background: white; }
        .invoice-wrapper { padding: 0; }
        #invoice-pdf-area { box-shadow: none; width: 100%; }
    }
</style>
@endpush

@section('content')

<div class="container-fluid py-4">
    <div class="invoice-wrapper">
        <div class="container no-print mb-4">
            <div class="d-flex justify-content-center gap-2">
                <a href="{{ route('reseller.orders') }}" class="btn btn-dark btn-sm rounded-pill px-4">
                   <i class="fa fa-arrow-left me-1"></i> আমার অর্ডার
                </a>
                <button onclick="window.print()" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm">
                    <i class="fa fa-print me-1"></i> প্রিন্ট করুন
                </button>
            </div>
        </div>

        {{-- Success Banner --}}
        <div class="success-banner no-print">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2 class="mb-2">অর্ডার সফলভাবে প্লেস করা হয়েছে!</h2>
            <p class="mb-0">আপনার অর্ডার নম্বর: <strong>#{{ $order->invoice_id }}</strong></p>
        </div>

        {{-- ⭐ ডিজিটাল আইটেম ডাউনলোড সেকশন (শুধুমাত্র পেইড হলে দেখাবে) ⭐ --}}
        @if($is_fully_paid && $downloads->count() > 0)
        <div class="digital-download-box no-print" style="background: #f0f9ff; border: 2px solid #3b82f6; border-radius: 12px; padding: 24px; margin: 20px auto; max-width: 850px; text-align: center;">
            <h6 class="fw-bold text-dark mb-1">
                <i class="fa fa-cloud-download me-2 text-primary"></i> ডিজিটাল আইটেম প্রস্তুত!
            </h6>
            <p class="small text-muted mb-3">পেমেন্ট সফল হওয়ায় আপনার ফাইলগুলো ডাউনলোডের জন্য উন্মুক্ত করা হয়েছে।</p>
            <div class="d-flex flex-wrap justify-content-center gap-2">
                @foreach($downloads as $dl)
                    <a href="{{ route('digital.download', $dl->token) }}" 
                       class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm" 
                       style="text-decoration: none;">
                        <i class="fa fa-download me-2"></i> Download: {{ $dl->product->name ?? 'File' }}
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        <div id="invoice-pdf-area">
            <div class="inv-container">
                <div class="inv-header">
                    <div class="inv-logo">
                        @if($generalsetting && $generalsetting->white_logo)
                            <img src="{{ asset($generalsetting->white_logo) }}" alt="Logo">
                        @endif
                        <div class="info-val">
                            <strong>{{ $generalsetting->name ?? 'Company Name' }}</strong><br>
                            @if($contact)
                                <span class="text-muted small">{{ $contact->address }}</span><br>
                                <span class="text-muted small">Phone: {{ $contact->phone }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="inv-title"><h1>INVOICE</h1></div>
                        <p class="mb-0 fw-bold">#{{ $order->invoice_id }}</p>
                        <p class="text-muted small">Date: {{ $order->created_at->format('d M, Y') }}</p>
                    </div>
                </div>

                <hr class="my-4" style="opacity: 0.1;">

                <div class="inv-grid">
                    <div>
                        <span class="info-label">Customer Details</span>
                        <div class="info-val">
                            <strong class="d-block mb-1">{{ $order->shipping ? $order->shipping->name : 'N/A' }}</strong>
                            {{ $order->shipping ? $order->shipping->phone : '' }}<br>
                            {{ $order->shipping ? $order->shipping->address : '' }}
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="info-label">Payment Information</span>
                        <div class="info-val text-uppercase fw-bold">{{ $payment->payment_method ?? 'COD' }}</div>
                        <span class="status-tag {{ $paid_amount >= $grand_total ? 'bg-paid-light' : 'bg-due-light' }}">
                            {{ $paid_amount >= $grand_total ? '✔ Verified Paid' : '✘ Payment Outstanding' }}
                        </span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="inv-table">
                        <thead>
                            <tr>
                                <th>Product Description</th>
                                <th class="text-center">Price</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderdetails as $item)
                            <tr>
                                <td>
                                    <span class="fw-bold d-block">{{ $item->product_name }}</span>
                                    @if($item->size && $item->size->name)
                                        <small class="text-muted">Size: {{ $item->size->name }}</small>
                                    @elseif($item->product_size)
                                        <small class="text-muted">Size: {{ $item->product_size }}</small>
                                    @endif
                                    @if($item->color && $item->color->name)
                                        <small class="text-muted"> | Color: {{ $item->color->name }}</small>
                                    @elseif($item->product_color)
                                        <small class="text-muted"> | Color: {{ $item->product_color }}</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div>৳{{ number_format($item->sale_price, 2) }}</div>
                                    @if($item->product && $item->product->reseller_price)
                                        <small class="text-muted d-block">
                                            রিসেলার: ৳{{ number_format($item->product->reseller_price, 2) }}
                                        </small>
                                    @endif
                                </td>
                                <td class="text-center">{{ $item->qty }}</td>
                                <td class="text-end fw-bold">
                                    <div>৳{{ number_format($item->sale_price * $item->qty, 2) }}</div>
                                    @if($item->product && $item->product->reseller_price)
                                        <small class="d-block text-muted">
                                            রিসেলার: ৳{{ number_format($item->product->reseller_price * $item->qty, 2) }}
                                        </small>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="sum-wrapper">
                    <div class="sum-box">
                        @if(isset($reseller_subtotal) && $reseller_subtotal > 0)
                        <div class="sum-row">
                            <span class="text-muted">Reseller Subtotal (আপনার কস্ট)</span>
                            <span>৳{{ number_format($reseller_subtotal, 2) }}</span>
                        </div>
                        @endif

                        <div class="sum-row">
                            <span class="text-muted">Subtotal</span>
                            <span>৳{{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="sum-row">
                            <span class="text-muted">Shipping Fee</span>
                            <span>৳{{ number_format($order->shipping_charge, 2) }}</span>
                        </div>
                        @if($order->discount > 0)
                        <div class="sum-row text-danger">
                            <span>Discount</span>
                            <span>-৳{{ number_format($order->discount, 2) }}</span>
                        </div>
                        @endif
                        
                        <div class="sum-row total-row">
                            <span>Grand Total</span>
                            <span>৳{{ number_format($grand_total, 2) }}</span>
                        </div>

                        {{-- Reseller Profit Info --}}
                        @if($order->reseller_profit)
                        <div class="sum-row" style="background: #f0f9ff; padding: 12px; border-radius: 8px; margin-top: 15px;">
                            <span class="text-primary fw-bold">আপনার লাভ</span>
                            <span class="text-primary fw-bold">৳{{ number_format($order->reseller_profit, 2) }}</span>
                        </div>
                        @endif

                        <div class="payment-badge-box">
                            <div class="sum-row border-0 p-0 mb-2">
                                <span style="color: #4ade80;">Paid Amount</span>
                                <span class="fw-bold">৳{{ number_format($paid_amount, 2) }}</span>
                            </div>
                            <div class="sum-row border-0 p-0">
                                <span style="color: #fb7185;">Remaining Due</span>
                                <span class="fw-bold">৳{{ number_format($due_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 pt-4 text-center border-top">
                    <p class="small text-muted mb-0">Thank you for your business! This is a computer-generated invoice.</p>
                    <p class="fw-bold small text-uppercase mt-1">{{ $generalsetting->name ?? 'Company Name' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
