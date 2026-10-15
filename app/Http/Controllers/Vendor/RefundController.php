<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Models\Order;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RefundController extends Controller
{
    /**
     * Display vendor refunds
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $vendorId = $user->vendor_id;

        if (!$vendorId) {
            return redirect()->route('vendor.dashboard')
                ->with('error', 'Vendor profile not found.');
        }

        $query = Refund::where('vendor_id', $vendorId)
            ->with(['order', 'customer']);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by order invoice
        if ($request->has('order_invoice') && $request->order_invoice != '') {
            $query->whereHas('order', function($q) use ($request) {
                $q->where('invoice_id', 'like', '%' . $request->order_invoice . '%');
            });
        }

        $refunds = $query->latest()->paginate(15);
        $statuses = ['pending', 'approved', 'rejected', 'processed'];
        $vendor = Vendor::findOrFail($vendorId);

        return view('vendor.refunds.index', compact('refunds', 'statuses', 'vendor'));
    }

    /**
     * Show refund details
     */
    public function show($id)
    {
        $user = Auth::user();
        $vendorId = $user->vendor_id;

        if (!$vendorId) {
            return redirect()->route('vendor.dashboard')
                ->with('error', 'Vendor profile not found.');
        }

        $refund = Refund::where('vendor_id', $vendorId)
            ->with(['order.orderdetails.product', 'customer'])
            ->findOrFail($id);
        $vendor = Vendor::findOrFail($vendorId);

        return view('vendor.refunds.show', compact('refund', 'vendor'));
    }
}
