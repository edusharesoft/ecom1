<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FundTransaction;
use App\Models\ResellerWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Toastr;

class ResellerWithdrawalController extends Controller
{
    public function index()
    {
        $data = ResellerWithdrawal::with('user')
            ->latest()
            ->paginate(15);

        return view('backEnd.reseller.withdrawals.index', compact('data'));
    }

    public function approve(Request $request, $id)
    {
        $withdrawal = ResellerWithdrawal::with('user')->findOrFail($id);

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

            // Update reseller's total_withdrawn (we can add this field later if needed)
            // For now, we just mark it as approved

            // Deduct from admin fund and create fund transaction
            FundTransaction::create([
                'direction'  => 'out',
                'source'     => 'reseller_withdrawal',
                'source_id'  => $withdrawal->id,
                'amount'     => $withdrawal->amount,
                'note'       => 'Reseller withdrawal approved - ' . ($withdrawal->user->shop_name ?? $withdrawal->user->name) . ' - Amount: ৳' . number_format($withdrawal->amount, 2),
                'created_by' => Auth::id(),
            ]);
        });

        Toastr::success('Withdrawal approved successfully.', 'Success');
        return back();
    }

    public function reject(Request $request, $id)
    {
        $withdrawal = ResellerWithdrawal::findOrFail($id);

        if ($withdrawal->status !== 'pending') {
            Toastr::error('Already processed.', 'Error');
            return back();
        }

        DB::transaction(function () use ($withdrawal, $request) {
            $withdrawal->status = 'rejected';
            $withdrawal->processed_at = now();
            $withdrawal->admin_note = $request->admin_note;
            $withdrawal->save();

            // Refund the held balance back to reseller wallet
            $reseller = $withdrawal->user;
            $reseller->wallet_balance = ($reseller->wallet_balance ?? 0) + $withdrawal->amount;
            $reseller->save();

            \App\Models\ResellerWalletTransaction::log(
                $reseller->id, 'withdrawal_reversed', (float) $withdrawal->amount,
                'ResellerWithdrawal', $withdrawal->id,
                'উইথড্র রিজেক্ট - ফেরত #' . $withdrawal->id
            );
        });

        Toastr::success('Withdrawal rejected and amount returned to reseller balance.', 'Success');
        return back();
    }
}
