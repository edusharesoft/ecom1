@extends('backEnd.layouts.master')
@section('title', 'Payment Details')

@section('css')
<style>
    /* --- Receipt Style Card --- */
    .receipt-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        background: #fff;
        max-width: 900px;
        margin: 0 auto;
        overflow: hidden;
    }
    .receipt-header {
        background: #f8fafc;
        padding: 2rem;
        border-bottom: 1px dashed #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    /* --- Info Grid --- */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        padding: 2rem;
    }
    .info-item { margin-bottom: 0.5rem; }
    .label {
        font-size: 0.8rem; text-transform: uppercase;
        color: #64748b; font-weight: 600; letter-spacing: 0.5px;
        margin-bottom: 4px; display: block;
    }
    .value {
        font-size: 1rem; color: #1e293b; font-weight: 500;
    }
    .value-highlight {
        font-size: 1.5rem; font-weight: 700; color: #16a34a;
    }

    /* --- Status Badges --- */
    .badge-status {
        padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: 6px;
    }
    .badge-paid { background: #dcfce7; color: #166534; }
    .badge-failed { background: #fee2e2; color: #991b1b; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .status-dot { width: 8px; height: 8px; border-radius: 50%; background: currentColor; }

    /* --- Related Info Box --- */
    .related-box {
        background: #f1f5f9;
        border-radius: 8px;
        padding: 1.5rem;
        margin: 0 2rem 2rem 2rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    {{-- TOP ACTION BAR --}}
    <div class="d-flex justify-content-between align-items-center mb-4 max-width-900 mx-auto" style="max-width: 900px;">
        <a href="{{ route('admin.salary_payments.index') }}" class="btn btn-white border shadow-sm rounded-pill px-3">
            <i data-feather="arrow-left" class="me-1" style="width: 16px;"></i> Back to History
        </a>
    </div>

    {{-- RECEIPT CARD --}}
    <div class="receipt-card">
        
        {{-- Header --}}
        <div class="receipt-header">
            <div>
                <h5 class="mb-1 fw-bold text-dark">Payment Receipt</h5>
                <div class="text-muted small font-monospace">TRX ID: #{{ $payment->payment_id }}</div>
            </div>
            <div>
                @if($payment->status == 'paid')
                    <span class="badge-status badge-paid"><span class="status-dot"></span> Paid Successfully</span>
                @elseif($payment->status == 'failed')
                    <span class="badge-status badge-failed"><span class="status-dot"></span> Payment Failed</span>
                @else
                    <span class="badge-status badge-pending"><span class="status-dot"></span> Pending Approval</span>
                @endif
            </div>
        </div>

        {{-- Main Info --}}
        <div class="info-grid">
            
            {{-- Left Column --}}
            <div>
                <div class="info-item mb-4">
                    <span class="label">Amount Paid</span>
                    <span class="value-highlight">৳{{ number_format($payment->amount, 2) }}</span>
                </div>
                
                <div class="info-item mb-4">
                    <span class="label">Payment To</span>
                    <span class="value">{{ $payment->employee->name }}</span>
                    <div class="small text-muted">ID: {{ $payment->employee->employee_id }}</div>
                </div>

                <div class="info-item">
                    <span class="label">Payment Date</span>
                    <span class="value">{{ $payment->payment_date->format('d F, Y') }}</span>
                </div>
            </div>

            {{-- Right Column --}}
            <div>
                <div class="info-item mb-4">
                    <span class="label">Payment Method</span>
                    <div class="d-flex align-items-center gap-2">
                        <span class="value">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span>
                        @if($payment->payment_method == 'bank') <i data-feather="briefcase" class="text-muted" style="width:16px;"></i>
                        @elseif($payment->payment_method == 'cash') <i data-feather="dollar-sign" class="text-muted" style="width:16px;"></i>
                        @else <i data-feather="smartphone" class="text-muted" style="width:16px;"></i> @endif
                    </div>
                </div>

                @if($payment->transaction_id)
                <div class="info-item mb-4">
                    <span class="label">Transaction Reference</span>
                    <span class="value font-monospace bg-light px-2 rounded">{{ $payment->transaction_id }}</span>
                </div>
                @endif

                @if($payment->bank_name || $payment->account_number)
                <div class="info-item">
                    <span class="label">Account Details</span>
                    <div class="value">{{ $payment->bank_name }}</div>
                    <div class="small text-muted">{{ $payment->account_number }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Related Salary Info --}}
        @if($payment->salary)
        <div class="related-box">
            <h6 class="fw-bold text-dark mb-3 small text-uppercase">Linked Salary Record</h6>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="d-block text-muted small">Salary Month</span>
                    <span class="fw-bold">{{ \Carbon\Carbon::parse($payment->salary->salary_month)->format('F Y') }}</span>
                </div>
                <div class="text-center">
                    <span class="d-block text-muted small">Total Working Days</span>
                    <span class="fw-bold">{{ $payment->salary->working_days }}</span>
                </div>
                <div class="text-end">
                    <span class="d-block text-muted small">Net Payable</span>
                    <span class="fw-bold text-primary">৳{{ number_format($payment->salary->net_salary, 2) }}</span>
                </div>
            </div>
        </div>
        @endif

        {{-- Notes --}}
        @if($payment->notes)
        <div class="px-4 pb-4">
            <div class="p-3 bg-white border rounded">
                <span class="label mb-1">Additional Notes</span>
                <p class="mb-0 text-muted small">{{ $payment->notes }}</p>
            </div>
        </div>
        @endif

        {{-- Footer Meta --}}
        <div class="bg-light px-4 py-3 border-top d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                Processed by: <strong>{{ $payment->paidBy->name ?? 'System' }}</strong>
            </div>
            @if($payment->paid_at)
            <div class="small text-muted">
                Time: {{ $payment->paid_at->format('h:i A') }}
            </div>
            @endif
        </div>

    </div>
</div>
@endsection