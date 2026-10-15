@extends('frontEnd.layouts.master')
@section('title','Track Your Order')
@php
    $generalsetting = \App\Models\GeneralSetting::first();
@endphp
@push('css')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
    /* ১. মেইন ব্যাকগ্রাউন্ড */
    .tracking-wrapper {
        background: #eef2f5;
        min-height: 85vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 15px;
        font-family: 'Poppins', sans-serif;
    }

    /* ২. মেইন কার্ড (Glass Effect & Shadow) */
    .track-box {
        background: #ffffff;
        border-radius: 25px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.08); /* সফট শ্যাডো */
        overflow: hidden;
        width: 100%;
        max-width: 950px;
        display: flex;
        flex-wrap: wrap;
        border: 1px solid #fff;
    }

    /* ৩. বাম পাশ (ইলাস্ট্রেশন এরিয়া) */
    .track-left {
        background: {{$generalsetting->primary_color}};
        width: 45%;
        padding: 50px 30px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: #fff;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    /* ব্যাকগ্রাউন্ড ডেকোরেশন (বৃত্ত) */
    .circle-deco {
        position: absolute;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }
    .circle-1 { width: 150px; height: 150px; top: -30px; left: -30px; }
    .circle-2 { width: 100px; height: 100px; bottom: 20px; right: -20px; }

    .track-left h2 {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 10px;
        z-index: 2;
    }
    .track-left p {
        font-size: 14px;
        opacity: 0.9;
        line-height: 1.6;
        z-index: 2;
        margin-bottom: 30px;
    }

    /* 3D ইলাস্ট্রেশন ইমেজ */
    .track-img {
        width: 100%;
        max-width: 280px;
        filter: drop-shadow(0 15px 30px rgba(0,0,0,0.2));
        transition: transform 0.5s ease;
        z-index: 2;
    }
    .track-img:hover { transform: scale(1.05) translateY(-5px); }

    /* ৪. ডান পাশ (ফর্ম এরিয়া) */
    .track-right {
        width: 55%;
        padding: 60px 50px;
        background: #fff;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .form-title {
        color: #2d3436;
        font-weight: 700;
        font-size: 24px;
        margin-bottom: 5px;
    }
    .form-subtitle {
        color: #636e72;
        font-size: 14px;
        margin-bottom: 35px;
    }

    /* ইনপুট ফিল্ড ডিজাইন */
    .input-wrapper {
        position: relative;
        margin-bottom: 20px;
    }

    .input-wrapper i {
        position: absolute;
        top: 50%;
        left: 20px;
        transform: translateY(-50%);
        color: #b2bec3;
        font-size: 18px;
        transition: 0.3s;
    }

    .modern-input {
        width: 100%;
        padding: 15px 20px 15px 55px;
        border: 2px solid #f1f2f6;
        border-radius: 12px;
        font-size: 15px;
        background: #fdfdfd;
        color: #2d3436;
        transition: all 0.3s ease;
    }

    .modern-input:focus {
        background: #fff;
        border-color: #764ba2;
        outline: none;
        box-shadow: 0 4px 15px rgba(118, 75, 162, 0.1);
    }
    .modern-input:focus + i { color: #764ba2; }

    /* লেবেল ডিজাইন */
    .input-label {
        font-weight: 600;
        font-size: 13px;
        color: #636e72;
        margin-bottom: 8px;
        display: block;
        margin-left: 5px;
    }

    /* OR ডিভাইডার */
    .or-divider {
        display: flex;
        align-items: center;
        margin: 15px 0 25px;
        color: #b2bec3;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 1px;
    }
    .or-divider::before, .or-divider::after {
        content: ""; flex: 1; height: 1px; background: #e0e0e0;
    }
    .or-divider span { padding: 0 10px; text-transform: uppercase; }

    /* বাটন */
    .modern-btn {
        width: 100%;
        padding: 16px;
        background: {{$generalsetting->secodery_color}};
        color: #fff;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 0.5px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        box-shadow: 0 10px 20px rgba(118, 75, 162, 0.2);
    }

    .modern-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(118, 75, 162, 0.3);
    }

    /* মোবাইল রেসপন্সিভ */
    @media (max-width: 991px) {
        .track-left { width: 100%; padding: 40px 20px; }
        .track-right { width: 100%; padding: 40px 20px; }
        .track-img { width: 60%; margin-bottom: 20px; }
    }
</style>
@endpush

@section('content')
<section class="tracking-wrapper">
    <div class="container d-flex justify-content-center">
        
        <div class="track-box">
            
            {{-- বাম পাশ: ভিজ্যুয়াল এবং তথ্য --}}
            <div class="track-left">
                <div class="circle-deco circle-1"></div>
                <div class="circle-deco circle-2"></div>

                <h2>Track Your Order</h2>
                <p><span style="color: white;">কোথাও যাওয়ার প্রয়োজন নেই। ঘরে বসেই এক ক্লিকে জানুন আপনার পণ্য এখন কোথায় আছে।</span></p>
                
                {{-- হাই কোয়ালিটি 3D ইলাস্ট্রেশন --}}
                <img src="{{ asset('public/frontEnd/images/7486744.png') }}" alt="Delivery Tracking" class="track-img">
            </div>

            {{-- ডান পাশ: ইনপুট ফর্ম --}}
            <div class="track-right">
                <div>
                    <h3 class="form-title">অর্ডার স্ট্যাটাস</h3>
                    <p class="form-subtitle">ট্র্যাক করতে আপনার ফোন নাম্বার অথবা ইনভয়েস আইডি দিন</p>
                </div>
                
                {{-- আপনার আগের রাউট এবং মেথড ঠিক রাখা হয়েছে --}}
                <form action="{{route('customer.order_track_result')}}" method="GET">
                    
                    {{-- Phone Input --}}
                    <div>
                        <label class="input-label">মোবাইল নাম্বার</label>
                        <div class="input-wrapper">
                            <input type="number" name="phone" 
                                   class="modern-input @error('phone') is-invalid @enderror" 
                                   value="{{ old('phone') }}" 
                                   placeholder="017xxxxxxxx">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        @error('phone')
                            <small class="text-danger d-block mb-3 ms-2">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- OR Divider --}}
                    <div class="or-divider"><span>অথবা</span></div>

                    {{-- Invoice Input --}}
                    <div>
                        <label class="input-label">ইনভয়েস আইডি</label>
                        <div class="input-wrapper">
                            <input type="text" name="invoice_id" 
                                   class="modern-input @error('invoice_id') is-invalid @enderror" 
                                   value="{{ old('invoice_id') }}" 
                                   placeholder="যেমন: 54321">
                            <i class="fas fa-receipt"></i>
                        </div>
                        @error('invoice_id')
                            <small class="text-danger d-block mb-3 ms-2">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" class="modern-btn mt-4">
                        ট্র্যাক করুন <i class="fas fa-search ms-2"></i>
                    </button>

                </form>
            </div>

        </div>
    </div>
</section>
@endsection

@push('script')
<script src="{{asset('public/frontEnd/')}}/js/parsley.min.js"></script>
<script src="{{asset('public/frontEnd/')}}/js/form-validation.init.js"></script>
@endpush