<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\VendorWallet;
use App\Models\VendorWalletTransaction;
use App\Models\VendorWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function index()
    {
        $vendorId = Auth::user()->vendor_id;
        $vendor = \App\Models\Vendor::findOrFail($vendorId);
        $wallet = VendorWallet::firstOrCreate(['vendor_id' => $vendorId]);
        $withdrawals = VendorWithdrawal::where('vendor_id', $vendorId)
            ->latest()
            ->paginate(10);

        return view('vendor.withdrawals.index', compact('vendor', 'wallet', 'withdrawals'));
    }

    public function store(Request $request)
    {
        $vendorId = Auth::user()->vendor_id;
        $wallet = VendorWallet::firstOrCreate(['vendor_id' => $vendorId]);

        $min = config('app.vendor_min_withdraw', 100);
        $request->validate([
            'amount' => 'required|numeric|min:' . $min,
            'payout_method' => 'required|string|max:50',
            'account_name' => 'nullable|string|max:191',
            'account_number' => 'nullable|string|max:191',
            'note' => 'nullable|string|max:500',
        ]);

        $amount = round((float) $request->amount, 2);

        if ($amount > $wallet->balance) {
            return back()->with('error', 'You do not have enough balance.');
        }

        DB::transaction(function () use ($wallet, $vendorId, $amount, $request) {
            // Hold balance immediately
            $wallet->balance -= $amount;
            $wallet->save();

            $withdrawal = VendorWithdrawal::create([
                'vendor_id' => $vendorId,
                'amount' => $amount,
                'charge' => 0,
                'payout_method' => $request->payout_method,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'note' => $request->note,
                'status' => 'pending',
            ]);

            VendorWalletTransaction::create([
                'vendor_id' => $vendorId,
                'type' => 'withdraw',
                'status' => 'pending',
                'amount' => $amount,
                'source_type' => 'withdraw',
                'source_id' => $withdrawal->id,
                'note' => 'Withdraw request pending approval',
            ]);
        });

        return back()->with('success', 'Withdraw request submitted successfully.');
    }
}
