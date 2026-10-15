@extends('reseller.layouts.app')

@section('title', 'প্রোডাক্ট যোগ করুন')
@section('page-title', 'প্রোডাক্ট যোগ করুন')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">ল্যান্ডিং পেজে প্রোডাক্ট যোগ করুন</h4>
            <p class="text-muted small mb-0">প্রোডাক্ট সিলেক্ট করুন এবং আপনার কাস্টম বিক্রয় মূল্য সেট করুন</p>
        </div>
        <a href="{{ route('reseller.landing.products') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> ফিরে যান
        </a>
    </div>

    @if($availableProducts->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                <h5>সব প্রোডাক্ট ইতিমধ্যে যোগ করা হয়েছে</h5>
                <a href="{{ route('reseller.landing.products') }}" class="btn btn-primary mt-3">প্রোডাক্ট তালিকায় ফিরে যান</a>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($availableProducts as $product)
                @php
                    $img = $product->image && $product->image->image ? $product->image->image : 'public/uploads/default.webp';
                    $basePrice = (float) ($product->reseller_price ?? 0);
                    $mainPrice = (float) ($product->new_price ?? $product->old_price ?? 0);
                @endphp
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <img src="{{ asset($img) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 180px; object-fit: cover;">
                        <div class="card-body">
                            <h6 class="card-title fw-bold text-dark">{{ Str::limit($product->name, 50) }}</h6>
                            <p class="small text-muted mb-2">
                                রিসেলার কস্ট: <strong>৳{{ number_format($basePrice, 0) }}</strong>
                                @if($mainPrice > 0)
                                    | রিটেইল: <span class="text-decoration-line-through">৳{{ number_format($mainPrice, 0) }}</span>
                                @endif
                            </p>
                            <form action="{{ route('reseller.landing.products.add.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">আপনার বিক্রয় মূল্য (৳)</label>
                                    <input type="number" name="custom_price" class="form-control" value="{{ max($basePrice, $mainPrice > 0 ? $mainPrice : $basePrice) }}" min="{{ $basePrice }}" step="1" required placeholder="প্রাইস">
                                    <small class="text-muted">কমপক্ষে ৳{{ number_format($basePrice, 0) }} (কস্ট প্রাইস)</small>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-plus me-2"></i> যোগ করুন
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
