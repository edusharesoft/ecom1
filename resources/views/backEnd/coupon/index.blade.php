@extends('backEnd.layouts.master')
@section('title', 'Manage Coupons')

@section('css')
<style>
    /* --- Card Styles --- */
    .card-modern {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
        background: #fff;
    }
    
    /* --- Table Styles --- */
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
        padding: 1rem;
        vertical-align: middle;
        font-size: 0.875rem;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-modern tr:last-child td { border-bottom: none; }
    .table-modern tr:hover td { background-color: #f8fafc; }

    /* --- Coupon Code Badge --- */
    .coupon-badge {
        font-family: 'Courier New', Courier, monospace;
        font-weight: 700;
        color: #4f46e5;
        background: #eef2ff;
        border: 1px dashed #6366f1;
        padding: 6px 12px;
        border-radius: 6px;
        letter-spacing: 1px;
    }

    /* --- Status & Type Badges --- */
    .badge-soft {
        padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .badge-active { background: #dcfce7; color: #166534; }
    .badge-inactive { background: #f1f5f9; color: #64748b; }
    .badge-expired { background: #fee2e2; color: #991b1b; }
    
    .type-icon {
        width: 24px; height: 24px; border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        margin-right: 8px; font-size: 12px;
    }
    .type-fixed { background: #e0f2fe; color: #0284c7; }
    .type-percent { background: #fef3c7; color: #d97706; }

    /* --- Action Buttons --- */
    .btn-icon {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 8px; transition: all 0.2s; border: none;
    }
    .btn-icon:hover { transform: translateY(-2px); }
    .btn-edit { background: #e0e7ff; color: #4338ca; }
    .btn-delete { background: #fee2e2; color: #991b1b; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    {{-- PAGE HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark">
                <i data-feather="tag" class="text-primary me-2"></i> Manage Coupons
            </h4>
            <p class="text-muted small mb-0">Create and manage discount codes for customers.</p>
        </div>
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
            <i data-feather="plus-circle" class="me-1" style="width: 16px;"></i> Create Coupon
        </a>
    </div>

    <div class="card card-modern">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="20%">Coupon Code</th>
                        <th width="15%">Discount Type</th>
                        <th width="10%">Value</th>
                        <th width="15%">Min Purchase</th>
                        <th width="20%">Validity Period</th>
                        <th width="10%">Status</th>
                        <th width="10%" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $key => $coupon)
                        <tr>
                            <td class="text-muted">{{ $key + 1 }}</td>
                            
                            {{-- Code --}}
                            <td>
                                <span class="coupon-badge">{{ $coupon->code }}</span>
                            </td>

                            {{-- Type --}}
                            <td>
                                @if(in_array($coupon->type, ['flat', 'fixed']))
                                    <div class="d-flex align-items-center">
                                        <span class="type-icon type-fixed"><i class="fas fa-dollar-sign"></i></span>
                                        <span>Fixed Amount</span>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center">
                                        <span class="type-icon type-percent"><i class="fas fa-percent"></i></span>
                                        <span>Percentage</span>
                                    </div>
                                @endif
                            </td>

                            {{-- Value --}}
                            <td>
                                <span class="fw-bold text-dark fs-6">
                                    @if(in_array($coupon->type, ['percent', 'percentage']))
                                        {{ $coupon->value }}%
                                    @else
                                        ৳{{ number_format($coupon->value, 2) }}
                                    @endif
                                </span>
                            </td>

                            {{-- Min Purchase --}}
                            <td class="text-muted">
                                {{ $coupon->min_purchase ? '৳'.number_format($coupon->min_purchase) : 'No Limit' }}
                            </td>

                            {{-- Validity --}}
                            <td>
                                <div class="d-flex flex-column small">
                                    <span class="text-success">
                                        <i class="far fa-calendar-check me-1"></i> {{ $coupon->valid_from ?? 'Anytime' }}
                                    </span>
                                    <span class="text-danger mt-1">
                                        <i class="far fa-calendar-times me-1"></i> {{ $coupon->valid_to ?? 'Lifetime' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td>
                                @php
                                    $isExpired = $coupon->valid_to && \Carbon\Carbon::parse($coupon->valid_to)->isPast();
                                @endphp

                                @if($isExpired)
                                    <span class="badge-soft badge-expired">Expired</span>
                                @elseif($coupon->status)
                                    <span class="badge-soft badge-active">Active</span>
                                @else
                                    <span class="badge-soft badge-inactive">Inactive</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn-icon btn-edit" title="Edit">
                                        <i data-feather="edit-2" style="width:14px;"></i>
                                    </a>
                                    
                                    <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this coupon?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon btn-delete" title="Delete">
                                            <i data-feather="trash-2" style="width:14px;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="60" class="mb-3 opacity-25">
                                <p class="text-muted fw-bold mb-0">No Coupons Found</p>
                                <small class="text-muted">Create a new coupon to get started.</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination (If passed) --}}
        @if(method_exists($coupons, 'links'))
        <div class="p-4 border-top d-flex justify-content-end">
            {{ $coupons->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
    {{-- Ensure FontAwesome is loaded for icons inside table if Feather is not enough --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush