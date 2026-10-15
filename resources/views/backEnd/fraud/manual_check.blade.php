@extends('backEnd.layouts.master')
@section('title', 'Manual Fraud Check')

@section('content')

<style>
    /* Success Circle */
    .success-circle {
        width: 160px;
        height: 160px;
        border-radius: 50%;
        border: 12px solid #28a745;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e9f7ef;
        margin: 20px auto;
    }

    /* Courier Logo */
    .courier-logo {
        width: 60px;
        height: 45px;
        object-fit: contain;
        margin-right: 8px;
        border-radius: 4px;
    }

    /* Status Box */
    .status-box {
        padding: 12px;
        border-radius: 10px;
        text-align: center;
        margin-top: 15px;
    }
    .status-green { border: 2px solid green; color: green; background: #eaffea; }
    .status-blue { border: 2px solid #0d6efd; color: #0d6efd; background: #eef6ff; }
    .status-orange { border: 2px solid orange; color: orange; background: #fff8e1; }
    .status-red { border: 2px solid red; color: red; background: #ffeeee; }

    /* Table Fix */
    table thead tr th {
        background: #2e8b57 !important;
        color: white !important;
    }

    /* Success Rate Round Badge */
    .rate-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        border: 4px solid #28a745;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: auto;
        font-weight: bold;
        color: #28a745;
        background: #e9ffe9;
        font-size: 14px;
    }
</style>

<div class="container-fluid py-4">
    <div class="card shadow-sm p-4">

        <h4 class="text-center fw-bold mb-4">
            আপনার যাচাই করতে চাওয়া মোবাইল নাম্বারটি দিয়ে সার্চ দিন
        </h4>

        {{-- Search Box --}}
        {{-- Route Name Fixed: admin.manual_fraud_check --}}
        <form action="{{ route('manualFraud.check') }}" method="POST" class="text-center mb-4">
            @csrf
            <div class="input-group justify-content-center" style="max-width:400px; margin:auto;">
                <input type="text" name="mobile" class="form-control text-center"
                    value="{{ $mobile ?? '' }}" placeholder="017XXXXXXXX" required>
                <button class="btn btn-success px-4">সার্চ দিন</button>
            </div>
        </form>

        {{-- API Error Message --}}
        @if(session('error'))
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="alert alert-danger text-center">
                        <strong>{{ session('error') }}</strong>
                    </div>
                </div>
            </div>
        @endif

        {{-- Results --}}
        @php $reports = $reports ?? []; @endphp
        @if(isset($data) && !empty($data))
        <div class="row">

            {{-- LEFT: SUMMARY SECTION --}}
            <div class="col-md-4">
                <div class="card shadow-sm p-4">

                    <h5 class="fw-bold bg-success text-white py-2 rounded text-center">
                        মোট সফলতার হার
                    </h5>

                    <h3 class="text-center fw-bold text-success mt-3">
                        # {{ $mobile }}
                    </h3>

                    @php
                        $summary = $data['summary'] ?? [];
                        $overallRate = isset($summary['success_ratio']) ? round($summary['success_ratio']) : null;
                        $rateText = $overallRate !== null ? $overallRate.'%' : 'N/A';
                        $totalParcels = $summary['total_parcel'] ?? 0;
                    @endphp

                    {{-- Circle --}}
                    <div class="success-circle" style="border-color: {{ $overallRate < 50 ? '#dc3545' : ($overallRate < 80 ? '#fd7e14' : '#28a745') }}">
                        <div class="text-center">
                            <span class="fw-bold fs-2" style="color: {{ $overallRate < 50 ? '#dc3545' : ($overallRate < 80 ? '#fd7e14' : '#28a745') }}">
                                {{ $rateText }}
                            </span>
                            <br>
                            <small class="text-muted" style="font-size: 12px;">({{ $totalParcels }} টি অর্ডার)</small>
                        </div>
                    </div>

                    {{-- Rate Message (Bangla) --}}
                    @if($overallRate !== null)
                        @php
                            if ($overallRate >= 90) {
                                $class = "status-green";
                                $msg = "✔ নিরাপদ - ঝুঁকিমুক্ত অবস্থা 😎";
                                $desc = "এই কাস্টমারের সফলতার হার চমৎকার। নিশ্চিন্তে অর্ডার প্রসেস করুন।";
                            }
                            elseif ($overallRate >= 70) {
                                $class = "status-blue";
                                $msg = "ℹ️ ভালো - তবে সতর্ক থাকুন 🙂";
                                $desc = "সফলতার হার ভালো, তবে লোকেশন বা অন্য বিষয়গুলো চেক করে নিন।";
                            }
                            elseif ($overallRate >= 40) {
                                $class = "status-orange";
                                $msg = "⚠ ঝুঁকি আছে – কনফার্ম হয়ে নিন ⚠";
                                $desc = "রিটার্নের হার বেশি। অবশ্যই ডেলিভারি চার্জ অগ্রিম নিন।";
                            }
                            else {
                                $class = "status-red";
                                $msg = "❗ উচ্চ ঝুঁকি – অর্ডার না নেওয়াই ভালো ❗";
                                $desc = "এই কাস্টমারের বেশিরভাগ পার্সেল ক্যানসেল হয়। সাবধান!";
                            }
                        @endphp

                        <div class="status-box {{ $class }}">
                            <h5 class="fw-bold">{{ $msg }}</h5>
                            <p class="mb-0">{{ $desc }}</p>
                        </div>
                    @endif

                </div>
            </div>

            {{-- RIGHT: TABLE --}}
            <div class="col-md-8">
                <div class="card shadow-sm p-3">

                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle">

                            <thead>
                                <tr>
                                    <th>কুরিয়ার</th>
                                    <th>মোট অর্ডার</th>
                                    <th>সফল</th>
                                    <th>বাতিল</th>
                                    <th>হার</th>
                                </tr>
                            </thead>

                            <tbody>

                                @php
                                    $couriers = [
                                        'pathao'    => 'Pathao',
                                        'steadfast' => 'SteadFast',
                                        'redx'      => 'RedX',
                                        'paperfly'  => 'PaperFly',
                                        'parceldex' => 'ParcelDex',
                                        'carrybee'  => 'CarryBee'
                                    ];

                                    // ⭐ আপনার লোকাল লোগোর পাথ এখানে সেট করুন ⭐
                                    $myLogos = [
                                        'pathao'    => asset('public/assets/images/courier/pathao-logo.png'),
                                        'steadfast' => asset('public/assets/images/courier/steadfast-logo.png'),
                                        'redx'      => asset('public/assets/images/courier/redx-logo.png'),
                                        'paperfly'  => asset('public/assets/images/courier/paperfly-logo.png'),
                                        'parceldex' => asset('public/assets/images/courier/parceldex-logo.png'),
                                        'carrybee'  => asset('public/assets/images/courier/carrybee-logo.webp'),
                                    ];
                                @endphp

                                @foreach($couriers as $key => $name)

                                    @php
                                        $info = $data[$key] ?? [];
                                        $s = (int) ($info['success_parcel'] ?? 0);
                                        $c = (int) ($info['cancelled_parcel'] ?? 0);
                                        $t = (int) ($info['total_parcel'] ?? ($s + $c));
                                        $rate = isset($info['success_ratio']) ? round($info['success_ratio']) : ($t > 0 ? round(($s/$t)*100) : 0);
                                        
                                        // API থেকে লোগো URL (BD Courier), না থাকলে লোকাল ফাইল
                                        $logo = !empty($info['logo']) ? $info['logo'] : ($myLogos[$key] ?? null);
                                    @endphp

                                    <tr>
                                        <td class="text-start ps-4">
                                            @if($logo)
                                                <img src="{{ $logo }}" class="courier-logo" alt="{{ $name }}">
                                            @endif
                                            <span class="fw-bold text-dark">{{ $name }}</span>
                                        </td>

                                        <td class="fw-bold">{{ $t }}</td>
                                        <td class="text-success fw-bold">{{ $s }}</td>
                                        <td class="text-danger fw-bold">{{ $c }}</td>

                                        <td>
                                            @php
                                                $borderColor = $rate < 50 ? '#dc3545' : ($rate < 80 ? '#fd7e14' : '#28a745');
                                                $bgColor     = $rate < 50 ? '#ffeeee' : ($rate < 80 ? '#fff8e1' : '#e9ffe9');
                                                $textColor   = $rate < 50 ? '#dc3545' : ($rate < 80 ? '#fd7e14' : '#28a745');
                                            @endphp

                                            <div class="rate-circle" style="border-color: {{ $borderColor }}; background: {{ $bgColor }}; color: {{ $textColor }}">
                                                {{ $rate }}%
                                            </div>

                                            <small class="d-block mt-1 text-muted" style="font-size: 11px;">
                                                @if($t == 0) তথ্য নেই
                                                @elseif($rate == 100) চমৎকার
                                                @elseif($rate >= 80) ভালো
                                                @elseif($rate >= 50) সাধারণ
                                                @else ঝুঁকিপূর্ণ
                                                @endif
                                            </small>
                                        </td>
                                    </tr>

                                @endforeach

                            </tbody>

                        </table>
                    </div>

                </div>
            </div>

        </div>

        @if(count($reports))
        <div class="card shadow-sm p-4 mt-4">
            <h5 class="fw-bold mb-3 text-danger">Fraud reports (merchant)</h5>
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Courier</th>
                            <th>Name</th>
                            <th class="text-start">Details</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $r)
                        <tr>
                            <td class="text-start ps-3">
                                @if(!empty($r['courierLogo']))
                                    <img src="{{ $r['courierLogo'] }}" class="courier-logo" alt="">
                                @endif
                                <span class="fw-bold">{{ $r['courierName'] ?? '—' }}</span>
                            </td>
                            <td>{{ $r['name'] ?? '—' }}</td>
                            <td class="text-start small">{{ $r['details'] ?? '—' }}</td>
                            <td class="small text-muted">{{ !empty($r['created_at']) ? \Carbon\Carbon::parse($r['created_at'])->timezone(config('app.timezone'))->format('d M Y, H:i') : '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @endif

    </div>
</div>

@endsection