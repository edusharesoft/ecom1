@extends('frontEnd.layouts.master')
@section('title','Customer Login')
@php
    $generalsetting = \App\Models\GeneralSetting::first();
@endphp
@section('content')
{{-- CSS সরাসরি এখানে দেওয়া হলো যাতে কোনো এরর না হয় --}}
<style>
    /* মডার্ন ফন্ট ইমপোর্ট */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    .modern-login-section {
        background-color: #f0f2f5;
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 50px 15px;
        font-family: 'Poppins', sans-serif;
    }

    .login-container {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        overflow: hidden;
        width: 100%;
        max-width: 950px;
        display: flex;
        flex-wrap: wrap;
    }

    /* বাম পাশের ডিজাইন (ইমেজ) */
    .login-image-area {
        width: 50%;
        background: {{$generalsetting->primary_color}};
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 40px;
        color: #fff;
        text-align: center;
    }

/* বাম পাশের ডিজাইন (ইমেজ পুরোটা জুড়ে থাকবে) */
.login-image-area {
    width: 50%;
    /* আপনার পছন্দের ছবিটি এখানে ব্যাকগ্রাউন্ড হিসেবে দিন */
    background-image: url('{{ asset('public/frontEnd/images/login.jpg') }}');
    background-size: cover;   /* পুরো বক্স কাভার করবে */
    background-position: center; /* ছবির মাঝখান দেখাবে */
    position: relative;       /* ওভারলে-এর জন্য জরুরি */
    display: flex;
    flex-direction: column;
    justify-content: flex-end; /* লেখাগুলো নিচে থাকবে */
    padding: 40px;
    color: #fff;
    text-align: center;
    /* আগের ব্যাকগ্রাউন্ড কালার বা গ্র্যাডিয়েন্ট মুছে দিন */
}

/* ছবির ওপর একটি স্বচ্ছ রঙিন আস্তরণ (Overlay) যাতে লেখা স্পষ্ট হয় */
.login-image-area::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
}

/* টেক্সট যেন ওভারলে-এর উপরে থাকে */
.login-image-area h2,
.login-image-area p {
    position: relative;
    z-index: 2;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2); /* লেখার নিচে হালকা ছায়া */
}

.login-image-area h2 { font-weight: 700; margin-bottom: 10px; font-size: 32px; }
.login-image-area p { font-size: 16px; opacity: 0.95; }

