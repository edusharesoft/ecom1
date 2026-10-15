@extends('backEnd.layouts.master')
@section('title','Manage Roles')

@section('css')
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-select-bs5/css/select.bootstrap5.min.css" rel="stylesheet" type="text/css" />

<style>
    /* Premium Card & Table */
    .card {
        border: none;
        box-shadow: 0 0 20px rgba(18, 38, 63, 0.03);
        border-radius: 12px;
        overflow: hidden;
    }
    .card-body { padding: 25px; }

    /* Table Styling */
    .table thead th {
        background-color: #f9fbfd;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 11px;
        color: #8391a2;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #eef2f7;
        padding: 12px 15px;
    }
    .table tbody td {
        vertical-align: middle;
        padding: 15px;
        border-bottom: 1px solid #f1f5f7;
        color: #313b5e;
        font-size: 14px;
    }

    /* Role Badge Styling */
    .role-badge {
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 5px;
        font-size: 12px;
        letter-spacing: 0.5px;
        text-transform: capitalize;
    }
    .role-admin { background-color: rgba(250, 92, 124, 0.15); color: #fa5c7c; border: 1px solid rgba(250, 92, 124, 0.2); }
    .role-user { background-color: rgba(114, 124, 245, 0.15); color: #727cf5; border: 1px solid rgba(114, 124, 245, 0.2); }
    .role-other { background-color: rgba(10, 207, 151, 0.15); color: #0acf97; border: 1px solid rgba(10, 207, 151, 0.2); }

    /* Action Buttons */
    .action-btn {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        color: #6c757d;
        transition: all 0.2s;
        border: 1px solid transparent;
        background: #f9fbfd;
    }
    .action-btn:hover { background-color: #eef2f7; color: #343a40; }
    
    .btn-view:hover { background-color: rgba(57, 175, 209, 0.1); color: #39afd1; }
    .btn-edit:hover { background-color: rgba(114, 124, 245, 0.1); color: #727cf5; }
    .btn-delete:hover { background-color: rgba(250, 92, 124, 0.1); color: #fa5c7c; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    
    <div class="row mb-3 mt-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="page-title mb-0" style="font-weight: 700; color: #2d3436;">Roles & Permissions</h4>
                <p class="text-muted font-size-13 mb-0">Manage user roles and access control.</p>
            </div>
            <a href="{{route('roles.create')}}" class="btn btn-primary rounded-pill shadow-sm px-4">
                <i class="fe-plus me-1"></i> Create New Role
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="datatable-buttons" class="table table-hover w-100 dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th style="width: 50px;">SL</th>
                                <th>Role Name</th>
                                <th class="text-end" style="width: 150px;">Action</th>
                            </tr>
                        </thead>                
                        <tbody>
                            @foreach($show_data as $key => $value)

                                @php
                                    // Logic to check if role is Admin
                                    $isAdminRole = (strtolower($value->name) === 'admin');

                                    // Logic to check if logged-in user is Admin
                                    $isLoginAdmin = auth()->check() && method_exists(auth()->user(), 'hasRole')
                                                    ? auth()->user()->hasRole('Admin')
                                                    : false;
                                @endphp

                                {{-- Hide Admin role from non-admin users --}}
                                @if($isAdminRole && !$isLoginAdmin)
                                    @continue
                                @endif

                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    
                                    <td>
                                        @if(strtolower($value->name) == 'admin')
                                            <span class="role-badge role-admin"><i class="fe-shield me-1"></i> {{ $value->name }}</span>
                                        @elseif(strtolower($value->name) == 'user' || strtolower($value->name) == 'customer')
                                            <span class="role-badge role-user"><i class="fe-user me-1"></i> {{ $value->name }}</span>
                                        @else
                                            <span class="role-badge role-other"><i class="fe-check-circle me-1"></i> {{ $value->name }}</span>
                                        @endif
                                    </td>

                                    <td class="text-end">
                                        <div class="d-inline-flex gap-2">
                                            
                                            {{-- Show/View --}}
                                            <a href="{{ route('roles.show', $value->id) }}" class="action-btn btn-view" title="View Permissions">
                                                <i class="fe-eye"></i>
                                            </a>

                                            {{-- Edit --}}
                                            <a href="{{ route('roles.edit', $value->id) }}" class="action-btn btn-edit" title="Edit Role">
                                                <i class="fe-edit"></i>
                                            </a>

                                            {{-- Delete --}}
                                            <form method="post" action="{{ route('roles.destroy') }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                                <button type="submit" class="action-btn btn-delete delete-confirm" title="Delete">
                                                    <i class="fe-trash-2"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div> </div> </div></div>
</div>
@endsection

@section('script')
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons/js/buttons.flash.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-select/js/dataTables.select.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/pdfmake/build/pdfmake.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/pdfmake/build/vfs_fonts.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/js/pages/datatables.init.js"></script>
@endsection