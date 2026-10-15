@extends('delivery.layouts.app')
@section('title', 'প্রফাইল')
@section('header_title', 'প্রফাইল সম্পাদনা')

@section('page_subtitle')
    <p>নাম, মোবাইল, ইমেইল ও ছবি আপডেট করুন। পাসওয়ার্ড খালি রাখলে পরিবর্তন হবে না।</p>
@endsection

@section('content')
<div class="d-card d-profile-preview">
    <div class="d-profile-avatar-wrap">
        @if($boy->photo_url)
            <img src="{{ $boy->photo_url }}" alt="" class="d-profile-avatar-img" width="88" height="88">
        @else
            <div class="d-profile-avatar-fallback">{{ Str::upper(Str::substr($boy->name ?? '?', 0, 1)) }}</div>
        @endif
    </div>
    <p class="d-muted-small" style="margin:0;text-align:center;">প্রদর্শনের ছবি</p>
</div>

<div class="d-card">
    <form method="post" action="{{ route('delivery.profile.update') }}" enctype="multipart/form-data" class="d-form">
        @csrf
        <label class="d-label">ছবি</label>
        <input type="file" name="image" accept="image/*">
        <small class="d-form-hint">JPG / PNG — সর্বোচ্চ ২ মেগাবাইট। নতুন ছবি দিলে পুরোনোটি মুছে যাবে।</small>

        <label class="d-label" style="margin-top:14px;">নাম *</label>
        <input type="text" name="name" value="{{ old('name', $boy->name) }}" required maxlength="191">

        <label class="d-label" style="margin-top:12px;">মোবাইল *</label>
        <input type="tel" name="phone" value="{{ old('phone', $boy->phone) }}" required autocomplete="tel">

        <label class="d-label" style="margin-top:12px;">ইমেইল</label>
        <input type="email" name="email" value="{{ old('email', $boy->email) }}" autocomplete="email" placeholder="পাসওয়ার্ড রিসেটের জন্য ইমেইল দিন">
        <small class="d-form-hint">ফরগট পাসওয়ার্ড লিঙ্ক এই ইমেইলে যাবে।</small>

        <label class="d-label" style="margin-top:14px;">নতুন পাসওয়ার্ড (ঐচ্ছিক)</label>
        <input type="password" name="password" autocomplete="new-password" placeholder="খালি রাখুন যদি না বদলান">

        <label class="d-label" style="margin-top:12px;">পাসওয়ার্ড নিশ্চিত করুন</label>
        <input type="password" name="password_confirmation" autocomplete="new-password">

        <button type="submit" class="d-btn d-btn--primary" style="margin-top:18px;">সংরক্ষণ করুন</button>
    </form>
</div>
@endsection

@push('css')
<style>
    .d-profile-preview {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding-top: 1.25rem;
        padding-bottom: 1.25rem;
    }
    .d-profile-avatar-wrap {
        margin-bottom: 8px;
    }
    .d-profile-avatar-img {
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #e2e8f0;
    }
    .d-profile-avatar-fallback {
        width: 88px;
        height: 88px;
        border-radius: 50%;
        background: linear-gradient(135deg, #dbeafe 0%, #e0e7ff 100%);
        color: #4338ca;
        font-size: 2rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid #e2e8f0;
    }
    .d-form-hint {
        display: block;
        font-size: 0.75rem;
        color: var(--d-muted);
        margin-top: 4px;
    }
</style>
@endpush
