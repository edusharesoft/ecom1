<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class ResellerVerificationController extends Controller
{
    /**
     * Display all reseller verification requests
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'reseller')
            ->orWhereHas('roles', function($q) {
                $q->where('name', 'reseller');
            });

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
                $q->where('name', 'like', '%' . $request->keyword . '%')
                  ->orWhere('shop_name', 'like', '%' . $request->keyword . '%')
                  ->orWhere('email', 'like', '%' . $request->keyword . '%');
            });
        }

        $resellers = $query->latest()->paginate(15);

        return view('backEnd.reseller.verification.index', compact('resellers'));
    }

    /**
     * Show verification details for a specific reseller
     */
    public function show($id)
    {
        $reseller = User::findOrFail($id);
        
        // Verify it's a reseller
        if ($reseller->role !== 'reseller' && !$reseller->hasRole('reseller')) {
            Toastr::error('User is not a reseller', 'Error');
            return redirect()->route('admin.reseller.verification.index');
        }
        
        return view('backEnd.reseller.verification.show', compact('reseller'));
    }

    /**
     * Approve reseller verification
     */
    public function approve(Request $request, $id)
    {
        $reseller = User::findOrFail($id);
        
        // Verify it's a reseller
        if ($reseller->role !== 'reseller' && !$reseller->hasRole('reseller')) {
            Toastr::error('User is not a reseller', 'Error');
            return redirect()->back();
        }
        
        if ($reseller->verification_status == 'approved') {
            Toastr::warning('Reseller is already approved.', 'Warning');
            return redirect()->back();
        }

        $reseller->verification_status = 'approved';
        $reseller->verified_at = now();
        $reseller->verification_note = $request->admin_note ?? null;
        $reseller->save();

        Toastr::success('Reseller verification approved successfully.', 'Success');
        return redirect()->back();
    }

    /**
     * Reject reseller verification
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $reseller = User::findOrFail($id);
        
        // Verify it's a reseller
        if ($reseller->role !== 'reseller' && !$reseller->hasRole('reseller')) {
            Toastr::error('User is not a reseller', 'Error');
            return redirect()->back();
        }
        
        if ($reseller->verification_status == 'rejected') {
            Toastr::warning('Reseller is already rejected.', 'Warning');
            return redirect()->back();
        }

        $reseller->verification_status = 'rejected';
        $reseller->verification_note = $request->rejection_reason;
        $reseller->save();

        Toastr::success('Reseller verification rejected.', 'Success');
        return redirect()->back();
    }
}
