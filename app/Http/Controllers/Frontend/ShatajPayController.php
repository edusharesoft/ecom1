<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\DigitalDownload;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Session;
use Toastr;

class ShatajPayController extends Controller
{

    public function ShatajPayInitPayment($data)
    {
        $orderId = $data['order_id'] ?? null;
        $paymentInfo = $data['paymentInfo'] ?? [];

        if (empty($orderId)) {
            return back()->with('shataj_status', 'Order not found');
        }

        $checkOrder = Order::find($orderId);

        if (!$checkOrder) {
            return back()->with('shataj_status', 'Order not found');
        }

        return $this->paymentGatewaySubmit($paymentInfo, $checkOrder);
    }

    private function paymentGatewaySubmit($paymentInfo, $checkOrder)
    {
        $shatajPay = $this->getShatajPayCredentials();

        if (!$shatajPay) {
            return back()->with('shataj_status', 'Oops! Shataj Pay gateway is inactive or credentials are not configured properly.');
        }

        try {
            if (!Schema::hasTable('transactions')) {
                Schema::create('transactions', function (Blueprint $table) {
                    $table->id();
                    $table->string('main_order_id')->nullable();
                    $table->string('name')->nullable();
                    $table->string('email')->nullable();
                    $table->string('method')->nullable();
                    $table->string('phone')->nullable();
                    $table->decimal('amount', 12, 2)->default(0);
                    $table->string('status')->default('pending');
                    $table->text('address')->nullable();
                    $table->string('transaction_id')->nullable();
                    $table->string('order_id')->nullable();
                    $table->string('currency', 10)->default('BDT');
                    $table->timestamps();
                });
            }

            $orderPayment = new Transaction();
            $orderPayment->main_order_id = $checkOrder->id;
            $orderPayment->name = $paymentInfo['customer_name'] ?? null;
            $orderPayment->email = $paymentInfo['email'] ?? null;
            $orderPayment->method = 'shatajpay';
            $orderPayment->phone = $paymentInfo['customer_phone'] ?? null;
            $orderPayment->amount = $paymentInfo['amount'] ?? 0;
            $orderPayment->status = 'pending';
            $orderPayment->address = $paymentInfo['customer_address'] ?? null;
            $orderPayment->transaction_id = $paymentInfo['trans_id'] ?? null;
            $orderPayment->order_id = $paymentInfo['order_id'] ?? null;
            $orderPayment->currency = $paymentInfo['currency'] ?? 'BDT';

            if (!$orderPayment->save()) {
                return back()->with('shataj_status', 'Payment creation failed! Record was not saved.');
            }

            return $this->createPayment($orderPayment);
        } catch (\Throwable $th) {
            return back()->with('shataj_status', 'Payment creation failed.');
        }
    }

    private function getShatajPayCredentials()
    {
        $gatewayCredential = PaymentGateway::where('type', 'shatajpay')
            ->where('status', 1)
            ->first();

        if (!$gatewayCredential) {
            return null;
        }

        $credentials = config('services.shatajpay');

        if (!is_array($credentials)) {
            return null;
        }

        $requiredCredentials = [
            'gateway_key',
            'app_secret',
            'app_key',
            'username',
            'password',
            'getway_mode',
        ];

        foreach ($requiredCredentials as $key) {
            if (!array_key_exists($key, $credentials) || empty($credentials[$key])) {
                return null;
            }
        }

        return [
            'gateway' => $gatewayCredential,
            'gateway_key' => $credentials['gateway_key'],
            'app_secret' => $credentials['app_secret'],
            'app_key' => $credentials['app_key'],
            'username' => $credentials['username'],
            'password' => $credentials['password'],
            'getway_mode' => $credentials['getway_mode'],
        ];
    }

