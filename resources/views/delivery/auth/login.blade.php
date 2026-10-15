@extends('layouts.mb-auth')
@section('title', 'ডেলিভারি লগইন')
@section('heading', 'ডেলিভারি পোর্টাল')
@section('subheading', 'রাইডার একাউন্টে প্রবেশ করতে মোবাইল নম্বর ও পাসওয়ার্ড লিখুন।')

@section('content')
@if ($errors->any())<div class="alert alert-danger small">{{ $errors->first() }}</div>@endif
<form method="post" action="{{ route('delivery.login.post') }}" novalidate>
    @csrf
    <div @class(['au-field', 'is-invalid' => $errors->has('phone')])>
        <span class="ic"><i class="fa-solid fa-mobile-screen"></i></span>
        <input id="login-phone" type="tel" name="phone" value="{{ old('phone') }}" placeholder="মোবাইল নম্বর (01XXXXXXXXX)" required autocomplete="username" inputmode="tel" autofocus>
    </div>
    <div @class(['au-field', 'is-invalid' => $errors->has('password')])>
        <span class="ic"><i class="fa-solid fa-lock"></i></span>
        <input id="login-password" type="password" name="password" required autocomplete="current-password" minlength="6" placeholder="পাসওয়ার্ড">
        <button type="button" class="eye" tabindex="-1" aria-label="পাসওয়ার্ড দেখান"><i class="fa-regular fa-eye-slash"></i></button>
    </div>
    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember" @checked(old('remember'))>
        <label class="form-check-label" for="remember">আমাকে মনে রাখুন</label>
    </div>
    <button type="submit" class="au-btn">লগইন</button>
    <a href="{{ route('delivery.password.request') }}" class="au-btn-2">পাসওয়ার্ড ভুলে গেছেন?</a>
</form>
<div class="au-links">মোবাইল OTP দিয়ে পাসওয়ার্ড রিসেট করা যাবে।</div>
@endsection
