@extends('backEnd.layouts.master')
@section('title', 'উপজেলা')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <a href="{{ route('admin.delivery.districts.index', $district->division_id) }}" class="btn btn-sm btn-outline-secondary mb-1">← জেলা</a>
            <h4 class="fw-bold m-0">{{ $district->division->name ?? '' }} — {{ $district->name }}</h4>
        </div>
        <a href="{{ route('admin.delivery.upazilas.create', ['district' => $district->id]) }}" class="btn btn-primary"><i class="fe-plus"></i> নতুন উপজেলা</a>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead><tr><th>#</th><th>উপজেলা</th><th>স্ট্যাটাস</th><th class="text-end">অ্যাকশন</th></tr></thead>
                    <tbody>
                        @forelse($show_data as $value)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $value->name }}</td>
                            <td>{{ $value->status ? 'চালু' : 'বন্ধ' }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.delivery.upazilas.edit', $value->id) }}" class="btn btn-sm btn-primary">এডিট</a>
                                <form action="{{ route('admin.delivery.upazilas.destroy') }}" method="POST" class="d-inline delete-form-delivery">
                                    @csrf
                                    <input type="hidden" name="hidden_id" value="{{ $value->id }}">
                                    <button type="submit" class="btn btn-sm btn-danger">ডিলিট</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">কোনো উপজেলা নেই।</td></tr>
                        @endforelse
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
