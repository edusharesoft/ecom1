@extends('backEnd.layouts.master')
@section('title', 'জেলা এডিট')
@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">জেলা এডিট</h5>
            <form action="{{ route('admin.delivery.districts.update') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $edit_data->id }}">
                <input type="hidden" name="division_id" value="{{ $edit_data->division_id }}">
                <div class="mb-3"><label class="form-label">বিভাগ</label><input type="text" class="form-control" value="{{ $edit_data->division->name ?? '' }}" disabled></div>
                <div class="mb-3"><label class="form-label">জেলার নাম *</label><input type="text" name="name" value="{{ old('name', $edit_data->name) }}" class="form-control" required maxlength="190"></div>
                <div class="mb-3"><label class="form-label">ডেলিভারি চার্জ (৳) *</label><input type="number" name="delivery_charge" value="{{ old('delivery_charge', $edit_data->delivery_charge) }}" class="form-control" required min="0"></div>
                <div class="mb-3"><label class="form-label">সাজানো</label><input type="number" name="sort_order" value="{{ old('sort_order', $edit_data->sort_order) }}" class="form-control"></div>
                <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="status" value="1" id="st" {{ $edit_data->status ? 'checked' : '' }}><label class="form-check-label" for="st">সক্রিয়</label></div>
                <button type="submit" class="btn btn-success">আপডেট</button>
                <a href="{{ route('admin.delivery.districts.index', $edit_data->division_id) }}" class="btn btn-light">ফিরে যান</a>
            </form>
        </div>
    </div>
</div>
@endsection
