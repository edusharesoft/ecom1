@extends('backEnd.layouts.master')
@section('title','Purchase Invoice')

@section('content')
<div class="container-fluid">

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h4 class="mb-0">Purchase Invoice #{{ $purchase->invoice_no }}</h4>
            <a href="javascript:window.print()" class="btn btn-sm btn-primary">Print</a>
        </div>
        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-6">
                    <h5>Supplier:</h5>
                    <p class="mb-0"><strong>{{ optional($purchase->supplier)->name }}</strong></p>
                    <p class="mb-0">{{ optional($purchase->supplier)->phone }}</p>
                    <p class="mb-0">{{ optional($purchase->supplier)->address }}</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-0"><strong>Date:</strong> {{ $purchase->purchase_date }}</p>
                    <p class="mb-0"><strong>Invoice:</strong> {{ $purchase->invoice_no }}</p>
                </div>
            </div>

            <table class="table table-bordered">
                <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th class="text-end">Qty</th>
                    <th class="text-end">Unit Cost</th>
                    <th class="text-end">Line Total</th>
                </tr>
                </thead>
                <tbody>
                @foreach($purchase->items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ optional($item->product)->name }}</td>
                        <td class="text-end">{{ $item->qty }}</td>
                        <td class="text-end">{{ number_format($item->unit_cost,2) }}</td>
                        <td class="text-end">{{ number_format($item->line_total,2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="row justify-content-end">
                <div class="col-md-4">
                    <table class="table table-sm">
                        <tr>
                            <th>Subtotal</th>
                            <td class="text-end">{{ number_format($purchase->subtotal,2) }} ৳</td>
                        </tr>
                        <tr>
                            <th>Discount</th>
                            <td class="text-end">{{ number_format($purchase->discount,2) }} ৳</td>
                        </tr>
                        <tr>
                            <th>Shipping</th>
                            <td class="text-end">{{ number_format($purchase->shipping_cost,2) }} ৳</td>
                        </tr>
                        <tr>
                            <th>Grand Total</th>
                            <td class="text-end"><strong>{{ number_format($purchase->grand_total,2) }} ৳</strong></td>
                        </tr>
                        <tr>
                            <th>Paid</th>
                            <td class="text-end text-success">{{ number_format($purchase->paid_amount,2) }} ৳</td>
                        </tr>
                        <tr>
                            <th>Due</th>
                            <td class="text-end text-danger">{{ number_format($purchase->due_amount,2) }} ৳</td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
