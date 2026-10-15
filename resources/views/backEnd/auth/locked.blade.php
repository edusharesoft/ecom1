@extends('layouts.mb-auth')
@section('auth_video', 'off')
@section('title', 'Locked')
@section('heading', 'Panel Locked')
@section('subheading', 'Enter your password to unlock the panel.')

@section('content')
<form method="POST" action="{{ route('unlocked') }}">
    @csrf
    <input type="hidden" name="remember" value="1">
    <div @class(['au-field', 'is-invalid' => $errors->has('password')])>
        <span class="ic"><i class="fa-solid fa-lock"></i></span>
        <input type="password" id="password" name="password" placeholder="Enter your password" autocomplete="current-password" required autofocus>
        <button type="button" class="eye" tabindex="-1"><i class="fa-regular fa-eye-slash"></i></button>
    </div>
    @error('password')<div class="au-err">{{ $message }}</div>@enderror
    <button type="submit" class="au-btn"><i class="fa-solid fa-unlock"></i> Unlock</button>
</form>
@endsection
