@extends('backEnd.layouts.master')
@section('title','Edit Fund Transaction')

@section('content')
<div class="container-fluid">

    {{-- PAGE HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">
                <i data-feather="edit-3" class="me-1"></i>
                Edit Fund Transaction / ফান্ড ট্রানজ্যাকশন এডিট
            </h4>
            <small class="text-muted">
                এখানে তুমি ফান্ড ট্রানজ্যাকশনের তথ্য আপডেট করতে পারো। (শুধুমাত্র Admin)
            </small>
        </div>

        <div>
            <a href="{{ route('admin.fund.index') }}" class="btn btn-sm btn-outline-secondary">
                <i data-feather="arrow-left" class="me-1"></i> Back to List
            </a>
        </div>
    </div>

    {{-- EDIT FORM --}}
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-header border-0 bg-light" style="border-radius: 12px 12px 0 0;">
                    <strong>
                        <i data-feather="file-text" class="me-1" style="width:16px;height:16px;"></i>
                        Edit Fund Transaction
                    </strong>
                </div>
                <div class="card-body">

                    <form action="{{ route('admin.fund.update', $transaction->id) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Direction / ধরণ *</label>
                            <select name="direction" class="form-select @error('direction') is-invalid @enderror" required>
                                <option value="in" {{ old('direction', $transaction->direction) == 'in' ? 'selected' : '' }}>IN (+)</option>
                                <option value="out" {{ old('direction', $transaction->direction) == 'out' ? 'selected' : '' }}>OUT (-)</option>
                            </select>
                            @error('direction')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Source</label>
                            <input type="text" 
                                   name="source" 
                                   class="form-control" 
                                   value="{{ old('source', $transaction->source) }}" 
                                   readonly>
                            <small class="text-muted">Source cannot be changed</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Amount (৳) *</label>
                            <input type="number" 
                                   step="0.01" 
                                   name="amount" 
                                   class="form-control @error('amount') is-invalid @enderror" 
                                   value="{{ old('amount', $transaction->amount) }}" 
                                   placeholder="0.00" 
                                   required>
                            @error('amount')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Note (optional)</label>
                            <textarea name="note" 
                                      class="form-control" 
                                      rows="3" 
                                      placeholder="এই ট্রানজ্যাকশন সম্পর্কে বাড়তি নোট...">{{ old('note', $transaction->note) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">
                                Created: {{ $transaction->created_at->format('d M Y, h:i A') }}<br>
                                @if($transaction->updated_by)
                                Last Updated: {{ $transaction->updated_at->format('d M Y, h:i A') }}
                                @endif
                            </small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="save" class="me-1" style="width:16px;height:16px;"></i>
                                Update Transaction
                            </button>

                            <a href="{{ route('admin.fund.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

</div>
@endsection
