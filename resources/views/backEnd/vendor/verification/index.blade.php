@extends('backEnd.layouts.master')
@section('title', 'Vendor Verification Requests')

@section('css')
<style>
    /* --- Modern Card --- */
    .card-modern {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        background: #fff;
    }

    /* --- Filter Section --- */
    .filter-box {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 1.25rem;
        border-radius: 12px 12px 0 0;
    }
    .form-control-modern, .form-select-modern {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.6rem 1rem;
        font-size: 0.875rem;
        background-color: #fff;
    }
    .form-control-modern:focus, .form-select-modern:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    /* --- Table Styling --- */
    .table-modern th {
        background-color: #fff;
        color: #64748b;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        padding: 1rem;
        border-bottom: 2px solid #f1f5f9;
        white-space: nowrap;
    }
    .table-modern td {
        vertical-align: middle;
        padding: 1rem;
        font-size: 0.875rem;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-modern tr:hover td { background-color: #f8fafc; }

    /* --- Status Badges --- */
    .badge-soft {
        padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .badge-approved { background: #dcfce7; color: #166534; }
    .badge-rejected { background: #fee2e2; color: #991b1b; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

    /* --- Action Buttons --- */
    .btn-icon {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 8px; transition: all 0.2s; border: none; background: #e0e7ff; color: #4338ca;
    }
    .btn-icon:hover { transform: translateY(-2px); background: #4338ca; color: #fff; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    {{-- PAGE HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark">
                <i data-feather="shield" class="text-primary me-2"></i> Verification Requests
            </h4>
            <p class="text-muted small mb-0">Review and manage vendor KYC verifications.</p>
        </div>
    </div>

    <div class="card card-modern">
        
        {{-- FILTERS --}}
        <div class="filter-box">
            <div class="row g-3">
                
                {{-- Status Filter --}}
                <div class="col-md-4">
                    <form method="GET" action="{{ route('admin.vendor.verification.index') }}">
                        <div class="input-group">
                            <select name="status" class="form-select form-select-modern">
                                <option value="">Filter by Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Requests</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved Vendors</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected Vendors</option>
                            </select>
                            <button type="submit" class="btn btn-dark"><i data-feather="filter" style="width: 14px;"></i></button>
                        </div>
                    </form>
                </div>

                {{-- Search --}}
                <div class="col-md-5 ms-auto">
                    <form method="GET" action="{{ route('admin.vendor.verification.index') }}">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i data-feather="search" style="width: 16px;"></i></span>
                            <input type="text" name="keyword" class="form-control form-control-modern border-start-0" 
                                   placeholder="Search shop, owner, email..." value="{{ request('keyword') }}">
                            <button type="submit" class="btn btn-primary fw-bold">Search</button>
                            @if(request('keyword') || request('status'))
                                <a href="{{ route('admin.vendor.verification.index') }}" class="btn btn-light border" title="Reset">
                                    <i data-feather="refresh-cw" style="width: 14px;"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="25%">Shop & Owner</th>
                        <th width="20%">Contact Info</th>
                        <th width="15%">Documents</th>
                        <th width="15%">Request Date</th>
                        <th width="10%">Status</th>
                        <th width="10%" class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vendors as $key => $vendor)
                        <tr>
                            <td class="text-muted">{{ $loop->iteration }}</td>
                            
                            {{-- Vendor Info --}}
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center me-3 border" 
                                         style="width: 40px; height: 40px; font-size: 14px;">
                                        {{ substr($vendor->shop_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $vendor->shop_name }}</div>
                                        <div class="small text-muted">Owner: {{ $vendor->owner_name }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Contact --}}
                            <td>
                                <div class="d-flex flex-column small">
                                    <span class="text-dark mb-1"><i data-feather="mail" style="width: 12px;" class="text-muted me-1"></i> {{ $vendor->email }}</span>
                                    <span class="text-dark"><i data-feather="phone" style="width: 12px;" class="text-muted me-1"></i> {{ $vendor->phone }}</span>
                                </div>
                            </td>

                            {{-- Documents Status --}}
                            <td>
                                @if($vendor->voter_id_front || $vendor->voter_id_back || $vendor->self_image)
                                    <span class="badge bg-light text-dark border">
                                        <i data-feather="file-text" style="width: 12px;" class="me-1"></i> Files Attached
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border">
                                        <i data-feather="alert-circle" style="width: 12px;" class="me-1"></i> Missing
                                    </span>
                                @endif
                            </td>

                            {{-- Request Date --}}
                            <td class="text-muted small">
                                @if($vendor->verified_at && $vendor->verification_status != 'pending')
                                    <div class="fw-bold">Verified On:</div>
                                    {{ is_object($vendor->verified_at) ? $vendor->verified_at->format('d M, Y') : \Carbon\Carbon::parse($vendor->verified_at)->format('d M, Y') }}
                                @else
                                    <div class="fw-bold">Requested:</div>
                                    {{ $vendor->created_at->format('d M, Y') }}
                                @endif
                            </td>

                            {{-- Status --}}
                            <td>
                                @if($vendor->verification_status == 'approved')
                                    <span class="badge-soft badge-approved"><span class="status-dot"></span> Approved</span>
                                @elseif($vendor->verification_status == 'rejected')
                                    <span class="badge-soft badge-rejected"><span class="status-dot"></span> Rejected</span>
                                @else
                                    <span class="badge-soft badge-pending"><span class="status-dot"></span> Pending</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="text-end">
                                <a href="{{ route('admin.vendor.verification.show', $vendor->id) }}" class="btn-icon" title="View Details">
                                    <i data-feather="eye" style="width: 14px;"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="p-4 border-top d-flex justify-content-between align-items-center bg-white rounded-bottom">
            <small class="text-muted">
                Showing <strong>{{ $vendors->firstItem() }}</strong> to <strong>{{ $vendors->lastItem() }}</strong> of <strong>{{ $vendors->total() }}</strong> requests
            </small>
            <div>
                {{ $vendors->links('pagination::bootstrap-4') }}
            </div>
        </div>

    </div>
</div>
@endsection