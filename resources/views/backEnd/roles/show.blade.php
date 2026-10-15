@extends('backEnd.layouts.master')
@section('title','View Role')

@section('css')
<style>
    /* Premium Card Design */
    .card {
        border: none;
        box-shadow: 0 0 20px rgba(18, 38, 63, 0.03);
        border-radius: 12px;
        background: #fff;
        margin-bottom: 24px;
    }
    .card-header {
        background: #fff;
        border-bottom: 1px solid #f1f5f7;
        padding: 20px 25px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    /* Role Title Styling */
    .role-title {
        font-size: 24px;
        font-weight: 700;
        color: #2d3436;
        margin-bottom: 5px;
        text-transform: capitalize;
    }
    .role-label {
        font-size: 13px;
        color: #98a6ad;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 600;
        margin-bottom: 10px;
        display: block;
    }

    /* Permission Badges */
    .permission-badge {
        font-size: 13px;
        font-weight: 500;
        padding: 8px 15px;
        border-radius: 50rem;
        background-color: rgba(114, 124, 245, 0.1);
        color: #727cf5;
        border: 1px solid rgba(114, 124, 245, 0.15);
        display: inline-block;
        margin: 4px;
        transition: all 0.2s;
    }
    .permission-badge:hover {
        background-color: #727cf5;
        color: #fff;
        transform: translateY(-2px);
    }

    /* Empty State */
    .empty-permissions {
        color: #fa5c7c;
        background-color: rgba(250, 92, 124, 0.1);
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 14px;
        display: inline-block;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    
    <div class="row mb-3 mt-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="page-title mb-0" style="font-weight: 700; color: #2d3436;">Role Details</h4>
                <p class="text-muted font-size-13 mb-0">View role information and permissions.</p>
            </div>
            <a href="{{ route('roles.index') }}" class="btn btn-light rounded-pill border shadow-sm px-4">
                <i class="fe-arrow-left me-1"></i> Back to Roles
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-4">
                    
                    <div class="text-center mb-5">
                        <div class="avatar-lg mx-auto mb-3">
                            <span class="avatar-title bg-soft-primary text-primary font-size-24 rounded-circle">
                                <i class="fe-shield"></i>
                            </span>
                        </div>
                        <h2 class="role-title">{{ $role->name }}</h2>
                        <span class="badge bg-dark rounded-pill px-3">ID: #{{ $role->id }}</span>
                    </div>

                    <hr class="my-4" style="border-color: #f1f5f7;">

                    <div class="row justify-content-center">
                        <div class="col-lg-10">
                            <span class="role-label text-center">Assigned Permissions</span>
                            
                            <div class="text-center mt-3">
                                @if(!empty($rolePermissions) && count($rolePermissions) > 0)
                                    @foreach($rolePermissions as $v)
                                        <span class="permission-badge">
                                            <i class="fe-check-circle me-1"></i> {{ $v->name }}
                                        </span>
                                    @endforeach
                                @else
                                    <div class="empty-permissions">
                                        <i class="fe-alert-circle me-1"></i> No permissions assigned to this role.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div> </div> </div> </div>
</div>
@endsection