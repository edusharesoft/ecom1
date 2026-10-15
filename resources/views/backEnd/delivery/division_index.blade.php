@extends('backEnd.layouts.master')
@section('title', 'ডেলিভারি বিভাগ')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h4 class="fw-bold m-0">ডেলিভারি লোকেশন — বিভাগ</h4>
        <a href="{{ route('admin.delivery.divisions.create') }}" class="btn btn-primary"><i class="fe-plus"></i> নতুন বিভাগ</a>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead><tr><th>#</th><th>নাম</th><th>সাজানো</th><th>স্ট্যাটাস</th><th class="text-end">অ্যাকশন</th></tr></thead>
                    <tbody>
                        @foreach($show_data as $value)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $value->name }}</td>
                            <td>{{ $value->sort_order }}</td>
                            <td>{{ $value->status ? 'চালু' : 'বন্ধ' }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.delivery.districts.index', ['division' => $value->id]) }}" class="btn btn-sm btn-soft-info">জেলাসমূহ</a>
                                <a href="{{ route('admin.delivery.divisions.edit', $value->id) }}" class="btn btn-sm btn-primary">এডিট</a>
                                <form action="{{ route('admin.delivery.divisions.destroy') }}" method="POST" class="d-inline delete-form-delivery">
                                    @csrf
                                    <input type="hidden" name="hidden_id" value="{{ $value->id }}">
                                    <button type="submit" class="btn btn-sm btn-danger">ডিলিট</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.delete-form-delivery').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({title:'মুছে ফেলবেন?', icon:'warning', showCancelButton:true, confirmButtonText:'হ্যাঁ'})
            .then(r => { if (r.isConfirmed) form.submit(); });
    });
});
</script>
@endsection
