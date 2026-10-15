@extends('backEnd.layouts.master')
@section('title', 'Manual Duplicate Order Check')

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
</style>

<div class="container-fluid py-4">
    <div class="card shadow-sm p-4">

        <h4 class="text-center fw-bold mb-4">
            ডুপ্লিকেট অর্ডার চেক করতে মোবাইল নাম্বারটি দিয়ে সার্চ দিন
        </h4>

        {{-- Search Box --}}
        <form action="{{ route('manualDuplicateOrder.check') }}" method="POST" class="text-center mb-4">
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
        @if(isset($data) && !empty($data))
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm p-4">

                    <h5 class="fw-bold bg-primary text-white py-2 rounded text-center mb-4">
                        ডুপ্লিকেট অর্ডার তথ্য
                    </h5>

                    <h3 class="text-center fw-bold text-primary mt-3">
                        # {{ $mobile }}
                    </h3>

                    @php
                        $isDuplicate = $data['is_duplicate'] ?? false;
                        $duplicateCount = $data['duplicate_count'] ?? 0;
                        $duplicateRate = $data['duplicate_rate'] ?? 0;
                        $lastDuplicateDate = $data['last_duplicate_date'] ?? null;
                    @endphp

                    {{-- Circle --}}
                    <div class="success-circle" style="border-color: {{ $isDuplicate ? '#dc3545' : '#28a745' }}">
                        <div class="text-center">
                            <span class="fw-bold fs-2" style="color: {{ $isDuplicate ? '#dc3545' : '#28a745' }}">
                                {{ $duplicateCount }}
                            </span>
                            <br>
                            <small class="text-muted" style="font-size: 12px;">ডুপ্লিকেট অর্ডার</small>
                        </div>
                    </div>

                    {{-- Status Message --}}
                    @if($isDuplicate)
                        <div class="status-box status-red">
                            <h5 class="fw-bold">❗ ডুপ্লিকেট অর্ডার ডিটেক্টেড!</h5>
                            <p class="mb-0">এই মোবাইল নাম্বার দিয়ে {{ $duplicateCount }} টি ডুপ্লিকেট অর্ডার পাওয়া গেছে।</p>
                            @if($lastDuplicateDate)
                                <p class="mb-0 mt-2"><small>সর্বশেষ ডুপ্লিকেট অর্ডার: {{ $lastDuplicateDate }}</small></p>
                            @endif
                        </div>
                    @else
                        <div class="status-box status-green">
                            <h5 class="fw-bold">✔ ডুপ্লিকেট অর্ডার নেই</h5>
                            <p class="mb-0">এই মোবাইল নাম্বার দিয়ে কোনো ডুপ্লিকেট অর্ডার পাওয়া যায়নি।</p>
                        </div>
                    @endif

                    {{-- Additional Info --}}
                    @if(isset($data['details']))
                    <div class="mt-4">
                        <h6 class="fw-bold">বিস্তারিত তথ্য:</h6>
                        <pre class="bg-light p-3 rounded">{{ json_encode($data['details'], JSON_PRETTY_PRINT) }}</pre>
                    </div>
                    @endif

                </div>
            </div>
        </div>
        @endif

    </div>
</div>

@endsection