    private function createPayment($orderPayment)
    {
        if (!$orderPayment) {
            return back()->with('shataj_status', 'Payment information not found.');
        }

        $shatajPay = $this->getShatajPayCredentials();
        $gateway = $shatajPay['gateway'];

        if (!$shatajPay || $gateway->status == false) {
            return back()->with('shataj_status', 'Oops! Shataj Pay gateway is inactive or credentials are not configured properly.');
        }


        if ($shatajPay['getway_mode'] !== 'production') {
            return back()->with('shataj_status', 'Your API must be live.');
        }

        $apiUrl = 'http://pay.shataj.com/api/v1/user/payment-system';

        $headers = [
            'password' => $shatajPay['password'],
            'username' => $shatajPay['username'],
            'appkey' => $shatajPay['app_key'],
            'appsecret' => $shatajPay['app_secret'],
            'sandbox' => 1,
            'gatewayid' => $shatajPay['gateway_key'],
        ];

        $body = [
            'amount' => $orderPayment->amount,
            'currency' => $orderPayment->currency ?? 'BDT',
            'name' => $orderPayment->name,
            'email' => $orderPayment->email,
            'mobile' => $orderPayment->phone,
            'Address' => $orderPayment->address,
            'opt_value_a' => '',
            'opt_value_b' => '',
            'transactionId' => $orderPayment->transaction_id,
            'orderId' => $orderPayment->order_id,
        ];

        try {
            $response = Http::timeout(10)
                ->withHeaders($headers)
                ->post($apiUrl, $body);

            if (!$response->successful()) {
                return back()->with('shataj_status', 'Payment failed: ' . $response->body());
            }

            $responseData = $response->json();

            if (
                ($responseData['status'] ?? false) === true &&
                ($responseData['status_code'] ?? null) === 200
            ) {
                $redirectUrl = $responseData['redirect_url'] ?? null;

                if (!empty($redirectUrl)) {
                    return redirect()->to($redirectUrl);
                }

                return back()->with('shataj_status', 'No redirection URL provided by the API.');
            }

            return back()->with(
                'shataj_status',
                $responseData['message'] ?? 'Payment gateway rejected the request.'
            );
        } catch (\Throwable $e) {
            return back()->with('shataj_status', 'An error occurred while processing payment.');
        }
    }


