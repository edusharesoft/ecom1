@extends('layouts.mb-auth')
@section('auth_video', 'off')
@section('title', 'নতুন পাসওয়ার্ড')
@section('heading', 'নতুন পাসওয়ার্ড সেট করুন')
@section('subheading')
<b>{{ $maskedPhone ?? '' }}</b> যাচাই হয়েছে। নতুন পাসওয়ার্ড সেট করুন।
@endsection

@section('content')
@if ($errors->any())<div class="alert alert-danger small">{{ $errors->first() }}</div>@endif
<form method="post" action="{{ route('delivery.password.store') }}">
    @csrf
    <div @class(['au-field', 'is-invalid' => $errors->has('password')])>
        <span class="ic"><i class="fa-solid fa-lock"></i></span>
        <input id="rs-password" type="password" name="password" required autocomplete="new-password" minlength="6" placeholder="নতুন পাসওয়ার্ড (কমপক্ষে ৬ অক্ষর)" autofocus>
        <button type="button" class="eye" tabindex="-1" aria-label="পাসওয়ার্ড দেখান"><i class="fa-regular fa-eye-slash"></i></button>
    </div>
    <div class="au-field">
        <span class="ic"><i class="fa-solid fa-lock"></i></span>
        <input id="rs-password-confirm" type="password" name="password_confirmation" required autocomplete="new-password" minlength="6" placeholder="পাসওয়ার্ড আবার লিখুন">
        <button type="button" class="eye" tabindex="-1"><i class="fa-regular fa-eye-slash"></i></button>
    </div>
    <button type="submit" class="au-btn">সংরক্ষণ করুন</button>
    <a href="{{ route('delivery.login') }}" class="au-btn-2">লগইন</a>
</form>
@endsection
