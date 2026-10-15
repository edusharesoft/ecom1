@extends('backEnd.layouts.master')
@section('title', 'Employee Profile')

@section('css')
<style>
    /* --- Layout & Card Styles --- */
    .profile-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        border: 1px solid #e9ecef;
        overflow: hidden;
    }
    .profile-header-bg {
        height: 100px;
        background: linear-gradient(to right, #2c3e50, #4ca1af);
    }
    .profile-avatar-wrap {
        margin-top: -50px;
        text-align: center;
    }
    .profile-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 4px solid #fff;
        background: #f8f9fa;
        object-fit: cover;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        font-weight: bold;
        color: #555;
    }
    
    /* --- Info List in Sidebar --- */
    .profile-info-list {
        list-style: none;
        padding: 0;
        margin: 20px 0;
    }
    .profile-info-list li {
        border-bottom: 1px solid #f0f2f5;
        padding: 12px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 14px;
    }
    .profile-info-list li:last-child { border-bottom: none; }
    .label-text { color: #6c757d; font-weight: 500; }
    .val-text { color: #343a40; font-weight: 600; text-align: right; }

    /* --- Right Side Widgets --- */
    .stat-widget {
        background: #fff;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #e9ecef;
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .stat-icon {
        width: 45px; height: 45px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; margin-right: 15px;
    }
    .bg-light-primary { background: #e3f2fd; color: #1976d2; }
    .bg-light-warning { background: #fff8e1; color: #ffa000; }
    .bg-light-success { background: #e8f5e9; color: #388e3c; }

    /* --- Tabs & Content --- */
    .nav-tabs-custom {
        border-bottom: 2px solid #e9ecef;
    }
    .nav-tabs-custom .nav-link {
        border: none;
        border-bottom: 2px solid transparent;
        color: #6c757d;
        font-weight: 600;
        padding: 12px 20px;
        transition: all 0.3s;
    }
    .nav-tabs-custom .nav-link.active {
        color: #2c3e50;
        border-bottom: 2px solid #2c3e50;
        background: transparent;
    }
    .tab-content {
        background: #fff;
        padding: 25px;
        border: 1px solid #e9ecef;
        border-top: none;
        border-radius: 0 0 8px 8px;
    }

    /* --- Overview Grid --- */
    .overview-item {
        margin-bottom: 20px;
    }
    .overview-label {
        font-size: 12px;
        text-transform: uppercase;
        color: #adb5bd;
        font-weight: 700;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }
    .overview-value {
        font-size: 15px;
        color: #212529;
        font-weight: 500;
        border-bottom: 1px solid #f8f9fa;
        padding-bottom: 5px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 text-dark fw-bold">Employee Profile</h4>
        <div>
            <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary btn-sm me-2">
                <i data-feather="arrow-left" class="me-1" style="width:14px;"></i> Back
            </a>
            <a href="{{ route('admin.employees.edit', $employee->id) }}" class="btn btn-dark btn-sm">
                <i data-feather="edit" class="me-1" style="width:14px;"></i> Edit Profile
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="profile-card">
                <div class="profile-header-bg"></div>
                <div class="profile-avatar-wrap">
                    <div class="profile-avatar">
                        {{ substr($employee->name, 0, 1) }}
                    </div>
                </div>
                <div class="text-center mt-3 px-3">
                    <h5 class="fw-bold mb-1">{{ $employee->name }}</h5>
                    <p class="text-muted mb-2">{{ $employee->designation ?? 'No Designation' }}</p>
                    
                    @if($employee->status == 'active')
                        <span class="badge bg-success px-3 py-1 rounded-pill">Active Employee</span>
                    @elseif($employee->status == 'inactive')
                        <span class="badge bg-warning text-dark px-3 py-1 rounded-pill">Inactive</span>
                    @else
                        <span class="badge bg-danger px-3 py-1 rounded-pill">Terminated</span>
                    @endif
                </div>

                <ul class="profile-info-list mt-4">
                    <li>
                        <span class="label-text"><i data-feather="hash" style="width:14px;" class="me-2"></i>Employee ID</span>
                        <span class="val-text">{{ $employee->employee_id }}</span>
                    </li>
                    <li>
                        <span class="label-text"><i data-feather="briefcase" style="width:14px;" class="me-2"></i>Department</span>
                        <span class="val-text">{{ $employee->department ?? 'N/A' }}</span>
                    </li>
                    <li>
                        <span class="label-text"><i data-feather="phone" style="width:14px;" class="me-2"></i>Phone</span>
                        <span class="val-text">{{ $employee->phone ?? 'N/A' }}</span>
                    </li>
                    <li>
                        <span class="label-text"><i data-feather="mail" style="width:14px;" class="me-2"></i>Email</span>
                        <span class="val-text" style="font-size:13px;">{{ $employee->email }}</span>
                    </li>
                    <li>
                        <span class="label-text"><i data-feather="calendar" style="width:14px;" class="me-2"></i>Joined</span>
                        <span class="val-text">{{ $employee->joining_date->format('d M, Y') }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-lg-8">
            
            <div class="row">
                <div class="col-md-4">
                    <div class="stat-widget">
                        <div class="stat-icon bg-light-primary">
                            <i data-feather="check-circle"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-bold">ATTENDANCE</div>
                            <div class="h5 mb-0 fw-bold">{{ $employee->attendances->where('status','present')->count() }} Days</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-widget">
                        <div class="stat-icon bg-light-warning">
                            <i data-feather="coffee"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-bold">LEAVES TAKEN</div>
                            <div class="h5 mb-0 fw-bold">{{ $employee->leaves->where('status','approved')->sum('total_days') }} Days</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-widget">
                        <div class="stat-icon bg-light-success">
                            <i data-feather="dollar-sign"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-bold">BASIC SALARY</div>
                            <div class="h5 mb-0 fw-bold">৳{{ number_format($employee->basic_salary) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#overview">Overview</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#attendance">Attendance Log</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#payroll">Payroll History</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#leaves">Leave History</a>
                </li>
            </ul>

            <div class="tab-content">
                
                <div class="tab-pane fade show active" id="overview">
                    <h6 class="text-primary fw-bold mb-4"><i data-feather="info" style="width:16px;" class="me-1"></i> General Information</h6>
                    <div class="row">
                        <div class="col-md-6 overview-item">
                            <div class="overview-label">Full Name</div>
                            <div class="overview-value">{{ $employee->name }}</div>
                        </div>
                        <div class="col-md-6 overview-item">
                            <div class="overview-label">Linked User Account</div>
                            <div class="overview-value">{{ $employee->user->name ?? 'Not Linked' }}</div>
                        </div>
                        <div class="col-md-6 overview-item">
                            <div class="overview-label">National ID (NID)</div>
                            <div class="overview-value">{{ $employee->nid ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 overview-item">
                            <div class="overview-label">Address</div>
                            <div class="overview-value">{{ $employee->address ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <h6 class="text-primary fw-bold mb-4 mt-3"><i data-feather="credit-card" style="width:16px;" class="me-1"></i> Banking Details</h6>
                    <div class="row">
                        <div class="col-md-6 overview-item">
                            <div class="overview-label">Bank Name</div>
                            <div class="overview-value">{{ $employee->bank_name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 overview-item">
                            <div class="overview-label">Account Number</div>
                            <div class="overview-value font-monospace">{{ $employee->bank_account ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="attendance">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employee->attendances as $attn)
                                    <tr>
                                        <td>{{ $attn->attendance_date->format('d M, Y') }}</td>
                                        <td>{{ $attn->check_in ?? '--:--' }}</td>
                                        <td>{{ $attn->check_out ?? '--:--' }}</td>
                                        <td>
                                            @if($attn->status == 'present') <span class="badge bg-success">Present</span>
                                            @elseif($attn->status == 'late') <span class="badge bg-warning text-dark">Late</span>
                                            @elseif($attn->status == 'absent') <span class="badge bg-danger">Absent</span>
                                            @else <span class="badge bg-secondary">{{ ucfirst($attn->status) }}</span> @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-3 text-muted">No attendance data.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="payroll">
                    <h6 class="fw-bold mb-3">Salary Slips</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-sm">
                            <thead class="bg-light">
                                <tr>
                                    <th>Month</th>
                                    <th>Work Days</th>
                                    <th>Net Salary</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employee->salaries as $salary)
                                    <tr>
                                        <td>{{ $salary->salary_month }}</td>
                                        <td>{{ $salary->working_days }} (P:{{ $salary->present_days }})</td>
                                        <td class="fw-bold">৳{{ number_format($salary->net_salary) }}</td>
                                        <td>
                                            <span class="badge {{ $salary->status == 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                                {{ ucfirst($salary->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted">No salary data.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <h6 class="fw-bold mb-3">Payment History</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="bg-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Month</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employee->salaryPayments as $payment)
                                    <tr>
                                        <td>#{{ $payment->payment_id }}</td>
                                        <td>{{ $payment->payment_date->format('d M, Y') }}</td>
                                        <td class="fw-bold text-success">৳{{ number_format($payment->amount) }}</td>
                                        <td>{{ $payment->payment_month }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted">No payment history.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="leaves">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Date Range</th>
                                    <th>Days</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employee->leaves as $leave)
                                    <tr>
                                        <td class="fw-bold">{{ ucfirst($leave->leave_type) }}</td>
                                        <td>
                                            {{ $leave->start_date->format('d M') }} - {{ $leave->end_date->format('d M, Y') }}
                                        </td>
                                        <td>{{ $leave->total_days }}</td>
                                        <td>
                                            @if($leave->status == 'approved') <span class="badge bg-success">Approved</span>
                                            @elseif($leave->status == 'rejected') <span class="badge bg-danger">Rejected</span>
                                            @else <span class="badge bg-warning text-dark">Pending</span> @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-3 text-muted">No leave history.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection