@extends('reseller.layouts.app')

@section('title', 'অ্যাকাউন্ট ভেরিফিকেশন')

@push('styles')
<style>
    /* --- Premium Page Styles --- */
    :root {
        --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        --surface-color: #ffffff;
        --border-color: #e2e8f0;
        --text-muted: #64748b;
    }

    /* Step Card Styles */
    .step-card {
        background: var(--surface-color);
        border-radius: 20px;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        position: relative;
    }
    .step-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.06);
    }

    .step-header {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }
    .step-number {
        background: var(--primary-gradient);
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        margin-right: 12px;
        box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);
    }

    /* Upload Zone */
    .upload-zone-wrapper { position: relative; height: 100%; }
    
    .upload-input-hidden {
        position: absolute;
        width: 0.1px; height: 0.1px; opacity: 0; overflow: hidden; z-index: -1;
    }

    .custom-upload-box {
        border: 2px dashed #cbd5e1;
        border-radius: 16px;
        background-color: #f8fafc;
        min-height: 220px;
        display: flex; /* Default Flex */
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .custom-upload-box:hover {
        border-color: #4f46e5;
        background-color: #eef2ff;
    }

    .upload-icon-circle {
        width: 60px; height: 60px;
        background: white;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        margin-bottom: 15px;
        transition: transform 0.3s ease;
    }
    .custom-upload-box:hover .upload-icon-circle { transform: scale(1.1); }

    /* Preview Styles */
    .preview-container {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        width: 100%;
        height: 220px;
        display: none; /* Hidden by default */
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border: 1px solid var(--border-color);
    }

    .preview-img {
        width: 100%; height: 100%; object-fit: cover;
    }

    .preview-overlay {
        position: absolute; bottom: 0; left: 0; right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
        padding: 20px;
        display: flex; justify-content: space-between; align-items: flex-end;
        opacity: 0; transition: opacity 0.3s ease; height: 100%;
    }
    .preview-container:hover .preview-overlay { opacity: 1; }

    .action-btn {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255,255,255,0.3);
        color: white;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 0.85rem;
        cursor: pointer;
    }
    .action-btn:hover { background: white; color: #333; }
    .btn-delete:hover { background: #ef4444; color: white; border-color: #ef4444; }

    /* Submit Footer */
    .submit-footer {
        background: white; border-top: 1px solid var(--border-color);
        padding: 15px 20px; position: sticky; bottom: 0; z-index: 99;
        margin: 0 -1.5rem -1.5rem -1.5rem;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.05);
    }
    @media (min-width: 992px) {
        .submit-footer { position: static; border: none; box-shadow: none; padding: 20px 0; margin: 0; background: transparent; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">
    
    <div class="mb-4">
        <h4 class="fw-bold text-dark mb-1">প্রোফাইল ভেরিফিকেশন</h4>
        <p class="text-muted small">নিরাপদ লেনদেনের জন্য আপনার সঠিক তথ্য প্রদান করুন</p>
    </div>

    @if($user->verification_status == 'rejected')
        <div class="alert alert-danger mb-4 rounded-3 border-0 bg-danger bg-opacity-10 text-danger">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle fa-2x me-3"></i>
                <div>
                    <h6 class="fw-bold mb-1">আবেদন বাতিল করা হয়েছে</h6>
                    <p class="mb-0 small">{{ $user->verification_note ?? 'অনুগ্রহ করে ডকুমেন্ট পুনরায় আপলোড করুন।' }}</p>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('reseller.verification.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">
            
            <div class="col-lg-4 col-md-6">
                <div class="step-card p-4">
                    <div class="step-header">
                        <span class="step-number">১</span>
                        <h6 class="fw-bold m-0 text-dark">NID সামনের অংশ</h6>
                    </div>
                    
                    <div class="upload-zone-wrapper">
                        <input type="file" name="voter_id_front" id="voter_id_front" class="upload-input-hidden" accept="image/*" onchange="previewImage(this, 'front')">
                        
                        @php $hasFront = !empty($user->voter_id_front); @endphp

                        <label for="voter_id_front" class="custom-upload-box w-100" id="box_front" style="{{ $hasFront ? 'display:none;' : 'display:flex;' }}">
                            <div class="upload-icon-circle">
                                <i class="fas fa-address-card fa-lg text-primary"></i>
                            </div>
                            <span class="fw-bold text-dark">ছবি আপলোড করুন</span>
                            <small class="text-muted mt-1">NID এর সামনের অংশ</small>
                        </label>

                        <div class="preview-container" id="preview_front_container" style="{{ $hasFront ? 'display:block;' : 'display:none;' }}">
                            <img src="{{ $hasFront ? asset($user->voter_id_front) : '' }}" class="preview-img" id="preview_front_img">
                            <div class="preview-overlay">
                                <span class="badge bg-success bg-opacity-75 backdrop-blur">
                                    {{ $hasFront ? 'সংরক্ষিত আছে' : 'নতুন ছবি' }}
                                </span>
                                <button type="button" class="action-btn btn-delete" onclick="removeImage('front')">
                                    <i class="fas fa-trash-alt me-1"></i> ডিলিট
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="step-card p-4">
                    <div class="step-header">
                        <span class="step-number">২</span>
                        <h6 class="fw-bold m-0 text-dark">NID পেছনের অংশ</h6>
                    </div>
                    
                    <div class="upload-zone-wrapper">
                        <input type="file" name="voter_id_back" id="voter_id_back" class="upload-input-hidden" accept="image/*" onchange="previewImage(this, 'back')">
                        
                        @php $hasBack = !empty($user->voter_id_back); @endphp

                        <label for="voter_id_back" class="custom-upload-box w-100" id="box_back" style="{{ $hasBack ? 'display:none;' : 'display:flex;' }}">
                            <div class="upload-icon-circle">
                                <i class="fas fa-id-card-alt fa-lg text-primary"></i>
                            </div>
                            <span class="fw-bold text-dark">ছবি আপলোড করুন</span>
                            <small class="text-muted mt-1">NID এর পেছনের অংশ</small>
                        </label>

                        <div class="preview-container" id="preview_back_container" style="{{ $hasBack ? 'display:block;' : 'display:none;' }}">
                            <img src="{{ $hasBack ? asset($user->voter_id_back) : '' }}" class="preview-img" id="preview_back_img">
                            <div class="preview-overlay">
                                <span class="badge bg-success bg-opacity-75 backdrop-blur">
                                    {{ $hasBack ? 'সংরক্ষিত আছে' : 'নতুন ছবি' }}
                                </span>
                                <button type="button" class="action-btn btn-delete" onclick="removeImage('back')">
                                    <i class="fas fa-trash-alt me-1"></i> ডিলিট
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-12">
                <div class="step-card p-4">
                    <div class="step-header">
                        <span class="step-number">৩</span>
                        <h6 class="fw-bold m-0 text-dark">আপনার নিজের ছবি</h6>
                    </div>
                    
                    <div class="upload-zone-wrapper">
                        <input type="file" name="self_image" id="self_image" class="upload-input-hidden" accept="image/*" capture="user" onchange="previewImage(this, 'self')">
                        
                        @php $hasSelf = !empty($user->self_image); @endphp

                        <label for="self_image" class="custom-upload-box w-100" id="box_self" style="{{ $hasSelf ? 'display:none;' : 'display:flex;' }}">
                            <div class="upload-icon-circle">
                                <i class="fas fa-camera fa-lg text-primary"></i>
                            </div>
                            <span class="fw-bold text-dark">সেলফি তুলুন</span>
                            <small class="text-muted mt-1">আপনার স্পষ্ট মুখের ছবি</small>
                        </label>

                        <div class="preview-container" id="preview_self_container" style="{{ $hasSelf ? 'display:block;' : 'display:none;' }}">
                            <img src="{{ $hasSelf ? asset($user->self_image) : '' }}" class="preview-img" id="preview_self_img">
                            <div class="preview-overlay">
                                <span class="badge bg-success bg-opacity-75 backdrop-blur">
                                    {{ $hasSelf ? 'সংরক্ষিত আছে' : 'নতুন ছবি' }}
                                </span>
                                <button type="button" class="action-btn btn-delete" onclick="removeImage('self')">
                                    <i class="fas fa-trash-alt me-1"></i> ডিলিট
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="submit-footer d-flex justify-content-between align-items-center mt-4">
            <div class="d-none d-md-block">
                <small class="text-muted"><i class="fas fa-shield-alt me-1"></i> আপনার তথ্য আমাদের কাছে ১০০% নিরাপদ</small>
            </div>
            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold w-100 w-md-auto shadow-sm" style="background: var(--primary-gradient); border: none;">
                ভেরিফিকেশন জমা দিন <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function previewImage(input, type) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(`preview_${type}_img`).src = e.target.result;
                document.getElementById(`box_${type}`).style.display = 'none';
                document.getElementById(`preview_${type}_container`).style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    }

    function removeImage(type) {
        // Clear input
        const input = document.getElementById(type === 'front' ? 'voter_id_front' : (type === 'back' ? 'voter_id_back' : 'self_image'));
        input.value = '';

        // Reset View
        document.getElementById(`preview_${type}_container`).style.display = 'none';
        document.getElementById(`box_${type}`).style.display = 'flex';
        
        // Optional: If you want to clear the 'src' to avoid showing old image if they cancel upload
        // document.getElementById(`preview_${type}_img`).src = ''; 
    }
</script>
@endpush