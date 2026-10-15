@extends('vendor.layouts.app')

@section('title', 'Account Settings')
@section('page-title', 'Account Settings')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --primary: #4f46e5;
        --primary-hover: #4338ca;
        --text-dark: #0f172a;
        --text-gray: #64748b;
        --bg-light: #f8fafc;
        --border-color: #e2e8f0;
    }

    body {
        font-family: 'Inter', sans-serif;
        background-color: var(--bg-light);
        color: var(--text-dark);
    }

    /* Layout Structure */
    .settings-container {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    
    @media (min-width: 992px) {
        .settings-container {
            flex-direction: row;
            align-items: flex-start;
        }
        .settings-sidebar {
            width: 280px;
            flex-shrink: 0;
            position: sticky;
            top: 20px;
        }
        .settings-content {
            flex-grow: 1;
        }
    }

    /* Sidebar Navigation */
    .nav-card {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    
    .settings-nav-item {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        color: var(--text-gray);
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.2s ease;
        margin-bottom: 4px;
        border: none;
        background: transparent;
        width: 100%;
        text-align: left;
    }
    
    .settings-nav-item:hover {
        background-color: #f1f5f9;
        color: var(--text-dark);
    }
    
    .settings-nav-item.active {
        background-color: #eef2ff;
        color: var(--primary);
        font-weight: 600;
    }
    
    .settings-nav-item i {
        width: 24px;
        font-size: 1.1rem;
        margin-right: 8px;
    }

    /* Content Cards */
    .section-card {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        margin-bottom: 24px;
        overflow: hidden;
    }
    
    .section-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
    }
    
    .section-body {
        padding: 24px;
    }

    /* Form Styles */
    .form-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--text-dark);
        margin-bottom: 6px;
    }
    
    .form-control {
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 0.95rem;
        color: #334155;
        background-color: #fff;
        transition: all 0.2s;
    }
    
    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    /* Branding Section (Logo & Banner) */
    .branding-preview {
        position: relative;
        margin-bottom: 40px;
    }
    
    .banner-preview {
        height: 160px;
        width: 100%;
        background-color: #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        background-size: cover;
        background-position: center;
        position: relative;
        border: 1px solid var(--border-color);
    }
    
    .logo-preview {
        width: 100px;
        height: 100px;
        background-color: #fff;
        border-radius: 50%;
        border: 4px solid #fff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        position: absolute;
        bottom: -30px;
        left: 24px;
        overflow: hidden;
    }
    .logo-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .upload-btn-wrapper {
        position: relative;
        overflow: hidden;
        display: inline-block;
    }
    
    .tab-content { display: none; animation: fadeIn 0.3s ease; }
    .tab-content.active { display: block; }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .btn-primary {
        background-color: var(--primary);
        border-color: var(--primary);
        padding: 10px 24px;
        font-weight: 500;
    }
    .btn-primary:hover {
        background-color: var(--primary-hover);
    }
</style>
@endpush

