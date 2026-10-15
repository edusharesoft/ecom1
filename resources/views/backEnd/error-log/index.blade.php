@extends('backEnd.layouts.master')
@section('title', 'Laravel Error Log')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between py-3">
                <h4 class="page-title mb-0">Laravel Error Log</h4>
                <div class="page-title-right">
                    <form action="{{ route('error-log.test') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-sm rounded-pill me-1">
                            <i class="fe-file-text me-1"></i> টেস্ট লগ লিখুন
                        </button>
                    </form>
                    <form action="{{ route('error-log.create') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm rounded-pill me-1">
                            <i class="fe-plus me-1"></i> লগ ফাইল তৈরি করুন
                        </button>
                    </form>
                    <a href="{{ route('error-log.index') }}" class="btn btn-primary btn-sm rounded-pill">
                        <i class="fe-refresh-cw me-1"></i> রিফ্রেশ
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($message ?? '')
        <div class="alert alert-info">{{ $message }}</div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if($exists)
                        <p class="text-muted small mb-2">
                            <strong>ফাইল:</strong> <code>{{ $path }}</code>
                            <span class="ms-3">(শেষ ৫০০ লাইন)</span>
                            @if($writable ?? false)
                                <span class="badge bg-success ms-2">লিখার পারমিশন আছে</span>
                            @else
                                <span class="badge bg-danger ms-2">লিখার পারমিশন নেই</span>
                            @endif
                            @if(isset($logChannel))
                                <span class="badge bg-secondary ms-1">Channel: {{ $logChannel }}</span>
                                <span class="badge bg-secondary ms-1">Level: {{ $logLevel ?? 'debug' }}</span>
                            @endif
                            @if(isset($configCached) && $configCached)
                                <span class="badge bg-warning text-dark ms-1">Config cached</span>
                            @endif
                        </p>
                        @if(isset($configCached) && $configCached)
                            <div class="alert alert-warning py-2 small mb-2">
                                Config ক্যাশ করা আছে। লগ না দেখা গেলে <code>php artisan config:clear</code> চালান।
                            </div>
                        @endif
                        <pre class="bg-dark text-light p-3 rounded" style="max-height:70vh;overflow:auto;font-size:12px;white-space:pre-wrap;word-wrap:break-word;">{{ $content ?: 'লগ ফাইল খালি' }}</pre>
                    @else
                        <div class="alert alert-warning">
                            <i class="fe-alert-triangle me-2"></i>
                            লগ ফাইল পাওয়া যায়নি: <code>{{ $path }}</code>
                            <p class="mt-2 mb-0">উপরের <strong>"লগ ফাইল তৈরি করুন"</strong> বাটনে ক্লিক করুন।</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
