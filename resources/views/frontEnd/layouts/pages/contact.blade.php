@extends('frontEnd.layouts.master')
@section('title','Contact Us')
@php
    $generalsetting = \App\Models\GeneralSetting::first();
@endphp
@section('content')

<style>
    :root {
        --primary-brand: #0d6efd;
        --secondary-brand: #004dc0;
        --soft-bg: #f4f7f6;
    }

    .contact-wrapper {
        padding: 80px 0;
        background-color: var(--soft-bg);
    }

    .contact-main-card {
        background: #ffffff;
        border-radius: 30px;
        overflow: hidden;
        box-shadow: 0 25px 70px rgba(0,0,0,0.1);
        border: none;
    }

    /* সাইডবার ইমেজ সেকশন উইথ স্পেশাল এফেক্ট */
    .contact-sidebar-img {
        background-image: url('{{ asset('public/frontEnd/images/login.avif') }}');
        background-size: cover;
        background-position: center;
        min-height: 100%;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 45px;
        color: white;
    }

    /* আপনার মার্ক করা এরিয়ার মতো ডার্ক এফেক্ট */
    .contact-sidebar-img::before {
        content: "";
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        /* নিচ থেকে উপরে ডার্ক গ্রাডিয়েন্ট এফেক্ট */
        background: linear-gradient(to top, rgba(0, 0, 0, 0.9) 10%, rgba(0, 0, 0, 0.4) 50%, rgba(0, 0, 0, 0.2) 100%);
    }

    .sidebar-content {
        position: relative;
        z-index: 2;
    }

    .sidebar-content h3 {
        font-size: 24px;
        letter-spacing: 0.5px;
        text-shadow: 0 2px 10px rgba(0,0,0,0.3); /* টেক্সট গ্লো */
    }

    /* সাইডবার ইনফো বক্স */
    .sidebar-info {
        margin-top: 30px;
        background: rgba(255, 255, 255, 0.1); /* হালকা কাঁচের মতো এফেক্ট */
        backdrop-filter: blur(5px);
        padding: 20px;
        border-radius: 15px;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .sidebar-info-item {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
        transition: 0.3s;
    }

    .sidebar-info-item:last-child { margin-bottom: 0; }

    .sidebar-info-item i {
        width: 22px;
        height: 22px;
        color: #fff;
        filter: drop-shadow(0 0 5px var(--primary-brand)); /* নিওন আইকন এফেক্ট */
    }

    .sidebar-info-item span {
        font-size: 15px;
        font-weight: 400;
        color: rgba(255,255,255,0.9);
    }

    /* ফর্ম সেকশন স্টাইল */
    .contact-form-side {
        padding: 60px;
    }

    .form-label {
        color: #333;
        margin-bottom: 8px;
    }

    .form-control {
        padding: 13px;
        border-radius: 12px;
        border: 1px solid #e0e0e0;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--primary-brand);
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
    }

    .submit-btn {
        background: var(--primary-brand);
        color: white;
        padding: 15px;
        border-radius: 12px;
        font-weight: 600;
        border: none;
        box-shadow: 0 10px 20px rgba(13, 110, 253, 0.2);
    }

    @media (max-width: 991px) {
        .contact-sidebar-img { min-height: 400px; padding: 30px; }
        .contact-form-side { padding: 40px 20px; }
    }
</style>

<div class="contact-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="contact-main-card">
                    <div class="row g-0">
                        
                        <div class="col-lg-5">
                            <div class="contact-sidebar-img">
                                <div class="sidebar-content">
                                    <h3 class="fw-bold mb-3">আমাদের সাথে যোগাযোগ করুন</h3>
                                    <p class="small opacity-75 mb-0"><span style="color: white;">আপনার যেকোনো প্রশ্ন বা মতামতের জন্য সরাসরি মেসেজ দিন। আমরা দ্রুত উত্তর দেব।</span></p>

                                    <div class="sidebar-info">
                                        <div class="sidebar-info-item">
                                            <i data-feather="map-pin"></i>
                                            <span>{{ $contact->address }}</span>
                                        </div>
                                        <div class="sidebar-info-item">
                                            <i data-feather="phone-call"></i>
                                            <span>{{ $contact->hotline }}</span>
                                        </div>
                                        <div class="sidebar-info-item">
                                            <i data-feather="mail"></i>
                                            <span>{{ $contact->email }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-7">
                            <div class="contact-form-side">
                                <h4 class="fw-bold mb-4" style="color: #222;">একটি মেসেজ পাঠান</h4>

                                @if(session('success'))
                                    <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 10px;">
                                        <i class="me-2" data-feather="check-circle"></i> {{ session('success') }}
                                    </div>
                                @endif

                                <form action="{{ route('frontend.contact.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="0">

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small">সম্পূর্ণ নাম *</label>
                                            <input type="text" name="full_name" class="form-control" placeholder="আপনার নাম" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small">মোবাইল নাম্বার *</label>
                                            <input type="text" name="mobile" class="form-control" placeholder="০১xxx-xxxxxx" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-bold small">ইমেইল এড্রেস</label>
                                            <input type="email" name="email" class="form-control" placeholder="example@mail.com">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-bold small">বিষয়</label>
                                            <input type="text" name="subject" class="form-control" placeholder="কি বিষয়ে জানতে চান?">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-bold small">মেসেজ লিখুন *</label>
                                            <textarea name="details" class="form-control" rows="5" placeholder="আপনার মেসেজ লিখুন..." required></textarea>
                                        </div>
                                        <div class="col-12 mt-4">
                                            <button type="submit" class="submit-btn w-100">
                                                মেসেজ পাঠান <i class="ms-2" data-feather="send" style="width: 18px"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script src="https://unpkg.com/feather-icons"></script>
<script>
    feather.replace();
</script>
@endpush