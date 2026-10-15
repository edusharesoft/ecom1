<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HandlesCheckoutOtp;
use App\Models\ResellerLandingPage;
use App\Services\FacebookCapiService;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Shipping;
use App\Models\Payment;
use App\Models\ShippingCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Brian2694\Toastr\Facades\Toastr;

class LandingOrderController extends Controller
{
    use HandlesCheckoutOtp;

    protected function getLanding(string $slug)
    {
        $landing = ResellerLandingPage::where('slug', $slug)->where('is_active', 1)->first();
        if (!$landing) {
            abort(404);
        }
        return $landing;
    }

    protected function getCartKey(string $slug): string
    {
        return 'landing_cart_' . $slug;
    }

    protected function getProductPrice($landing, $product): float
    {
        $lp = $landing->landingProducts()->where('product_id', $product->id)->first();
        if ($lp && $lp->pivot && $lp->pivot->custom_price > 0) {
            return (float) $lp->pivot->custom_price;
        }
        return (float) ($product->reseller_price ?? 0);
    }

    public function addToCart(Request $request, string $slug)
    {
        $landing = $this->getLanding($slug)->load('landingProducts');

        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'qty' => 'required|integer|min:1|max:50',
        ]);

        $productIds = $landing->landingProducts()->pluck('product_id')->toArray();
        if (empty($productIds)) {
            Toastr::error('প্রোডাক্ট যোগ করুন প্রথমে ল্যান্ডিং পেজে।', 'Error');
            return redirect()->back();
        }

        $product = Product::where('id', $request->product_id)
            ->whereIn('id', $productIds)
            ->where('status', 1)
            ->first();

        if (!$product) {
            Toastr::error('প্রোডাক্টটি অর্ডারযোগ্য নয়। ল্যান্ডিং পেজে যোগ করুন।', 'Error');
            return redirect()->back();
        }

        $cart = session($this->getCartKey($slug), []);
        $pid = (string) $product->id;
        $qty = (int) $request->qty;
        $cart[$pid] = ($cart[$pid] ?? 0) + $qty;
        if ($cart[$pid] > 50) {
            $cart[$pid] = 50;
        }
        session([$this->getCartKey($slug) => $cart]);

        Toastr::success('কার্টে যোগ হয়েছে', 'Success');
        return redirect(landing_url($slug, 'order'));
    }

    public function removeFromCart(string $slug, int $productId)
    {
        $this->getLanding($slug);
        $cart = session($this->getCartKey($slug), []);
        unset($cart[(string) $productId]);
        session([$this->getCartKey($slug) => $cart]);
        return redirect(landing_url($slug, 'order'));
    }

    public function orderForm(string $slug)
    {
        $landing = $this->getLanding($slug)->load('landingProducts');

        $cart = session($this->getCartKey($slug), []);
        if (empty($cart)) {
            Toastr::warning('আপনার কার্ট খালি। অর্ডার করতে প্রথমে প্রোডাক্ট যোগ করুন।', 'Warning');
            return redirect(landing_url($slug, ''));
        }

        $landingProductIds = $landing->landingProducts()->pluck('product_id')->toArray();
        $productIds = array_intersect(array_map('intval', array_keys($cart)), $landingProductIds);
        if (empty($productIds)) {
            session()->forget($this->getCartKey($slug));
            Toastr::error('কার্টের প্রোডাক্ট আর ল্যান্ডিং পেজে নেই।', 'Error');
            return redirect(landing_url($slug, ''));
        }

        $products = Product::whereIn('id', $productIds)
            ->where('status', 1)
            ->with('image')
            ->get()
            ->keyBy('id');

        $cartItems = [];
        $subtotal = 0;
        foreach ($cart as $pid => $qty) {
            $product = $products->get((int) $pid);
            if (!$product) continue;
            $price = $this->getProductPrice($landing, $product);
            if ($price <= 0) continue;
            $cartItems[] = (object) [
                'product' => $product,
                'qty' => (int) $qty,
                'price' => $price,
                'total' => $price * (int) $qty,
            ];
            $subtotal += $price * (int) $qty;
        }

        if (empty($cartItems)) {
            session()->forget($this->getCartKey($slug));
            Toastr::error('কোনো প্রোডাক্ট অর্ডারযোগ্য নয়', 'Error');
            return redirect(landing_url($slug, ''));
        }

        $shippingcharge = ShippingCharge::where('status', 1)->get();
        $defaultShipping = $shippingcharge->first();
        $defaultShippingAmount = $defaultShipping ? (float) $defaultShipping->amount : 0;

        return view('reseller.landing.order', compact(
            'landing',
            'cartItems',
            'subtotal',
            'shippingcharge',
            'defaultShippingAmount'
        ));
    }

    public function storeOrder(Request $request, string $slug)
    {
        $landing = $this->getLanding($slug);

        $cart = session($this->getCartKey($slug), []);
        if (empty($cart)) {
            Toastr::error('আপনার কার্ট খালি', 'Error');
            return redirect()->route('reseller.landing.public', $slug);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'area' => 'required',
            'payment_method' => 'required|in:cod,bkash,shurjopay,uddoktapay,aamarpay',
        ]);

        $landingOtpChannel = 'landing_'.md5($slug);
        $otpRedirect = $this->checkoutOtpGate($request, $landingOtpChannel);
        if ($otpRedirect !== null) {
            return $otpRedirect;
        }

        $landing = $landing->load('landingProducts');
        $landingProductIds = $landing->landingProducts()->pluck('product_id')->toArray();
        $productIds = array_intersect(array_map('intval', array_keys($cart)), $landingProductIds);
        $products = Product::whereIn('id', $productIds)->where('status', 1)->get()->keyBy('id');

        $subtotal = 0;
        $orderDetailsData = [];
        foreach ($cart as $pid => $qty) {
            $product = $products->get((int) $pid);
            if (!$product) continue;
            $price = $this->getProductPrice($landing, $product);
            if ($price <= 0) continue;
            $q = (int) $qty;
            $subtotal += $price * $q;
            $orderDetailsData[] = [
                'product' => $product,
                'qty' => $q,
                'price' => $price,
            ];
        }

        $totalCostPrice = 0;
        foreach ($orderDetailsData as $item) {
            $totalCostPrice += ($item['product']->reseller_price ?? 0) * $item['qty'];
        }
        $resellerProfit = max(0, $subtotal - $totalCostPrice);

        if (empty($orderDetailsData)) {
            session()->forget($this->getCartKey($slug));
            Toastr::error('কোনো প্রোডাক্ট অর্ডারযোগ্য নয়', 'Error');
            return redirect(landing_url($slug, ''));
        }

        $shippingfee = 0;
        if ($request->area && $request->area !== 'free_shipping') {
            $shippingArea = ShippingCharge::find($request->area);
            if ($shippingArea) {
                $shippingfee = (float) $shippingArea->amount;
            }
        }

        $grandTotal = $subtotal + $shippingfee;

        $customer = Customer::where('phone', $request->phone)->first();
        if (!$customer) {
            $customer = Customer::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name . '-' . rand(100, 999)),
                'phone' => $request->phone,
                'password' => bcrypt(Str::random(8)),
                'verify' => 1,
                'status' => 'active',
            ]);
        }

        $rawSource = $request->input('traffic_source', session('order_traffic_source', 'direct'));
        $rawReferrer = $request->input('traffic_referrer', session('order_traffic_referrer', ''));

        $order = Order::create([
            'invoice_id' => rand(11111, 99999),
            'amount' => $grandTotal,
            'shipping_charge' => $shippingfee,
            'customer_id' => $customer->id,
            'order_status' => 1,
            'note' => $request->note ?? null,
            'order_note' => $request->order_note ?? null,
            'payment_status' => 'pending',
            'discount' => 0,
            'ip_address' => $request->ip(),
            'traffic_source' => \App\Support\TrafficSourceDetector::normalize($rawSource),
            'traffic_referrer' => \App\Support\TrafficSourceDetector::clip((string) $rawReferrer),
            'user_id' => $landing->user_id,
            'customer_payable_amount' => $grandTotal,
            'reseller_profit' => $resellerProfit,
        ]);

        foreach ($orderDetailsData as $item) {
            OrderDetails::create([
                'order_id' => $order->id,
                'product_id' => $item['product']->id,
                'product_name' => $item['product']->name,
                'purchase_price' => $item['product']->purchase_price ?? 0,
                'sale_price' => $item['price'],
                'product_discount' => 0,
                'qty' => $item['qty'],
            ]);
        }

        $shippingArea = ShippingCharge::find($request->area);
        Shipping::create([
            'order_id' => $order->id,
            'customer_id' => $customer->id,
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'area' => $shippingArea ? $shippingArea->name : 'Free Shipping',
        ]);

        Payment::create([
            'order_id' => $order->id,
            'customer_id' => $customer->id,
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
            'amount' => $request->payment_method === 'cod' ? $grandTotal : 0,
        ]);

        foreach ($orderDetailsData as $item) {
            $product = $item['product'];
            if ($product->stock >= $item['qty']) {
                $product->stock -= $item['qty'];
                $product->save();
            }
        }

        session()->forget($this->getCartKey($slug));

        // Facebook Conversion API (reseller landing)
        $this->sendLandingPurchaseCapi($landing, $order, $request->phone, $request->name);

        if (in_array($request->payment_method, ['bkash', 'shurjopay', 'uddoktapay', 'aamarpay'])) {
            session()->put('landing_order_id', $order->id);
            return redirect(landing_url($slug, 'order'))
                ->with('payment_redirect', true)
                ->with('order_id', $order->id);
        }

        Toastr::success('অর্ডার সফল হয়েছে!', 'Success');
        return redirect(landing_url($slug, 'order/success/' . $order->id));
    }

    public function orderSuccess(string $slug, int $orderId)
    {
        $landing = $this->getLanding($slug);
        $order = Order::with(['orderdetails.product', 'shipping', 'payment'])->findOrFail($orderId);

        if ($order->user_id != $landing->user_id) {
            abort(404);
        }

        return view('reseller.landing.order-success', compact('landing', 'order'));
    }

    public function orderTrack(string $slug)
    {
        $landing = $this->getLanding($slug);
        return view('reseller.landing.order-track', compact('landing'));
    }

    public function orderTrackResult(Request $request, string $slug)
    {
        $landing = $this->getLanding($slug);
        $phone = $request->phone;
        $invoice_id = $request->invoice_id;

        if (!$phone && !$invoice_id) {
            Toastr::error('অনুগ্রহ করে মোবাইল নাম্বার অথবা ইনভয়েস আইডি দিন', 'Error');
            return redirect()->route('reseller.landing.order-track', $slug);
        }

        $query = Order::where('user_id', $landing->user_id);

        if ($invoice_id) {
            $query->where('invoice_id', $invoice_id);
        }
        if ($phone) {
            $query->whereHas('shipping', function ($q) use ($phone) {
                $q->where('phone', $phone);
            });
        }

        $orders = $query->with(['shipping', 'status', 'orderdetails'])->latest()->get();

        if ($orders->isEmpty()) {
            Toastr::error('দুঃখিত! কোনো অর্ডার পাওয়া যায়নি।', 'Failed');
            return redirect()->route('reseller.landing.order-track', $slug);
        }

        return view('reseller.landing.order-track-result', compact('landing', 'orders'));
    }

    protected function sendLandingPurchaseCapi($landing, $order, string $phone, string $name): void
    {
        if (empty($landing->facebook_pixel_id) || empty($landing->facebook_capi_access_token)) {
            return;
        }
        $capi = app(FacebookCapiService::class);
        $contentIds = $order->orderdetails->pluck('product_id')->map(fn ($id) => (string) $id)->toArray();
        $numItems = (int) $order->orderdetails->sum('qty');
        $userData = [
            'phone' => $phone,
            'first_name' => $name,
        ];
        if (isset($_COOKIE['_fbp'])) {
            $userData['fbp'] = $_COOKIE['_fbp'];
        }
        if (isset($_COOKIE['_fbc'])) {
            $userData['fbc'] = $_COOKIE['_fbc'];
        }
        $capi->sendEventWithCredentials(
            trim($landing->facebook_pixel_id),
            trim($landing->facebook_capi_access_token),
            'Purchase',
            [
                'value' => (float) $order->amount,
                'currency' => 'BDT',
                'order_id' => (string) $order->invoice_id,
                'content_ids' => $contentIds,
                'num_items' => $numItems,
            ],
            $userData,
            ['event_id' => 'landing_' . $order->id . '_' . $order->invoice_id]
        );
    }

    public function resendCheckoutOtp(Request $request, string $slug)
    {
        $this->getLanding($slug);
        $request->validate(['phone' => 'required|string']);
        $channel = 'landing_'.md5($slug);

        return $this->checkoutOtpResendForChannel($request, $channel);
    }
}
