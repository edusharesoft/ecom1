<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;

class RefundController extends Controller
{
    /**
     * Display customer's refund requests
     */
    public function index()
    {
        $customer = Auth::guard('customer')->user();
        
        $refunds = Refund::where('customer_id', $customer->id)
            ->with(['order'])
            ->latest()
            ->paginate(10);
        
        return view('frontEnd.layouts.customer.refunds', compact('refunds'));
    }

    /**
     * Show refund request form
     */
    public function create($order_id)
    {
        $customer = Auth::guard('customer')->user();
        
        $order = Order::where('id', $order_id)
            ->where('customer_id', $customer->id)
            ->with(['orderdetails.product', 'payment'])
            ->firstOrFail();

        // Check if order is eligible for refund
        if ($order->order_status == 11) { // Already cancelled
            Toastr::warning('This order is already cancelled.', 'Warning');
            return redirect()->route('customer.orders');
        }

        // Check if refund already exists
        $existingRefund = Refund::where('order_id', $order->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingRefund) {
            Toastr::info('You already have a pending refund request for this order.', 'Info');
            return redirect()->route('customer.refunds.show', $existingRefund->id);
        }

        return view('frontEnd.layouts.customer.refund_request', compact('order'));
    }

    /**
     * Store refund request
     */
    public function store(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:1',
            'shipping_charge' => 'nullable|numeric|min:0',
            'reason' => 'required|string|max:1000',
            'refund_method' => 'required|in:original_payment,bkash,nagad,bank,manual',
            'refund_account' => 'required|string|max:255',
            'refund_account_name' => 'nullable|string|max:255',
        ]);

        $order = Order::where('id', $request->order_id)
            ->where('customer_id', $customer->id)
            ->firstOrFail();

        // Check if order is eligible for refund
        if ($order->order_status == 11) {
            Toastr::error('This order is already cancelled.', 'Error');
            return back();
        }

        // Check if refund already exists
        $existingRefund = Refund::where('order_id', $order->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingRefund) {
            Toastr::error('You already have a pending refund request for this order.', 'Error');
            return back();
        }

        // Validate refund amount (should not exceed order amount)
        $maxRefundAmount = $order->amount + $order->shipping_charge;
        $requestedAmount = $request->amount + ($request->shipping_charge ?? 0);
        
        if ($requestedAmount > $maxRefundAmount) {
            Toastr::error('Refund amount cannot exceed order total amount.', 'Error');
            return back();
        }

        // Check if order contains vendor products
        $orderDetails = \App\Models\OrderDetails::where('order_id', $order->id)
            ->whereNotNull('vendor_id')
            ->first();
        
        $vendorId = $orderDetails ? $orderDetails->vendor_id : null;

        $refund = Refund::create([
            'order_id' => $order->id,
            'customer_id' => $customer->id,
            'vendor_id' => $vendorId,
            'refund_id' => Refund::generateRefundId(),
            'amount' => $request->amount,
            'shipping_charge' => $request->shipping_charge ?? 0,
            'reason' => $request->reason,
            'status' => 'pending',
            'refund_method' => $request->refund_method,
            'refund_account' => $request->refund_account,
            'refund_account_name' => $request->refund_account_name,
        ]);

        Toastr::success('Refund request submitted successfully. Refund ID: ' . $refund->refund_id, 'Success');
        return redirect()->route('customer.refunds.show', $refund->id);
    }

    /**
     * Show refund details
     */
    public function show($id)
    {
        $customer = Auth::guard('customer')->user();
        
        $refund = Refund::where('id', $id)
            ->where('customer_id', $customer->id)
            ->with(['order.orderdetails.product', 'processedBy'])
            ->firstOrFail();
        
        return view('frontEnd.layouts.customer.refund_details', compact('refund'));
    }

    /**
     * Cancel refund request (only if pending)
     */
    public function cancel($id)
    {
        $customer = Auth::guard('customer')->user();
        
        $refund = Refund::where('id', $id)
            ->where('customer_id', $customer->id)
            ->firstOrFail();

        if ($refund->status !== 'pending') {
            Toastr::error('Only pending refund requests can be cancelled.', 'Error');
            return back();
        }

        $refund->delete();
        Toastr::success('Refund request cancelled successfully.', 'Success');
        return redirect()->route('customer.refunds');
    }
}
