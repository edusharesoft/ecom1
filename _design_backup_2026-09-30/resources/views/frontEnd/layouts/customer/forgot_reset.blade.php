@extends('frontEnd.layouts.master')
@section('title','Verify OTP')

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
        max-width: 900px;
        display: flex;
        flex-wrap: wrap;
    }

    /* ---- বাম পাশ (ইমেজ এরিয়া - ব্যাকগ্রাউন্ড হিসেবে) ---- */
    .auth-image-area {
        width: 50%;
        /* সিকিউরিটি/ভেরিফিকেশন রিলেটেড একটি সুন্দর ব্যাকগ্রাউন্ড ইমেজ */
        background-image: url('https://images.unsplash.com/photo-1563986768609-322da13575f3?q=80&w=1470&auto=format&fit=crop');
        background-size: cover;        /* পুরো বক্স কাভার করবে */
        background-position: center;   /* ছবির মাঝখান দেখাবে */
        position: relative;            /* ওভারলে-এর জন্য জরুরি */
        display: flex;
        flex-direction: column;
        justify-content: flex-end;     /* লেখাগুলো নিচে থাকবে */
        padding: 40px;
        color: #fff;
        text-align: center;
    }

    /* ছবির ওপর একটি স্বচ্ছ রঙিন আস্তরণ (Overlay) */
    .auth-image-area::before {
        content: "";
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        /* সেইম গ্র্যাডিয়েন্ট ওভারলে */
        background: linear-gradient(to top, rgba(118, 75, 162, 0.85), rgba(102, 126, 234, 0.3));
        z-index: 1;
    }

    /* টেক্সট ডিজাইন */
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
    .custom-input-group { position: relative; margin-bottom: 25px; }
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
    .input-icon {
        position: absolute; left: 15px; top: 43px; color: #aaa; font-size: 16px;
    }

    /* সাবমিট বাটন */
    .btn-auth-submit {
        width: 100%; height: 50px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none; border-radius: 10px;
        color: #fff; font-weight: 600; font-size: 16px;
        cursor: pointer; transition: 0.3s;
        text-transform: uppercase; letter-spacing: 1px;
    }
    .btn-auth-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(118, 75, 162, 0.3);
    }

    /* রিসেন্ড ওটিপি বাটন */
    .resend-box {
        text-align: center; margin-top: 25px; padding-top: 20px; border-top: 1px dashed #ddd;
    }
    .btn-resend {
        background: none; border: none; color: #764ba2;
        font-weight: 700; cursor: pointer; text-decoration: none; font-size: 14px;
        transition: 0.3s;
        padding: 0;
    }
    .btn-resend:hover { text-decoration: underline; color: #5a3b85; }

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
                <h2>Account Recovery</h2>
                <p>আপনার ফোনে পাঠানো OTP কোডটি দিয়ে পাসওয়ার্ড রিসেট করুন।</p>
            </div>

            {{-- ডান পাশ: ফর্ম --}}
            <div class="auth-form-area">
                <div class="auth-header">
                    <h3>OTP ভেরিফিকেশন 🔐</h3>
                    <p>OTP কোড এবং নতুন পাসওয়ার্ড দিন</p>
                </div>

                {{-- মেইন ফর্ম --}}
                <form action="{{route('customer.forgot.store')}}" method="POST" data-parsley-validate="">
                    @csrf
                    
                    {{-- OTP Input --}}
                    <div class="custom-input-group">
                        <label for="otp">OTP কোড</label>
                        <i class="fas fa-key input-icon"></i>
                        <input type="number" id="otp" 
                               class="custom-input @error('otp') is-invalid @enderror" 
                               name="otp" value="{{ old('otp') }}" 
                               placeholder="আপনার OTP কোড লিখুন" required>
                        @error('otp')
                            <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- New Password Input --}}
                    <div class="custom-input-group">
                        <label for="password">নতুন পাসওয়ার্ড</label>
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

                    {{-- সাবমিট বাটন --}}
                    <div class="form-group mb-3">
                        <button class="btn-auth-submit"> রিসেট করুন </button>
                    </div>
                </form>

                {{-- রিসেন্ড ওটিপি সেকশন --}}
                <div class="resend-box">
                    <p class="mb-1 text-muted small">কোড পাননি?</p>
                    <form action="{{route('customer.forgot.resendotp')}}" method="POST">
                        @csrf
                        <button class="btn-resend">
                            <i data-feather="rotate-cw" style="width: 14px;"></i> Resend OTP
                        </button>
                    </form>
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
</script>

@endsection

@push('script')
<script src="{{asset('public/frontEnd/')}}/js/parsley.min.js"></script>
<script src="{{asset('public/frontEnd/')}}/js/form-validation.init.js"></script>
@endpush