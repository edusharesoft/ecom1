<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Models\Shipping;
use App\Models\Customer;
use App\Models\DigitalDownload;
use App\Services\FacebookCapiService;
use Session;
use Toastr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AamarPayController extends Controller
{
    private $store_id;
    private $signature_key;
    private $base_url;
    protected $facebookCapiService;

    public function __construct(FacebookCapiService $facebookCapiService)
    {
        $this->facebookCapiService = $facebookCapiService;
        $aamarpay_gateway = PaymentGateway::where(['status' => 1, 'type' => 'aamarpay'])->first();
        
        if($aamarpay_gateway) {
            $this->store_id = $aamarpay_gateway->app_key; // store_id is stored in app_key field
            $this->signature_key = $aamarpay_gateway->app_secret; // signature_key is stored in app_secret field
            $this->base_url = $aamarpay_gateway->base_url ?? 'https://sandbox.aamarpay.com/jsonpost.php';
        } else {
            // Sandbox credentials (for development)
            $this->store_id = 'aamarpaytest';
            $this->signature_key = 'dbb74894e82415a2f7ff0ec3a97e4183';
            $this->base_url = 'https://sandbox.aamarpay.com/jsonpost.php';
        }
    }

    /**
     * Initiate payment with aamarPay
     */
    public function checkout(Request $request, $order_id = null)
    {
        $orderId = $order_id ?? $request->order_id;

        if(!$orderId){
            return redirect()->back()->with('error', 'Order ID missing for aamarPay.');
        }

        $order = Order::findOrFail($orderId);
        $shipping = Shipping::where('order_id', $order->id)->first();

        // Get customer details
        $customer = $order->customer;
        $fullName = $shipping->name ?? ($customer->name ?? 'Customer');
        $email = $customer->email ?? 'customer@example.com';
        $phone = $shipping->phone ?? ($customer->phone ?? '01700000000');

        // Get amount from session (advance payment) or order
        if (Session::has('payable_amount') && Session::get('payable_amount') > 0) {
            $amount = (float) Session::get('payable_amount');
        } elseif ($order->customer_payable_amount) {
            // Reseller order: use customer_payable_amount (includes shipping charge)
            $amount = (float) $order->customer_payable_amount;
        } else {
            $amount = (float) $order->amount;
        }

        Log::info("aamarPay checkout for order {$order->id} with amount={$amount}");

        // Generate unique transaction ID (max 32 characters)
        $tran_id = 'ORD' . $order->id . '_' . Str::random(8);
        $tran_id = substr($tran_id, 0, 32); // Ensure max 32 chars

        // Base URL for callbacks – বর্তমান সাইটের URL ব্যবহার করলে APP_URL ভুল থাকলেও ক্যালব্যাক ঠিক যাবে
        $baseUrl = rtrim(env('APP_URL') ?: request()->getSchemeAndHttpHost(), '/');

        $successUrl = $baseUrl . '/aamarpay/success';
        $failUrl = $baseUrl . '/aamarpay/fail';
        $cancelUrl = $baseUrl . '/aamarpay/cancel';
        Log::info('aamarPay callback URLs', ['success' => $successUrl, 'fail' => $failUrl]);

        // Prepare payment data
        $paymentData = [
            'store_id' => $this->store_id,
            'signature_key' => $this->signature_key,
            'tran_id' => $tran_id,
            'amount' => number_format($amount, 2, '.', ''),
            'currency' => 'BDT',
            'desc' => 'Order Payment - Order #' . $order->id,
            'cus_name' => $fullName,
            'cus_email' => $email,
            'cus_phone' => $phone,
            'success_url' => $successUrl,
            'fail_url' => $failUrl,
            'cancel_url' => $cancelUrl,
            'type' => 'json',
            'opt_a' => $order->id, // Store order ID in optional parameter
        ];

        // Add optional address fields if available
        if ($shipping) {
            $paymentData['cus_add1'] = $shipping->address ?? '';
            $paymentData['cus_city'] = $shipping->area ?? '';
            $paymentData['cus_country'] = 'Bangladesh';
        }

        // Update order with payment gateway info
        $order->payment_gateway = 'aamarpay';
        $order->payment_status = 'pending';
        $order->save();

        // Store transaction ID in session for verification
        Session::put('aamarpay_tran_id', $tran_id);
        Session::put('aamarpay_order_id', $order->id);

        // Make API call to aamarPay
        try {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $this->base_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($paymentData),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json'
                ],
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl);
            curl_close($curl);

            if ($curlError) {
                Log::error('aamarPay cURL Error: ' . $curlError);
                Toastr::error('Payment initiation failed. Please try again.', 'Error!');
                return redirect()->back();
            }

            $responseData = json_decode($response, true);

            if (isset($responseData['result']) && $responseData['result'] === 'true' && isset($responseData['payment_url'])) {
                Log::info('aamarPay payment URL generated: ' . $responseData['payment_url']);
                return redirect($responseData['payment_url']);
            } else {
                Log::error('aamarPay API Error: ' . json_encode($responseData));
                Toastr::error('Payment initiation failed. Please try again.', 'Error!');
                return redirect()->back();
            }

        } catch (\Exception $e) {
            Log::error('aamarPay Checkout Error: ' . $e->getMessage());
            Toastr::error('Payment Error: ' . $e->getMessage(), 'Error!');
            return redirect()->back();
        }
    }

    /**
     * Handle successful payment callback
     */
    public function success(Request $request)
    {
        Log::info('aamarPay Success Callback', $request->all());

        // সেশন হারালেও ক্যালব্যাকে opt_a দিয়ে অর্ডার মিলবে (পেমেন্ট গেটওয়ে রিডাইরেক্টে সেশন অনেক সময় থাকে না)
        $orderId = Session::get('aamarpay_order_id') ?? $request->opt_a ?? $request->value1 ?? null;
        $orderId = $orderId ? (int) $orderId : null;

        if (!$orderId) {
            Log::warning('aamarPay success: order ID not found in session or request', $request->all());
            Toastr::error('Order not found.', 'Error!');
            return redirect()->route('customer.account');
        }

        $order = Order::where('id', $orderId)->first();

        if (!$order) {
            Toastr::error('Order not found.', 'Error!');
            return redirect()->route('customer.account');
        }

        // Check payment status from callback
        $statusCode = $request->status_code ?? null;
        $payStatus = $request->pay_status ?? null;

        // Status code: 2 = successful, 0 = initiated, 3 = expired, 7 = failed
        if ($statusCode == '2' && strtolower($payStatus) == 'successful') {
            // Payment successful
            $order->payment_status = 'paid';
            $order->payment_gateway = 'aamarpay';
            $order->order_status = 1; // Processing
            $order->save();

            // Update payment record
            $payment = Payment::where('order_id', $order->id)->first();
            if ($payment) {
                $payment->payment_status = 'paid';
                $payment->trx_id = $request->pg_txnid ?? $request->mer_txnid ?? null;
                $payment->sender_number = $request->cus_phone ?? null;
                
                // Update amount from callback or session
                if ($request->amount) {
                    $payment->amount = (float) $request->amount;
                } elseif (Session::has('payable_amount')) {
                    $payment->amount = Session::get('payable_amount');
                } elseif ($order->customer_payable_amount) {
                    $payment->amount = $order->customer_payable_amount;
                } else {
                    $payment->amount = $order->amount;
                }
                
                $payment->save();
            }

            // Create digital downloads if applicable
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

            // Clear session
            Session::forget(['payable_amount', 'aamarpay_tran_id', 'aamarpay_order_id']);

            Toastr::success('Thanks, Your payment was successful!', 'Success!');
            $redirectRoute = ($order->reseller_profit) ? 'reseller.order.success' : 'customer.order_success';
            return redirect()->route($redirectRoute, $order->id);

        } else {
            // Payment failed or pending
            $order->payment_status = 'failed';
            $order->payment_gateway = 'aamarpay';
            $order->save();

            Session::forget(['payable_amount', 'aamarpay_tran_id', 'aamarpay_order_id']);

            Toastr::error('Payment failed or incomplete.', 'Failed!');
            $redirectRoute = ($order->reseller_profit) ? 'reseller.order.success' : 'customer.order_success';
            return redirect()->route($redirectRoute, $order->id);
        }
    }

    /**
     * Handle failed payment callback
     */
    public function fail(Request $request)
    {
        Log::info('aamarPay Fail Callback', $request->all());

        $orderId = Session::get('aamarpay_order_id') ?? $request->opt_a ?? $request->value1 ?? null;
        $orderId = $orderId ? (int) $orderId : null;
        $order = null;

        if ($orderId) {
            $order = Order::where('id', $orderId)->first();
            if ($order) {
                $order->payment_status = 'failed';
                $order->payment_gateway = 'aamarpay';
                $order->save();
            }
        }

        Session::forget(['payable_amount', 'aamarpay_tran_id', 'aamarpay_order_id']);

        Toastr::error('Payment failed. Please try again.', 'Failed!');

        if ($orderId && $order) {
            $redirectRoute = ($order->reseller_profit) ? 'reseller.order.success' : 'customer.order_success';
            return redirect()->route($redirectRoute, $orderId);
        }

        return redirect()->route('customer.account');
    }

    /**
     * Handle cancelled payment
     */
    public function cancel()
    {
        Log::info('aamarPay Payment Cancelled');

        $orderId = Session::get('aamarpay_order_id');
        $order = null;

        if ($orderId) {
            $order = Order::where('id', $orderId)->first();
            if ($order) {
                $order->payment_status = 'cancelled';
                $order->payment_gateway = 'aamarpay';
                $order->save();
            }
        }

        Session::forget(['payable_amount', 'aamarpay_tran_id', 'aamarpay_order_id']);

        Toastr::error('Payment cancelled by user.', 'Cancelled!');
        
        if ($orderId && $order) {
            $redirectRoute = ($order->reseller_profit) ? 'reseller.order.success' : 'customer.order_success';
            return redirect()->route($redirectRoute, $orderId);
        }
        
        return redirect()->route('customer.account');
    }

    /**
     * Create digital downloads for order
     */
    private function createDigitalDownloads(Order $order)
    {
        $order->loadMissing('orderdetails.product');

        foreach ($order->orderdetails as $item) {
            $product = $item->product;

            if ($product && $product->is_digital == 1 && $product->digital_file) {
                DigitalDownload::firstOrCreate(
                    [
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'customer_id' => $order->customer_id,
                    ],
                    [
                        'token' => Str::uuid(),
                        'file_path' => $product->digital_file,
                        'remaining_downloads' => $product->download_limit ?? 5,
                        'expires_at' => $product->download_expire_days
                            ? now()->addDays($product->download_expire_days)
                            : null,
                    ]
                );
            }
        }
    }
}
