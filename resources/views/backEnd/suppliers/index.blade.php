@extends('backEnd.layouts.master')
@section('title', isset($supplier) ? 'Edit Supplier' : 'Suppliers Management')

@section('css')
<style>
    /* --- Form & Card Styles --- */
    .card-modern {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -1px rgba(0,0,0,0.02);
        background: #fff;
        transition: all 0.3s ease;
    }
    .card-header-modern {
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        padding: 1.25rem 1.5rem;
        border-radius: 12px 12px 0 0 !important;
        font-weight: 700;
        color: #1e293b;
    }
    
    .form-control-modern {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.65rem 1rem;
        font-size: 0.9rem;
        color: #334155;
    }
    .form-control-modern:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    .form-label-modern {
        font-size: 0.8rem;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 0.4rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* --- Table Styles --- */
    .table-modern th {
        background-color: #f8fafc;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        padding: 1rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .table-modern td {
        padding: 1rem;
        vertical-align: middle;
        font-size: 0.9rem;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-modern tr:last-child td { border-bottom: none; }
    .table-modern tr:hover td { background-color: #f8fafc; }

    /* --- Action Buttons --- */
    .btn-icon {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 8px; transition: all 0.2s;
    }
    .btn-icon:hover { transform: translateY(-2px); }
    .btn-edit { background: #e0e7ff; color: #4338ca; }
    .btn-delete { background: #fee2e2; color: #991b1b; }
    
    .due-amount { font-weight: 600; color: #ef4444; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    {{-- PAGE HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark">
                <i data-feather="users" class="text-primary me-2"></i> Supplier Management
            </h4>
            <p class="text-muted small mb-0">Manage your supplier list and track dues.</p>
        </div>
        @if(isset($supplier))
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-white border shadow-sm rounded-pill px-3">
                <i data-feather="plus" class="me-1"></i> Add New Supplier
            </a>
        @endif
    </div>

    <div class="row g-4">

        {{-- LEFT COLUMN: FORM --}}
        <div class="col-lg-4">
            <div class="card card-modern h-100">
                <div class="card-header-modern">
                    <i data-feather="{{ isset($supplier) ? 'edit-2' : 'plus-circle' }}" class="me-2" style="width:18px;"></i>
                    {{ isset($supplier) ? 'Edit Supplier' : 'Add New Supplier' }}
                </div>
                <div class="card-body p-4">
                    <form action="{{ isset($supplier) ? route('admin.suppliers.update', $supplier->id) : route('admin.suppliers.store') }}" method="POST">
                        @csrf
                        @if(isset($supplier)) @method('PUT') @endif

                        <div class="mb-3">
                            <label class="form-label-modern">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-modern @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $supplier->name ?? '') }}" placeholder="e.g. John Doe" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label-modern">Phone Number</label>
                            <input type="text" name="phone" class="form-control form-control-modern" 
                                   value="{{ old('phone', $supplier->phone ?? '') }}" placeholder="e.g. 017xxxxxxxx">
                        </div>

                        <div class="mb-3">
                            <label class="form-label-modern">Email Address</label>
                            <input type="email" name="email" class="form-control form-control-modern" 
                                   value="{{ old('email', $supplier->email ?? '') }}" placeholder="supplier@example.com">
                        </div>

                        <div class="mb-4">
                            <label class="form-label-modern">Address</label>
                            <textarea name="address" class="form-control form-control-modern" rows="3" 
                                      placeholder="Full address here...">{{ old('address', $supplier->address ?? '') }}</textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-2 fw-bold">
                                {{ isset($supplier) ? 'Update Supplier' : 'Save Supplier' }}
                            </button>
                            @if(isset($supplier))
                                <a href="{{ route('admin.suppliers.index') }}" class="btn btn-light py-2">Cancel Edit</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: TABLE --}}
        <div class="col-lg-8">
            <div class="card card-modern h-100">
                <div class="card-header-modern d-flex justify-content-between align-items-center">
                    <span>Registered Suppliers</span>
                    <span class="badge bg-light text-dark border">{{ $suppliers->total() }} Found</span>
                </div>
                
                <div class="card-body p-0">
                    <div id="supplier-table-wrapper" class="table-responsive">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="25%">Supplier Info</th>
                                    <th width="20%">Contact</th>
                                    <th width="20%">Address</th>
                                    <th width="15%">Due Amount</th>
                                    <th width="15%" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($suppliers as $s)
                                    <tr>
                                        <td>{{ $loop->iteration + ($suppliers->currentPage()-1)*$suppliers->perPage() }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $s->name }}</div>
                                            <small class="text-muted">{{ $s->phone }}</small>
                                        </td>
                                        <td>
                                            @if($s->email)
                                                <div class="d-flex align-items-center text-muted small">
                                                    <i data-feather="mail" class="me-1" style="width:12px;"></i> {{ $s->email }}
                                                </div>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-muted small">{{ Str::limit($s->address, 30) ?? '-' }}</span>
                                        </td>
                                        <td>
                                            @if($s->current_due > 0)
                                                <span class="due-amount">{{ number_format($s->current_due, 2) }} ৳</span>
                                            @else
                                                <span class="badge bg-light text-success border border-success">Paid</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.suppliers.edit', $s->id) }}" class="btn-icon btn-edit me-1" title="Edit">
                                                <i data-feather="edit-2" style="width:16px;"></i>
                                            </a>
                                            
                                            <form action="{{ route('admin.suppliers.destroy', $s->id) }}" method="POST" class="d-inline" 
                                                  onsubmit="return confirm('Are you sure? This will delete all history related to this supplier.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-icon btn-delete" title="Delete">
                                                    <i data-feather="trash-2" style="width:16px;"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="60" class="mb-3 opacity-25">
                                            <p class="text-muted fw-bold mb-0">No Suppliers Found</p>
                                            <small class="text-muted">Add a new supplier from the left form.</small>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div id="supplier-pagination" class="p-3 border-top d-flex justify-content-end">
                            {{ $suppliers->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    // AJAX Pagination Script
    $(document).on('click', '#supplier-pagination a', function(e){
        e.preventDefault();
        let url = $(this).attr('href');
        $('#supplier-table-wrapper').css('opacity', '0.5'); // Loading effect
        
        $.get(url, function(response){
            let html = $(response).find('#supplier-table-wrapper').html();
            $('#supplier-table-wrapper').html(html);
            $('#supplier-table-wrapper').css('opacity', '1');
            feather.replace(); // Re-init icons
        });
    });
</script>
@endpush