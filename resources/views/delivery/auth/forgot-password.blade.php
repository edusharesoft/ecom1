@extends('layouts.mb-auth')
@section('title', 'পাসওয়ার্ড ভুলে গেছেন')
@section('heading', 'পাসওয়ার্ড ভুলে গেছেন?')
@section('subheading', 'নিবন্ধিত মোবাইল নম্বর দিন। ৬ অঙ্কের OTP SMS-এ পাঠানো হবে।')

@section('content')
<form method="post" action="{{ route('delivery.password.email') }}">
    @csrf
    <div @class(['au-field', 'is-invalid' => $errors->any()])>
        <span class="ic"><i class="fa-solid fa-mobile-screen"></i></span>
        <input id="fp-phone" type="tel" name="phone" value="{{ old('phone') }}" placeholder="মোবাইল নম্বর (01XXXXXXXXX)" required autocomplete="username" inputmode="tel" autofocus>
    </div>
    @if ($errors->any())<div class="au-err">{{ $errors->first() }}</div>@endif
    <button type="submit" class="au-btn">OTP পাঠান</button>
    <a href="{{ route('delivery.login') }}" class="au-btn-2">লগইনে ফিরে যান</a>
</form>
@endsection
