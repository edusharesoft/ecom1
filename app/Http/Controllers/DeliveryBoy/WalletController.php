<?php

namespace App\Http\Controllers\DeliveryBoy;

use App\Http\Controllers\Controller;
use App\Models\DeliveryBoyWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        $boy = Auth::guard('delivery_boy')->user();
        $tx = $boy->walletTransactions()->orderByDesc('id')->paginate(25);
        $pending = $boy->withdrawals()->where('status', 'pending')->orderByDesc('id')->get();

        return view('delivery.wallet.index', compact('boy', 'tx', 'pending'));
    }

    public function withdraw(Request $request)
    {
        $boy = Auth::guard('delivery_boy')->user();
        $request->validate([
            'amount'         => 'required|numeric|min:50',
            'payout_method'  => 'required|string|max:64',
            'payout_number'  => 'required|string|max:64',
            'note'           => 'nullable|string|max:300',
        ]);
        if ((float) $request->amount > (float) $boy->wallet_balance + 0.0001) {
            return redirect()->back()->with('error', 'Amount exceeds wallet');
        }

        DeliveryBoyWithdrawal::create([
            'delivery_boy_id' => $boy->id,
            'amount'          => $request->amount,
            'status'          => 'pending',
            'payout_method'   => $request->payout_method,
            'payout_number'   => $request->payout_number,
            'note'            => $request->note,
        ]);

        return redirect()->back()->with('success', 'Withdrawal request submitted');
    }
}
