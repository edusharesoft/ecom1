@extends('backEnd.layouts.master')
@section('title', 'Payment Gateway Settings')

@section('css')
    <link href="{{ asset('public/backEnd/assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        /* Professional Grid Card Styling */
        .gateway-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            height: 100%;
            /* Equal height */
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: #fff;
            overflow: hidden;
            border: 1px solid #f1f5f9;
        }

        .gateway-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
        }

        /* Header Styling */
        .card-header-custom {
            padding: 20px 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #f1f5f9;
        }

        .gateway-icon-box {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .gateway-icon-box img {
            max-height: 50px;
        }

        /* Specific Gateway Colors & Borders */
        .header-bkash {
            background: linear-gradient(135deg, #fff0f5 0%, #fff 100%);
            border-left: 5px solid #e2136e;
        }

        .header-shurjo {
            background: linear-gradient(135deg, #f0f7ff 0%, #fff 100%);
            border-left: 5px solid #4e73df;
        }

        .header-uddokta {
            background: linear-gradient(135deg, #f0fff4 0%, #fff 100%);
            border-left: 5px solid #28a745;
        }

        .header-aamarpay {
            background: linear-gradient(135deg, #fff8e1 0%, #fff 100%);
            border-left: 5px solid #ff9800;
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        /* Form Elements */
        .form-label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 8px;
            letter-spacing: 0.6px;
        }

        .form-control {
            border: 1px solid #e2e8f0;
            padding: 12px 15px;
            border-radius: 8px;
            font-size: 14px;
            background-color: #f8fafc;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: #6366f1;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        /* Status Switch Box */
        .status-wrapper {
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
        }

        /* Save Button */
        .btn-save {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 1px;
            border: none;
            transition: all 0.3s;
            cursor: pointer;
        }

        .btn-bkash {
            background: #e2136e;
            color: white;
            box-shadow: 0 4px 15px rgba(226, 19, 110, 0.3);
        }

        .btn-bkash:hover {
            background: #c20e5c;
            transform: translateY(-2px);
        }

        .btn-shurjo {
            background: #4e73df;
            color: white;
            box-shadow: 0 4px 15px rgba(78, 115, 223, 0.3);
        }

        .btn-shurjo:hover {
            background: #2e59d9;
            transform: translateY(-2px);
        }

        .btn-uddokta {
            background: #28a745;
            color: white;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        .btn-uddokta:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        .btn-aamarpay {
            background: #ff9800;
            color: white;
            box-shadow: 0 4px 15px rgba(255, 152, 0, 0.3);
        }

        .btn-aamarpay:hover {
            background: #f57c00;
            transform: translateY(-2px);
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-dark m-0" style="font-size: 22px;">Payment Gateways</h4>
                <span class="text-muted">Manage API credentials & status</span>
            </div>
            <a href="{{ route('manual-payment-gateway.manage') }}" class="btn btn-outline-primary btn-sm">
                ম্যানুয়াল পেমেন্ট
            </a>
        </div>

        <div class="row g-4">
            <div class="col-lg-6 col-md-12">
                <div class="card gateway-card">
                    <div class="card-header-custom header-shataj">
                        <div>
                            <h5 class="card-title">Shataj Pay</h5>
                            <small class="text-muted">Payment Gateway</small>
                        </div>
                        <div class="gateway-icon-box">
                            <img src="{{ asset('public/frontEnd/images/shatajpay.png') }}" alt="bKash">
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <form action="{{ route('paymentgeteway.update') }}" method="POST" data-parsley-validate>
                            @csrf

                            <input type="hidden" name="id" value="{{ $shatajpay->id ?? '' }}">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Gateway Key</label>
                                    <input type="text" class="form-control" name="base_url"
                                        value="{{ $shatajpay->base_url ?? '' }}" placeholder="Gateway Key" required />
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">App Key</label>
                                    <input type="text" class="form-control" name="app_key"
                                        value="{{ $shatajpay->app_key ?? '' }}" placeholder="App Key" required />
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">App Secret</label>
                                    <input type="text" class="form-control" name="app_secret"
                                        value="{{ $shatajpay->app_secret ?? '' }}" placeholder="App Secret" required />
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control" name="username"
                                        value="{{ $shatajpay->username ?? '' }}" placeholder="Username" required />
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Password</label>
                                <input type="text" class="form-control" name="password"
                                    value="{{ $shatajpay->password ?? '' }}" placeholder="Password" required />
                            </div>

                            <div class="status-wrapper">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-power-off me-2" style="color: #6f42c1;"></i>
                                    <span class="fw-bold text-dark" style="font-size: 14px;">Gateway Status</span>
                                </div>

                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status" value="1"
                                        @if (isset($shatajpay) && $shatajpay->status == 1) checked @endif
                                        style="cursor: pointer; width: 3em; height: 1.5em;">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-save btn-shataj">
                                <i class="fas fa-save me-2"></i> Update Shataj Pay Config
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12">
                <div class="card gateway-card">
                    <div class="card-header-custom header-bkash">
                        <div>
                            <h5 class="card-title">bKash Merchant</h5>
                            <small class="text-muted">Direct API Integration</small>
                        </div>
                        <div class="gateway-icon-box">
                            <img src="{{ asset('public/frontEnd/images/bkash.svg') }}" alt="bKash">
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <form action="{{ route('paymentgeteway.update') }}" method="POST" data-parsley-validate>
                            @csrf
                            <input type="hidden" name="id" value="{{ $bkash->id }}">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">User Name</label>
                                    <input type="text" class="form-control" name="username"
                                        value="{{ $bkash->username }}" required />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="text" class="form-control" name="password"
                                        value="{{ $bkash->password }}" required />
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">App Key</label>
                                    <input type="text" class="form-control" name="app_key"
                                        value="{{ $bkash->app_key }}" required />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">App Secret</label>
                                    <input type="text" class="form-control" name="app_secret"
                                        value="{{ $bkash->app_secret }}" required />
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Base URL</label>
                                <input type="text" class="form-control" name="base_url"
                                    value="{{ $bkash->base_url }}" required />
                            </div>

                            <div class="status-wrapper">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-power-off me-2 text-danger"></i>
                                    <span class="fw-bold text-dark" style="font-size: 14px;">Gateway Status</span>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status" value="1"
                                        @if ($bkash->status == 1) checked @endif
                                        style="cursor: pointer; width: 3em; height: 1.5em;">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-save btn-bkash">
                                <i class="fas fa-save me-2"></i> Update bKash Config
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-12">
                <div class="card gateway-card">
                    <div class="card-header-custom header-shurjo">
                        <div>
                            <h5 class="card-title">ShurjoPay</h5>
                            <small class="text-muted">Payment Aggregator</small>
                        </div>
                        <div class="gateway-icon-box">
                            <img src="{{ asset('public/frontEnd/images/shurjoPay.png') }}" alt="ShurjoPay">
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <form action="{{ route('paymentgeteway.update') }}" method="POST" data-parsley-validate>
                            @csrf
                            <input type="hidden" name="id" value="{{ $shurjopay->id }}">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">User Name</label>
                                    <input type="text" class="form-control" name="username"
                                        value="{{ $shurjopay->username }}" required />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Prefix</label>
                                    <input type="text" class="form-control" name="prefix"
                                        value="{{ $shurjopay->prefix }}" required />
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="text" class="form-control" name="password"
                                        value="{{ $shurjopay->password }}" required />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Base URL</label>
                                    <input type="text" class="form-control" name="base_url"
                                        value="{{ $shurjopay->base_url }}" required />
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Success URL</label>
                                    <input type="text" class="form-control" name="success_url"
                                        value="{{ $shurjopay->success_url }}" required />
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Return URL</label>
                                    <input type="text" class="form-control" name="return_url"
                                        value="{{ $shurjopay->return_url }}" required />
                                </div>
                            </div>

                            <div class="status-wrapper">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-power-off me-2 text-primary"></i>
                                    <span class="fw-bold text-dark" style="font-size: 14px;">Gateway Status</span>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status" value="1"
                                        @if ($shurjopay->status == 1) checked @endif
                                        style="cursor: pointer; width: 3em; height: 1.5em;">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-save btn-shurjo">
                                <i class="fas fa-save me-2"></i> Update ShurjoPay Config
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-12">
                <div class="card gateway-card">
                    <div class="card-header-custom header-uddokta">
                        <div>
                            <h5 class="card-title">UddoktaPay</h5>
                            <small class="text-muted">Automated Payment</small>
                        </div>
                        <div class="gateway-icon-box">
                            <img src="{{ asset('public/frontEnd/images/uddokta.png') }}" alt="Uddokta">
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <form action="{{ route('paymentgeteway.update') }}" method="POST" data-parsley-validate>
                            @csrf
                            <input type="hidden" name="id" value="{{ $uddoktapay->id ?? '' }}">

                            <div class="mb-3">
                                <label class="form-label">API Key</label>
                                <input type="text" class="form-control" name="app_key"
                                    value="{{ $uddoktapay->app_key ?? '' }}" placeholder="UDDOKTAPAY_API_KEY" required />
                            </div>

                            <div class="mb-4">
                                <label class="form-label">API Base URL</label>
                                <input type="text" class="form-control" name="base_url"
                                    value="{{ $uddoktapay->base_url ?? 'https://sandbox.uddoktapay.com/api/checkout-v2' }}"
                                    placeholder="UDDOKTAPAY_API_URL" required />
                            </div>

                            <div class="status-wrapper">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-power-off me-2 text-success"></i>
                                    <span class="fw-bold text-dark" style="font-size: 14px;">Gateway Status</span>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status" value="1"
                                        @if (isset($uddoktapay) && $uddoktapay->status == 1) checked @endif
                                        style="cursor: pointer; width: 3em; height: 1.5em;">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-save btn-uddokta">
                                <i class="fas fa-save me-2"></i> Update UddoktaPay Config
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-12">
                <div class="card gateway-card">
                    <div class="card-header-custom header-aamarpay">
                        <div>
                            <h5 class="card-title">aamarPay</h5>
                            <small class="text-muted">Card & Mobile Banking</small>
                        </div>
                        <div class="gateway-icon-box">
                            <img src="{{ asset('public/frontEnd/images/aamarpay.png') }}" alt="Uddokta">
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <form action="{{ route('paymentgeteway.update') }}" method="POST" data-parsley-validate>
                            @csrf
                            <input type="hidden" name="id" value="{{ $aamarpay->id ?? '' }}">

                            <div class="mb-3">
                                <label class="form-label">Store ID</label>
                                <input type="text" class="form-control" name="app_key"
                                    value="{{ $aamarpay->app_key ?? '' }}" placeholder="aamarpaytest (Sandbox)"
                                    required />
                                <small class="text-muted">Store ID is stored in App Key field</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Signature Key</label>
                                <input type="text" class="form-control" name="app_secret"
                                    value="{{ $aamarpay->app_secret ?? '' }}"
                                    placeholder="dbb74894e82415a2f7ff0ec3a97e4183 (Sandbox)" required />
                                <small class="text-muted">Signature Key is stored in App Secret field</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">API Base URL</label>
                                <input type="text" class="form-control" name="base_url"
                                    value="{{ $aamarpay->base_url ?? 'https://sandbox.aamarpay.com/jsonpost.php' }}"
                                    placeholder="https://sandbox.aamarpay.com/jsonpost.php" required />
                                <small class="text-muted">Sandbox: https://sandbox.aamarpay.com/jsonpost.php<br>Live:
                                    https://secure.aamarpay.com/jsonpost.php</small>
                            </div>

                            <div class="status-wrapper">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-power-off me-2 text-warning"></i>
                                    <span class="fw-bold text-dark" style="font-size: 14px;">Gateway Status</span>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status" value="1"
                                        @if (isset($aamarpay) && $aamarpay->status == 1) checked @endif
                                        style="cursor: pointer; width: 3em; height: 1.5em;">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-save btn-aamarpay">
                                <i class="fas fa-save me-2"></i> Update aamarPay Config
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('public/backEnd/assets/libs/parsleyjs/parsley.min.js') }}"></script>
    <script src="{{ asset('public/backEnd/assets/js/pages/form-validation.init.js') }}"></script>
    <script src="{{ asset('public/backEnd/assets/libs/select2/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(".select2").select2();
        });
    </script>
@endsection
