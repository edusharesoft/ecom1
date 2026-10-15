@extends('backEnd.layouts.master')
@section('title','Category Management')

@section('css')
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<style>
    /* Professional Table Styling */
    .card-modern {
        border: none;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    
    .table-pro thead th {
        background-color: #f8f9fa;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.8px;
        border-bottom: 2px solid #e2e8f0;
        padding: 15px;
    }
    
    .table-pro tbody td {
        vertical-align: middle;
        color: #334155;
        font-size: 14px;
        font-weight: 500;
        padding: 15px;
        border-bottom: 1px solid #f1f5f9;
    }
    
    .table-pro tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    .status-active {
        background-color: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
    .status-inactive {
        background-color: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    /* Action Buttons */
    .btn-action {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        margin-right: 5px;
        transition: all 0.2s;
        border: 1px solid transparent;
    }
    .btn-edit {
        color: #2563eb;
        background-color: #eff6ff;
        border-color: #dbeafe;
    }
    .btn-edit:hover {
        background-color: #2563eb;
        color: #fff;
    }
    .btn-delete {
        color: #dc2626;
        background-color: #fef2f2;
        border-color: #fee2e2;
    }
    .btn-delete:hover {
        background-color: #dc2626;
        color: #fff;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0 text-dark">Category Manager</h4>
            <span class="text-muted small">Organize and manage banner categories</span>
        </div>
        <a href="{{ route('banner_category.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
            <i class="fe-plus me-1"></i> Add New Category
        </a>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card card-modern">
                <div class="card-body">
                    <table id="datatable-buttons" class="table table-pro dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th width="10%">SL</th>
                                <th width="50%">Category Name</th>
                                <th width="20%">Status</th>
                                <th width="20%" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $key => $value)
                            <tr>
                                <td>
                                    <span class="fw-bold text-secondary">#{{$loop->iteration}}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark" style="font-size: 15px;">{{$value->name}}</span>
                                </td>
                                <td>
                                    @if($value->status==1)
                                        <span class="status-badge status-active">
                                            <i class="mdi mdi-check-circle me-1"></i> Active
                                        </span> 
                                    @else 
                                        <span class="status-badge status-inactive">
                                            <i class="mdi mdi-close-circle me-1"></i> Inactive
                                        </span> 
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end">
                                        <a href="{{route('banner_category.edit',$value->id)}}" class="btn-action btn-edit" title="Edit">
                                            <i class="fe-edit-1" style="font-size: 16px;"></i>
                                        </a>

                                        <form action="{{ route('banner_category.destroy') }}" method="POST" class="delete-form d-inline">
                                            @csrf
                                            <input type="hidden" name="hidden_id" value="{{$value->id}}">
                                            <button type="submit" class="btn-action btn-delete" title="Delete">
                                                <i class="fe-trash-2" style="font-size: 16px;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div> </div> </div> </div>
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
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/js/pages/datatables.init.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        
        // Success/Error Toast Notification
        var Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        @if(Session::has('success'))
            Toast.fire({ icon: 'success', title: "{{ Session::get('success') }}" });
        @endif
        @if(Session::has('error'))
            Toast.fire({ icon: 'error', title: "{{ Session::get('error') }}" });
        @endif

        // Delete Confirmation
        $('.delete-form').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection