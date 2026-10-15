@extends('layouts.mb-auth')
@section('title', 'Admin Login')
@section('heading', 'Login To Your Account')
@section('subheading', 'Sign in to manage your store.')

@section('content')
@if ($errors->any() && ! $errors->has('email') && ! $errors->has('password'))
    <div class="alert alert-danger small">{{ $errors->first() }}</div>
@endif
<form method="POST" action="{{ route('login') }}">
    @csrf
    <div @class(['au-field', 'is-invalid' => $errors->has('email')])>
        <span class="ic"><i class="fa-regular fa-envelope"></i></span>
        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Enter your Email" autocomplete="username" required autofocus>
    </div>
    @error('email')<div class="au-err">{{ $message }}</div>@enderror
    <div @class(['au-field', 'is-invalid' => $errors->has('password')])>
        <span class="ic"><i class="fa-solid fa-lock"></i></span>
        <input id="password" type="password" name="password" placeholder="Enter Password" autocomplete="current-password" required>
        <button type="button" class="eye" tabindex="-1" aria-label="Show password"><i class="fa-regular fa-eye-slash"></i></button>
    </div>
    @error('password')<div class="au-err">{{ $message }}</div>@enderror
    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember">
        <label class="form-check-label" for="remember">Remember me</label>
    </div>
    <button type="submit" class="au-btn">Login</button>
    <a href="{{ route('admin.password.request') }}" class="au-btn-2">Forgot your password?</a>
</form>

@if(isset($demoMode) && $demoMode)
    <div class="au-note">
        <div class="mb-1"><i class="fa-solid fa-circle-info text-primary"></i> ডেমো একাউন্ট</div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <input type="text" class="form-control form-control-sm" id="demo-email" value="info@creativedesign.com.bd" readonly style="flex:1;min-width:0;">
            <input type="text" class="form-control form-control-sm" id="demo-password" value="12345678" readonly style="width:100px;">
            <button type="button" class="btn btn-outline-primary btn-sm" id="demoFill">Use</button>
        </div>
    </div>
    @push('scripts')
    <script>
    document.getElementById('demoFill').addEventListener('click', function () {
        document.getElementById('email').value = document.getElementById('demo-email').value;
        document.getElementById('password').value = document.getElementById('demo-password').value;
        this.textContent = 'Filled!';
        setTimeout(() => { this.textContent = 'Use'; }, 1200);
    });
    </script>
    @endpush
@endif
@endsection