@section('content')
<div class="settings-container">
    
    <aside class="settings-sidebar">
        <div class="nav-card">
            <button onclick="switchTab('shop-info')" class="settings-nav-item active" id="nav-shop-info">
                <i class="fas fa-store"></i> Shop Profile
            </button>
            <button onclick="switchTab('profile')" class="settings-nav-item" id="nav-profile">
                <i class="fas fa-user-cog"></i> Personal Info
            </button>
            <button onclick="switchTab('password')" class="settings-nav-item" id="nav-password">
                <i class="fas fa-lock"></i> Security
            </button>
        </div>
        
        <div class="mt-4 p-3 rounded-3 bg-white border border-light shadow-sm">
            <h6 class="text-dark fw-bold mb-2 small"><i class="fas fa-lightbulb text-warning me-1"></i> Pro Tip</h6>
            <p class="text-muted small mb-0">Complete your shop profile with a high-quality logo and banner to attract more customers.</p>
        </div>
    </aside>

    <main class="settings-content">
        
        <div id="shop-info" class="tab-content active">
            <form action="{{ route('vendor.settings.shop-info') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="section-card">
                    <div class="section-header">
                        <h6 class="fw-bold m-0 text-dark">Shop Branding</h6>
                        <p class="text-muted small mb-0">Customize how your shop looks to customers.</p>
                    </div>
                    <div class="section-body pb-5">
                        <div class="branding-preview">
                            <div class="banner-preview" style="background-image: url('{{ $vendor->banner ? asset($vendor->banner) : '' }}'); background-color: #f1f5f9;">
                                @if(!$vendor->banner)
                                    <div class="d-flex h-100 justify-content-center align-items-center text-muted small">No Banner Uploaded</div>
                                @endif
                            </div>
                            
                            <div class="logo-preview">
                                <img src="{{ $vendor->logo ? asset($vendor->logo) : asset('public/backEnd/assets/images/users/avatar-1.jpg') }}" alt="Shop Logo">
                            </div>
                        </div>

                        <div class="row mt-4 pt-2 g-3">
                            <div class="col-md-6">
                                <label class="form-label">Shop Logo (Square)</label>
                                <input type="file" class="form-control form-control-sm" name="logo" accept="image/*">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shop Banner (Landscape)</label>
                                <input type="file" class="form-control form-control-sm" name="banner" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-header">
                        <h6 class="fw-bold m-0 text-dark">General Information</h6>
                    </div>
                    <div class="section-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label">Shop Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="shop_name" value="{{ old('shop_name', $vendor->shop_name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Owner Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="owner_name" value="{{ old('owner_name', $vendor->owner_name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Business Email</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email', $vendor->email) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact Phone</label>
                                <input type="text" class="form-control" name="phone" value="{{ old('phone', $vendor->phone) }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Shop Address</label>
                                <textarea class="form-control" name="address" rows="3">{{ old('address', $vendor->address) }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-top p-3 text-end">
                        <button type="submit" class="btn btn-primary rounded-pill shadow-sm">
                            <i class="fas fa-check me-2"></i>Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div id="profile" class="tab-content">
            <div class="section-card">
                <div class="section-header">
                    <h6 class="fw-bold m-0 text-dark">Personal Profile</h6>
                    <p class="text-muted small mb-0">Manage your login details and personal info.</p>
                </div>
                <div class="section-body">
                    <form action="{{ route('vendor.settings.profile') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="d-flex align-items-center mb-4">
                            <div class="me-4">
                                <img src="{{ $user->image ? asset($user->image) : asset('public/backEnd/assets/images/users/avatar-1.jpg') }}" 
                                     class="rounded-circle border" width="80" height="80" style="object-fit: cover;">
                            </div>
                            <div>
                                <label class="form-label mb-1">Update Avatar</label>
                                <input type="file" class="form-control form-control-sm w-auto" name="image">
                                <small class="text-muted d-block mt-1">Allowed JPG, GIF or PNG. Max size of 800K</small>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Login Email</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" required>
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary rounded-pill shadow-sm">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="password" class="tab-content">
            <div class="section-card">
                <div class="section-header">
                    <h6 class="fw-bold m-0 text-dark">Security Settings</h6>
                    <p class="text-muted small mb-0">Ensure your account stays secure.</p>
                </div>
                <div class="section-body">
                    <form action="{{ route('vendor.settings.password') }}" method="POST">
                        @csrf
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <div class="mb-3">
                                    <label class="form-label">Current Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-lock text-muted"></i></span>
                                        <input type="password" class="form-control border-start-0 ps-0" name="current_password" required>
                                    </div>
                                    @error('current_password') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-key text-muted"></i></span>
                                        <input type="password" class="form-control border-start-0 ps-0" name="password" required>
                                    </div>
                                    @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Confirm New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-check-circle text-muted"></i></span>
                                        <input type="password" class="form-control border-start-0 ps-0" name="password_confirmation" required>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 rounded-pill shadow-sm">
                                    Change Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </main>
</div>
@endsection

@push('scripts')
<script>
    function switchTab(tabId) {
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(el => {
            el.classList.remove('active');
        });
        
        // Remove active class from buttons
        document.querySelectorAll('.settings-nav-item').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Activate current selection
        document.getElementById(tabId).classList.add('active');
        document.getElementById('nav-' + tabId).classList.add('active');
    }
</script>
@endpush