@extends('backEnd.layouts.master')
@section('title', 'Make Salary Payment')

@section('css')
<style>
    /* --- Card & Form Styles --- */
    .card-modern {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        background: #fff;
    }
    .card-header-modern {
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        padding: 1.5rem;
        border-radius: 16px 16px 0 0 !important;
        display: flex; justify-content: space-between; align-items: center;
    }
    
    .form-label-custom {
        font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;
    }
    .form-control-custom, .form-select-custom {
        border: 1px solid #e2e8f0; border-radius: 10px;
        padding: 0.75rem 1rem; font-size: 0.95rem;
        transition: all 0.2s;
    }
    .form-control-custom:focus, .form-select-custom:focus {
        border-color: #6366f1; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }
    .input-group-text-custom {
        background-color: #f8fafc; border: 1px solid #e2e8f0; border-right: none;
        color: #64748b; border-radius: 10px 0 0 10px;
    }

    /* --- Auto-fill Box --- */
    .auto-fill-box {
        background: #eff6ff;
        border: 1px dashed #60a5fa;
        border-radius: 10px;
        padding: 1.25rem;
    }
    .fund-alert {
        background: #fffbeb; border: 1px solid #fcd34d; color: #92400e;
        border-radius: 10px; padding: 1rem; font-size: 0.9rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-7">
            
            <form action="{{ route('admin.salary_payments.store') }}" method="POST">
                @csrf

                <div class="card card-modern">
                    
                    {{-- Header --}}
                    <div class="card-header-modern">
                        <div>
                            <h5 class="mb-1 fw-bold text-dark">Process Salary Payment</h5>
                            <p class="text-muted small mb-0">Disburse salary to an employee.</p>
                        </div>
                        <a href="{{ route('admin.salary_payments.index') }}" class="btn btn-light btn-sm rounded-pill px-3">
                            <i data-feather="list" style="width:14px;" class="me-1"></i> History
                        </a>
                    </div>

                    <div class="card-body p-4">
                        
                        {{-- Employee Select --}}
                        <div class="mb-4">
                            <label class="form-label-custom">Select Employee <span class="text-danger">*</span></label>
                            <select name="employee_id" id="employee_id" class="form-control select2 form-select-custom @error('employee_id') is-invalid @enderror" required>
                                <option value="">-- Choose Employee --</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ (request('employee_id') == $emp->id || old('employee_id') == $emp->id) ? 'selected' : '' }}>
                                        {{ $emp->name }} (ID: {{ $emp->employee_id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        {{-- Auto Fill Section (Unpaid Salaries) --}}
                        @if(count($unpaidSalaries) > 0)
                        <div class="auto-fill-box mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <i data-feather="zap" class="text-primary me-2" style="width:16px;"></i>
                                <span class="fw-bold text-primary small text-uppercase">Smart Auto-fill</span>
                            </div>
                            <label class="form-label-custom">Select a calculated (unpaid) salary record:</label>
                            <select name="salary_id" class="form-select form-select-custom bg-white">
                                <option value="">-- Select to Auto-fill Amount --</option>
                                @foreach($unpaidSalaries as $salary)
                                    <option value="{{ $salary->id }}" data-amount="{{ $salary->net_salary }}">
                                        {{ \Carbon\Carbon::parse($salary->salary_month)->format('F Y') }} - Net: ৳{{ number_format($salary->net_salary, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        {{-- Payment Details --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label-custom">Payment For Month <span class="text-danger">*</span></label>
                                <input type="month" name="payment_month" class="form-control form-control-custom @error('payment_month') is-invalid @enderror" 
                                       value="{{ old('payment_month', request('month', date('Y-m'))) }}" required>
                                @error('payment_month') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Payment Date <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i data-feather="calendar" style="width:16px;"></i></span>
                                    <input type="date" name="payment_date" class="form-control form-control-custom border-start-0 @error('payment_date') is-invalid @enderror" 
                                           value="{{ old('payment_date', date('Y-m-d')) }}" required>
                                </div>
                                @error('payment_date') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Amount --}}
                        <div class="mb-4">
                            <label class="form-label-custom">Paying Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text input-group-text-custom fw-bold">৳</span>
                                <input type="number" step="0.01" name="amount" id="amount" class="form-control form-control-custom border-start-0 ps-2 @error('amount') is-invalid @enderror" 
                                       value="{{ old('amount') }}" placeholder="0.00" required>
                            </div>
                            @error('amount') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        {{-- Method & Transaction --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label-custom">Payment Method <span class="text-danger">*</span></label>
                                <select name="payment_method" class="form-select form-select-custom @error('payment_method') is-invalid @enderror" required>
                                    <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="bkash" {{ old('payment_method') == 'bkash' ? 'selected' : '' }}>Bkash</option>
                                    <option value="nagad" {{ old('payment_method') == 'nagad' ? 'selected' : '' }}>Nagad</option>
                                    <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Transaction / Check ID</label>
                                <input type="text" name="transaction_id" class="form-control form-control-custom" value="{{ old('transaction_id') }}" placeholder="Optional">
                            </div>
                        </div>

                        {{-- Bank Details (Optional) --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label-custom">Bank/Provider Name</label>
                                <input type="text" name="bank_name" class="form-control form-control-custom" value="{{ old('bank_name') }}" placeholder="e.g. City Bank">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Account Number</label>
                                <input type="text" name="account_number" class="form-control form-control-custom" value="{{ old('account_number') }}" placeholder="Account No.">
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div class="mb-4">
                            <label class="form-label-custom">Notes</label>
                            <textarea name="notes" class="form-control form-control-custom" rows="2" placeholder="Any comments...">{{ old('notes') }}</textarea>
                        </div>

                        {{-- Fund Warning --}}
                        <div class="fund-alert mb-4 d-flex align-items-center">
                            <i data-feather="alert-triangle" class="me-2"></i>
                            <div>
                                <strong>Warning:</strong> This amount will be deducted from your main fund. <br>
                                Current Balance: <strong>৳{{ number_format(\App\Helpers\FundHelper::balance(), 2) }}</strong>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">
                                <i data-feather="check-circle" class="me-1" style="width: 16px;"></i> Confirm Payment
                            </button>
                            <a href="{{ route('admin.salary_payments.index') }}" class="btn btn-light py-2">Cancel</a>
                        </div>

                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if ($.fn.select2) {
            $('.select2').select2({ width: '100%' });
        }

        // Auto-fill amount when salary is selected
        $('select[name="salary_id"]').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            var amount = selectedOption.data('amount'); // Use data attribute for cleaner value
            
            if (!amount) {
                // Fallback to regex if data-attribute isn't used
                var salaryText = selectedOption.text();
                var match = salaryText.match(/৳([\d,]+\.?\d*)/);
                if (match) {
                    amount = match[1].replace(/,/g, '');
                }
            }

            if (amount) {
                $('#amount').val(amount);
                // Highlight input briefly
                $('#amount').css('background-color', '#dcfce7').animate({backgroundColor: '#fff'}, 1000);
            }
        });
    });
</script>
@endpush