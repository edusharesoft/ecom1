@extends('backEnd.layouts.master')
@section('title','Vendor Verification Details')
@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('admin.vendor.verification.index') }}" class="btn btn-primary rounded-pill">
                        <i class="fe-arrow-left"></i> Back to List
                    </a>
                </div>
                <h4 class="page-title">Vendor Verification Details</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <!-- Vendor Information Card -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fe-user"></i> Vendor Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Shop Name:</strong> {{ $vendor->shop_name }}</p>
                            <p><strong>Owner Name:</strong> {{ $vendor->owner_name }}</p>
                            <p><strong>Email:</strong> {{ $vendor->email }}</p>
                            <p><strong>Phone:</strong> {{ $vendor->phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Address:</strong> {{ $vendor->address ?? 'N/A' }}</p>
                            <p><strong>Status:</strong> 
                                @if($vendor->status == 1)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </p>
                            <p><strong>Commission Rate:</strong> {{ $vendor->commission_rate }}%</p>
                            @if($vendor->wallet)
                                <p><strong>Wallet Balance:</strong> ৳{{ number_format($vendor->wallet->balance, 2) }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Verification Status Card -->
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fe-check-circle"></i> Verification Status</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Current Status:</strong>
                                @if($vendor->verification_status == 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($vendor->verification_status == 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </p>
                            @if($vendor->verified_at)
                                <p><strong>Verified At:</strong> 
                                    @if(is_object($vendor->verified_at))
                                        {{ $vendor->verified_at->format('d M, Y h:i A') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($vendor->verified_at)->format('d M, Y h:i A') }}
                                    @endif
                                </p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            @if($vendor->verification_note)
                                <p><strong>Admin Note:</strong></p>
                                <div class="alert alert-info">
                                    {{ $vendor->verification_note }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Verification Documents Card -->
            <div class="card mb-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fe-file-text"></i> Verification Documents</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Voter ID Front -->
                        <div class="col-md-4 mb-4">
                            <label class="form-label"><strong>Voter ID Card - Front Side</strong></label>
                            @if($vendor->voter_id_front)
                                <div class="text-center">
                                    <img src="{{ asset($vendor->voter_id_front) }}" alt="Voter ID Front" 
                                         class="img-thumbnail" style="max-width: 100%; cursor: pointer; max-height: 400px;" 
                                         onclick="window.open('{{ asset($vendor->voter_id_front) }}', '_blank')">
                                    <p class="mt-2">
                                        <a href="{{ asset($vendor->voter_id_front) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fe-maximize-2"></i> View Full Size
                                        </a>
                                    </p>
                                </div>
                            @else
                                <div class="alert alert-warning">Not uploaded</div>
                            @endif
                        </div>

                        <!-- Voter ID Back -->
                        <div class="col-md-4 mb-4">
                            <label class="form-label"><strong>Voter ID Card - Back Side</strong></label>
                            @if($vendor->voter_id_back)
                                <div class="text-center">
                                    <img src="{{ asset($vendor->voter_id_back) }}" alt="Voter ID Back" 
                                         class="img-thumbnail" style="max-width: 100%; cursor: pointer; max-height: 400px;" 
                                         onclick="window.open('{{ asset($vendor->voter_id_back) }}', '_blank')">
                                    <p class="mt-2">
                                        <a href="{{ asset($vendor->voter_id_back) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fe-maximize-2"></i> View Full Size
                                        </a>
                                    </p>
                                </div>
                            @else
                                <div class="alert alert-warning">Not uploaded</div>
                            @endif
                        </div>

                        <!-- Self Image -->
                        <div class="col-md-4 mb-4">
                            <label class="form-label"><strong>Self Image (Your Photo)</strong></label>
                            @if($vendor->self_image)
                                <div class="text-center">
                                    <img src="{{ asset($vendor->self_image) }}" alt="Self Image" 
                                         class="img-thumbnail" style="max-width: 100%; cursor: pointer; max-height: 400px;" 
                                         onclick="window.open('{{ asset($vendor->self_image) }}', '_blank')">
                                    <p class="mt-2">
                                        <a href="{{ asset($vendor->self_image) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fe-maximize-2"></i> View Full Size
                                        </a>
                                    </p>
                                </div>
                            @else
                                <div class="alert alert-warning">Not uploaded</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            @if($vendor->verification_status == 'pending')
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fe-check-circle"></i> Verification Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <form action="{{ route('admin.vendor.verification.approve', $vendor->id) }}" method="POST">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="admin_note_approve">Admin Note (Optional)</label>
                                    <textarea name="admin_note" id="admin_note_approve" class="form-control" rows="3" placeholder="Add a note..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('Are you sure you want to approve this vendor verification?')">
                                    <i class="fe-check"></i> Approve Verification
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <form action="{{ route('admin.vendor.verification.reject', $vendor->id) }}" method="POST">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="rejection_reason">Rejection Reason *</label>
                                    <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="3" placeholder="Enter reason for rejection..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-danger btn-lg" onclick="return confirm('Are you sure you want to reject this vendor verification?')">
                                    <i class="fe-x"></i> Reject Verification
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fe-info"></i> This vendor verification has already been {{ $vendor->verification_status }}.
                        @if($vendor->verification_status == 'rejected' && $vendor->verification_note)
                            <br><strong>Reason:</strong> {{ $vendor->verification_note }}
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
