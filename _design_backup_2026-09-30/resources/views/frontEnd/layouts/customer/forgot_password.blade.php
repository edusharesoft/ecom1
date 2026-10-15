@extends('frontEnd.layouts.master')
@section('title','Forgot Password')

@section('content')
{{-- CSS সরাসরি এখানে দেওয়া হলো --}}
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

    /* ---- বাম পাশের ডিজাইন (ইমেজ এরিয়া - ব্যাকগ্রাউন্ড হিসেবে) ---- */
    .auth-image-area {
        width: 50%;
        /* আপনার লগইন পেজের স্টাইলের সাথে মিল রেখে একটি সুন্দর ব্যাকগ্রাউন্ড ইমেজ */
        background-image: url('https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=2070&auto=format&fit=crop');
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
        /* আপনার লগইন পেজের সেইম গ্র্যাডিয়েন্ট ওভারলে */
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

    /* ---- ডান পাশের ডিজাইন (ফর্ম) ---- */
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

    /* ব্যাক লিংক */
    .back-login {
        text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px dashed #ddd;
    }
    .back-login a {
        text-decoration: none; color: #764ba2; font-weight: 600; font-size: 14px;
    }
    .back-login a:hover { text-decoration: underline; }

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
                {{-- <img> ট্যাগ সরানো হয়েছে, এখন ব্যাকগ্রাউন্ড ইমেজ কাজ করবে --}}
                <h2>Forgot Password?</h2>
                <p>চিন্তার কিছু নেই! আপনার ফোন নাম্বার দিয়ে খুব সহজেই পাসওয়ার্ড রিসেট করুন।</p>
            </div>

            {{-- ডান পাশ: ফর্ম --}}
            <div class="auth-form-area">
                <div class="auth-header">
                    <h3>পাসওয়ার্ড রিসেট 🔒</h3>
                    <p>আপনার রেজিস্টার্ড ফোন নাম্বারটি লিখুন</p>
                </div>

                <form action="{{route('customer.forgot.verify')}}" method="POST" data-parsley-validate="">
                    @csrf
                    
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

                    {{-- Submit Button --}}
                    <div class="form-group mb-3">
                        <button class="btn-auth-submit"> সাবমিট করুন </button>
                    </div>

                </form>

                {{-- Back to Login --}}
                <div class="back-login">
                    <a href="{{route('customer.login')}}">
                        <i class="fas fa-arrow-left me-1"></i> লগইন পেজে ফিরে যান
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection

@push('script')
<script src="{{asset('public/frontEnd/')}}/js/parsley.min.js"></script>
<script src="{{asset('public/frontEnd/')}}/js/form-validation.init.js"></script>
@endpush