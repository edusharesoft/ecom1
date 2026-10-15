<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\ResellerWithdrawal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function index()
    {
        $user = Auth::guard('admin')->user();
        $withdrawals = ResellerWithdrawal::where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        $setting = GeneralSetting::first();
        $minBalance = (float) ($setting->reseller_wallet_min_balance ?? 0);
        $walletBalance = (float) ($user->wallet_balance ?? 0);
        $maxWithdrawable = max(0, $walletBalance - $minBalance);
        $minWithdraw = config('app.reseller_min_withdraw', 100);

        return view('reseller.withdrawals.index', compact('user', 'withdrawals', 'minBalance', 'maxWithdrawable', 'minWithdraw'));
    }

    public function store(Request $request)
    {
        $user = Auth::guard('admin')->user();
        $walletBalance = (float) ($user->wallet_balance ?? 0);

        $setting = GeneralSetting::first();
        $minBalance = (float) ($setting->reseller_wallet_min_balance ?? 0);
        $maxWithdrawable = max(0, $walletBalance - $minBalance);

        $min = max(config('app.reseller_min_withdraw', 100), 1);

        $request->validate([
            'amount' => 'required|numeric|min:' . $min,
            'payout_method' => 'required|string|max:50',
            'account_name' => 'nullable|string|max:191',
            'account_number' => 'nullable|string|max:191',
            'note' => 'nullable|string|max:500',
        ]);

        $amount = round((float) $request->amount, 2);

        if ($amount > $walletBalance) {
            return back()->with('error', 'আপনার ব্যালেন্স যথেষ্ট নেই।');
        }

        if ($walletBalance - $amount < $minBalance) {
            return back()->with('error', 'সর্বনিম্ন ব্যালেন্স ৳' . number_format($minBalance, 0) . ' অ্যাকাউন্টে রাখতে হবে। উত্তোলনের পর সর্বোচ্চ ৳' . number_format($maxWithdrawable, 0) . ' উত্তোলন করতে পারবেন।');
        }

        DB::transaction(function () use ($user, $amount, $request) {
            // Hold balance immediately
            $user->wallet_balance -= $amount;
            $user->save();

            $withdrawal = ResellerWithdrawal::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'charge' => 0,
                'payout_method' => $request->payout_method,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'note' => $request->note,
                'status' => 'pending',
            ]);

            \App\Models\ResellerWalletTransaction::log(
                $user->id, 'withdrawal', -$amount,
                'ResellerWithdrawal', $withdrawal->id,
                'উইথড্র রিকুয়েস্ট #' . $withdrawal->id
            );
        });

        return back()->with('success', 'Withdraw request submitted successfully.');
    }
}