/* আগের .login-image-area img এর কোডটি পুরোপুরি মুছে দিন */

    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0px); }
    }

    /* ডান পাশের ডিজাইন (ফর্ম) */
    .login-form-area {
        width: 50%;
        padding: 60px 50px;
        background: #fff;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .login-header { margin-bottom: 30px; }
    .login-header h3 { font-weight: 700; color: #333; margin-bottom: 5px; }
    .login-header p { color: #888; font-size: 14px; }

    /* ইনপুট ফিল্ড ডিজাইন */
    .custom-input-group {
        position: relative;
        margin-bottom: 25px;
    }
    .custom-input-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #555;
        font-size: 14px;
    }
    .custom-input {
        width: 100%;
        height: 50px;
        padding: 10px 20px;
        border: 2px solid #eee;
        border-radius: 10px;
        font-size: 15px;
        transition: 0.3s;
        background: #fdfdfd;
    }
    .custom-input:focus {
        border-color: #764ba2;
        background: #fff;
        outline: none;
        box-shadow: 0 0 0 4px rgba(118, 75, 162, 0.1);
    }

    /* বাটন ডিজাইন */
    .btn-modern-submit {
        width: 100%;
        height: 50px;
        background: {{$generalsetting->secodery_color}};
        border: none;
        border-radius: 10px;
        color: #fff;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: 0.3s;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .btn-modern-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(118, 75, 162, 0.3);
    }

    /* লিংকস */
    .forgot-pass-link {
        text-align: right;
        display: block;
        margin-top: -10px;
        margin-bottom: 20px;
        font-size: 13px;
        color: #666;
        text-decoration: none;
    }
    .forgot-pass-link:hover { color: #764ba2; text-decoration: underline; }

    .register-box {
        text-align: center;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px dashed #ddd;
    }
    .register-link {
        color: {{$generalsetting->primary_color}};
        font-weight: 700;
        text-decoration: none;
    }

    /* ডেমো Use বাটন - কমলার বর্ডার */
    .demo-use-btn {
        border: 2px solid #fd7e14;
        color: #fd7e14;
        background: transparent;
        font-weight: 600;
        padding: 6px 16px;
        border-radius: 8px;
    }
    .demo-use-btn:hover {
        background: #fd7e14;
        color: #fff;
        border-color: #fd7e14;
    }

    /* মোবাইল রেসপন্সিভ */
    @media (max-width: 768px) {
        .login-image-area { display: none; } /* মোবাইলে ছবি হাইড */
        .login-form-area { width: 100%; padding: 40px 20px; }
    }
</style>

<section class="modern-login-section">
    <div class="container d-flex justify-content-center">
        <div class="login-container">
            
            {{-- বাম পাশ: ছবি --}}
<div class="login-image-area">
    {{-- এখানে আর কোনো <img> ট্যাগ থাকবে না --}}
    <h2>HOTLINE: 01733919791</h2>
    <p><span style="color: white;">লগিন করে নিরাপদ কেনাকাটা করুন।</span></p>
</div>

            {{-- ডান পাশ: ফর্ম --}}
            <div class="login-form-area">
                <div class="login-header">
                    <h3>কাস্টমার লগিন 👋</h3>
                    <p>আপনার ফোন নাম্বার এবং পাসওয়ার্ড দিন</p>
                </div>

                {{-- আপনার অরিজিনাল ফর্ম অ্যাকশন এবং মেথড --}}
                <form action="{{route('customer.signin')}}" method="POST" data-parsley-validate="">
                    @csrf
                    
                    {{-- ফোন নাম্বার --}}
                    <div class="custom-input-group">
                        <label for="login">মোবাইল নাম্বার বা ইমেইল</label>
                        <input type="text" id="login" 
                               class="custom-input @error('login') is-invalid @enderror" 
                               name="login" value="{{ old('login') }}" 
                               placeholder="017xxxxxxxx অথবা email@example.com" required>
                        @error('login')
                            <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- পাসওয়ার্ড --}}
                    <div class="custom-input-group">
                        <label for="password">পাসওয়ার্ড</label>
                        <div style="position: relative;">
                            <input type="password" id="password" 
                                   class="custom-input @error('password') is-invalid @enderror" 
                                   name="password" placeholder="********" required>
                            {{-- পাসওয়ার্ড দেখার আইকন (অপশনাল) --}}
                            <span onclick="showPass()" style="position: absolute; right: 15px; top: 15px; cursor: pointer; color: #999;">
                                <i class="fa fa-eye"></i>
                            </span>
                        </div>
                        @error('password')
                            <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- ফরগট পাসওয়ার্ড --}}
                    <a href="{{route('customer.forgot.password')}}" class="forgot-pass-link">
                        <i class="fa-solid fa-unlock"></i> পাসওয়ার্ড ভুলে গেছেন?
                    </a>

                    {{-- সাবমিট --}}
                    <div class="form-group mb-3">
                        <button class="btn-modern-submit"> লগিন করুন </button>
                    </div>

                </form>

                @if(isset($demoMode) && $demoMode)
                <div class="mt-4 pt-3 border-top">
                    <div class="mb-2">
                        <small class="d-block mb-1 text-muted">রিসেলার ইউজার</small>
                        <div class="d-flex gap-2 align-items-center flex-wrap mb-2">
                            <input type="text" class="form-control form-control-sm bg-light" value="01631843149" readonly style="flex:1;min-width:0;border:1px solid #ddd;">
                            <input type="text" class="form-control form-control-sm bg-light" value="12345678" readonly style="width:100px;border:1px solid #ddd;">
                            <button type="button" class="btn btn-sm demo-use-btn" onclick="fillDemoCreds('01631843149','12345678')">Use</button>
                        </div>
                    </div>
                    <div>
                        <small class="d-block mb-1 text-muted">ভেন্ড্রর ইউজার</small>
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <input type="text" class="form-control form-control-sm bg-light" value="01870829343" readonly style="flex:1;min-width:0;border:1px solid #ddd;">
                            <input type="text" class="form-control form-control-sm bg-light" value="123456789" readonly style="width:100px;border:1px solid #ddd;">
                            <button type="button" class="btn btn-sm demo-use-btn" onclick="fillDemoCreds('01870829343','123456789')">Use</button>
                        </div>
                    </div>
                </div>
                @endif

                {{-- রেজিস্ট্রেশন --}}
                <div class="register-box">
                    <p class="mb-1 text-muted">একাউন্ট না থাকলে?</p>
                    <a href="{{route('customer.register')}}" class="register-link">
                        <i data-feather="edit-3"></i> রেজিস্ট্রেশন করুন
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- পাসওয়ার্ড শো করার ছোট স্ক্রিপ্ট --}}
<script>
    function fillDemoCreds(login, pass) {
        document.getElementById('login').value = login;
        document.getElementById('password').value = pass;
    }
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