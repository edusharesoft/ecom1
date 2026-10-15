@extends('backEnd.layouts.master')
@section('title', 'Edit Bonus')

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
    .form-control-custom:disabled {
        background-color: #f8fafc; color: #64748b; border-color: #e2e8f0; opacity: 1;
    }
    .input-group-text-custom {
        background-color: #f8fafc; border: 1px solid #e2e8f0; border-right: none;
        color: #64748b; border-radius: 10px 0 0 10px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <form action="{{ route('admin.bonuses.update', $bonus->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card card-modern">
                    
                    {{-- Header --}}
                    <div class="card-header-modern">
                        <div>
                            <h5 class="mb-1 fw-bold text-dark">Edit Bonus</h5>
                            <p class="text-muted small mb-0">Modify existing bonus details.</p>
                        </div>
                        <a href="{{ route('admin.bonuses.index') }}" class="btn btn-light btn-sm rounded-pill px-3">
                            <i data-feather="x" style="width:14px;"></i> Close
                        </a>
                    </div>

                    <div class="card-body p-4">
                        
                        {{-- Employee Info (Read Only) --}}
                        <div class="mb-4">
                            <label class="form-label-custom">Employee Name</label>
                            <input type="text" class="form-control form-control-custom" 
                                   value="{{ $bonus->employee->name }} (ID: {{ $bonus->employee->employee_id }})" disabled>
                        </div>

                        {{-- Type & Amount --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label-custom">Bonus Type <span class="text-danger">*</span></label>
                                <input type="text" name="bonus_type" class="form-control form-control-custom @error('bonus_type') is-invalid @enderror" 
                                       value="{{ old('bonus_type', $bonus->bonus_type) }}" required>
                                @error('bonus_type') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text input-group-text-custom">৳</span>
                                    <input type="number" step="0.01" name="amount" class="form-control form-control-custom border-start-0 ps-2 @error('amount') is-invalid @enderror" 
                                           value="{{ old('amount', $bonus->amount) }}" required>
                                </div>
                                @error('amount') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Salary Month --}}
                        <div class="mb-4">
                            <label class="form-label-custom">Applicable Month (Optional)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i data-feather="calendar" style="width:16px;"></i></span>
                                <input type="month" name="salary_month" class="form-control form-control-custom border-start-0" 
                                       value="{{ old('salary_month', $bonus->salary_month) }}">
                            </div>
                        </div>

                        {{-- Reason --}}
                        <div class="mb-4">
                            <label class="form-label-custom">Reason</label>
                            <textarea name="reason" class="form-control form-control-custom" rows="2">{{ old('reason', $bonus->reason) }}</textarea>
                        </div>

                        {{-- Notes --}}
                        <div class="mb-4">
                            <label class="form-label-custom">Private Notes</label>
                            <textarea name="notes" class="form-control form-control-custom" rows="2">{{ old('notes', $bonus->notes) }}</textarea>
                        </div>

                        {{-- Actions --}}
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">
                                <i data-feather="save" class="me-1" style="width: 16px;"></i> Update Changes
                            </button>
                            <a href="{{ route('admin.bonuses.index') }}" class="btn btn-light py-2">Cancel</a>
                        </div>

                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection