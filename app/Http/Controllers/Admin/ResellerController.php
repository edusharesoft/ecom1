<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class ResellerController extends Controller
{
    /**
     * Display a listing of resellers.
     */
    public function index(Request $request)
    {
        $query = User::where(function ($q) {
            $q->where('role', 'reseller')
                ->orWhereHas('roles', function ($r) {
                    $r->where('name', 'reseller');
                });
        });

        if ($request->keyword) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->keyword . '%')
                    ->orWhere('shop_name', 'like', '%' . $request->keyword . '%')
                    ->orWhere('email', 'like', '%' . $request->keyword . '%');
            });
        }

        $resellers = $query->latest()->paginate(20)->withQueryString();

        $resellerScope = function ($q) {
            $q->where('role', 'reseller')
                ->orWhereHas('roles', function ($r) {
                    $r->where('name', 'reseller');
                });
        };

        $stats = [
            'total'    => User::where($resellerScope)->count(),
            'active'   => User::where($resellerScope)->where('status', 1)->count(),
            'verified' => User::where($resellerScope)->where('verification_status', 'approved')->count(),
            'pending'  => User::where($resellerScope)->where(function ($q) {
                $q->whereNull('verification_status')
                    ->orWhereNotIn('verification_status', ['approved', 'rejected']);
            })->count(),
        ];

        return view('backEnd.reseller.index', compact('resellers', 'stats'));
    }

    /**
     * Show the form for editing the specified reseller.
     */
    public function edit($id)
    {
        $reseller = User::findOrFail($id);
        
        // Verify it's a reseller
        if ($reseller->role !== 'reseller' && !$reseller->hasRole('reseller')) {
            Toastr::error('User is not a reseller', 'Error');
            return redirect()->route('admin.resellers.index');
        }
        
        return view('backEnd.reseller.edit', compact('reseller'));
    }

    /**
     * Update the specified reseller.
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->hidden_id,
            'shop_name' => 'nullable|string|max:255',
            'status' => 'required|in:0,1',
            'password' => 'nullable|min:6|confirmed',
        ]);

        $reseller = User::findOrFail($request->hidden_id);
        
        // Verify it's a reseller
        if ($reseller->role !== 'reseller' && !$reseller->hasRole('reseller')) {
            Toastr::error('User is not a reseller', 'Error');
            return redirect()->route('admin.resellers.index');
        }
        
        $input = $request->except('hidden_id', 'password', 'password_confirmation');
        
        // Update password if provided
        if ($request->password) {
            $input['password'] = Hash::make($request->password);
        } else {
            unset($input['password']);
        }
        
        $reseller->update($input);
        
        // Also update customer record if exists
        $customer = Customer::where('email', $reseller->email)->first();
        if ($customer) {
            $customer->name = $request->name;
            $customer->email = $request->email;
            if ($request->password) {
                $customer->password = bcrypt($request->password);
            }
            $customer->save();
        }

        Toastr::success('Reseller updated successfully', 'Success');
        return redirect()->route('admin.resellers.index');
    }

    /**
     * Remove the specified reseller.
     */
    public function destroy($id)
    {
        $reseller = User::findOrFail($id);
        
        // Verify it's a reseller
        if ($reseller->role !== 'reseller' && !$reseller->hasRole('reseller')) {
            Toastr::error('User is not a reseller', 'Error');
            return redirect()->route('admin.resellers.index');
        }
        
        // Delete verification documents
        if ($reseller->voter_id_front && File::exists($reseller->voter_id_front)) {
            File::delete($reseller->voter_id_front);
        }
        if ($reseller->voter_id_back && File::exists($reseller->voter_id_back)) {
            File::delete($reseller->voter_id_back);
        }
        if ($reseller->self_image && File::exists($reseller->self_image)) {
            File::delete($reseller->self_image);
        }
        
        // Delete associated customer record
        $customer = Customer::where('email', $reseller->email)->first();
        if ($customer) {
            $customer->delete();
        }
        
        // Delete reseller
        $reseller->delete();

        Toastr::success('Reseller deleted successfully', 'Success');
        return redirect()->route('admin.resellers.index');
    }

    /**
     * Toggle reseller status.
     */
    public function toggleStatus($id)
    {
        $reseller = User::findOrFail($id);
        
        // Verify it's a reseller
        if ($reseller->role !== 'reseller' && !$reseller->hasRole('reseller')) {
            Toastr::error('User is not a reseller', 'Error');
            return redirect()->back();
        }
        
        $reseller->status = $reseller->status == 1 ? 0 : 1;
        $reseller->save();

        Toastr::success('Reseller status updated successfully', 'Success');
        return redirect()->back();
    }
}
