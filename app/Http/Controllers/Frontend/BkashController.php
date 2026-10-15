<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Models\DigitalDownload;
use App\Models\Customer;
use App\Services\FacebookCapiService;
use Session;
use Toastr;
use Illuminate\Support\Str;

class BkashController extends Controller
{
    private $base_url;
    private $app_key;
    private $app_secret;
    private $username;
    private $password;
    protected $facebookCapiService;

    public function __construct(FacebookCapiService $facebookCapiService)
    {
        $this->facebookCapiService = $facebookCapiService;
        // 👇 সমস্যা ১: আপনার PaymentGateway টেবিলে যদি 'status' কলাম না থাকে, তাহলে এটি 'active' বা 'is_active' হতে পারে। 
        // তবে সাধারণত এটি 'status' থাকে। যদি এখানে এরর দেয়, তবে কলাম নাম চেক করুন।
        $bkash_gateway = PaymentGateway::where(['status'=> 1, 'type'=>'bkash'])->first();
        
        if($bkash_gateway) {
            $this->base_url  = $bkash_gateway->base_url;
            $this->app_key   = $bkash_gateway->app_key;
            $this->app_secret= $bkash_gateway->app_secret;
            $this->username  = $bkash_gateway->username;
            $this->password  = $bkash_gateway->password;
        } else {
            // স্যান্ডবক্স ক্রেডেনশিয়াল (ডেভেলপমেন্টের জন্য)
            $this->base_url  = 'https://tokenized.pay.bka.sh/v1.2.0-beta';
            $this->app_key   = ''; 
            $this->app_secret= '';
            $this->username  = '';
            $this->password  = '';
        }
    }

    public function authHeaders(){
        return [
            'Content-Type:application/json',
            'Authorization:' . $this->grant(),
            'X-APP-Key:'.$this->app_key
        ];
    }
          
