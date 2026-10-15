@extends('frontEnd.layouts.master')
@section('title','Request Refund')

@section('content')
<section class="customer-section">
    <div class="container">
        <div class="row">

            <div class="col-sm-3">
                <div class="customer-sidebar">
                    @include('frontEnd.layouts.customer.sidebar')
                </div>
            </div>

            <div class="col-sm-9">
                <div class="customer-content">
                   <h5 class="account-title" style="color:#000;">Request Refund</h5>

                   <div class="card">
                       <div class="card-header bg-primary text-white">
                           <h6 class="mb-0">Order Information</h6>
                       </div>
                       <div class="card-body">
                           <div class="row">
                               <div class="col-md-6">
                                   <p><strong>Order Invoice:</strong> #{{ $order->invoice_id }}</p>
                                   <p><strong>Order Date:</strong> {{ $order->created_at->format('d-m-Y h:i A') }}</p>
                                   <p><strong>Order Status:</strong> 
                                       <span class="badge bg-secondary">{{ $order->status ? $order->status->name : 'Pending' }}</span>
                                   </p>
                               </div>
                               <div class="col-md-6">
                                   <p><strong>Total Amount:</strong> ৳{{ number_format($order->amount, 2) }}</p>
                                   <p><strong>Shipping Charge:</strong> ৳{{ number_format($order->shipping_charge, 2) }}</p>
                                   <p><strong>Grand Total:</strong> 
                                       <strong class="text-primary">৳{{ number_format($order->amount + $order->shipping_charge, 2) }}</strong>
                                   </p>
                               </div>
                           </div>

                           <div class="mt-3">
                               <h6>Order Items:</h6>
                               <table class="table table-sm table-bordered">
                                   <thead>
                                       <tr>
                                           <th>Product</th>
                                           <th>Qty</th>
                                           <th>Price</th>
                                       </tr>
                                   </thead>
                                   <tbody>
                                       @foreach($order->orderdetails as $item)
                                           <tr>
                                               <td>{{ $item->product_name }}</td>
                                               <td>{{ $item->qty }}</td>
                                               <td>৳{{ number_format($item->sale_price * $item->qty, 2) }}</td>
                                           </tr>
                                       @endforeach
                                   </tbody>
                               </table>
                           </div>
                       </div>
                   </div>

                   <form action="{{ route('customer.refunds.store') }}" method="POST" class="mt-4">
                       @csrf
                       <input type="hidden" name="order_id" value="{{ $order->id }}">

                       <div class="card">
                           <div class="card-header bg-warning text-dark">
                               <h6 class="mb-0">Refund Details</h6>
                           </div>
                           <div class="card-body">
                               <div class="row">
                                   <div class="col-md-6 mb-3">
                                       <label for="amount" class="form-label">Refund Amount <span class="text-danger">*</span></label>
                                       <input type="number" 
                                              class="form-control @error('amount') is-invalid @enderror" 
                                              id="amount" 
                                              name="amount" 
                                              value="{{ old('amount', $order->amount) }}" 
                                              min="1" 
                                              max="{{ $order->amount }}" 
                                              step="0.01" 
                                              required>
                                       <small class="text-muted">Maximum: ৳{{ number_format($order->amount, 2) }}</small>
                                       @error('amount')
                                           <div class="invalid-feedback">{{ $message }}</div>
                                       @enderror
                                   </div>

                                   <div class="col-md-6 mb-3">
                                       <label for="shipping_charge" class="form-label">Shipping Charge Refund</label>
                                       <input type="number" 
                                              class="form-control @error('shipping_charge') is-invalid @enderror" 
                                              id="shipping_charge" 
                                              name="shipping_charge" 
                                              value="{{ old('shipping_charge', $order->shipping_charge) }}" 
                                              min="0" 
                                              max="{{ $order->shipping_charge }}" 
                                              step="0.01">
                                       <small class="text-muted">Maximum: ৳{{ number_format($order->shipping_charge, 2) }}</small>
                                       @error('shipping_charge')
                                           <div class="invalid-feedback">{{ $message }}</div>
                                       @enderror
                                   </div>
                               </div>

                               <div class="mb-3">
                                   <label for="reason" class="form-label">Reason for Refund <span class="text-danger">*</span></label>
                                   <textarea class="form-control @error('reason') is-invalid @enderror" 
                                             id="reason" 
                                             name="reason" 
                                             rows="4" 
                                             required 
                                             placeholder="Please explain why you want a refund...">{{ old('reason') }}</textarea>
                                   @error('reason')
                                       <div class="invalid-feedback">{{ $message }}</div>
                                   @enderror
                               </div>

                               <div class="mb-3">
                                   <label for="refund_method" class="form-label">Refund Method <span class="text-danger">*</span></label>
                                   <select class="form-control @error('refund_method') is-invalid @enderror" 
                                           id="refund_method" 
                                           name="refund_method" 
                                           required>
                                       <option value="">Select Method</option>
                                       <option value="original_payment" {{ old('refund_method') == 'original_payment' ? 'selected' : '' }}>Original Payment Method</option>
                                       <option value="bkash" {{ old('refund_method') == 'bkash' ? 'selected' : '' }}>bKash</option>
                                       <option value="nagad" {{ old('refund_method') == 'nagad' ? 'selected' : '' }}>Nagad</option>
                                       <option value="bank" {{ old('refund_method') == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                                       <option value="manual" {{ old('refund_method') == 'manual' ? 'selected' : '' }}>Manual/Cash</option>
                                   </select>
                                   @error('refund_method')
                                       <div class="invalid-feedback">{{ $message }}</div>
                                   @enderror
                               </div>

                               <div class="mb-3">
                                   <label for="refund_account" class="form-label">Account Number/Phone <span class="text-danger">*</span></label>
                                   <input type="text" 
                                          class="form-control @error('refund_account') is-invalid @enderror" 
                                          id="refund_account" 
                                          name="refund_account" 
                                          value="{{ old('refund_account') }}" 
                                          required 
                                          placeholder="Enter bKash/Nagad number or Bank account number">
                                   @error('refund_account')
                                       <div class="invalid-feedback">{{ $message }}</div>
                                   @enderror
                               </div>

                               <div class="mb-3">
                                   <label for="refund_account_name" class="form-label">Account Holder Name</label>
                                   <input type="text" 
                                          class="form-control @error('refund_account_name') is-invalid @enderror" 
                                          id="refund_account_name" 
                                          name="refund_account_name" 
                                          value="{{ old('refund_account_name') }}" 
                                          placeholder="Enter account holder name (if applicable)">
                                   @error('refund_account_name')
                                       <div class="invalid-feedback">{{ $message }}</div>
                                   @enderror
                               </div>

                               <div class="alert alert-info">
                                   <i class="fa fa-info-circle"></i> 
                                   <strong>Note:</strong> Your refund request will be reviewed by our admin team. You will be notified once the refund is processed.
                               </div>

                               <div class="mt-4">
                                   <button type="submit" class="btn btn-primary">
                                       <i class="fa fa-paper-plane"></i> Submit Refund Request
                                   </button>
                                   <a href="{{ route('customer.orders') }}" class="btn btn-secondary">
                                       <i class="fa fa-arrow-left"></i> Back to Orders
                                   </a>
                               </div>
                           </div>
                       </div>
                   </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
