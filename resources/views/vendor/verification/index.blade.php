@extends('vendor.layouts.app')

@section('title', 'KYC Verification')
@section('page-title', 'Verification Center')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --primary: #4f46e5;
        --secondary: #64748b;
        --bg-light: #f8fafc;
        --border-color: #e2e8f0;
    }

    body { font-family: 'Inter', sans-serif; background: #f1f5f9; }

    /* Status Banner */
    .kyc-status-card {
        background: #fff;
        border: 1px solid var(--border-color);
        border-left: 4px solid var(--primary);
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .status-approved { border-left-color: #10b981; }
    .status-rejected { border-left-color: #ef4444; }
    .status-pending { border-left-color: #f59e0b; }

    /* Section Title */
    .section-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e293b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
    }
    .section-title::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--border-color);
        margin-left: 15px;
    }

    /* ID Card Upload Box */
    .id-upload-wrapper {
        position: relative;
        width: 100%;
        padding-top: 63%; /* Aspect Ratio for ID Card */
        background-color: #ffffff;
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        transition: all 0.2s;
        cursor: pointer;
        overflow: hidden;
    }
    
    .id-upload-wrapper:hover {
        border-color: var(--primary);
        background-color: #f8fafc;
    }

    .id-upload-content {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 20px;
        z-index: 1;
    }

    .id-preview-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 2;
        display: none;
    }
    .id-preview-img.active { display: block; }

    /* Change Button Overlay */
    .btn-change-overlay {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: rgba(0,0,0,0.6);
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        z-index: 3;
        display: none;
    }
    .id-upload-wrapper:hover .id-preview-img.active ~ .btn-change-overlay {
        display: block;
    }

    /* Profile Photo Box */
    .profile-upload-box {
        width: 140px;
        height: 140px;
        border-radius: 12px;
        border: 2px dashed #cbd5e1;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        background: #fff;
    }
    .profile-upload-box:hover { border-color: var(--primary); }
    
    .info-list { list-style: none; padding: 0; margin: 0; }
    .info-list li {
        position: relative;
        padding-left: 20px;
        margin-bottom: 8px;
        font-size: 0.85rem;
        color: var(--secondary);
    }
    .info-list li::before {
        content: '•';
        color: var(--primary);
        font-weight: bold;
        position: absolute;
        left: 0;
    }

</style>
@endpush

@section('content')
<div class="container-fluid px-0">

    <div class="mb-4">
        <h4 class="fw-bold text-dark mb-1">Identity Verification</h4>
        <p class="text-secondary small mb-0">Please provide valid documents to verify your vendor account.</p>
    </div>

    @if($vendor->verification_status == 'approved')
        <div class="kyc-status-card status-approved">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle text-success fa-2x me-3"></i>
                <div>
                    <h6 class="fw-bold text-dark mb-0">Verified Account</h6>
                    <small class="text-muted">You are fully verified to sell products.</small>
                </div>
            </div>
            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">Approved</span>
        </div>
    @elseif($vendor->verification_status == 'rejected')
        <div class="kyc-status-card status-rejected">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle text-danger fa-2x me-3"></i>
                <div>
                    <h6 class="fw-bold text-dark mb-0">Verification Failed</h6>
                    <small class="text-danger">{{ $vendor->verification_note ?? 'Documents did not match requirements.' }}</small>
                </div>
            </div>
            <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2">Rejected</span>
        </div>
    @elseif($vendor->verification_status == 'pending')
        <div class="kyc-status-card status-pending">
            <div class="d-flex align-items-center">
                <i class="fas fa-clock text-warning fa-2x me-3"></i>
                <div>
                    <h6 class="fw-bold text-dark mb-0">Under Review</h6>
                    <small class="text-muted">Your documents are being processed by our team.</small>
                </div>
            </div>
            <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2">Pending</span>
        </div>
    @endif

    <form action="{{ route('vendor.verification.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-4">
            
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-4">
                        <div class="section-title">
                            <i class="fas fa-id-card me-2 text-primary"></i> National ID / Passport
                        </div>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted mb-2">Front Side</label>
                                <input type="file" name="voter_id_front" id="id_front" class="d-none" accept="image/*" onchange="previewUpload(this, 'preview_front')">
                                <label for="id_front" class="id-upload-wrapper">
                                    <div class="id-upload-content">
                                        <div class="mb-3 text-secondary opacity-50">
                                            <i class="fas fa-image fa-2x"></i>
                                        </div>
                                        <span class="fw-bold text-dark small">Upload Front</span>
                                        <span class="text-muted" style="font-size: 11px;">JPG or PNG, Max 2MB</span>
                                    </div>
                                    <img id="preview_front" src="{{ $vendor->voter_id_front ? asset($vendor->voter_id_front) : '' }}" class="id-preview-img {{ $vendor->voter_id_front ? 'active' : '' }}">
                                    <div class="btn-change-overlay"><i class="fas fa-pen"></i> Change</div>
                                </label>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted mb-2">Back Side</label>
                                <input type="file" name="voter_id_back" id="id_back" class="d-none" accept="image/*" onchange="previewUpload(this, 'preview_back')">
                                <label for="id_back" class="id-upload-wrapper">
                                    <div class="id-upload-content">
                                        <div class="mb-3 text-secondary opacity-50">
                                            <i class="fas fa-image fa-2x"></i>
                                        </div>
                                        <span class="fw-bold text-dark small">Upload Back</span>
                                        <span class="text-muted" style="font-size: 11px;">Ensure text is readable</span>
                                    </div>
                                    <img id="preview_back" src="{{ $vendor->voter_id_back ? asset($vendor->voter_id_back) : '' }}" class="id-preview-img {{ $vendor->voter_id_back ? 'active' : '' }}">
                                    <div class="btn-change-overlay"><i class="fas fa-pen"></i> Change</div>
                                </label>
                            </div>
                        </div>

                        <div class="mt-4 p-3 bg-light rounded border border-light">
                            <h6 class="fw-bold small text-dark mb-2">Requirements:</h6>
                            <ul class="info-list">
                                <li>Government issued ID card, Driver's License or Passport.</li>
                                <li>All four corners of the ID must be visible.</li>
                                <li>Photo and text must be clear and not blurry.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="section-title">
                            <i class="fas fa-user-check me-2 text-primary"></i> Your Photo
                        </div>

                        <div class="text-center py-3 flex-grow-1">
                            <input type="file" name="self_image" id="self_photo" class="d-none" accept="image/*" onchange="previewUpload(this, 'preview_self')">
                            <label for="self_photo" class="profile-upload-box mb-3">
                                <div class="text-center" style="position: relative; z-index: 1;">
                                    <i class="fas fa-camera text-secondary mb-1"></i>
                                    <div class="small fw-bold text-dark" style="font-size: 12px;">Upload Photo</div>
                                </div>
                                <img id="preview_self" src="{{ $vendor->self_image ? asset($vendor->self_image) : '' }}" class="id-preview-img {{ $vendor->self_image ? 'active' : '' }}">
                            </label>
                            <p class="text-muted small mb-0">Please upload a recent photo of yourself holding your ID card (Optional but recommended).</p>
                        </div>

                        <div class="mt-auto pt-3 border-top">
                            @if($vendor->verification_status != 'approved')
                                <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-bold shadow-sm">
                                    Submit for Review
                                </button>
                            @else
                                <button type="button" class="btn btn-success w-100 py-2 rounded-3 fw-bold disabled" disabled>
                                    <i class="fas fa-check me-2"></i> Verified
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function previewUpload(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var img = document.getElementById(previewId);
                img.src = e.target.result;
                img.classList.add('active');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush