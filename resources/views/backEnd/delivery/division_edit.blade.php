@extends('backEnd.layouts.master')
@section('title', 'বিভাগ এডিট')
@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">বিভাগ এডিট</h5>
            <form action="{{ route('admin.delivery.divisions.update') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $edit_data->id }}">
                <div class="mb-3"><label class="form-label">নাম *</label><input type="text" name="name" value="{{ old('name', $edit_data->name) }}" class="form-control" required maxlength="190"></div>
                <div class="mb-3"><label class="form-label">সাজানো</label><input type="number" name="sort_order" value="{{ old('sort_order', $edit_data->sort_order) }}" class="form-control"></div>
                <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="status" value="1" id="st" {{ $edit_data->status ? 'checked' : '' }}><label class="form-check-label" for="st">সক্রিয়</label></div>
                <button type="submit" class="btn btn-success">আপডেট</button>
                <a href="{{ route('admin.delivery.divisions.index') }}" class="btn btn-light">ফিরে যান</a>
            </form>
        </div>
    </div>
</div>
@endsection
