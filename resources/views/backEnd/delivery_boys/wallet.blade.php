@extends('backEnd.layouts.master')
@section('title','Rider wallet')
@section('content')
<div class="container-fluid">
    <div class="mb-3 d-flex justify-content-between">
        <div>
            <h4 class="m-0">{{ $boy->name }} — Wallet ৳{{ number_format($boy->wallet_balance,2) }}</h4>
            <small class="text-muted">Panel: /delivery/login — Phone: {{ $boy->phone }}</small>
        </div>
        <a href="{{ route('admin.delivery-boys.index') }}" class="btn btn-light btn-sm">Back</a>
    </div>
    <div class="row">
        <div class="col-md-5">
            <div class="card mb-3">
                <div class="card-header">Pay salary (credits wallet)</div>
                <div class="card-body">
                    <form method="post" action="{{ route('admin.delivery-boys.salary') }}">
                        @csrf
                        <input type="hidden" name="delivery_boy_id" value="{{ $boy->id }}">
                        <div class="mb-2"><label class="form-label">Amount (৳)</label><input type="number" name="amount" class="form-control" min="1" step="0.01" required></div>
                        <div class="mb-2"><label class="form-label">Month (YYYY-MM)</label><input type="text" name="salary_month" class="form-control" value="{{ date('Y-m') }}" pattern="\d{4}-\d{2}" required></div>
                        <div class="mb-2"><label class="form-label">Note</label><input type="text" name="note" class="form-control"></div>
                        <button class="btn btn-success btn-sm">Credit salary</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">Transactions</div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Date</th><th>Type</th><th>±</th><th>Balance</th><th>Note</th></tr></thead>
                        <tbody>
                        @foreach($tx as $t)
                            <tr>
                                <td><small>{{ $t->created_at }}</small></td>
                                <td>{{ $t->type }}</td>
                                <td>{{ $t->direction }} ৳{{ number_format($t->amount,2) }}</td>
                                <td>৳{{ number_format($t->balance_after,2) }}</td>
                                <td><small>{{ \Illuminate\Support\Str::limit($t->note,40) }}</small></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-body">{{ $tx->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
