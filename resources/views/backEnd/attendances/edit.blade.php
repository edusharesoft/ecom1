@extends('backEnd.layouts.master')
@section('title', 'Edit Attendance')

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

    /* --- Status Radio Group --- */
    .status-radio-group {
        display: flex; gap: 10px; flex-wrap: wrap;
    }
    .status-radio-input { display: none; }
    .status-radio-label {
        padding: 8px 16px; border-radius: 8px; border: 1px solid #e2e8f0;
        cursor: pointer; font-size: 0.85rem; font-weight: 600; color: #64748b;
        transition: all 0.2s; background: #fff; flex: 1; text-align: center;
    }
    .status-radio-input:checked + .status-radio-label {
        border-color: transparent; color: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    /* Status Colors */
    #status_present:checked + .status-radio-label { background-color: #10b981; }
    #status_absent:checked + .status-radio-label { background-color: #ef4444; }
    #status_late:checked + .status-radio-label { background-color: #f59e0b; }
    #status_half_day:checked + .status-radio-label { background-color: #3b82f6; }
    #status_holiday:checked + .status-radio-label { background-color: #6366f1; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            
            <form action="{{ route('admin.attendances.update', $attendance->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card card-modern">
                    {{-- Header --}}
                    <div class="card-header-modern">
                        <div>
                            <h5 class="mb-1 fw-bold text-dark">Edit Attendance</h5>
                            <p class="text-muted small mb-0">Update employee attendance record.</p>
                        </div>
                        <a href="{{ route('admin.attendances.index') }}" class="btn btn-light btn-sm rounded-pill px-3">
                            <i data-feather="x" style="width:14px;"></i> Close
                        </a>
                    </div>

                    <div class="card-body p-4">
                        
                        {{-- Employee Info --}}
                        <div class="bg-light p-3 rounded-3 mb-4 d-flex align-items-center">
                            <div class="bg-white p-2 rounded-circle shadow-sm me-3 text-primary fw-bold d-flex align-items-center justify-content-center" style="width:45px; height:45px;">
                                {{ substr($attendance->employee->name, 0, 1) }}
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">{{ $attendance->employee->name }}</h6>
                                <small class="text-muted">ID: {{ $attendance->employee->employee_id }}</small>
                            </div>
                            <div class="ms-auto text-end">
                                <small class="text-muted d-block">Date</small>
                                <span class="fw-bold text-dark">{{ $attendance->attendance_date->format('d M, Y') }}</span>
                            </div>
                        </div>

                        {{-- Time Inputs --}}
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <label class="form-label-custom">Check In Time</label>
                                <input type="time" name="check_in" class="form-control form-control-custom" 
                                       value="{{ old('check_in', $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '') }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label-custom">Check Out Time</label>
                                <input type="time" name="check_out" class="form-control form-control-custom" 
                                       value="{{ old('check_out', $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '') }}">
                            </div>
                        </div>

                        {{-- Status Selection --}}
                        <div class="mb-4">
                            <label class="form-label-custom d-block mb-2">Attendance Status <span class="text-danger">*</span></label>
                            <div class="status-radio-group">
                                
                                <input type="radio" id="status_present" name="status" value="present" class="status-radio-input" 
                                    {{ $attendance->status == 'present' ? 'checked' : '' }}>
                                <label for="status_present" class="status-radio-label">Present</label>

                                <input type="radio" id="status_late" name="status" value="late" class="status-radio-input"
                                    {{ $attendance->status == 'late' ? 'checked' : '' }}>
                                <label for="status_late" class="status-radio-label">Late</label>

                                <input type="radio" id="status_half_day" name="status" value="half_day" class="status-radio-input"
                                    {{ $attendance->status == 'half_day' ? 'checked' : '' }}>
                                <label for="status_half_day" class="status-radio-label">Half Day</label>

                                <input type="radio" id="status_absent" name="status" value="absent" class="status-radio-input"
                                    {{ $attendance->status == 'absent' ? 'checked' : '' }}>
                                <label for="status_absent" class="status-radio-label">Absent</label>

                                <input type="radio" id="status_holiday" name="status" value="holiday" class="status-radio-input"
                                    {{ $attendance->status == 'holiday' ? 'checked' : '' }}>
                                <label for="status_holiday" class="status-radio-label">Holiday</label>
                            </div>
                            @error('status') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Notes --}}
                        <div class="mb-4">
                            <label class="form-label-custom">Additional Notes</label>
                            <textarea name="notes" class="form-control form-control-custom" rows="3" placeholder="Any remarks...">{{ old('notes', $attendance->notes) }}</textarea>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">
                                <i data-feather="check-circle" class="me-1" style="width: 16px;"></i> Update Attendance
                            </button>
                            <a href="{{ route('admin.attendances.index') }}" class="btn btn-light py-2">Cancel</a>
                        </div>

                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection