@extends('backEnd.layouts.master')

@section('title','Fraud API Settings')

@section('content')

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --glass-white: rgba(255, 255, 255, 0.95);
        --text-dark: #2d3748;
        --text-muted: #718096;
        --border-color: #e2e8f0;
    }

    .fraud-page-wrapper {
        padding-top: 30px;
        background-color: #f8f9fc;
        min-height: 100vh;
        font-family: 'Inter', sans-serif;
    }

    /* Header Styling */
    .fraud-header-card {
        background: var(--primary-gradient);
        border-radius: 16px;
        padding: 30px;
        color: white;
        box-shadow: 0 10px 25px rgba(118, 75, 162, 0.2);
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }

    /* Form Card Styling */
    .settings-card {
        background: var(--glass-white);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
    }

    .settings-card-header {
        background: transparent;
        border-bottom: 1px solid var(--border-color);
        padding: 20px 25px;
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-dark);
        display: flex;
        align-items: center;
    }

    .form-control-lg-custom {
        padding: 12px 15px;
        font-size: 0.95rem;
        border-radius: 8px;
        border: 1px solid #cbd5e0;
    }

    .form-control-lg-custom:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    /* Button Styling */
    .btn-save {
        background: var(--primary-gradient);
        border: 0;
        padding: 12px;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(118, 75, 162, 0.3);
    }

    /* Timeline Styling */
    .timeline {
        position: relative;
        padding-left: 10px;
    }
    .timeline-item {
        position: relative;
        padding-left: 40px;
        padding-bottom: 30px;
    }
    .timeline-item:last-child {
        padding-bottom: 0;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        width: 2px;
        height: 100%;
        background: #e2e8f0;
    }
    .timeline-item:last-child::before {
        display: none;
    }
    .timeline-badge {
        position: absolute;
        left: -9px;
        top: 0;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #667eea;
        border: 4px solid #fff;
        box-shadow: 0 0 0 1px #667eea;
    }
    .timeline-content h6 {
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 5px;
    }
    .timeline-content p {
        color: var(--text-muted);
        font-size: 0.9rem;
        line-height: 1.5;
    }

    /* Alert Styling */
    .alert-custom {
        border-radius: 10px;
        border: none;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
</style>

<div class="container-fluid fraud-page-wrapper">

    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="fraud-header-card d-flex flex-wrap justify-content-between align-items-center">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <div class="bg-white p-2 rounded-circle me-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fe-shield text-primary" style="font-size:28px;"></i>
                    </div>
                    <div>
                        <h2 class="mb-1 text-white fw-bold">ফ্রড চেকিং API সেটিংস</h2>
                        <p class="mb-0 text-white-50 small">BD Courier — কুরিয়ার রেশিও ও ফ্রড চেক (<code class="text-white-50">courier-check</code>)</p>
                    </div>
                </div>

                <a href="https://app.bdcourier.com/" target="_blank" rel="noopener" class="btn btn-light text-primary fw-bold px-4 py-2 rounded-pill shadow-sm">
                    <i class="fe-external-link me-2"></i> BD Courier প্যানেল
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        
        <div class="col-lg-5 mb-4">
            
            @if(session()->has('success') || session()->has('message'))
                <div class="alert alert-success alert-custom alert-dismissible fade show mb-4 d-flex align-items-center" role="alert">
                    <i class="fe-check-circle fs-4 me-2"></i>
                    <div>
                        <strong>সফল হয়েছে!</strong> {{ session('success') ?? session('message') }}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-custom alert-dismissible fade show mb-4" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card settings-card h-100">
                <div class="settings-card-header">
                    <i class="fe-sliders me-2 text-primary"></i> API Configuration
                </div>
                
                <div class="card-body p-4 d-flex flex-column justify-content-center">
                    
                    <form action="{{ route('admin.fraud.update') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark mb-2">BD Courier API Key <span class="text-danger">*</span></label>

                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fe-key text-muted"></i></span>
                                <input type="text" name="fraud_api_key"
                                       class="form-control form-control-lg-custom border-start-0"
                                       placeholder="bdcourier.com থেকে Bearer টোকেন — খালি রাখলে .env এর BDCOURIER_API_KEY ব্যবহার হবে"
                                       value="{{ old('fraud_api_key', $data->fraud_api_key ?? '') }}"
                                       autocomplete="off">
                            </div>
                            <small class="text-muted mt-2 d-block">
                                <i class="fe-shield me-1"></i>
                                এই কী দিয়ে সিস্টেম <strong>POST https://api.bdcourier.com/courier-check</strong> ও ড্যাশবোর্ডের <strong>my-plan</strong> কল করে।
                                অর্ডার লিস্টের ফ্রড চেক ও ম্যানুয়াল চেক একই কী ব্যবহার করে।
                            </small>
                            <small class="text-muted d-block mt-1">
                                <strong>অগ্রাধিকার:</strong> এখানে কী থাকলে সেটাই ব্যবহার হয়। খালি থাকলে <code>.env</code> এর <code>BDCOURIER_API_KEY</code>।
                            </small>
                        </div>

                        <button type="submit" class="btn btn-primary btn-save w-100 text-white rounded-pill">
                            <i class="fe-save me-2"></i> সেটিংস আপডেট করুন
                        </button>

                        <button type="button" class="btn btn-outline-secondary w-100 rounded-pill mt-3" id="btn-bdcourier-plan-test">
                            <i class="fe-activity me-2"></i> কী টেস্ট: আমার প্ল্যান (BD Courier)
                        </button>
                    </form>

                    <div id="bdc-plan-test-result" class="mt-3 small text-muted" style="display:none;"></div>

                    <div class="mt-4 p-3 bg-light rounded border border-light">
                        <div class="d-flex">
                            <i class="fe-info text-primary mt-1 me-2"></i>
                            <p class="small text-muted mb-0">
                                <strong>মনে রাখবেন:</strong> ভুল কী দিলে অর্ডার ফ্রড চেক ও ড্যাশবোর্ডের BD Courier উইজেট কাজ করবে না। কী <a href="https://bdcourier.com/" target="_blank" rel="noopener">bdcourier.com</a> / অ্যাপ থেকে সংগ্রহ করুন।
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="ms-lg-3">
                <h5 class="mb-4 fw-bold text-dark px-2 border-start border-4 border-primary">
                    &nbsp;BD Courier API Key পাবেন কীভাবে?
                </h5>

                <div class="timeline mt-2">
                    <div class="timeline-item">
                        <div class="timeline-badge"></div>
                        <div class="timeline-content ms-3">
                            <h6>১. অ্যাকাউন্ট</h6>
                            <p><a href="https://bdcourier.com/register" target="_blank" rel="noopener" class="fw-bold text-primary text-decoration-none">bdcourier.com</a> এ রেজিস্টার করে লগইন করুন।</p>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-badge"></div>
                        <div class="timeline-content ms-3">
                            <h6>২. API কী</h6>
                            <p><a href="https://app.bdcourier.com/" target="_blank" rel="noopener">BD Courier অ্যাপ প্যানেল</a> থেকে আপনার API কী জেনারেট / কপি করুন। এটি Bearer টোকেন হিসেবে হেডারে যায়।</p>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-badge"></div>
                        <div class="timeline-content ms-3">
                            <h6>৩. এই পেজে বসান</h6>
                            <p>উপরের ফর্মে কী সংরক্ষণ করুন। এর পর অর্ডার লিস্টের ফ্রড চেক, ম্যানুয়াল ফ্রড চেক ও ড্যাশবোর্ডের <strong>My Plan</strong> একই কী ব্যবহার করবে।</p>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-badge"></div>
                        <div class="timeline-content ms-3">
                            <h6>৪. টেকনিক্যাল</h6>
                            <p class="mb-1"><code>POST https://api.bdcourier.com/courier-check</code> — বডিতে <code>phone</code></p>
                            <p class="mb-0"><code>GET https://api.bdcourier.com/my-plan</code> — প্ল্যান ও ব্যবহার সীমা</p>
                        </div>
                    </div>
                </div>

                <div class="mt-2 ms-4 ps-2">
                    <a href="https://bdcourier.com/" target="_blank" rel="noopener"
                       class="btn btn-outline-primary btn-sm rounded-pill px-4">
                        <i class="fe-book-open me-1"></i> BD Courier ওয়েবসাইট
                    </a>
                </div>
            </div>
        </div>

    </div>
</div> 

@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var btn = document.getElementById('btn-bdcourier-plan-test');
    var out = document.getElementById('bdc-plan-test-result');
    if (!btn || !out) return;

    btn.addEventListener('click', function () {
        btn.disabled = true;
        out.style.display = 'block';
        out.className = 'mt-3 small text-muted';
        out.textContent = 'লোড হচ্ছে…';

        fetch(@json(route('bdcourier.myplan')), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(function (r) { return r.json(); })
        .then(function (j) {
            btn.disabled = false;
            if (j.success && j.data) {
                var name = j.data.plan_name || j.data.planName || 'প্ল্যান';
                var due = j.data.next_due_display || j.data.next_due_date || '';
                var days = j.data.days_remaining != null ? (' · আরও ' + j.data.days_remaining + ' দিন') : '';
                out.className = 'mt-3 small text-success';
                out.innerHTML = '<strong>সফল:</strong> ' + name + (due ? (' · ' + due) : '') + days;
            } else {
                out.className = 'mt-3 small text-danger';
                out.textContent = j.message || 'প্ল্যান লোড হয়নি। কী চেক করুন।';
            }
        })
        .catch(function () {
            btn.disabled = false;
            out.className = 'mt-3 small text-danger';
            out.textContent = 'নেটওয়ার্ক ত্রুটি।';
        });
    });
});
</script>
@endsection