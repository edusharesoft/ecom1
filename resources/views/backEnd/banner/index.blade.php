@extends('backEnd.layouts.master')
@section('title','Banner Management')

@section('css')
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />

<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<style>
    /* Custom CSS for Professional Look */
    .table thead th {
        background-color: #f8f9fa;
        color: #555;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #eef2f7;
    }
    .table tbody td {
        vertical-align: middle;
        color: #444;
        font-size: 14px;
    }
    .banner-thumb {
        width: 80px;
        height: 45px;
        object-fit: cover;
        border-radius: 6px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }
    .banner-thumb:hover {
        transform: scale(1.1);
    }
    .status-badge {
        padding: 5px 12px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 600;
    }
    .action-btn {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin: 0 2px;
        transition: all 0.3s ease;
        border: none;
        background: transparent;
    }
    .action-btn:hover {
        background-color: #eef2f7;
        transform: translateY(-2px);
    }
    .card-custom {
        border: none;
        box-shadow: 0 0 35px 0 rgba(154,161,171,.15);
        border-radius: 8px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    
    <div class="row mb-3 mt-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="page-title mb-0" style="font-weight: 700; color: #333;">Banner Management</h4>
            <a href="{{route('banners.create')}}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fe-plus me-1"></i> Create New
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-body">
                    <table id="datatable-buttons" class="table table-hover dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th width="5%">SL</th>
                                <th width="20%">Image</th>
                                <th width="25%">Category</th>
                                <th width="15%">Status</th>
                                <th width="15%" class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $key=>$value)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>
                                    <img src="{{asset($value->image)}}" class="banner-thumb" alt="Banner Image">
                                </td>
                                <td>
                                    <span class="fw-bold">{{$value->category ? $value->category->name : 'N/A'}}</span>
                                </td>
                                <td>
                                    @if($value->status==1)
                                        <span class="badge bg-soft-success text-success status-badge">
                                            <i class="mdi mdi-circle-medium me-1"></i>Active
                                        </span> 
                                    @else 
                                        <span class="badge bg-soft-danger text-danger status-badge">
                                            <i class="mdi mdi-circle-medium me-1"></i>Inactive
                                        </span> 
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end">
                                        <a href="{{route('banners.edit',$value->id)}}" class="action-btn text-primary" title="Edit">
                                            <i class="fe-edit-1" style="font-size: 18px;"></i>
                                        </a>

                                        <form method="post" action="{{route('banners.destroy')}}" class="delete-form">        
                                            @csrf
                                            <input type="hidden" value="{{$value->id}}" name="hidden_id">
                                            <button type="submit" class="action-btn text-danger" title="Delete">
                                                <i class="fe-trash-2" style="font-size: 18px;"></i>
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
        
        // 1. Toast Notification Logic (For Create/Edit Success)
        // কন্ট্রোলার থেকে অবশ্যই return redirect(...)->with('success', 'Message here'); পাঠাতে হবে
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
            Toast.fire({
                icon: 'success',
                title: "{{ Session::get('success') }}"
            });
        @endif

        @if(Session::has('error'))
            Toast.fire({
                icon: 'error',
                title: "{{ Session::get('error') }}"
            });
        @endif


        // 2. Delete Confirmation Logic
        $('.delete-form').on('submit', function(e) {
            e.preventDefault(); // ফর্ম সাবমিট বন্ধ করা
            var form = this;

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // ইউজার Yes দিলে ফর্ম সাবমিট হবে
                }
            });
        });

    });
</script>
@endsection