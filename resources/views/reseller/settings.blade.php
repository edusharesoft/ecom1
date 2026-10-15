@extends('reseller.layouts.app')

@section('title', 'প্রোফাইল সেটিংস')
@section('page-title', 'সেটিংস')

@push('styles')
<style>
    :root {
        --primary: #4f46e5;
        --primary-light: #eef2ff;
        --text-dark: #1e293b;
        --text-muted: #64748b;
        --border-color: #e2e8f0;
        --card-radius: 16px;
    }

    /* Layout */
    .settings-card {
        background: #fff;
        border-radius: var(--card-radius);
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    /* Navigation Menu */
    .settings-nav {
        padding: 10px;
    }

    .nav-item-link {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        color: var(--text-muted);
        border-radius: 12px;
        transition: all 0.3s;
        border: none;
        background: transparent;
        width: 100%;
        text-align: left;
        margin-bottom: 5px;
        font-weight: 500;
    }

    .nav-item-link:hover {
        background-color: #f8fafc;
        color: var(--primary);
    }

    .nav-item-link.active {
        background-color: var(--primary-light);
        color: var(--primary);
        font-weight: 600;
    }

    .nav-item-link i {
        width: 24px;
        font-size: 1.1rem;
    }

    /* Form Styles */
    .form-section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 5px;
    }

    .form-control {
        border-radius: 10px;
        border: 1px solid var(--border-color);
        padding: 12px 15px;
        font-size: 0.95rem;
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }

    .input-group-text {
        background: #f8fafc;
        border: 1px solid var(--border-color);
        border-right: none;
        border-radius: 10px 0 0 10px;
        color: var(--text-muted);
    }
    
    .input-group .form-control {
        border-left: none;
    }

    /* Avatar Upload */
    .avatar-wrapper {
        position: relative;
        width: 100px;
        height: 100px;
        margin: 0 auto;
    }

    .profile-pic {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .upload-btn-wrapper {
        position: absolute;
        bottom: 0;
        right: 0;
    }

    .btn-upload {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: 2px solid #fff;
        transition: transform 0.2s;
    }

    .btn-upload:hover {
        transform: scale(1.1);
    }

    /* Tab Logic */
    .tab-pane { display: none; animation: fadeIn 0.3s ease; }
    .tab-pane.active { display: block; }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')

<div class="row g-4">
    <div class="col-lg-3">
        <div class="settings-card h-100">
            <div class="p-4 border-bottom text-center bg-light">
                <div class="avatar-wrapper mb-3">
                    <img src="{{ $user->image ? asset($user->image) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=4f46e5&color=fff' }}" 
                         alt="Profile" class="profile-pic">
                </div>
                <h6 class="fw-bold text-dark mb-0">{{ $user->name }}</h6>
                <small class="text-muted">{{ $user->shop_name ?? 'Reseller' }}</small>
            </div>
            <div class="settings-nav">
                <button onclick="switchTab('profile', this)" class="nav-item-link active">
                    <i class="fas fa-user-circle me-2"></i> প্রোফাইল তথ্য
                </button>
                <button onclick="switchTab('password', this)" class="nav-item-link">
                    <i class="fas fa-lock me-2"></i> পাসওয়ার্ড পরিবর্তন
                </button>
                <button onclick="switchTab('payment', this)" class="nav-item-link">
                    <i class="fas fa-credit-card me-2"></i> পেমেন্ট মেথড
                </button>
            </div>
        </div>
    </div>

    <div class="col-lg-9">
        
        <div id="profile" class="tab-pane active">
            <div class="settings-card p-4">
                <div class="mb-4">
                    <h5 class="form-section-title">ব্যক্তিগত তথ্য</h5>
                    <p class="text-muted small">আপনার প্রোফাইল তথ্য আপডেট করুন</p>
                </div>

                <form action="{{ route('reseller.settings.profile') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="d-flex align-items-center mb-4 p-3 bg-light rounded-3 border">
                        <div class="avatar-wrapper me-4 ms-0">
                            <img id="previewImg" src="{{ $user->image ? asset($user->image) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=random' }}" class="profile-pic">
                            <div class="upload-btn-wrapper">
                                <label for="imageUpload" class="btn-upload">
                                    <i class="fas fa-camera" style="font-size: 12px;"></i>
                                </label>
                                <input type="file" name="image" id="imageUpload" class="d-none" accept="image/*" onchange="previewFile(this)">
                            </div>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">প্রোফাইল ছবি</h6>
                            <small class="text-muted d-block">PNG, JPG বা WEBP (সর্বোচ্চ 2MB)</small>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">আপনার নাম <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $user->name) }}" required>
                            </div>
                            @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">শপ/দোকানের নাম</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-store"></i></span>
                                <input type="text" class="form-control @error('shop_name') is-invalid @enderror" name="shop_name" value="{{ old('shop_name', $user->shop_name) }}">
                            </div>
                            @error('shop_name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">ইমেইল এড্রেস <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email) }}" required>
                            </div>
                            @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">ফোন নাম্বার <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $customer->phone ?? '') }}" required>
                            </div>
                            @error('phone') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top text-end">
                        <button type="submit" class="btn btn-primary px-4 rounded-pill fw-bold shadow-sm">
                            <i class="fas fa-save me-2"></i> পরিবর্তন সেভ করুন
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div id="password" class="tab-pane">
            <div class="settings-card p-4">
                <div class="mb-4">
                    <h5 class="form-section-title">নিরাপত্তা সেটিংস</h5>
                    <p class="text-muted small">আপনার অ্যাকাউন্টের পাসওয়ার্ড পরিবর্তন করুন</p>
                </div>

                <form action="{{ route('reseller.settings.password') }}" method="POST">
                    @csrf
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label fw-bold small">বর্তমান পাসওয়ার্ড <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" name="current_password" required placeholder="••••••••">
                            </div>
                            @error('current_password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">নতুন পাসওয়ার্ড <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required placeholder="কমপক্ষে ৬ অক্ষর">
                            </div>
                            @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">কনফার্ম পাসওয়ার্ড <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                                <input type="password" class="form-control" name="password_confirmation" required placeholder="পুনরায় লিখুন">
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top text-end">
                        <button type="submit" class="btn btn-primary px-4 rounded-pill fw-bold shadow-sm">
                            <i class="fas fa-shield-alt me-2"></i> পাসওয়ার্ড আপডেট করুন
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div id="payment" class="tab-pane">
            <div class="settings-card p-5 text-center">
                <div class="mb-3">
                    <i class="fas fa-wallet fa-4x text-muted opacity-25"></i>
                </div>
                <h5 class="text-dark fw-bold">পেমেন্ট মেথড</h5>
                <p class="text-muted">এই ফিচারটি শীঘ্রই আসছে। আপনি এখন ম্যানুয়াল উইথড্র রিকোয়েস্ট করতে পারেন।</p>
                <a href="{{ route('reseller.withdrawals.index') }}" class="btn btn-outline-primary rounded-pill">
                    উইথড্র রিকোয়েস্ট পেজে যান
                </a>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    // Tab Switching Logic
    function switchTab(tabId, btn) {
        // Hide all tabs
        document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active'));
        // Deactivate all buttons
        document.querySelectorAll('.nav-item-link').forEach(el => el.classList.remove('active'));
        
        // Show target tab
        document.getElementById(tabId).classList.add('active');
        // Activate button
        btn.classList.add('active');
    }

    // Image Preview
    function previewFile(input) {
        var file = input.files[0];
        if(file){
            var reader = new FileReader();
            reader.onload = function(){
                document.getElementById('previewImg').src = reader.result;
            }
            reader.readAsDataURL(file);
        }
    }
</script>
@endpush