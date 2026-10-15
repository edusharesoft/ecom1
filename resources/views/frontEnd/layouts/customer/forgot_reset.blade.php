@php
    $generalsetting = $generalsetting ?? \App\Models\GeneralSetting::first();
@endphp
@extends('layouts.mb-auth')
@section('auth_video', 'off')
@section('title', 'Reset Password')
@section('heading', 'OTP Verification')
@section('subheading', 'আপনার ফোনে পাঠানো OTP কোড এবং নতুন পাসওয়ার্ড দিন')

@section('content')
<form action="{{ route('customer.forgot.store') }}" method="POST">
    @csrf
    <div @class(['au-field', 'is-invalid' => $errors->has('otp')])>
        <span class="ic"><i class="fa-solid fa-key"></i></span>
        <input type="text" id="otp" name="otp" value="{{ old('otp') }}" placeholder="OTP কোড" inputmode="numeric" autocomplete="one-time-code" required autofocus>
    </div>
    @error('otp')<div class="au-err">{{ $message }}</div>@enderror
    <div @class(['au-field', 'is-invalid' => $errors->has('password')])>
        <span class="ic"><i class="fa-solid fa-lock"></i></span>
        <input type="password" id="password" name="password" placeholder="নতুন পাসওয়ার্ড" autocomplete="new-password" required>
        <button type="button" class="eye" tabindex="-1"><i class="fa-regular fa-eye-slash"></i></button>
    </div>
    @error('password')<div class="au-err">{{ $message }}</div>@enderror
    <button type="submit" class="au-btn">রিসেট করুন</button>
</form>
<form action="{{ route('customer.forgot.resendotp') }}" method="POST">
    @csrf
    <button type="submit" class="au-btn-2"><i class="fa-solid fa-rotate-right"></i> কোড পাননি? Resend OTP</button>
</form>
@endsection
