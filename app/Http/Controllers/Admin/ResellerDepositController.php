<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResellerDeposit;
use App\Models\User;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class ResellerDepositController extends Controller
{
    public function index(Request $request)
    {
        $query = ResellerDeposit::with('user:id,name,email,shop_name');

        if ($request->status === 'pending') {
            $query->where('status', 'pending');
        } elseif ($request->status === 'completed') {
            $query->where('status', 'completed');
        } elseif ($request->status === 'failed') {
            $query->where('status', 'failed');
        }

        $deposits = $query->latest()->paginate(20);

        return view('backEnd.reseller.deposits', compact('deposits'));
    }

    public function markAsPaid($id)
    {
        $deposit = ResellerDeposit::where('id', $id)->where('status', 'pending')->firstOrFail();

        $deposit->status = 'completed';
        $deposit->transaction_id = $deposit->transaction_id ?? 'admin_' . now()->format('YmdHis');
        $deposit->save();

        $user = $deposit->user;
        $user->wallet_balance = ($user->wallet_balance ?? 0) + (float) $deposit->amount;
        $user->save();

        \App\Models\ResellerWalletTransaction::log(
            $user->id, 'deposit', (float) $deposit->amount,
            'ResellerDeposit', $deposit->id,
            'এডমিন কর্তৃক পেইড মার্ক - ডিপোজিট #' . $deposit->id
        );

        Toastr::success('ডিপোজিট পেইড মার্ক করা হয়েছে এবং ওয়ালেটে যোগ হয়েছে।', 'Success');
        return redirect()->back();
    }
}
