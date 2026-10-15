@extends('backEnd.layouts.master')
@section('title','Add delivery person')
@section('content')
<div class="container-fluid" style="max-width:720px;">
    <h4 class="mb-3">Add delivery person</h4>
    <div class="card card-body">
        <form action="{{ route('admin.delivery-boys.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="mb-3"><label class="form-label">Name *</label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
            <div class="mb-3"><label class="form-label">Phone * (login)</label><input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required></div>
            <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email') }}"></div>
            <div class="mb-3"><label class="form-label">Password *</label><input type="password" name="password" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Commission per delivery (৳) *</label><input type="number" name="commission_per_delivery" class="form-control" value="{{ old('commission_per_delivery', 80) }}" min="0" step="0.01" required></div>
            <div class="mb-3"><label class="form-label">Reference monthly salary (৳)</label><input type="number" name="monthly_salary_amount" class="form-control" value="{{ old('monthly_salary_amount', 0) }}" min="0" step="0.01"></div>
            <div class="mb-3"><label class="form-label">Status</label><select name="status" class="form-control"><option value="1" selected>Active</option><option value="0">Inactive</option></select></div>
            <div class="mb-3"><label class="form-label">Photo</label><input type="file" name="image" class="form-control" accept="image/*"></div>
            <button class="btn btn-primary">Save</button>
            <a href="{{ route('admin.delivery-boys.index') }}" class="btn btn-light">Cancel</a>
        </form>
    </div>
</div>
@endsection
