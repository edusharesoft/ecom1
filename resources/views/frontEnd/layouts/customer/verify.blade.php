@php
    $generalsetting = $generalsetting ?? \App\Models\GeneralSetting::first();
@endphp
@extends('layouts.mb-auth')
@section('auth_video', 'off')
@section('title', 'Customer Verify')
@section('heading', 'Verify Your Account')
@section('subheading', 'আপনার মোবাইলে পাঠানো OTP কোডটি লিখুন')

@section('content')
<form action="{{ route('customer.account.verify') }}" method="POST">
    @csrf
    <div @class(['au-field', 'is-invalid' => $errors->has('otp') || $errors->has('phone')])>
        <span class="ic"><i class="fa-solid fa-key"></i></span>
        <input type="text" id="otp" name="otp" value="{{ old('otp') }}" placeholder="Enter OTP" inputmode="numeric" autocomplete="one-time-code" required autofocus>
    </div>
    @if ($errors->has('otp') || $errors->has('phone'))<div class="au-err">{{ $errors->first('otp') ?: $errors->first('phone') }}</div>@endif
    <button type="submit" class="au-btn">Submit</button>
</form>
<form action="{{ route('customer.resendotp') }}" method="POST">
    @csrf
    <button type="submit" class="au-btn-2"><i class="fa-solid fa-rotate-right"></i> Resend OTP</button>
</form>
@endsection