    public function paymentCallbackUrl(Request $request)
    {
        try {
            $status = $request->input('status');
            $orderId = $request->input('orderId');

            if (!$orderId || !$status) {
                Toastr::error('Invalid payment information', 'Failed!');
                return redirect()->route('customer.order_success', $orderId ?? 0);
            }

            $transaction = Transaction::where('order_id', $orderId)->where('status', 'pending')->first();
            $order = Order::find($transaction->main_order_id);

            if (!$order) {
                Toastr::error('Order not found', 'Failed!');
                return redirect()->route('customer.order_success', $orderId);
            }

            $redirectRoute = ($order->reseller_profit)
                ? 'reseller.order.success'
                : 'customer.order_success';

            if ($status === 'Complete') {

                $transaction->update([
                    'status' => 'completed',
                ]);

                if ($order->payment_status === 'paid') {

                    Toastr::success(
                        'Your payment has already been completed',
                        'Success!'
                    );

                    return redirect()->route(
                        $redirectRoute,
                        $order->id
                    );
                }

                DB::transaction(function () use ($order, $request) {

                    $order = Order::where('id', $order->id)
                        ->lockForUpdate()
                        ->first();

                    if (!$order) {
                        throw new \Exception('Order not found.');
                    }

                    if ($order->payment_status === 'paid') {
                        return;
                    }

                    $order->order_status = 1;
                    $order->payment_status = 'paid';
                    $order->payment_gateway='shatajpay';
                    $order->save();

                    $payment = Payment::where(
                        'order_id',
                        $order->id
                    )->lockForUpdate()->first();

                    if ($payment) {

                        /*
                    | Your gateway may return trxId / transactionId.
                    | Adjust the field name according to your gateway response.
                    */
                        $trxId = $request->input('trxId')
                            ?? $request->input('trxID')
                            ?? $request->input('transactionId')
                            ?? $request->input('paymentID')
                            ?? $payment->trx_id;

                        $payment->trx_id = $trxId;
                        $payment->payment_status = 'paid';

                        if ($request->filled('amount')) {

                            $payment->amount = $request->input('amount');
                        } elseif (Session::has('payable_amount')) {

                            $payment->amount = Session::get('payable_amount');
                        } elseif ($order->customer_payable_amount) {

                            $payment->amount = $order->customer_payable_amount;
                        } else {

                            $payment->amount = $order->amount;
                        }

                        $payment->save();
                    }
                });

                $this->createDigitalDownloads($order);

                try {
                    $customer = Customer::find($order->customer_id);
                    $userData = [];
                    if ($customer && $customer->email) {

                        $userData['email'] = $customer->email;
                    } elseif ($customer && $customer->phone) {

                        $userData['phone'] = $customer->phone;
                    }

                    $shipping = \App\Models\Shipping::where(
                        'order_id',
                        $order->id
                    )->first();

                    if (
                        empty($userData['phone']) &&
                        $shipping &&
                        $shipping->phone
                    ) {

                        $userData['phone'] = $shipping->phone;
                    }

                    if (isset($_COOKIE['_fbp'])) {
                        $userData['fbp'] = $_COOKIE['_fbp'];
                    }

                    if (isset($_COOKIE['_fbc'])) {
                        $userData['fbc'] = $_COOKIE['_fbc'];
                    }

                    register_shutdown_function(
                        function () use ($order, $userData) {

                            try {

                                $payment = Payment::where(
                                    'order_id',
                                    $order->id
                                )->first();

                                $orderDetails =
                                    $order->orderdetails
                                    ?? \App\Models\Order::with('orderdetails')
                                    ->find($order->id)?->orderdetails
                                    ?? collect();

                                $contentIds = $orderDetails
                                    ->pluck('product_id')
                                    ->map(
                                        fn($id) => (string) $id
                                    )
                                    ->values()
                                    ->toArray();

                                $contents = $orderDetails
                                    ->map(
                                        fn($item) => [
                                            'id' => (string) $item->product_id,
                                            'quantity' => (int) $item->qty,
                                            'item_price' => (float) $item->sale_price,
                                        ]
                                    )
                                    ->values()
                                    ->toArray();

                                app(
                                    \App\Services\FacebookCapiService::class
                                )->sendEvent(
                                    'Purchase',
                                    [
                                        'currency' => 'BDT',
                                        'value' => (float) (
                                            $payment->amount
                                            ?? $order->amount
                                        ),
                                        'order_id' =>
                                        $order->invoice_id
                                            ?? $order->id,
                                        'content_ids' => $contentIds,
                                        'contents' => $contents,
                                        'num_items' => count($contents),
                                        'content_type' => 'product',
                                    ],
                                    $userData,
                                    [
                                        'event_id' =>
                                        'purchase_' .
                                            (
                                                $order->invoice_id
                                                ?? $order->id
                                            ),

                                        'event_source_url' =>
                                        request()->fullUrl(),
                                    ]
                                );
                            } catch (\Throwable $e) {

                                \Log::error(
                                    'Facebook CAPI Purchase event failed for order ' .
                                        $order->id .
                                        ': ' .
                                        $e->getMessage()
                                );
                            }
                        }
                    );
                } catch (\Throwable $e) {

                    \Log::error(
                        'Facebook CAPI setup failed for order ' .
                            $order->id .
                            ': ' .
                            $e->getMessage()
                    );
                }

                Session::forget('payable_amount');

                Toastr::success(
                    'Thanks, Your payment successfully done',
                    'Success!'
                );

                return redirect()->route(
                    $redirectRoute,
                    $order->id
                );
            }

            if ($status === 'Failed') {
                $transaction->update([
                    'status' => 'failed',
                ]);

                DB::transaction(function () use ($order) {

                    $order = Order::where('id', $order->id)
                        ->lockForUpdate()
                        ->first();

                    if (!$order) {
                        return;
                    }

                    if ($order->payment_status === 'paid') {
                        return;
                    }

                    $payment = Payment::where(
                        'order_id',
                        $order->id
                    )->lockForUpdate()->first();

                    if ($payment) {
                        $payment->payment_status = 'failed';
                        $payment->save();
                    }
                });

                Session::forget('payable_amount');

                Toastr::error(
                    'Opps, Your payment failed',
                    'Failed!'
                );

                return redirect()->route(
                    $redirectRoute,
                    $order->id
                );
            }

            Toastr::error(
                'Unknown payment status',
                'Failed!'
            );

            return redirect()->route(
                $redirectRoute,
                $order->id
            );
        } catch (\Throwable $e) {

            \Log::error(
                'Payment callback failed',
                [
                    'message' => $e->getMessage(),
                    'request' => $request->all(),
                ]
            );

            $orderId = $request->input('orderId');

            if ($orderId) {

                $order = Order::find($orderId);

                $redirectRoute = ($order && $order->reseller_profit)
                    ? 'reseller.order.success'
                    : 'customer.order_success';

                Toastr::error(
                    'Something went wrong while processing your payment',
                    'Payment Error!'
                );

                return redirect()->route(
                    $redirectRoute,
                    $orderId
                );
            }

            Toastr::error(
                'Something went wrong while processing your payment',
                'Payment Error!'
            );

            return redirect()->route(
                'customer.order_success',
                0
            );
        }
    }

    private function createDigitalDownloads(Order $order)
    {
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
    private function setSuccessSession($participantId)
    {
        session()->put([
            'seminar_participant_id' => $participantId,
            'seminar_success_expires_at' => now()->addMinute()->timestamp,
        ]);

        session()->forget([
            'seminar_failed_transaction_id',
            'seminar_failed_expires_at',
        ]);
    }

    private function setFailedSession($transactionId)
    {
        session()->put([
            'seminar_failed_transaction_id' => $transactionId,
            'seminar_failed_expires_at' => now()->addMinute()->timestamp,
        ]);

        session()->forget([
            'seminar_participant_id',
            'seminar_success_expires_at',
        ]);
    }

    private function sendConfirmMessage($UserPhoneNumber, $totalAmount)
    {
        $message = "Registration successful! Your payment of {$totalAmount}/- TK has been received.";
        $phoneNumber = "+88" . $UserPhoneNumber;

        $response = Http::get('https://sms.shataj.com/services/send.php', [
            'key' => '03e568ed96136a3c7e5d93f9e5aa9ca93f579400',
            'number' => $phoneNumber,
            'message' => $message,
            'type' => 'sms',
            'prioritize' => 0,
        ]);
    }
}
