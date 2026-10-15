@extends('frontEnd.layouts.master')
@section('title','Change Password')

@section('content')
<section class="customer-section">
    <div class="container">
        <div class="row">

            {{-- Sidebar --}}
            <div class="col-sm-3">
                <div class="customer-sidebar">
                    @include('frontEnd.layouts.customer.sidebar')
                </div>
            </div>

            {{-- Main Content --}}
            <div class="col-sm-9">
                <div class="customer-content checkout-shipping account-card">

                    <h5 class="account-title">Change Password</h5>
                    <div class="account-divider"></div>

                    <form action="{{ route('customer.password_update') }}"
                          method="POST"
                          class="row"
                          data-parsley-validate>
                        @csrf

                        {{-- Old Password --}}
                        <div class="col-sm-12">
                            <div class="form-group mb-3">
                                <label for="old_password">Old Password *</label>
                                <input type="password"
                                       id="old_password"
                                       name="old_password"
                                       class="form-control @error('old_password') is-invalid @enderror"
                                       placeholder="Enter old password"
                                       required>
                                @error('old_password')
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        {{-- New Password --}}
                        <div class="col-sm-12">
                            <div class="form-group mb-3">
                                <label for="new_password">New Password *</label>
                                <input type="password"
                                       id="new_password"
                                       name="new_password"
                                       class="form-control @error('new_password') is-invalid @enderror"
                                       placeholder="Enter new password"
                                       required>
                                @error('new_password')
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        {{-- Confirm Password --}}
                        <div class="col-sm-12">
                            <div class="form-group mb-4">
                                <label for="confirm_password">Confirm Password *</label>
                                <input type="password"
                                       id="confirm_password"
                                       name="confirm_password"
                                       class="form-control @error('confirm_password') is-invalid @enderror"
                                       placeholder="Confirm new password"
                                       required>
                                @error('confirm_password')
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        {{-- Submit --}}
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="submit-btn">
                                Update Password
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</section>
@endsection

@push('script')
<script src="{{ asset('public/frontEnd/js/parsley.min.js') }}"></script>
<script src="{{ asset('public/frontEnd/js/form-validation.init.js') }}"></script>
@endpush
