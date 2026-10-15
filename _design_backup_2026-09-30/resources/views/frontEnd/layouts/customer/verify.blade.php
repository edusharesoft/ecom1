@extends('frontEnd.layouts.master')
@section('title','Customer Verify')
@section('content')
<section class="auth-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-5">
                <div class="form-content">
                    <p class="auth-title">Customer Verify</p>
                    <form action="{{route('customer.account.verify')}}" method="POST"  data-parsley-validate="">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="otp">OTP</label>
                            <input type="number" id="otp" class="form-control @error('otp') is-invalid @enderror" name="otp" value="{{ old('otp') }}" placeholder="Enter OTP" required>
                            @error('phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!-- col-end -->
                        <div class="form-group mb-3">
                            <button class="submit-btn">submit</button>
                        </div>
                     <!-- col-end -->
                     </form>
                     <div class="resend_otp">
                        <form action="{{route('customer.resendotp')}}" method="POST">
                            @csrf
                            <button><i data-feather="rotate-cw"></i> Resend OTP</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('script')
<script src="{{asset('public/frontEnd/')}}/js/parsley.min.js"></script>
<script src="{{asset('public/frontEnd/')}}/js/form-validation.init.js"></script>
@endpush