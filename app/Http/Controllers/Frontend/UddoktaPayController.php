<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Shipping;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\DigitalDownload; 
use App\Services\FacebookCapiService;
use UddoktaPay\LaravelSDK\UddoktaPay;
use UddoktaPay\LaravelSDK\Requests\CheckoutRequest;
use UddoktaPay\LaravelSDK\Exceptions\UddoktaPayException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Session;

class UddoktaPayController extends Controller
{
    protected $facebookCapiService;

    public function __construct(FacebookCapiService $facebookCapiService)
    {
        $this->facebookCapiService = $facebookCapiService;
    }
    /**
     * Reseller wallet deposit checkout - redirect to UddoktaPay
     */
    public function depositCheckout($deposit_id)
    {
        $deposit = \App\Models\ResellerDeposit::where('id', $deposit_id)->where('status', 'pending')->firstOrFail();
        $user = $deposit->user;
        $authUser = \Illuminate\Support\Facades\Auth::guard('admin')->user();
        if (!$authUser || $authUser->id != $user->id) {
            return redirect()->route('reseller.dashboard')->with('error', 'Unauthorized.');
        }

        $uddoktapay = UddoktaPay::make(env('UDDOKTAPAY_API_KEY'), env('UDDOKTAPAY_API_URL'));
        try {
            $checkoutRequest = CheckoutRequest::make()
                ->setFullName($user->name ?? 'Reseller')
                ->setEmail($user->email ?? 'reseller@example.com')
                ->setAmount((float) $deposit->amount)
                ->addMetadata('payment_type', 'deposit')
                ->addMetadata('deposit_id', $deposit->id)
                ->addMetadata('user_id', $user->id)
                ->setRedirectUrl(route('uddoktapay.verify'))
                ->setCancelUrl(route('reseller.wallet') . '?deposit=cancelled')
                ->setWebhookUrl(route('uddoktapay.ipn'));

            $response = $uddoktapay->checkout($checkoutRequest);
            if ($response->failed()) {
                Log::error('UddoktaPay Deposit Checkout Failed: ' . $response->message());
                return redirect()->route('reseller.deposit')->with('error', 'Payment initiation failed.');
            }
            return redirect($response->paymentURL());
        } catch (UddoktaPayException $e) {
            Log::error('UddoktaPay Deposit Error: ' . $e->getMessage());
            return redirect()->route('reseller.deposit')->with('error', 'Payment Error: ' . $e->getMessage());
        }
    }

    /**
     * Redirect user to UddoktaPay checkout page
     */
    public function checkout(Request $request, $order_id = null)
    {
        // 🔹 order_id দুইভাবে নেব — route param বা request থেকে
        $orderId = $order_id ?? $request->order_id;

        if(!$orderId){
            return redirect()->back()->with('error', 'Order ID missing for UddoktaPay.');
        }

        $order = Order::findOrFail($orderId);
        $shipping = Shipping::where('order_id', $order->id)->first();

        $fullName = $shipping->name ?? 'Customer';
        $email    = 'customer@example.com';

        // ======================================================
        // 🔥 FIXED: সেশন থেকে এমাউন্ট চেক (এডভান্স বা ফুল)
        // ======================================================
        if (Session::has('payable_amount') && Session::get('payable_amount') > 0) {
            $amount = (float) Session::get('payable_amount');
        } elseif ($order->customer_payable_amount) {
            // Reseller order: use customer_payable_amount (includes shipping charge)
            $amount = (float) $order->customer_payable_amount;
        } else {
            // সেশনে না পেলে ডিফল্ট ফুল এমাউন্ট
            $amount = (float) $order->amount;
        }

        Log::info("UddoktaPay checkout for order {$order->id} with amount={$amount}");

        // উদ্যোক্তা পে ইনিশিয়ালাইজ
        $uddoktapay = UddoktaPay::make(env('UDDOKTAPAY_API_KEY'), env('UDDOKTAPAY_API_URL'));

        try {
            $checkoutRequest = CheckoutRequest::make()
                ->setFullName($fullName)
                ->setEmail($email)
                ->setAmount($amount) // ✅ এখন সঠিক এমাউন্ট যাবে
                ->addMetadata('order_id', $order->id)
                ->setRedirectUrl(route('uddoktapay.verify'))
                ->setCancelUrl(route('uddoktapay.cancel'))
                ->setWebhookUrl(route('uddoktapay.ipn'));

            $response = $uddoktapay->checkout($checkoutRequest);

            if ($response->failed()) {
                Log::error('UddoktaPay Checkout Failed: ' . $response->message());
                return redirect()->back()->with('error', 'Payment initiation failed.');
            }

            // ✅ gateway info আপডেট (Payment status is still pending)
            $order->payment_gateway = 'uddoktapay';
            $order->payment_status  = 'pending';
            $order->save();

            // সেশন থেকে এমাউন্ট মুছে ফেলছি না, কারণ ভেরিফাই বা ক্যান্সেল হওয়ার পর মুছব
            // অথবা রিডাইরেক্ট হয়ে গেলে অটোমেটিক পরের স্টেপে হ্যান্ডেল হবে

            return redirect($response->paymentURL());

        } catch (UddoktaPayException $e) {
            Log::error('UddoktaPay Checkout Error: ' . $e->getMessage());
            return redirect()->back()->with('error', "Payment Error: " . $e->getMessage());
        }
    }

