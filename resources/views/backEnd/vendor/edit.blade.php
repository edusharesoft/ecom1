@extends('backEnd.layouts.master')
@section('title', 'Edit Vendor Profile')

@section('css')
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<style>
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
    .form-control-custom {
        border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.65rem 1rem;
    }
    .form-control-custom:focus {
        border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    /* --- Image Previews --- */
    .img-preview-box {
        border: 1px dashed #cbd5e1; border-radius: 8px; padding: 10px;
        text-align: center; position: relative; background: #f8fafc;
    }
    .img-preview-box img {
        max-width: 100%; height: auto; border-radius: 6px; cursor: zoom-in;
        transition: transform 0.2s;
    }
    .img-preview-box img:hover { transform: scale(1.02); }
    
    /* --- Verification Badge --- */
    .verification-card {
        border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;
    }
    .verification-header {
        background: #f1f5f9; padding: 1rem; display: flex; justify-content: space-between; align-items: center;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    {{-- PAGE HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark">Edit Vendor</h4>
            <p class="text-muted small mb-0">Update shop details, verification status, and settings.</p>
        </div>
        <a href="{{ route('admin.vendors.index') }}" class="btn btn-white border shadow-sm rounded-pill px-4">
            <i data-feather="arrow-left" class="me-1" style="width: 16px;"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.vendors.update') }}" method="POST" enctype="multipart/form-data" data-parsley-validate>
        @csrf
        <input type="hidden" value="{{ $vendor->id }}" name="hidden_id">

        <div class="row">
            
            {{-- LEFT COLUMN: Main Info --}}
            <div class="col-lg-8">
                
                {{-- Shop Information --}}
                <div class="card card-modern">
                    <div class="card-header-modern">
                        <h5 class="section-title"><i data-feather="shopping-bag" class="text-primary" style="width: 18px;"></i> Shop Information</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label-custom">Shop Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-custom @error('shop_name') is-invalid @enderror" name="shop_name" value="{{ $vendor->shop_name }}" required>
                                @error('shop_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Shop Slug/URL <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-custom @error('slug') is-invalid @enderror" name="slug" value="{{ $vendor->slug }}" required>
                                @error('slug') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label-custom">Address</label>
                                <textarea class="form-control form-control-custom @error('address') is-invalid @enderror" name="address" rows="3">{{ $vendor->address }}</textarea>
                                @error('address') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Owner Information --}}
                <div class="card card-modern">
                    <div class="card-header-modern">
                        <h5 class="section-title"><i data-feather="user" class="text-info" style="width: 18px;"></i> Owner Details</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label-custom">Owner Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-custom @error('owner_name') is-invalid @enderror" name="owner_name" value="{{ $vendor->owner_name }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control form-control-custom @error('email') is-invalid @enderror" name="email" value="{{ $vendor->email }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-custom @error('phone') is-invalid @enderror" name="phone" value="{{ $vendor->phone }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Verification Section --}}
                <div class="card card-modern">
                    <div class="card-header-modern d-flex justify-content-between">
                        <h5 class="section-title"><i data-feather="shield" class="text-success" style="width: 18px;"></i> KYC & Verification</h5>
                        <div class="status-badge">
                            @if($vendor->verification_status == 'approved')
                                <span class="badge bg-success"><i class="mdi mdi-check-decagram"></i> Verified</span>
                            @elseif($vendor->verification_status == 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending Review</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-4">
                        
                        {{-- Documents Grid --}}
                        @if($vendor->voter_id_front || $vendor->voter_id_back || $vendor->self_image)
                        <div class="row g-4 mb-4">
                            <div class="col-md-4">
                                <label class="form-label-custom mb-2">Voter ID Front</label>
                                @if($vendor->voter_id_front)
                                    <div class="img-preview-box">
                                        <img src="{{ asset($vendor->voter_id_front) }}" onclick="window.open(this.src, '_blank')">
                                    </div>
                                @else
                                    <div class="text-muted small text-center p-3 border rounded">Not Uploaded</div>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom mb-2">Voter ID Back</label>
                                @if($vendor->voter_id_back)
                                    <div class="img-preview-box">
                                        <img src="{{ asset($vendor->voter_id_back) }}" onclick="window.open(this.src, '_blank')">
                                    </div>
                                @else
                                    <div class="text-muted small text-center p-3 border rounded">Not Uploaded</div>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom mb-2">Self Image</label>
                                @if($vendor->self_image)
                                    <div class="img-preview-box">
                                        <img src="{{ asset($vendor->self_image) }}" onclick="window.open(this.src, '_blank')">
                                    </div>
                                @else
                                    <div class="text-muted small text-center p-3 border rounded">Not Uploaded</div>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- Action Buttons --}}
                        @if($vendor->verification_status == 'pending')
                            <div class="border-top pt-3 mt-2">
                                <label class="form-label-custom d-block mb-2">Take Action:</label>
                                <button type="submit" formaction="{{ route('admin.vendors.approve-verification', $vendor->id) }}" class="btn btn-success me-2" onclick="return confirm('Approve verification?')">
                                    <i data-feather="check-circle" class="me-1" style="width: 16px;"></i> Approve
                                </button>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectVerificationModal">
                                    <i data-feather="x-circle" class="me-1" style="width: 16px;"></i> Reject
                                </button>
                            </div>
                        @endif

                        @if($vendor->verification_note)
                            <div class="alert alert-light border mt-3 mb-0">
                                <strong><i class="mdi mdi-information-outline"></i> Note:</strong> {{ $vendor->verification_note }}
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- RIGHT COLUMN: Settings & Media --}}
            <div class="col-lg-4">
                
                {{-- Publish Settings --}}
                <div class="card card-modern">
                    <div class="card-header-modern">
                        <h5 class="section-title"><i data-feather="settings" class="text-dark" style="width: 18px;"></i> Settings</h5>
                    </div>
                    <div class="card-body p-4">
                        
                        <div class="mb-3">
                            <label class="form-label-custom d-block">Account Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="status" value="1" id="statusSwitch" @if($vendor->status==1) checked @endif>
                                <label class="form-check-label ms-2" for="statusSwitch">Active</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label-custom">Commission Rate (%)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control form-control-custom" name="commission_rate" value="{{ $vendor->commission_rate ?? 10.00 }}" required>
                                <span class="input-group-text bg-light">%</span>
                            </div>
                        </div>

                        @if($vendor->wallet)
                        <div class="p-3 bg-light rounded border">
                            <label class="form-label-custom mb-1">Wallet Balance</label>
                            <h4 class="mb-0 text-primary">৳{{ number_format($vendor->wallet->balance, 2) }}</h4>
                        </div>
                        @endif

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary fw-bold py-2 shadow-sm">
                                <i data-feather="save" class="me-1" style="width: 16px;"></i> Update Vendor
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Media Uploads --}}
                <div class="card card-modern">
                    <div class="card-header-modern">
                        <h5 class="section-title"><i data-feather="image" class="text-warning" style="width: 18px;"></i> Media</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label-custom">Shop Logo</label>
                            <input type="file" class="form-control mb-2" name="logo">
                            @if($vendor->logo)
                                <img src="{{ asset($vendor->logo) }}" class="rounded shadow-sm border" width="80" height="80">
                            @endif
                        </div>
                        <div class="mb-0">
                            <label class="form-label-custom">Shop Banner</label>
                            <input type="file" class="form-control mb-2" name="banner">
                            @if($vendor->banner)
                                <img src="{{ asset($vendor->banner) }}" class="rounded shadow-sm border w-100" style="height: 100px; object-fit: cover;">
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Security --}}
                <div class="card card-modern">
                    <div class="card-header-modern">
                        <h5 class="section-title"><i data-feather="lock" class="text-danger" style="width: 18px;"></i> Security</h5>
                    </div>
                    <div class="card-body p-4">
                        <label class="form-label-custom">Change Password</label>
                        <input type="password" class="form-control form-control-custom" name="password" placeholder="Leave blank to keep current">
                    </div>
                </div>

            </div>
        </div>
    </form>

    {{-- Reject Modal --}}
    @if($vendor->verification_status == 'pending')
    <div class="modal fade" id="rejectVerificationModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold text-danger">Reject Verification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.vendors.reject-verification', $vendor->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label-custom mb-2">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason" class="form-control form-control-custom" rows="4" required placeholder="Explain why the documents were rejected..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Confirm Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection

@section('script')
<script src="{{asset('public/backEnd/')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-validation.init.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/libs/select2/js/select2.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-advanced.init.js"></script>
@endsection