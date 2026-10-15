@extends('backEnd.layouts.master')
@section('title', 'Add New Coupon')

@section('css')
<style>
    /* --- Card & Form Styles --- */
    .card-modern {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        background: #fff;
    }
    .card-header-modern {
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        padding: 1.5rem;
        border-radius: 16px 16px 0 0 !important;
        display: flex; justify-content: space-between; align-items: center;
    }
    
    .form-label-custom {
        font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;
    }
    .form-control-custom, .form-select-custom {
        border: 1px solid #e2e8f0; border-radius: 10px;
        padding: 0.75rem 1rem; font-size: 0.95rem;
        transition: all 0.2s;
    }
    .form-control-custom:focus, .form-select-custom:focus {
        border-color: #6366f1; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }
    .input-group-text-custom {
        background-color: #f8fafc; border: 1px solid #e2e8f0;
        color: #64748b; border-radius: 10px;
    }
    
    /* --- Generate Button --- */
    .btn-generate {
        background-color: #e0e7ff; color: #4338ca; border: 1px solid #e0e7ff;
        font-size: 0.85rem; font-weight: 600;
    }
    .btn-generate:hover { background-color: #4338ca; color: #fff; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            
            <form action="{{ route('admin.coupons.store') }}" method="POST">
                @csrf

                <div class="card card-modern">
                    
                    {{-- Header --}}
                    <div class="card-header-modern">
                        <div>
                            <h5 class="mb-1 fw-bold text-dark">Create Coupon</h5>
                            <p class="text-muted small mb-0">Set up a new discount code for your customers.</p>
                        </div>
                        <a href="{{ route('admin.coupons.index') }}" class="btn btn-light btn-sm rounded-pill px-3">
                            <i data-feather="list" style="width:14px;" class="me-1"></i> All Coupons
                        </a>
                    </div>

                    <div class="card-body p-4">
                        
                        {{-- Coupon Code Section --}}
                        <div class="mb-4">
                            <label class="form-label-custom">Coupon Code <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted border-start-custom" style="border-radius: 10px 0 0 10px; border: 1px solid #e2e8f0; border-right: 0;">
                                    <i data-feather="tag" style="width:16px;"></i>
                                </span>
                                <input type="text" name="code" id="coupon_code" class="form-control form-control-custom border-start-0" 
                                       placeholder="e.g. SUMMER2024" style="text-transform: uppercase; font-weight: bold; letter-spacing: 1px;" required>
                                <button type="button" class="btn btn-generate" onclick="generateCoupon()">
                                    <i data-feather="refresh-cw" style="width:14px;" class="me-1"></i> Random
                                </button>
                            </div>
                            <small class="text-muted ms-1">Customers will enter this code at checkout.</small>
                        </div>

                        {{-- Value & Type --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label-custom">Discount Type</label>
                                <select name="type" id="discount_type" class="form-select form-select-custom">
                                    <option value="flat">Fixed Amount (Flat)</option>
                                    <option value="percent">Percentage (%)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Discount Value <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text input-group-text-custom" id="value_icon" style="border-radius: 10px 0 0 10px; border-right: 0;">৳</span>
                                    <input type="number" step="0.01" name="value" class="form-control form-control-custom border-start-0" 
                                           placeholder="0.00" required style="border-radius: 0 10px 10px 0;">
                                </div>
                            </div>
                        </div>

                        {{-- Limits --}}
                        <div class="mb-4">
                            <label class="form-label-custom">Minimum Purchase Amount (Optional)</label>
                            <div class="input-group">
                                <span class="input-group-text input-group-text-custom" style="border-radius: 10px 0 0 10px; border-right: 0;">৳</span>
                                <input type="number" step="0.01" name="min_purchase" class="form-control form-control-custom border-start-0" 
                                       placeholder="Enter amount" style="border-radius: 0 10px 10px 0;">
                            </div>
                            <small class="text-muted ms-1">Minimum cart value required to apply this coupon.</small>
                        </div>

                        {{-- Validity Dates --}}
                        <div class="p-3 bg-light rounded-3 mb-4 border">
                            <h6 class="text-dark fw-bold mb-3 small text-uppercase"><i data-feather="calendar" style="width:14px;" class="me-1"></i> Validity Period</h6>
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label-custom mb-1">Valid From</label>
                                    <input type="date" name="valid_from" class="form-control form-control-custom" value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label-custom mb-1">Valid To</label>
                                    <input type="date" name="valid_to" class="form-control form-control-custom">
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">
                                <i data-feather="check-circle" class="me-1" style="width: 16px;"></i> Create Coupon
                            </button>
                            <a href="{{ route('admin.coupons.index') }}" class="btn btn-light py-2">Cancel</a>
                        </div>

                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Script to generate random coupon code
    function generateCoupon() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let result = '';
        for (let i = 0; i < 8; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('coupon_code').value = result;
    }

    // Toggle Icon based on type
    document.getElementById('discount_type').addEventListener('change', function() {
        const icon = document.getElementById('value_icon');
        if(this.value === 'percent') {
            icon.innerHTML = '%';
        } else {
            icon.innerHTML = '৳';
        }
    });
</script>
@endpush