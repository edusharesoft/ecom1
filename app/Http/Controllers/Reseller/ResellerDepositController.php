<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\ResellerDeposit;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;

class ResellerDepositController extends Controller
{
    /**
     * Show deposit form / initiate deposit
     */
    public function index()
    {
        $user = Auth::guard('admin')->user();
        if (!$user || ($user->role !== 'reseller' && !$user->hasRole('reseller'))) {
            Toastr::error('Access denied', 'Error');
            return redirect()->route('reseller.dashboard');
        }

        $deposits = ResellerDeposit::where('user_id', $user->id)
            ->where('status', 'completed')
            ->latest()
            ->limit(20)
            ->get();

        $setting = GeneralSetting::where('status', 1)->first();
        $depositMin = (float) ($setting->reseller_deposit_min ?? 100);
        $depositMax = (float) ($setting->reseller_deposit_max ?? 1000000);

        return view('reseller.deposit', compact('user', 'deposits', 'depositMin', 'depositMax'));
    }

    /**
     * Initiate deposit - redirect to UddoktaPay
     */
    public function store(Request $request)
    {
        $user = Auth::guard('admin')->user();
        if (!$user || ($user->role !== 'reseller' && !$user->hasRole('reseller'))) {
            Toastr::error('Access denied', 'Error');
            return redirect()->route('reseller.dashboard');
        }

        $setting = GeneralSetting::where('status', 1)->first();
        $depositMin = (float) ($setting->reseller_deposit_min ?? 100);
        $depositMax = (float) ($setting->reseller_deposit_max ?? 1000000);

        $request->validate([
            'amount' => "required|numeric|min:{$depositMin}|max:{$depositMax}",
        ], [
            'amount.min' => "ন্যূনতম ৳" . number_format($depositMin, 0) . " ডিপোজিট করতে হবে।",
            'amount.max' => "সর্বোচ্চ ৳" . number_format($depositMax, 0) . " ডিপোজিট করা যাবে।",
        ]);

        $deposit = ResellerDeposit::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'payment_gateway' => 'uddoktapay',
            'status' => 'pending',
        ]);

        return redirect()->route('uddoktapay.deposit.checkout', $deposit->id);
    }
}
