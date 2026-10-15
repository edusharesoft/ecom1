<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class VendorVerificationController extends Controller
{
    /**
     * Display all vendor verification requests
     */
    public function index(Request $request)
    {
        $query = Vendor::with('wallet');

        // Filter by status
        if ($request->status) {
            $query->where('verification_status', $request->status);
        } else {
            // Default: show pending first
            $query->orderByRaw("CASE WHEN verification_status = 'pending' THEN 1 WHEN verification_status = 'rejected' THEN 2 ELSE 3 END");
        }

        // Search
        if ($request->keyword) {
            $query->where(function($q) use ($request) {
                $q->where('shop_name', 'like', '%' . $request->keyword . '%')
                  ->orWhere('owner_name', 'like', '%' . $request->keyword . '%')
                  ->orWhere('email', 'like', '%' . $request->keyword . '%')
                  ->orWhere('phone', 'like', '%' . $request->keyword . '%');
            });
        }

        $vendors = $query->latest()->paginate(15);

        return view('backEnd.vendor.verification.index', compact('vendors'));
    }

    /**
     * Show verification details for a specific vendor
     */
    public function show($id)
    {
        $vendor = Vendor::with('wallet')->findOrFail($id);
        return view('backEnd.vendor.verification.show', compact('vendor'));
    }

    /**
     * Approve vendor verification
     */
    public function approve(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);
        
        if ($vendor->verification_status == 'approved') {
            Toastr::warning('Vendor is already approved.', 'Warning');
            return redirect()->back();
        }

        $vendor->verification_status = 'approved';
        $vendor->verified_at = now();
        $vendor->verification_note = $request->admin_note ?? null;
        $vendor->save();

        Toastr::success('Vendor verification approved successfully.', 'Success');
        return redirect()->back();
    }

    /**
     * Reject vendor verification
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $vendor = Vendor::findOrFail($id);
        
        if ($vendor->verification_status == 'rejected') {
            Toastr::warning('Vendor is already rejected.', 'Warning');
            return redirect()->back();
        }

        $vendor->verification_status = 'rejected';
        $vendor->verification_note = $request->rejection_reason;
        $vendor->save();

        Toastr::success('Vendor verification rejected.', 'Success');
        return redirect()->back();
    }
}
