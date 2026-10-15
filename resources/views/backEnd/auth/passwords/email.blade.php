@extends('layouts.mb-auth')
@section('title', 'Forgot Password')
@section('heading', 'Forgot Password')
@section('subheading', 'Enter your email to reset password.')

@section('content')
<form method="POST" action="{{ route('admin.password.email') }}">
    @csrf
    <div @class(['au-field', 'is-invalid' => $errors->has('email')])>
        <span class="ic"><i class="fa-regular fa-envelope"></i></span>
        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Enter Email" required autofocus>
    </div>
    @if ($errors->any())<div class="au-err">{{ $errors->first() }}</div>@endif
    <button type="submit" class="au-btn">Send Password Reset Link</button>
    <a href="{{ route('login') }}" class="au-btn-2">Back to Login</a>
</form>
@endsection
