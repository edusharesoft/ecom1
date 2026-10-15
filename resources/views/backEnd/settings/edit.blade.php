@extends('backEnd.layouts.master')
@section('title','General Settings Configuration')

@section('css')
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('public/backEnd')}}/assets/libs/summernote/summernote-lite.min.css" rel="stylesheet" type="text/css" />
<style>
    /* 1. PROFESSIONAL CARD DESIGN */
    .settings-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        margin-bottom: 30px;
    }
    
    .section-title-pro {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
        padding: 15px 25px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* 2. LOGO PREVIEW STYLING */
    .logo-preview-box {
        background: #f1f5f9;
        border-radius: 12px;
        padding: 15px;
        text-align: center;
        border: 1px dashed #cbd5e1;
        margin-top: 10px;
    }
    .edit-image-pro {
        max-height: 60px;
        width: auto;
        border-radius: 6px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    /* 3. INPUT REFINEMENT */
    .form-label-pro {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
    }
    .custom-input {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 15px;
        transition: all 0.2s;
    }
    .custom-input:focus {
        background: #fff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    /* 4. COLOR PICKER WRAPPER */
    .color-box-pro {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* 5. ACTION BUTTON */
    .btn-save-pro {
        background: #0f172a;
        color: #fff;
        padding: 12px 35px;
        border-radius: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        border: none;
        transition: 0.3s;
    }
    .btn-save-pro:hover {
        background: #334155;
        transform: translateY(-2px);
        box-shadow: 0 10px 15px rgba(0,0,0,0.1);
        color: #fff;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0 text-dark">General Settings</h4>
            <span class="text-muted small">Update site identity, appearance and business rules</span>
        </div>
    </div>

    <form action="{{route('settings.update')}}" method="POST" data-parsley-validate="" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{$edit_data->id}}">

        <div class="row">
            <div class="col-lg-8">
                <div class="settings-card">
                    <div class="section-title-pro">
                        <i class="mdi mdi-web text-primary"></i> Basic Information
                    </div>
                    <div class="p-4 row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label-pro">Site Name *</label>
                            <input type="text" name="name" class="form-control custom-input" value="{{ $edit_data->name }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label-pro">FB Page Username</label>
                            <input type="text" name="facebook_page_username" class="form-control custom-input" value="{{ $edit_data->facebook_page_username }}" required>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                <label class="form-label-pro mb-0">Breaking News Ticker</label>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="news_ticker_enabled" value="1" id="newsTickerEnabled"
                                        {{ ($edit_data->news_ticker_enabled ?? 1) == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="newsTickerEnabled">
                                        <strong>টিকার চালু</strong>
                                    </label>
                                </div>
                            </div>
                            <textarea name="top_headline" class="form-control custom-input" rows="2" placeholder="World Cup-এর সকল জনপ্রিয় প্রোডাক্ট এখন একসাথে পাওয়া যাচ্ছে...">{{ $edit_data->top_headline }}</textarea>
                            <small class="text-muted">টিকার চালু থাকলে এবং টেক্সট লিখলে সাইটের উপরে Breaking News বার দেখাবে। বন্ধ করলে টেক্সট সংরক্ষিত থাকবে কিন্তু দেখাবে না।</small>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label-pro">Footer About Text</label>
                            <textarea name="footer_about_text" class="form-control custom-input" rows="3" placeholder="আপনার ব্যবসার ডিজিটাল পার্টনার। আমরা বিশ্বাস করি গুণগত মান এবং গ্রাহক সন্তুষ্টিতে। প্রযুক্তির সাথে এগিয়ে চলুন আমাদের সাথে।">{{ $edit_data->footer_about_text ?? '' }}</textarea>
                            <small class="text-muted">This text appears in the footer about section on the frontend</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label-pro">Google Play App Link</label>
                            <input type="url" name="google_play_link" class="form-control custom-input" value="{{ $edit_data->google_play_link ?? '' }}" placeholder="https://play.google.com/store/apps/...">
                            <small class="text-muted">Footer - Google Play download button</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label-pro">App Store Link</label>
                            <input type="url" name="app_store_link" class="form-control custom-input" value="{{ $edit_data->app_store_link ?? '' }}" placeholder="https://apps.apple.com/...">
                            <small class="text-muted">Footer - App Store download button</small>
                        </div>
                    </div>
                </div>

                <div class="settings-card">
                    <div class="section-title-pro">
                        <i class="mdi mdi-palette text-success"></i> Theme Appearance
                    </div>
                    <div class="p-4">
                        <div class="row g-3">
                            @php
                                $colors = [
                                    'primary_color' => ['label' => 'Primary Color', 'default' => '#0d6efd'],
                                    'secodery_color' => ['label' => 'Secondary Color', 'default' => '#198754'],
                                    'footer_color' => ['label' => 'Footer Color', 'default' => '#222222'],
                                    'copyright_color' => ['label' => 'Copyright Color', 'default' => '#111111']
                                ];
                            @endphp
                            @foreach($colors as $key => $color)
                            <div class="col-md-6 col-xl-3">
                                <label class="form-label-pro">{{ $color['label'] }}</label>
                                <div class="color-box-pro">
                                    <input type="color" name="{{ $key }}" id="{{ $key }}_cp" 
                                           value="{{ old($key, $edit_data->$key ?? $color['default']) }}"
                                           class="form-control-color border-0 bg-transparent" 
                                           oninput="document.getElementById('{{ $key }}_txt').value=this.value;">
                                    <input type="text" id="{{ $key }}_txt" value="{{ old($key, $edit_data->$key ?? $color['default']) }}" 
                                           class="form-control border-0 p-0 small text-uppercase fw-bold" 
                                           style="font-size: 11px;"
                                           oninput="document.getElementById('{{ $key }}_cp').value=this.value;">
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="row mt-4 g-3">
                            @php
                                $logos = [
                                    'white_logo' => 'White Logo (For Dark Bg)',
                                    'dark_logo' => 'Dark Logo (For Light Bg)',
                                    'favicon' => 'Favicon Icon',
                                    'og_baner' => 'Social Banner (OG)'
                                ];
                            @endphp
                            @foreach($logos as $slug => $label)
                            <div class="col-md-6">
                                <label class="form-label-pro">{{ $label }}</label>
                                <input type="file" name="{{ $slug }}" class="form-control custom-input mb-2">
                                <div class="logo-preview-box">
                                    <img src="{{asset($edit_data->$slug)}}" class="edit-image-pro" alt="Preview">
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="settings-card">
                    <div class="section-title-pro">
                        <i class="mdi mdi-shield-check text-danger"></i> Business Logic
                    </div>
                    <div class="p-4">
                        <div class="mb-4">
                            <label class="form-label-pro">Hot Deal End Date</label>
                            <input type="date" name="hot_deal_end_date" class="form-control custom-input" value="{{ $edit_data->hot_deal_end_date }}">
                        </div>
                        <div class="mb-4">
                            <label class="form-label-pro">Flash Sale End Date</label>
                            <input type="date" name="flash_sale_end_date" class="form-control custom-input" value="{{ $edit_data->flash_sale_end_date }}">
                        </div>
                        <div class="mb-4">
                            <label class="form-label-pro">Visibility Controls</label>
                            <div class="d-grid gap-2">
                                <select class="form-select custom-input" name="show_all_products">
                                    <option value="1" @if($edit_data->show_all_products==1) selected @endif>Home: Show All Products</option>
                                    <option value="0" @if($edit_data->show_all_products==0) selected @endif>Home: Hide All Products</option>
                                </select>
                                <select class="form-select custom-input" name="show_category_wise_products">
                                    <option value="1" @if($edit_data->show_category_wise_products==1) selected @endif>Home: Category Wise On</option>
                                    <option value="0" @if($edit_data->show_category_wise_products==0) selected @endif>Home: Category Wise Off</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label-pro">হোমপেজ সেকশন (On/Off)</label>
                            <div class="d-grid gap-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="homepage_brands_enabled" value="1" id="homepageBrandsEnabled"
                                        {{ ($edit_data->homepage_brands_enabled ?? 1) == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="homepageBrandsEnabled">
                                        <strong>Brands</strong> — হোমপেজে ব্র্যান্ড সেকশন দেখাবে
                                    </label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="homepage_vendors_enabled" value="1" id="homepageVendorsEnabled"
                                        {{ ($edit_data->homepage_vendors_enabled ?? 1) == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="homepageVendorsEnabled">
                                        <strong>Our Featured Shops</strong> — হোমপেজে ভেন্ডর শপ সেকশন দেখাবে
                                    </label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="homepage_blogs_enabled" value="1" id="homepageBlogsEnabled"
                                        {{ ($edit_data->homepage_blogs_enabled ?? 1) == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="homepageBlogsEnabled">
                                        <strong>Latest Blogs</strong> — হোমপেজে ব্লগ সেকশন দেখাবে
                                    </label>
                                </div>
                            </div>
                            <small class="text-muted">বন্ধ করলে শুধু হোমপেজ থেকে লুকাবে; ব্র্যান্ড/ভেন্ডর/ব্লগ পেজ আলাদা থাকবে।</small>
                        </div>
                        <div class="mb-4">
                            <label class="form-label-pro">System Modules</label>
                            <div class="d-grid gap-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="vendor_enabled" value="1" id="vendorEnabled" {{ ($edit_data->vendor_enabled ?? 1) == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="vendorEnabled">
                                        <strong>Vendor System</strong> - Enable/Disable vendor functionality
                                    </label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="reseller_enabled" value="1" id="resellerEnabled" {{ ($edit_data->reseller_enabled ?? 1) == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="resellerEnabled">
                                        <strong>Reseller System</strong> - Enable/Disable reseller functionality
                                    </label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="checkout_otp_enabled" value="1" id="checkoutOtpEnabled" {{ ($edit_data->checkout_otp_enabled ?? 0) == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="checkoutOtpEnabled">
                                        <strong>Checkout OTP (SMS)</strong> — চেকআউট জমার সময় কাস্টমারের মোবাইলে OTP পাঠিয়ে ভেরিফাই করুন। বন্ধ থাকলে আগের মতো সরাসরি অর্ডার হবে।
                                    </label>
                                </div>
                            </div>
                            <small class="text-muted">When disabled, these features will be hidden from the system</small>
                        </div>
                        <div class="mb-4">
                            <label class="form-label-pro">রিসেলার ডিপোজিট সীমা (৳)</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="reseller_deposit_min" class="form-control custom-input" 
                                           value="{{ $edit_data->reseller_deposit_min ?? 100 }}" min="1" step="1" placeholder="ন্যূনতম">
                                    <small class="text-muted">ন্যূনতম ডিপোজিট</small>
                                </div>
                                <div class="col-6">
                                    <input type="number" name="reseller_deposit_max" class="form-control custom-input" 
                                           value="{{ $edit_data->reseller_deposit_max ?? 1000000 }}" min="100" step="1" placeholder="সর্বোচ্চ">
                                    <small class="text-muted">সর্বোচ্চ ডিপোজিট</small>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label-pro">রিসেলার ওয়ালেট সর্বনিম্ন ব্যালেন্স (৳)</label>
                            <input type="number" name="reseller_wallet_min_balance" class="form-control custom-input" 
                                   value="{{ $edit_data->reseller_wallet_min_balance ?? 0 }}" min="0" step="1" placeholder="0">
                            <small class="text-muted">এমন পরিমাণ যা রিসেলার কখনো উত্তোলন করতে পারবে না। উত্তোলনের পর ব্যালেন্স এই অংকের কম হতে পারবে না।</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="settings-card">
                    <div class="section-title-pro">
                        <i class="mdi mdi-text-box-outline text-info"></i> Policies & Notes
                    </div>
                    <div class="p-4">
                        <div class="mb-4">
                            <label class="form-label-pro">Checkout Note</label>
                            <textarea class="summernote" name="checkout_note">{{ $edit_data->checkout_note }}</textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label-pro">Order Policy</label>
                            <textarea class="summernote" name="order_policy">{{ $edit_data->order_policy }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 text-center mb-5">
                <button type="submit" class="btn-save-pro">
                    <i class="mdi mdi-content-save-all me-2"></i> Update Global Settings
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
<script src="{{asset('public/backEnd/')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-validation.init.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/libs/select2/js/select2.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-advanced.init.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/libs/summernote/summernote-lite.min.js"></script>
<script>
    $(document).ready(function() {
        $('.summernote').summernote({
            placeholder: 'Type your policy or notes here...',
            height: 200,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview']]
            ]
        });
    });
</script>
@endsection