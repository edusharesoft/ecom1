@php
    $generalsetting = $generalsetting ?? \App\Models\GeneralSetting::first();
@endphp
@extends('layouts.mb-auth')
@section('title', 'Forgot Password')
@section('heading', 'Forgot Password')
@section('subheading', 'আপনার রেজিস্টার্ড মোবাইল নাম্বারটি লিখুন, OTP পাঠানো হবে')

@section('content')
<form action="{{ route('customer.forgot.verify') }}" method="POST">
    @csrf
    <div @class(['au-field', 'is-invalid' => $errors->has('phone')])>
        <span class="ic"><i class="fa-solid fa-mobile-screen"></i></span>
        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="মোবাইল নাম্বার (017xxxxxxxx)" inputmode="tel" required autofocus>
    </div>
    @error('phone')<div class="au-err">{{ $message }}</div>@enderror
    <button type="submit" class="au-btn">সাবমিট করুন</button>
    <a href="{{ route('customer.login') }}" class="au-btn-2">লগিন পেজে ফিরে যান</a>
</form>
@endsection