    /**
     * Payment verification after returning from UddoktaPay
     */
    public function verify(Request $request)
    {
        $uddoktapay = UddoktaPay::make(env('UDDOKTAPAY_API_KEY'), env('UDDOKTAPAY_API_URL'));

        try {
            $response = $uddoktapay->verify($request);
            $metadata = $response->metadata();

            // Reseller deposit
            if (isset($metadata['payment_type']) && $metadata['payment_type'] === 'deposit' && !empty($metadata['deposit_id'])) {
                return $this->handleDepositVerify($response);
            }

            $order_id = $metadata['order_id'] ?? null;
            $order    = Order::where('id', $order_id)->first();

            if (!$order) {
                return redirect()->route('customer.account')->with('error', 'Order not found.');
            }

            // ✅ পেমেন্ট সফল
            if ($response->success()) {
                $order->payment_status  = 'paid';
                $order->payment_gateway = 'uddoktapay';
                $order->save();

                // ✅ Payment Table Update (CRITICAL FIX)
                // পেমেন্ট সফল হলে amount আপডেট করতে হবে, কারণ ডাটাবেসে এটি ০ ছিল
                $payment = Payment::where('order_id', $order->id)->first();
                if($payment){
                    $payment->payment_status = 'paid';
                    
                    // সেশন থেকে এমাউন্ট নিয়ে আপডেট
                    if (Session::has('payable_amount')) {
                        $payment->amount = Session::get('payable_amount');
                    } elseif ($order->customer_payable_amount) {
                        // Reseller order: use customer_payable_amount (includes shipping charge)
                        $payment->amount = $order->customer_payable_amount;
                    } else {
                        // Fallback: use order amount
                        $payment->amount = $order->amount; 
                    }
                    $payment->save();
                }

                // ⭐ সফল পেমেন্টের পরে ডিজিটাল ডাউনলোড তৈরি
                $this->createDigitalDownloads($order);

                // Send Facebook Purchase event
                try {
                    $customer = Customer::find($order->customer_id);
                    $userData = [];
                    
                    // Get customer email or phone
                    if ($customer && $customer->email) {
                        $userData['email'] = $customer->email;
                    } elseif ($customer && $customer->phone) {
                        $userData['phone'] = $customer->phone;
                    }
                    
                    // Get shipping phone if available
                    $shipping = Shipping::where('order_id', $order->id)->first();
                    if (empty($userData['phone']) && $shipping && $shipping->phone) {
                        $userData['phone'] = $shipping->phone;
                    }
                    
                    // Get Facebook Pixel cookies if available
                    if (isset($_COOKIE['_fbp'])) {
                        $userData['fbp'] = $_COOKIE['_fbp'];
                    }
                    if (isset($_COOKIE['_fbc'])) {
                        $userData['fbc'] = $_COOKIE['_fbc'];
                    }
                    
                    // Send Purchase event after response is sent (non-blocking)
                    register_shutdown_function(function () use ($order, $payment, $userData) {
                        try {
                            $orderDetails = $order->orderdetails ?? \App\Models\Order::with('orderdetails')->find($order->id)?->orderdetails ?? collect();
                            $contentIds  = $orderDetails->pluck('product_id')->map(fn($id) => (string)$id)->values()->toArray();
                            $contents    = $orderDetails->map(fn($i) => ['id' => (string)$i->product_id, 'quantity' => (int)$i->qty, 'item_price' => (float)$i->sale_price])->values()->toArray();
                            app(\App\Services\FacebookCapiService::class)->sendEvent('Purchase', [
                                'currency'     => 'BDT',
                                'value'        => $payment->amount ?? $order->amount,
                                'order_id'     => $order->invoice_id ?? $order->id,
                                'content_ids'  => $contentIds,
                                'contents'     => $contents,
                                'num_items'    => count($contents),
                                'content_type' => 'product',
                            ], $userData, [
                                'event_id'         => 'purchase_' . ($order->invoice_id ?? $order->id),
                                'event_source_url' => request()->fullUrl(),
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Facebook CAPI Purchase event failed for order ' . $order->id . ': ' . $e->getMessage());
                        }
                    });
                } catch (\Exception $e) {
                    Log::error('Facebook CAPI setup failed for order ' . $order->id . ': ' . $e->getMessage());
                }

                // সেশন ক্লিয়ার
                Session::forget('payable_amount');

                $redirectRoute = ($order->reseller_profit) ? 'reseller.order.success' : 'customer.order_success';
                return redirect()->route($redirectRoute, $order->id)
                                 ->with('success', 'Payment Successful! Your digital downloads are ready.');
            } else {
                // ❌ ব্যর্থ পেমেন্ট
                $order->payment_status  = 'failed';
                $order->payment_gateway = 'uddoktapay';
                $order->save();

                $redirectRoute = ($order->reseller_profit) ? 'reseller.order.success' : 'customer.order_success';
                return redirect()->route($redirectRoute, $order->id)
                                 ->with('error', 'Payment Failed!');
            }

        } catch (UddoktaPayException $e) {
            Log::error('UddoktaPay Verify Error: ' . $e->getMessage());
            return redirect()->route('customer.account')->with('error', "Verification Error: " . $e->getMessage());
        }
    }

    /**
     * If customer cancels payment
     */
    public function cancel()
    {
        return redirect()->route('customer.account')
                         ->with('error', 'Payment cancelled by user.');
    }

    /**
     * Handle reseller deposit verification
     */
    private function handleDepositVerify($response)
    {
        $metadata = $response->metadata();
        $depositId = $metadata['deposit_id'] ?? null;
        $deposit = \App\Models\ResellerDeposit::find($depositId);

        if (!$deposit || $deposit->status !== 'pending') {
            return redirect()->route('reseller.wallet')->with('error', 'Deposit not found or already processed.');
        }

        if ($response->success()) {
            $deposit->status = 'completed';
            $deposit->transaction_id = $response->transactionId();
            $deposit->save();

            $user = $deposit->user;
            $user->wallet_balance = ($user->wallet_balance ?? 0) + (float) $deposit->amount;
            $user->save();

            \App\Models\ResellerWalletTransaction::log(
                $user->id, 'deposit', (float) $deposit->amount,
                'ResellerDeposit', $deposit->id,
                'উদ্যোক্তা পে ডিপোজিট #' . $deposit->id
            );

            Log::info("Reseller deposit #{$deposit->id} completed. User #{$user->id} wallet +{$deposit->amount}");
            return redirect()->route('reseller.wallet')->with('success', 'ডিপোজিট সফল! আপনার ওয়ালেটে ৳' . number_format($deposit->amount, 2) . ' যোগ হয়েছে।');
        }

        if ($response->pending()) {
            return redirect()->route('reseller.wallet')->with('info', 'পেমেন্ট পেন্ডিং রয়েছে। এডমিন পেমেন্ট নিশ্চিত করলে আপনার ওয়ালেটে যোগ হবে।');
        }

        $deposit->status = 'failed';
        $deposit->save();
        return redirect()->route('reseller.wallet')->with('error', 'পেমেন্ট ব্যর্থ হয়েছে।');
    }

    /**
     * IPN (Instant Payment Notification)
     * Called automatically from UddoktaPay server after payment.
     */
    public function ipn(Request $request)
    {
        $uddoktapay = UddoktaPay::make(env('UDDOKTAPAY_API_KEY'), env('UDDOKTAPAY_API_URL'));
        $response   = $uddoktapay->ipn($request);

        if ($response->success()) {
            $metadata = $response->metadata();

            // Reseller deposit
            if (isset($metadata['payment_type']) && $metadata['payment_type'] === 'deposit' && !empty($metadata['deposit_id'])) {
                $deposit = \App\Models\ResellerDeposit::find($metadata['deposit_id']);
                if ($deposit && $deposit->status === 'pending') {
                    $deposit->status = 'completed';
                    $deposit->transaction_id = $response->transactionId();
                    $deposit->save();
                    $user = $deposit->user;
                    $user->wallet_balance = ($user->wallet_balance ?? 0) + (float) $deposit->amount;
                    $user->save();

                    \App\Models\ResellerWalletTransaction::log(
                        $user->id, 'deposit', (float) $deposit->amount,
                        'ResellerDeposit', $deposit->id,
                        'উদ্যোক্তা পে ডিপোজিট #' . $deposit->id . ' (IPN)'
                    );

                    Log::info("Reseller deposit #{$deposit->id} completed via IPN.");
                }
                return response()->json(['received' => true]);
            }

            $order_id = $metadata['order_id'] ?? null;
            $order    = Order::where('id', $order_id)->first();

            if ($order && $order->payment_status != 'paid') {
                $order->payment_status  = 'paid';
                $order->payment_gateway = 'uddoktapay';
                $order->save();

                // ✅ Payment update in IPN as well
                $payment = Payment::where('order_id', $order->id)->first();
                if($payment){
                    $payment->payment_status = 'paid';
                    // IPN রেসপন্সে এমাউন্ট থাকে, সেটা ব্যবহার করা বেটার
                    // $payment->amount = $request->amount; // (Check IPN payload structure)
                    $payment->save();
                }

                // ⭐ IPN দিয়েও যদি প্রথমবার paid হয় → তখনও ডিজিটাল ডাউনলোড তৈরি
                $this->createDigitalDownloads($order);

                Log::info("Order #{$order_id} marked as paid via IPN and digital downloads created.");
            }
        } else {
            Log::warning('UddoktaPay IPN Failed or Invalid');
        }
    }

    // =====================================
    // ⭐ DIGITAL DOWNLOAD CREATOR (HELPER)
    // =====================================
    private function createDigitalDownloads(Order $order)
    {
        // orderdetails + product রিলেশন লোড করি
        $order->loadMissing('orderdetails.product');

        foreach ($order->orderdetails as $item) {
            $product = $item->product;

            if ($product && $product->is_digital == 1 && $product->digital_file) {

                DigitalDownload::firstOrCreate(
                    [
                        'order_id'    => $order->id,
                        'product_id'  => $product->id,
                        'customer_id' => $order->customer_id,
                    ],
                    [
                        'token'               => Str::uuid(),
                        'file_path'           => $product->digital_file,
                        'remaining_downloads' => $product->download_limit ?? 5,
                        'expires_at'          => $product->download_expire_days
                                                    ? now()->addDays($product->download_expire_days)
                                                    : null,
                    ]
                );
            }
        }
    }
}