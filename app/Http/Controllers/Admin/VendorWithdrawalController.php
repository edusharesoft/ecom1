<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FundTransaction;
use App\Models\VendorWallet;
use App\Models\VendorWalletTransaction;
use App\Models\VendorWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Toastr;

class VendorWithdrawalController extends Controller
{
    public function index()
    {
        $data = VendorWithdrawal::with('vendor')
            ->latest()
            ->paginate(15);

        return view('backEnd.vendor.withdrawals.index', compact('data'));
    }

    public function approve(Request $request, $id)
    {
        $withdrawal = VendorWithdrawal::with('vendor')->findOrFail($id);

        if ($withdrawal->status !== 'pending') {
            Toastr::error('Already processed.', 'Error');
            return back();
        }

        // Check admin fund balance
        $adminFundBalance = \App\Helpers\FundHelper::balance();
        if ($adminFundBalance < $withdrawal->amount) {
            Toastr::error('Insufficient fund balance. Current balance: ৳' . number_format($adminFundBalance, 2), 'Error');
            return back();
        }

        DB::transaction(function () use ($withdrawal, $request) {
            $withdrawal->status = 'approved';
            $withdrawal->processed_at = now();
            $withdrawal->admin_note = $request->admin_note;
            $withdrawal->save();

            $wallet = VendorWallet::firstOrCreate(['vendor_id' => $withdrawal->vendor_id]);
            $wallet->total_withdrawn += $withdrawal->amount;
            $wallet->save();

            VendorWalletTransaction::where('source_type', 'withdraw')
                ->where('source_id', $withdrawal->id)
                ->update(['status' => 'completed']);

            // Deduct from admin fund and create fund transaction
            FundTransaction::create([
                'direction'  => 'out',
                'source'     => 'vendor_withdrawal',
                'source_id'  => $withdrawal->id,
                'amount'     => $withdrawal->amount,
                'note'       => 'Vendor withdrawal approved - ' . ($withdrawal->vendor->shop_name ?? 'Vendor #' . $withdrawal->vendor_id) . ' - Amount: ৳' . number_format($withdrawal->amount, 2),
                'created_by' => Auth::id(),
            ]);
        });

        Toastr::success('Withdrawal approved successfully.', 'Success');
        return back();
    }

    public function reject(Request $request, $id)
    {
        $withdrawal = VendorWithdrawal::findOrFail($id);

        if ($withdrawal->status !== 'pending') {
            Toastr::error('Already processed.', 'Error');
            return back();
        }

        DB::transaction(function () use ($withdrawal, $request) {
            $withdrawal->status = 'rejected';
            $withdrawal->processed_at = now();
            $withdrawal->admin_note = $request->admin_note;
            $withdrawal->save();

            $wallet = VendorWallet::firstOrCreate(['vendor_id' => $withdrawal->vendor_id]);
            // Refund the held balance
            $wallet->balance += $withdrawal->amount;
            $wallet->save();

            VendorWalletTransaction::where('source_type', 'withdraw')
                ->where('source_id', $withdrawal->id)
                ->update(['status' => 'rejected']);
        });

        Toastr::success('Withdrawal rejected and amount returned to vendor balance.', 'Success');
        return back();
    }
}
