@extends('backEnd.layouts.master')
@section('title','Roles Show')
@section('content')
<div class="container-fluid">
    
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{route('users.index')}}" class="btn btn-primary rounded-pill">Manage</a>
                </div>
                <h4 class="page-title">Roles Show</h4>
            </div>
        </div>
    </div>       
    <!-- end page title --> 
   <div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <h5><strong>Name:</strong>
                            {{ $role->name }}</h5>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Permissions:</strong>
                            @if(!empty($rolePermissions))
                                @foreach($rolePermissions as $v)
                                    <button class="btn btn-blue btn-xs rounded-pill">{{ $v->name }}</button>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->
   </div>
</div>
@endsection
