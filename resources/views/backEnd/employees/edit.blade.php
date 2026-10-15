@extends('backEnd.layouts.master')
@section('title','Edit Employee')

@section('css')
<style>
    /* --- Form Styles --- */
    .card-form {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        background: #fff;
        margin-bottom: 20px;
    }
    .card-header-form {
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        padding: 1.5rem;
        border-radius: 12px 12px 0 0 !important;
    }
    .section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0;
        display: flex; align-items: center; gap: 8px;
    }
    
    .form-label-custom {
        font-size: 0.85rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 0.5rem;
    }
    .form-control-custom, .form-select-custom {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.7rem 1rem;
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    .form-control-custom:focus, .form-select-custom:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    .form-control-custom:disabled, .form-control-custom[readonly] {
        background-color: #f8fafc;
        opacity: 1;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark">Edit Employee</h4>
            <p class="text-muted small mb-0">Update employee profile and information.</p>
        </div>
        <a href="{{ route('admin.employees.index') }}" class="btn btn-white border shadow-sm rounded-pill px-4">
            <i data-feather="arrow-left" class="me-1" style="width: 16px;"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.employees.update', $employee->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            
            {{-- LEFT COLUMN: Main Form --}}
            <div class="col-lg-8">
                
                {{-- Employee Information --}}
                <div class="card card-form">
                    <div class="card-header-form">
                        <div class="section-title">
                            <i data-feather="user" class="text-primary" style="width: 18px;"></i> Employee Information
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label-custom">Employee ID</label>
                                <input type="text" class="form-control form-control-custom" value="{{ $employee->employee_id }}" disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">Linked User Account</label>
                                <select name="user_id" class="form-control select2">
                                    <option value="">No User Linked</option>
                                    @foreach($availableUsers as $user)
                                        <option value="{{ $user->id }}" {{ $employee->user_id == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-custom @error('name') is-invalid @enderror" value="{{ old('name', $employee->name) }}" required>
                                @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label-custom">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control form-control-custom @error('email') is-invalid @enderror" value="{{ old('email', $employee->email) }}" required>
                                @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">Phone Number</label>
                                <input type="text" name="phone" class="form-control form-control-custom" value="{{ old('phone', $employee->phone) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">Joining Date <span class="text-danger">*</span></label>
                                <input type="date" name="joining_date" class="form-control form-control-custom @error('joining_date') is-invalid @enderror" value="{{ old('joining_date', $employee->joining_date->format('Y-m-d')) }}" required>
                                @error('joining_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">Department</label>
                                <input type="text" name="department" class="form-control form-control-custom" value="{{ old('department', $employee->department) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">Designation</label>
                                <input type="text" name="designation" class="form-control form-control-custom" value="{{ old('designation', $employee->designation) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">Employment Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select form-select-custom" required>
                                    <option value="active" {{ $employee->status == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $employee->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="terminated" {{ $employee->status == 'terminated' ? 'selected' : '' }}>Terminated</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- RIGHT COLUMN: Financial & Other Info --}}
            <div class="col-lg-4">
                
                {{-- Financial Details --}}
                <div class="card card-form">
                    <div class="card-header-form">
                        <div class="section-title">
                            <i data-feather="credit-card" class="text-success" style="width: 18px;"></i> Financial Details
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label-custom">Basic Salary (৳) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="basic_salary" class="form-control form-control-custom @error('basic_salary') is-invalid @enderror" value="{{ old('basic_salary', $employee->basic_salary) }}" required>
                            @error('basic_salary') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label-custom">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control form-control-custom" value="{{ old('bank_name', $employee->bank_name) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label-custom">Bank Account No.</label>
                            <input type="text" name="bank_account" class="form-control form-control-custom" value="{{ old('bank_account', $employee->bank_account) }}">
                        </div>
                    </div>
                </div>

                {{-- Other Info --}}
                <div class="card card-form">
                    <div class="card-header-form">
                        <div class="section-title">
                            <i data-feather="file-text" class="text-dark" style="width: 18px;"></i> Other Information
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label-custom">NID Number</label>
                            <input type="text" name="nid" class="form-control form-control-custom" value="{{ old('nid', $employee->nid) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label-custom">Address</label>
                            <textarea name="address" class="form-control form-control-custom" rows="2">{{ old('address', $employee->address) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label-custom">Additional Notes</label>
                            <textarea name="notes" class="form-control form-control-custom" rows="2">{{ old('notes', $employee->notes) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">
                        <i data-feather="save" class="me-1" style="width: 16px;"></i> Update Employee
                    </button>
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-light py-2">Cancel</a>
                </div>

            </div>

        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if ($.fn.select2) {
            $('.select2').select2({ width: '100%' });
        }
    });
</script>
@endpush