    public function curlWithBody($url,$header,$method,$body_data_json){
        $curl = curl_init($this->base_url.$url);
        curl_setopt($curl,CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl,CURLOPT_POSTFIELDS, $body_data_json);
        curl_setopt($curl,CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function grant()
    {
        $header = [
            'Content-Type:application/json',
            'username:'.$this->username,
            'password:'.$this->password
        ];

        $body_data = ['app_key'=> $this->app_key, 'app_secret'=>$this->app_secret];
        $body_data_json = json_encode($body_data);

        $response = $this->curlWithBody('/tokenized/checkout/token/grant',$header,'POST',$body_data_json);
        
        // টোকেন এরর হ্যান্ডলিং
        $decoded_response = json_decode($response);
        if(isset($decoded_response->id_token)) {
            return $decoded_response->id_token;
        } else {
            return null; 
        }
    }

    public function create(Request $request)
    {      
        $order = Order::where('id',$request->order_id)->firstOrFail();

        if (Session::has('payable_amount') && Session::get('payable_amount') > 0) {
            $amount = Session::get('payable_amount');
        } elseif ($order->customer_payable_amount) {
            // Reseller order: use customer_payable_amount (includes shipping charge)
            $amount = $order->customer_payable_amount;
        } else {
            $amount = $order->amount;
        }

        $orderId = $order->id;
        $token = $this->grant();
        
        if(!$token){
             Toastr::error('Invalid API Credentials', 'Error!');
             return redirect()->back();
        }

        $header = [
            'Content-Type:application/json',
            'Authorization:' . $token,
            'X-APP-Key:'.$this->app_key
        ];

        $baseUrl = rtrim(env('APP_URL'), '/');
        $callbackURL = $baseUrl . '/bkash/checkout-url/callback?orderId=' . $orderId;

        $body_data = [
            'mode'              => '0011',
            'payerReference'    => ' ',
            'callbackURL'       => $callbackURL,
            'amount'            => $amount,
            'currency'          => 'BDT',
            'intent'            => 'sale',
            'merchantInvoiceNumber' => "Inv" . Str::random(10),
        ];

        $body_data_json = json_encode($body_data);

        $response = $this->curlWithBody('/tokenized/checkout/create',$header,'POST',$body_data_json);
        $res = json_decode($response);

        if (isset($res->paymentID) && isset($res->bkashURL)) {
            Session::forget('paymentID');
            Session::put('paymentID', $res->paymentID);
            return redirect($res->bkashURL);
        } else {
            Toastr::error('bKash payment create failed', 'Error!');
            return redirect()->back();
        }
    }

    public function execute($paymentID)
    {
        $header =$this->authHeaders();
        $body_data = [
            'paymentID' => $paymentID
        ];
        $body_data_json=json_encode($body_data);
        $response = $this->curlWithBody('/tokenized/checkout/execute',$header,'POST',$body_data_json);
        return $response;
    }

    public function query($paymentID)
    {
        $header =$this->authHeaders();
        $body_data = [
            'paymentID' => $paymentID,
        ];
        $body_data_json=json_encode($body_data);
        $response = $this->curlWithBody('/tokenized/checkout/payment/status',$header,'POST',$body_data_json);
        return $response;
    }

    public function callback(Request $request)
    {
        $allRequest = $request->all();
        
        if(isset($allRequest['status']) && $allRequest['status'] == 'failure'){
            Toastr::error('Opps, Your bkash payment failed', 'Failed!');
            $order = Order::find($allRequest['orderId']);
            $redirectRoute = ($order && $order->reseller_profit) ? 'reseller.order.success' : 'customer.order_success';
            return redirect()->route($redirectRoute, $allRequest['orderId']);

        }else if(isset($allRequest['status']) && $allRequest['status'] == 'cancel'){
            Toastr::error('Opps, Your bkash payment cancelled', 'Cancelled!');
            $order = Order::find($allRequest['orderId']);
            $redirectRoute = ($order && $order->reseller_profit) ? 'reseller.order.success' : 'customer.order_success';
            return redirect()->route($redirectRoute, $allRequest['orderId']);

        }else{
            
            $response = $this->execute($allRequest['paymentID']);
            $arr = json_decode($response,true);

            if(array_key_exists("statusCode",$arr) && $arr['statusCode'] != '0000'){
                Toastr::error('Opps, Your bkash payment failed', 'Failed!');
                $order = Order::find($allRequest['orderId']);
                $redirectRoute = ($order && $order->reseller_profit) ? 'reseller.order.success' : 'customer.order_success';
                return redirect()->route($redirectRoute, $allRequest['orderId']);

            }else if(array_key_exists("message",$arr)){
                sleep(1);
                $queryResponse = $this->query($allRequest['paymentID']);
                $order = Order::find($allRequest['orderId']);
                $redirectRoute = ($order && $order->reseller_profit) ? 'reseller.order.success' : 'customer.order_success';
                return redirect()->route($redirectRoute, $allRequest['orderId']);
            }
            
            // Payment Successful Logic
            $queryResponse = $this->query($allRequest['paymentID']);
            
            // ✅ ফিক্স ১: Order Status Update
            $order = Order::where('id',$allRequest['orderId'])->first();
            
            // আপনার ডাটাবেসে 'status' কলাম নেই, তাই 'order_status' ব্যবহার করা হয়েছে।
            // যদি 'order_status' কলামও না থাকে, তবে নিচের লাইনটি কমেন্ট করে দিন।
if($order) {
    // আপনার ডাটাবেসের order_statuses টেবিলে দেখুন Pending এর ID কত। সাধারণত 1 হয়।
    $order->order_status = 1; // ✅ সঠিক: সংখ্যা বসানো হয়েছে
    $order->payment_status = 'paid';
    $order->save();
}
            
            // ✅ ফিক্স ২: Payment Update
            $payment = Payment::where('order_id',$allRequest['orderId'])->first();
            
            if($payment){
                $payment->trx_id = $allRequest['paymentID']; // এখানে bKash এর trxID ($arr['trxID']) রাখলে ভালো হয়
                if(isset($arr['trxID'])) {
                    $payment->trx_id = $arr['trxID'];
                }
                
                $payment->payment_status = 'paid';
                
                if(isset($arr['amount'])){
                    $payment->amount = $arr['amount'];
                } elseif (Session::has('payable_amount')) {
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

            // Digital Download Create
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
                $shipping = \App\Models\Shipping::where('order_id', $order->id)->first();
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
                        \Log::error('Facebook CAPI Purchase event failed for order ' . $order->id . ': ' . $e->getMessage());
                    }
                });
            } catch (\Exception $e) {
                \Log::error('Facebook CAPI setup failed for order ' . $order->id . ': ' . $e->getMessage());
            }
            
            Session::forget('payable_amount'); 
            
            Toastr::success('Thanks, Your bkash payment successfully done', 'Success!');
            $order = Order::find($allRequest['orderId']);
            $redirectRoute = ($order && $order->reseller_profit) ? 'reseller.order.success' : 'customer.order_success';
            return redirect()->route($redirectRoute, $allRequest['orderId']);
        }
    }
 
    // Refund methods remain same...
    public function getRefund(Request $request)
    {
        return view('CheckoutURL.refund');
    }

    public function refund(Request $request)
    {
        $header =$this->authHeaders();
        $body_data = [
            'paymentID' => $request->paymentID,
            'amount' =>  $request->amount,
            'trxID' => $request->trxID,
            'sku' => 'sku',
            'reason' => 'Quality issue'
        ];
       
        $body_data_json=json_encode($body_data);
        $response = $this->curlWithBody('/tokenized/checkout/payment/refund',$header,'POST',$body_data_json);

        return view('CheckoutURL.refund')->with([
            'response' => $response,
        ]);
    }
    
    public function getRefundStatus(Request $request)
    {
        return view('CheckoutURL.refund-status');
    }

    public function refundStatus(Request $request)
    {       
        Session::forget('bkash_token');  
        $token = $this->grant();
        Session::put('bkash_token', $token);

        $header =$this->authHeaders();

        $body_data = [
            'paymentID' => $request->paymentID,
            'trxID' => $request->trxID,
        ];
        $body_data_json = json_encode($body_data);

        $response = $this->curlWithBody('/tokenized/checkout/payment/refund',$header,'POST',$body_data_json);
                
        return view('CheckoutURL.refund-status')->with([
            'response' => $response,
        ]);
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
}