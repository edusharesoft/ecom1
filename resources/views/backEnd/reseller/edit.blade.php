@extends('backEnd.layouts.master')
@section('title', 'Edit Reseller Profile')

@section('css')
@include('backEnd.partials.admin_layout_gap_fix')
<style>
    html, body { background: #eef1f8; }
    /* --- Modern Card --- */
    .card-modern {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        background: #fff;
        margin-bottom: 20px;
    }
    .card-header-modern {
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        padding: 1.25rem 1.5rem;
        border-radius: 12px 12px 0 0 !important;
    }
    .section-title {
        font-size: 0.95rem; font-weight: 700; color: #334155;
        display: flex; align-items: center; gap: 8px; margin: 0;
    }

    /* --- Form Elements --- */
    .form-label-custom {
        font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;
    }
    .form-control-custom, .form-select-custom {
        border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.65rem 1rem;
    }
    .form-control-custom:focus, .form-select-custom:focus {
        border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    /* --- Badges & Highlights --- */
    .wallet-card {
        background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; padding: 15px;
    }
    .verification-badge {
        font-size: 0.8rem; padding: 5px 10px; border-radius: 6px; font-weight: 600;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    {{-- PAGE HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark">Edit Reseller</h4>
            <p class="text-muted small mb-0">Update reseller profile and account status.</p>
        </div>
        <a href="{{ route('admin.resellers.index') }}" class="btn btn-white border shadow-sm rounded-pill px-4">
            <i data-feather="arrow-left" class="me-1" style="width: 16px;"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.resellers.update') }}" method="POST" data-parsley-validate>
        @csrf
        <input type="hidden" value="{{ $reseller->id }}" name="hidden_id">

        <div class="row">
            
            {{-- LEFT COLUMN: Basic Info --}}
            <div class="col-lg-8">
                
                {{-- Personal Information --}}
                <div class="card card-modern">
                    <div class="card-header-modern">
                        <h5 class="section-title"><i data-feather="user" class="text-primary" style="width: 18px;"></i> Reseller Information</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label-custom">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-custom @error('name') is-invalid @enderror" name="name" value="{{ $reseller->name }}" required>
                                @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label-custom">Shop Name</label>
                                <input type="text" class="form-control form-control-custom @error('shop_name') is-invalid @enderror" name="shop_name" value="{{ $reseller->shop_name }}">
                                @error('shop_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control form-control-custom @error('email') is-invalid @enderror" name="email" value="{{ $reseller->email }}" required>
                                @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">Phone Number</label>
                                <input type="text" class="form-control form-control-custom" value="{{ $reseller->phone ?? 'N/A' }}" readonly disabled>
                                <small class="text-muted">Phone number cannot be changed directly.</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status & Verification --}}
                <div class="card card-modern">
                    <div class="card-header-modern">
                        <h5 class="section-title"><i data-feather="shield" class="text-info" style="width: 18px;"></i> Account Status</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4 align-items-center">
                            <div class="col-md-6">
                                <label class="form-label-custom">Active Status</label>
                                <select class="form-select form-select-custom" name="status">
                                    <option value="1" {{ $reseller->status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $reseller->status == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom d-block">Verification Status</label>
                                @if($reseller->verification_status == 'approved')
                                    <span class="badge bg-success verification-badge"><i class="mdi mdi-check-decagram"></i> Verified</span>
                                @elseif($reseller->verification_status == 'rejected')
                                    <span class="badge bg-danger verification-badge">Rejected</span>
                                @else
                                    <span class="badge bg-warning text-dark verification-badge">Pending</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- RIGHT COLUMN: Wallet & Security --}}
            <div class="col-lg-4">
                
                {{-- Wallet Info --}}
                <div class="card card-modern">
                    <div class="card-header-modern">
                        <h5 class="section-title"><i data-feather="credit-card" class="text-success" style="width: 18px;"></i> Financials</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="wallet-card text-center">
                            <label class="form-label-custom mb-1 text-muted">Current Wallet Balance</label>
                            <h3 class="mb-0 text-dark fw-bold">৳{{ number_format($reseller->wallet_balance ?? 0, 2) }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Security --}}
                <div class="card card-modern">
                    <div class="card-header-modern">
                        <h5 class="section-title"><i data-feather="lock" class="text-danger" style="width: 18px;"></i> Security</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label-custom">New Password</label>
                            <input type="password" class="form-control form-control-custom @error('password') is-invalid @enderror" name="password" placeholder="Leave blank to keep current">
                            @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom">Confirm Password</label>
                            <input type="password" class="form-control form-control-custom" name="password_confirmation" placeholder="Retype password">
                        </div>
                        
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary fw-bold py-2 shadow-sm">
                                <i data-feather="save" class="me-1" style="width: 16px;"></i> Update Reseller
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>

</div>
@endsection

@section('script')
<script src="{{asset('public/backEnd/')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-validation.init.js"></script>
@endsection