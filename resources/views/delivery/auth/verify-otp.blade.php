@extends('layouts.mb-auth')
@section('auth_video', 'off')
@section('title', 'OTP যাচাই')
@section('heading', 'OTP যাচাই')
@section('subheading')
<b>{{ $maskedPhone }}</b> নম্বরে পাঠানো ৬ অঙ্কের কোড লিখুন।
@endsection

@section('content')
<form method="post" action="{{ route('delivery.password.verify.post') }}">
    @csrf
    <div @class(['au-field', 'is-invalid' => $errors->any()])>
        <span class="ic"><i class="fa-solid fa-key"></i></span>
        <input id="otp" type="text" name="otp" value="{{ old('otp') }}" placeholder="000000" required inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" autofocus style="letter-spacing:6px;font-size:18px;font-weight:700">
    </div>
    @if ($errors->any())<div class="au-err">{{ $errors->first() }}</div>@endif
    <button type="submit" class="au-btn">যাচাই করুন</button>
</form>
<form method="post" action="{{ route('delivery.password.resend') }}">
    @csrf
    <button type="submit" class="au-btn-2"><i class="fa-solid fa-rotate-right"></i> কোড পাননি? আবার পাঠান</button>
</form>
<div class="au-links"><a href="{{ route('delivery.password.request') }}">নম্বর পরিবর্তন</a> · <a href="{{ route('delivery.login') }}">লগইন</a></div>
@endsection
