<?php

namespace App\Http\Controllers\DeliveryBoy;

use App\Helpers\SmsHelper;
use App\Http\Controllers\Controller;
use App\Models\DeliveryBoyWithdrawal;
use App\Models\GeneralSetting;
use App\Models\Order;
use App\Services\DeliveryBoyWalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class OrderController extends Controller
{
    public function index()
    {
        $boy = Auth::guard('delivery_boy')->user();
        $orders = Order::query()
            ->with(['shipping', 'customer'])
            ->where('delivery_boy_id', $boy->id)
            ->whereNull('rider_delivered_at')
            ->where('order_status', '!=', 11)
            ->orderByDesc('delivery_assigned_at')
            ->orderByDesc('id')
            ->paginate(15);

        return view('delivery.orders.index', compact('orders'));
    }

    public function history()
    {
        $boy = Auth::guard('delivery_boy')->user();
        $orders = Order::query()
            ->with(['shipping'])
            ->where('delivery_boy_id', $boy->id)
            ->whereNotNull('rider_delivered_at')
            ->orderByDesc('rider_delivered_at')
            ->paginate(20);

        return view('delivery.orders.history', compact('orders'));
    }

    public function show(int $id)
    {
        $boy = Auth::guard('delivery_boy')->user();
        $order = Order::with([
            'shipping',
            'customer',
            'payment',
            'status',
            'orderdetails.image',
            'orderdetails.color',
            'orderdetails.size',
        ])
            ->where('delivery_boy_id', $boy->id)
            ->findOrFail($id);

        return view('delivery.orders.show', compact('order'));
    }

    public function cancel(Request $request, int $id)
    {
        $request->validate([
            'cancel_note' => 'nullable|string|max:500',
        ]);

        $boy = Auth::guard('delivery_boy')->user();
        $order = Order::where('delivery_boy_id', $boy->id)->findOrFail($id);

        if ($order->rider_delivered_at) {
            return redirect()->back()->with('error', 'Already marked delivered');
        }

        if ((int) $order->order_status === 11) {
            return redirect()->route('delivery.orders.index')->with('success', 'Order already cancelled.');
        }

        $order->order_status = 11; // Cancel
        $order->delivery_otp_hash = null;
        $order->delivery_otp_expires_at = null;
        $order->delivery_otp_verified_at = null;
        $order->save();

        \App\Helpers\ResellerOrderHelper::deductDeliveryChargeOnCancel($order);

        return redirect()->route('delivery.orders.index')->with('success', 'Order cancelled successfully.');
    }

    public function deliver(Request $request, DeliveryBoyWalletService $wallet, int $id)
    {
        $request->validate([
            'delivery_otp' => 'nullable|digits:6',
            'resend_otp'   => 'nullable|boolean',
            'note'         => 'nullable|string|max:500',
        ], [
            'delivery_otp.digits' => 'OTP ৬ ডিজিটের হতে হবে।',
        ]);

        $boy = Auth::guard('delivery_boy')->user();
        $order = Order::with('shipping')->where('delivery_boy_id', $boy->id)->findOrFail($id);

        if ($order->rider_delivered_at) {
            return redirect()->back()->with('error', 'Already marked delivered');
        }

        if ($request->filled('delivery_otp')) {
            if (! $this->deliveryOtpIsValid($order, $request->delivery_otp)) {
                return redirect()
                    ->back()
                    ->withErrors(['delivery_otp' => 'OTP সঠিক নয় বা মেয়াদ শেষ। আবার চেষ্টা করুন।'])
                    ->withInput();
            }

            $order->delivery_otp_verified_at = now();
            $order->delivery_otp_hash = null;
            $order->delivery_otp_expires_at = null;
        } else {
            if ($this->deliveryOtpIsPending($order) && ! $request->boolean('resend_otp')) {
                return redirect()
                    ->back()
                    ->with('success', 'OTP ইতিমধ্যে কাস্টমারের ফোনে পাঠানো হয়েছে। কোডটি বসিয়ে ডেলিভারি সম্পন্ন করুন।');
            }

            return $this->sendDeliveryOtp($order);
        }

        $order->order_status = 6; // Complete
        $order->rider_delivered_at = now();
        $order->save();

        $boy->refresh();
        $wallet->creditCommission($boy, $order);

        return redirect()->route('delivery.orders.index')->with('success', 'Delivery completed. Commission added if configured.');
    }

    private function sendDeliveryOtp(Order $order)
    {
        $phone = $order->shipping->phone ?? null;
        if (! $phone) {
            return redirect()->back()->with('error', 'কাস্টমারের মোবাইল নম্বর পাওয়া যায়নি।');
        }

        $otp = (string) random_int(100000, 999999);
        $site = GeneralSetting::where('status', 1)->first();
        $siteName = $site->name ?? config('app.name');

        $message = "আপনার অর্ডার #{$order->invoice_id} ডেলিভারি OTP: {$otp}। OTPটি রাইডারকে দিন। — {$siteName}";

        $sent = SmsHelper::send($phone, $message, ['order' => 1]);
        if (! $sent) {
            return redirect()
                ->back()
                ->with('error', 'কাস্টমারের ফোনে OTP পাঠানো যায়নি। SMS গেটওয়ে চেক করুন।');
        }

        $order->delivery_otp_hash = Hash::make($otp);
        $order->delivery_otp_sent_at = now();
        $order->delivery_otp_expires_at = now()->addMinutes(10);
        $order->delivery_otp_verified_at = null;
        $order->save();

        return redirect()
            ->back()
            ->with('success', 'কাস্টমারের ফোনে OTP পাঠানো হয়েছে। OTP বসিয়ে ডেলিভারি সম্পন্ন করুন।');
    }

    private function deliveryOtpIsPending(Order $order): bool
    {
        return ! empty($order->delivery_otp_hash)
            && $order->delivery_otp_expires_at
            && $order->delivery_otp_expires_at->isFuture();
    }

    private function deliveryOtpIsValid(Order $order, string $otp): bool
    {
        return $this->deliveryOtpIsPending($order)
            && Hash::check($otp, $order->delivery_otp_hash);
    }
}
