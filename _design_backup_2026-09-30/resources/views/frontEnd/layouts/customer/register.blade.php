@extends('frontEnd.layouts.master')
@section('title','Customer Register')

@section('content')
{{-- CSS সরাসরি এখানে --}}
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    .modern-auth-section {
        background-color: #f0f2f5;
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 50px 15px;
        font-family: 'Poppins', sans-serif;
    }

    .auth-container {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        overflow: hidden;
        width: 100%;
        max-width: 950px;
        display: flex;
        flex-wrap: wrap;
    }

    /* ---- বাম পাশ (ইমেজ এরিয়া) ---- */
    .auth-image-area {
        width: 50%;
        /* শপিং রিলেটেড একটি সুন্দর ব্যাকগ্রাউন্ড ইমেজ */
        background-image: url('{{ asset('public/frontEnd/images/login.avif') }}');
        background-size: cover;
        background-position: center;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 40px;
        color: #fff;
        text-align: center;
    }

    /* ওভারলে */
    .auth-image-area::before {
        content: "";
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        z-index: 1;
    }

    /* টেক্সট */
    .auth-image-area h2,
    .auth-image-area p {
        position: relative;
        z-index: 2;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        color: #fff;
    }
    .auth-image-area h2 { font-weight: 700; margin-bottom: 10px; font-size: 32px; }
    .auth-image-area p { font-size: 16px; opacity: 1; }


    /* ---- ডান পাশ (ফর্ম এরিয়া) ---- */
    .auth-form-area {
        width: 50%;
        padding: 60px 50px;
        background: #fff;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .auth-header { margin-bottom: 30px; }
    .auth-header h3 { font-weight: 700; color: #333; margin-bottom: 5px; }
    .auth-header p { color: #888; font-size: 14px; }

    /* ইনপুট ডিজাইন */
    .custom-input-group { position: relative; margin-bottom: 20px; }
    .custom-input-group label {
        display: block; margin-bottom: 8px; font-weight: 600; color: #555; font-size: 14px;
    }
    .custom-input {
        width: 100%; height: 50px; padding: 10px 20px 10px 45px; /* আইকনের জন্য বামে প্যাডিং */
        border: 2px solid #eee; border-radius: 10px;
        font-size: 15px; transition: 0.3s; background: #fdfdfd;
    }
    .custom-input:focus {
        border-color: #764ba2; background: #fff; outline: none;
        box-shadow: 0 0 0 4px rgba(118, 75, 162, 0.1);
    }
    textarea.custom-input {
        height: auto; min-height: 80px; padding-top: 15px; padding-bottom: 15px;
        resize: vertical;
    }
    input[type="file"].custom-input {
        padding-left: 20px; height: auto; padding-top: 12px; padding-bottom: 12px;
    }
    .input-icon {
        position: absolute; left: 15px; top: 43px; color: #aaa; font-size: 16px;
    }

    /* সাবমিট বাটন */
    .btn-auth-submit {
        width: 100%; height: 50px;
        background: {{$generalsetting->secodery_color}};
        border: none; border-radius: 10px;
        color: #fff; font-weight: 600; font-size: 16px;
        cursor: pointer; transition: 0.3s;
        text-transform: uppercase; letter-spacing: 1px;
    }
    .btn-auth-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(118, 75, 162, 0.3);
    }

    /* ফুটার লিংক */
    .login-redirect {
        text-align: center; margin-top: 25px; padding-top: 20px; border-top: 1px dashed #ddd;
    }
    .login-redirect p { margin-bottom: 5px; color: #666; font-size: 14px; }
    .login-link {
        text-decoration: none; color: {{$generalsetting->primary_color}}; font-weight: 700; font-size: 15px;
    }
    .login-link:hover { text-decoration: underline; }

    /* মোবাইল রেসপন্সিভ */
    @media (max-width: 768px) {
        .auth-image-area { display: none; }
        .auth-form-area { width: 100%; padding: 40px 20px; }
    }
</style>

<section class="modern-auth-section">
    <div class="container d-flex justify-content-center">
        <div class="auth-container">
            
            {{-- বাম পাশ: ব্যাকগ্রাউন্ড ইমেজ --}}
            <div class="auth-image-area">
                <h2>Join Us Today!</h2>
                <p>নতুন একাউন্ট খুলে আমাদের সেরা শপিং অভিজ্ঞতা উপভোগ করুন।</p>
            </div>

            {{-- ডান পাশ: ফর্ম --}}
            <div class="auth-form-area">
                <div class="auth-header">
                    <h3>রেজিস্ট্রেশন করুন</h3>
                    <p>আপনার তথ্য দিয়ে ফর্মটি পূরণ করুন</p>
                </div>

                <form action="{{route('customer.store')}}" method="POST" enctype="multipart/form-data" data-parsley-validate="">
                    @csrf
                    
                    {{-- Name Input --}}
                    <div class="custom-input-group">
                        <label for="name">আপনার নাম <span id="owner_name_label" style="display: none;">(মালিকের নাম)</span></label>
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" id="name" 
                               class="custom-input @error('name') is-invalid @enderror" 
                               name="name" value="{{ old('name') }}" 
                               placeholder="পুরো নাম লিখুন" required>
                        @error('name')
                            <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Phone Input --}}
                    <div class="custom-input-group">
                        <label for="phone">মোবাইল নাম্বার</label>
                        <i class="fas fa-phone-alt input-icon"></i>
                        <input type="number" id="phone" 
                               class="custom-input @error('phone') is-invalid @enderror" 
                               name="phone" value="{{ old('phone') }}" 
                               placeholder="017xxxxxxxx" required>
                        @error('phone')
                            <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Email Input (for seller) --}}
                    <div class="custom-input-group" id="email_field" style="display: none;">
                        <label for="email">ইমেইল <span class="text-danger">*</span></label>
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" id="email" 
                               class="custom-input @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email') }}" 
                               placeholder="email@example.com">
                        @error('email')
                            <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Shop Name (for seller) --}}
                    <div class="custom-input-group" id="shop_name_field" style="display: none;">
                        <label for="shop_name">শপের নাম <span class="text-danger">*</span></label>
                        <i class="fas fa-store input-icon"></i>
                        <input type="text" id="shop_name" 
                               class="custom-input @error('shop_name') is-invalid @enderror" 
                               name="shop_name" value="{{ old('shop_name') }}" 
                               placeholder="আপনার শপের নাম লিখুন">
                        @error('shop_name')
                            <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Shop Slug (for seller) --}}
                    <div class="custom-input-group" id="slug_field" style="display: none;">
                        <label for="slug">শপ URL (Slug) <span class="text-danger">*</span></label>
                        <i class="fas fa-link input-icon"></i>
                        <input type="text" id="slug" 
                               class="custom-input @error('slug') is-invalid @enderror" 
                               name="slug" value="{{ old('slug') }}" 
                               placeholder="my-shop-name">
                        <small class="text-muted">শুধুমাত্র ইংরেজি অক্ষর, সংখ্যা এবং হাইফেন (-) ব্যবহার করুন</small>
                        @error('slug')
                            <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Address (for seller) --}}
                    <div class="custom-input-group" id="address_field" style="display: none;">
                        <label for="address">ঠিকানা</label>
                        <i class="fas fa-map-marker-alt input-icon"></i>
                        <textarea id="address" 
                                  class="custom-input @error('address') is-invalid @enderror" 
                                  name="address" rows="2" 
                                  placeholder="আপনার শপের ঠিকানা">{{ old('address') }}</textarea>
                        @error('address')
                            <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Logo (for seller) --}}
                    <div class="custom-input-group" id="logo_field" style="display: none;">
                        <label for="logo">শপ লোগো</label>
                        <i class="fas fa-image input-icon"></i>
                        <input type="file" id="logo" 
                               class="custom-input @error('logo') is-invalid @enderror" 
                               name="logo" accept="image/*">
                        <small class="text-muted">সর্বোচ্চ 2MB</small>
                        @error('logo')
                            <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Banner (for seller) --}}
                    <div class="custom-input-group" id="banner_field" style="display: none;">
                        <label for="banner">শপ ব্যানার</label>
                        <i class="fas fa-image input-icon"></i>
                        <input type="file" id="banner" 
                               class="custom-input @error('banner') is-invalid @enderror" 
                               name="banner" accept="image/*">
                        <small class="text-muted">সর্বোচ্চ 3MB</small>
                        @error('banner')
                            <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- বিক্রেতা (ভেন্ডর): ভোটার আইডি/ছবি রেজিস্ট্রেশনে নয় — ড্যাশবোর্ড ভেরিফিকেশনে --}}
                    <div id="seller_verification_note" style="display: none;">
                        <div style="padding: 16px 18px; margin: 12px 0 0; border-radius: 10px; border: 1px solid rgba(118,75,162,0.25); background: linear-gradient(135deg, #f9f7fc 0%, #f3f8ff 100%); color: #444; font-size: 14px; line-height: 1.55;">
                            <strong style="display:block;margin-bottom:6px;"><i class="fas fa-info-circle mr-1" style="color:{{ $generalsetting->primary_color ?? '#764ba2' }};"></i> একাউন্ট ভেরিফিকেশন</strong>
                            লগইন করার পর <strong>ভেন্ডর ড্যাশবোর্ড → ভেরিফিকেশন</strong> থেকে এনআইডি ও আপনার ছবি আপলোড করুন। (লোগো ও ব্যানার এখানে ঐচ্ছিক।)
                        </div>
                    </div>

                    {{-- Password Input --}}
                    <div class="custom-input-group">
                        <label for="password">পাসওয়ার্ড</label>
                        <i class="fas fa-lock input-icon"></i>
                        <div style="position: relative;">
                            <input type="password" id="password" 
                                   class="custom-input @error('password') is-invalid @enderror" 
                                   name="password" placeholder="********" required>
                            
                            {{-- পাসওয়ার্ড দেখার আইকন --}}
                            <span onclick="showPass()" style="position: absolute; right: 15px; top: 15px; cursor: pointer; color: #999;">
                                <i class="fa fa-eye"></i>
                            </span>
                        </div>
                        @error('password')
                            <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Password Confirmation (for seller) --}}
                    <div class="custom-input-group" id="password_confirmation_field" style="display: none;">
                        <label for="password_confirmation">পাসওয়ার্ড নিশ্চিত করুন</label>
                        <i class="fas fa-lock input-icon"></i>
                        <div style="position: relative;">
                            <input type="password" id="password_confirmation" 
                                   class="custom-input @error('password_confirmation') is-invalid @enderror" 
                                   name="password_confirmation" placeholder="********">
                            
                            {{-- পাসওয়ার্ড দেখার আইকন --}}
                            <span onclick="showPassConfirm()" style="position: absolute; right: 15px; top: 15px; cursor: pointer; color: #999;">
                                <i class="fa fa-eye"></i>
                            </span>
                        </div>
                        @error('password_confirmation')
                            <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Reseller Account Toggle --}}
                    @if(($generalsetting?->reseller_enabled ?? 1) == 1)
                    <div class="custom-input-group" style="margin-bottom: 20px; margin-top: 15px;">
                        <label style="display: flex; align-items: center; cursor: pointer; padding: 12px; background: #f8f9fa; border-radius: 8px; border: 2px solid #e9ecef;">
                            <input type="checkbox" id="is_reseller" name="is_reseller" value="1" 
                                   {{ old('is_reseller') ? 'checked' : '' }} 
                                   onchange="toggleResellerFields()"
                                   style="width: 20px; height: 20px; margin-right: 10px; cursor: pointer;">
                            <span style="font-weight: 600; color: #333; font-size: 15px;">
                               আমি রিসেলার একাউন্ট তৈরি করতে চাই
                            </span>
                        </label>
                    </div>

                    {{-- Shop Name (for reseller) --}}
                    <div class="custom-input-group" id="reseller_shop_name_field" style="display: none;">
                        <label for="reseller_shop_name">Shop Name <span class="text-danger">*</span></label>
                        <i class="fas fa-store input-icon"></i>
                        <input type="text" id="reseller_shop_name" 
                               class="custom-input @error('reseller_shop_name') is-invalid @enderror" 
                               name="reseller_shop_name" value="{{ old('reseller_shop_name') }}" 
                               placeholder="Enter your shop name">
                        @error('reseller_shop_name')
                            <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- রিসেলার: ডকিমেন্ট ড্যাশবোর্ডের ভেরিফিকেশনে — রেজিস্ট্রেশনে আপলোড লাগে না --}}
                    <div id="reseller_verification_note" style="display: none;">
                        <div style="padding: 16px 18px; margin: 16px 0; border-radius: 10px; border: 1px solid rgba(118,75,162,0.25); background: linear-gradient(135deg, #f9f7fc 0%, #f3f8ff 100%); color: #444; font-size: 14px; line-height: 1.55;">
                            <strong style="display:block;margin-bottom:6px;"><i class="fas fa-info-circle mr-1" style="color:{{ $generalsetting->primary_color ?? '#764ba2' }};"></i> একাউন্ট ভেরিফিকেশন</strong>
                            রেজিস্ট্রেশনের পর লগইন করে <strong>রিসেলার ড্যাশবোর্ড → ভেরিফিকেশন</strong> পেজ থেকে ভোটার আইডি (উভয় পাশ) ও আপনার ছবি আপলোড করুন।
                        </div>
                    </div>
                    @endif

                    {{-- Seller Account Toggle --}}
                    @if(($generalsetting?->vendor_enabled ?? 1) == 1)
                    <div class="custom-input-group" style="margin-bottom: 20px; margin-top: 15px;">
                        <label style="display: flex; align-items: center; cursor: pointer; padding: 12px; background: #f8f9fa; border-radius: 8px; border: 2px solid #e9ecef;">
                            <input type="checkbox" id="is_seller" name="is_seller" value="1" 
                                   {{ old('is_seller') ? 'checked' : '' }} 
                                   onchange="toggleSellerFields()"
                                   style="width: 20px; height: 20px; margin-right: 10px; cursor: pointer;">
                            <span style="font-weight: 600; color: #333; font-size: 15px;">
                                আমি সেলার একাউন্ট তৈরি করতে চাই
                            </span>
                        </label>
                    </div>
                    @endif

                    {{-- Submit Button --}}
                    <div class="form-group mt-4">
                        <button class="btn-auth-submit" type="submit"> রেজিস্ট্রেশন করুন </button>
                    </div>

                </form>

                {{-- Login Redirect --}}
                <div class="login-redirect">
                    <p>আগেই রেজিস্ট্রেশন করা আছে?</p>
                    <a href="{{route('customer.login')}}" class="login-link">
                        <i class="fas fa-sign-in-alt me-1"></i> লগিন করুন
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- পাসওয়ার্ড শো করার স্ক্রিপ্ট --}}
<script>
    function showPass() {
        var x = document.getElementById("password");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }

    function showPassConfirm() {
        var x = document.getElementById("password_confirmation");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }

    function toggleResellerFields() {
        var isReseller = document.getElementById("is_reseller").checked;
        var resellerShopNameField = document.getElementById("reseller_shop_name_field");
        var resellerVerificationNote = document.getElementById("reseller_verification_note");
        var passwordConfirmationField = document.getElementById("password_confirmation_field");
        var emailField = document.getElementById("email_field");
        
        if (resellerShopNameField) {
            resellerShopNameField.style.display = isReseller ? 'block' : 'none';
            
            var shopNameInput = document.getElementById("reseller_shop_name");
            if (shopNameInput) {
                if (isReseller) {
                    shopNameInput.setAttribute('required', 'required');
                } else {
                    shopNameInput.removeAttribute('required');
                }
            }
        }

        if (resellerVerificationNote) {
            resellerVerificationNote.style.display = isReseller ? 'block' : 'none';
        }

        // Show email field for resellers
        if (emailField) {
            if (isReseller) {
                emailField.style.display = 'block';
                var emailInput = document.getElementById("email");
                if (emailInput) {
                    emailInput.setAttribute('required', 'required');
                }
            } else {
                // Only hide if seller is also not checked
                var isSeller = document.getElementById("is_seller").checked;
                if (!isSeller) {
                    emailField.style.display = 'none';
                    var emailInput = document.getElementById("email");
                    if (emailInput) {
                        emailInput.removeAttribute('required');
                    }
                }
            }
        }

        // Show password confirmation for resellers
        if (passwordConfirmationField) {
            if (isReseller) {
                passwordConfirmationField.style.display = 'block';
                var passwordConfirmInput = document.getElementById("password_confirmation");
                if (passwordConfirmInput) {
                    passwordConfirmInput.setAttribute('required', 'required');
                }
            } else {
                // Only hide if seller is also not checked
                var isSeller = document.getElementById("is_seller").checked;
                if (!isSeller) {
                    passwordConfirmationField.style.display = 'none';
                    var passwordConfirmInput = document.getElementById("password_confirmation");
                    if (passwordConfirmInput) {
                        passwordConfirmInput.removeAttribute('required');
                    }
                }
            }
        }

        // If reseller is checked, uncheck seller checkbox
        if (isReseller) {
            var sellerCheckbox = document.getElementById("is_seller");
            if (sellerCheckbox && sellerCheckbox.checked) {
                sellerCheckbox.checked = false;
                toggleSellerFields();
            }
        }
    }

    function toggleSellerFields() {
        var isSeller = document.getElementById("is_seller").checked;
        var fields = [
            'email_field', 'shop_name_field', 'slug_field', 
            'address_field', 'logo_field', 'banner_field', 
            'password_confirmation_field'
        ];
        var sellerNote = document.getElementById('seller_verification_note');
        
        fields.forEach(function(fieldId) {
            var field = document.getElementById(fieldId);
            if (field) {
                field.style.display = isSeller ? 'block' : 'none';
                
                // Make required fields required when seller is checked
                var inputs = field.querySelectorAll('input, textarea');
                inputs.forEach(function(input) {
                    if (isSeller && fieldId !== 'address_field' && fieldId !== 'logo_field' && fieldId !== 'banner_field') {
                        input.setAttribute('required', 'required');
                    } else {
                        input.removeAttribute('required');
                    }
                });
            }
        });

        if (sellerNote) {
            sellerNote.style.display = isSeller ? 'block' : 'none';
        }

        // Update label
        var ownerLabel = document.getElementById("owner_name_label");
        if (ownerLabel) {
            ownerLabel.style.display = isSeller ? 'inline' : 'none';
        }

        // If seller is checked, uncheck reseller checkbox
        if (isSeller) {
            var resellerCheckbox = document.getElementById("is_reseller");
            if (resellerCheckbox && resellerCheckbox.checked) {
                resellerCheckbox.checked = false;
                toggleResellerFields();
            }
        }
    }

    // Auto-generate slug from shop name
    document.addEventListener('DOMContentLoaded', function() {
        var shopNameInput = document.getElementById('shop_name');
        var slugInput = document.getElementById('slug');
        
        if (shopNameInput && slugInput) {
            shopNameInput.addEventListener('input', function() {
                var slug = this.value.toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim();
                slugInput.value = slug;
            });
        }

        // Initialize on page load if checkbox is checked
        if (document.getElementById("is_seller").checked) {
            toggleSellerFields();
        }
        if (document.getElementById("is_reseller").checked) {
            toggleResellerFields();
        }
    });
</script>

@endsection

@push('script')
<script src="{{asset('public/frontEnd/')}}/js/parsley.min.js"></script>
<script src="{{asset('public/frontEnd/')}}/js/form-validation.init.js"></script>
@endpush