<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Cart as CartModel;
use App\Models\Shipping;
use App\Models\Payment;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Support\DeliveryLocation;
use App\Helpers\OrderHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Get Orders List
     * 
     * GET /api/v1/mobile/orders
     */
    public function index(Request $request)
    {
        $customer = $request->user();
        
        $query = Order::where('customer_id', $customer->id)
            ->with(['orderdetails.product.image', 'status', 'payment', 'shipping'])
            ->orderBy('id', 'desc');

        // Filter by status
        if ($request->has('status')) {
            $query->where('order_status', $request->status);
        }

        // Pagination
        $perPage = $request->get('per_page', 10);
        $orders = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Orders retrieved successfully',
            'data' => [
                'orders' => $orders->map(function($order) {
                    return $this->formatOrder($order);
                }),
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                ]
            ]
        ], 200);
    }

    /**
     * Get Order Details
     * 
     * GET /api/v1/mobile/orders/{id}
     */
    public function show($id, Request $request)
    {
        $customer = $request->user();
        
        $order = Order::where('id', $id)
            ->where('customer_id', $customer->id)
            ->with(['orderdetails.product.image', 'status', 'payment', 'shipping'])
            ->firstOrFail();

        return response()->json([
            'status' => 'success',
            'message' => 'Order retrieved successfully',
            'data' => $this->formatOrder($order, true)
        ], 200);
    }

    /**
     * Create Order
     * 
     * POST /api/v1/mobile/orders
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'payment_method' => 'required|in:cod,bkash,shurjopay,uddoktapay,aamarpay',
            'note' => 'nullable|string|max:500',
            'order_note' => 'nullable|string|max:500',
            'division_id' => 'nullable|integer|exists:divisions,id',
            'district_id' => 'nullable|integer|exists:districts,id',
            'upazila_id' => 'nullable|integer|exists:upazilas,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $customer = $request->user();

        // Check cart
        $cartItems = CartModel::where('customer_id', $customer->id)->get();
        
        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your cart is empty'
            ], 400);
        }

        // Calculate totals
        $subtotal = $cartItems->sum(function($item) {
            return $item->price * $item->quantity;
        });

        // Check for digital products
        $hasDigital = false;
        foreach ($cartItems as $item) {
            if ($item->product && $item->product->is_digital) {
                $hasDigital = true;
                break;
            }
        }

        if ($hasDigital && $request->payment_method === 'cod') {
            return response()->json([
                'status' => 'error',
                'message' => 'Digital products cannot be purchased with Cash on Delivery. Please select online payment.'
            ], 400);
        }

        // Check for free delivery products
        $hasAllFreeDelivery = $cartItems->every(function ($item) {
            return $item->product && ($item->product->free_delivery ?? false);
        });

        $requiresPhysical = false;
        foreach ($cartItems as $item) {
            if ($item->product && ! $item->product->is_digital) {
                $requiresPhysical = true;
                break;
            }
        }

        $divisionId = null;
        $districtId = null;
        $upazilaId = null;
        $shippingfee = 0;

        if ($requiresPhysical && ! $hasAllFreeDelivery) {
            $locVal = Validator::make($request->all(), [
                'division_id' => 'required|integer|exists:divisions,id',
                'district_id' => 'required|integer|exists:districts,id',
                'upazila_id'  => 'required|integer|exists:upazilas,id',
            ]);

            if ($locVal->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $locVal->errors(),
                ], 422);
            }

            $divisionId = (int) $request->division_id;
            $districtId = (int) $request->district_id;
            $upazilaId = (int) $request->upazila_id;

            if (! DeliveryLocation::validateChain($divisionId, $districtId, $upazilaId)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid delivery location.',
                ], 400);
            }

            $shippingfee = DeliveryLocation::chargeForDistrictId($districtId);
        }

        // Discount (if coupon applied - can be added later)
        $discount = 0; // $request->discount ?? 0;

        // Grand total
        $grandTotal = ($subtotal + $shippingfee) - $discount;

        DB::beginTransaction();
        try {
            // Create order
            $order = Order::create([
                'invoice_id' => rand(11111, 99999),
                'amount' => $grandTotal,
                'shipping_charge' => $shippingfee,
                'customer_id' => $customer->id,
                'order_status' => 1, // Pending
                'note' => $request->note,
                'order_note' => $request->order_note,
                'payment_status' => 'pending',
                'discount' => $discount,
                'ip_address' => $request->ip(),
            ]);

            // Create shipping
            $shipping = Shipping::create([
                'order_id' => $order->id,
                'customer_id' => $customer->id,
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'division_id' => $divisionId,
                'district_id' => $districtId,
                'upazila_id' => $upazilaId,
                'area' => ($divisionId && $districtId && $upazilaId)
                    ? DeliveryLocation::shippingLabel($divisionId, $districtId, $upazilaId)
                    : 'Digital / Free Shipping',
            ]);

            // Create payment
            $payment = Payment::create([
                'order_id' => $order->id,
                'customer_id' => $customer->id,
                'payment_method' => $request->payment_method,
                'amount' => in_array($request->payment_method, ['bkash', 'shurjopay', 'uddoktapay', 'aamarpay']) ? 0 : $grandTotal,
                'payment_status' => 'pending',
            ]);

            // Create order details
            foreach ($cartItems as $cartItem) {
                OrderDetails::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'purchase_price' => $cartItem->product->purchase_price ?? null,
                    'sale_price' => $cartItem->price,
                    'qty' => $cartItem->quantity,
                    'product_color' => $cartItem->color_id,
                    'product_size' => $cartItem->size_id,
                ]);

                // Reduce stock
                $product = $cartItem->product;
                if ($product) {
                    $product->stock = max(0, $product->stock - $cartItem->quantity);
                    $product->save();
                }
            }

            // Clear cart
            CartModel::where('customer_id', $customer->id)->delete();

            DB::commit();

            // Load relationships
            $order->load(['orderdetails.product.image', 'status', 'payment', 'shipping']);

            return response()->json([
                'status' => 'success',
                'message' => 'Order placed successfully',
                'data' => [
                    'order' => $this->formatOrder($order, true),
                    'payment_url' => $this->getPaymentUrl($order, $request->payment_method),
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Order creation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Track Order
     * 
     * GET /api/v1/mobile/orders/track/{invoiceId}
     */
    public function track($invoiceId, Request $request)
    {
        $order = Order::where('invoice_id', $invoiceId)
            ->with(['orderdetails.product.image', 'status', 'payment', 'shipping'])
            ->first();

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Order retrieved successfully',
            'data' => $this->formatOrder($order, true)
        ], 200);
    }

    /**
     * Format Order Data
     */
    private function formatOrder($order, $detailed = false)
    {
        $data = [
            'id' => $order->id,
            'invoice_id' => $order->invoice_id,
            'amount' => $order->amount,
            'shipping_charge' => $order->shipping_charge,
            'discount' => $order->discount ?? 0,
            'grand_total' => $order->amount,
            'order_status' => [
                'id' => $order->order_status,
                'name' => $order->status ? $order->status->name : 'Pending',
            ],
            'payment_status' => $order->payment_status,
            'payment_method' => $order->payment ? $order->payment->payment_method : null,
            'created_at' => $order->created_at->format('Y-m-d H:i:s'),
            'shipping' => $order->shipping ? [
                'name' => $order->shipping->name,
                'phone' => $order->shipping->phone,
                'address' => $order->shipping->address,
                'area' => $order->shipping->area,
            ] : null,
        ];

        if ($detailed) {
            $data['items'] = $order->orderdetails->map(function($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'quantity' => $item->qty,
                    'price' => $item->sale_price,
                    'subtotal' => $item->qty * $item->sale_price,
                    'product_image' => $item->product && $item->product->image ? url($item->product->image->image) : null,
                ];
            });
        } else {
            $data['item_count'] = $order->orderdetails->count();
            $data['total_items'] = $order->orderdetails->sum('qty');
        }

        return $data;
    }

    /**
     * Get Payment URL for online payment
     */
    private function getPaymentUrl($order, $paymentMethod)
    {
        if (in_array($paymentMethod, ['bkash', 'shurjopay', 'uddoktapay', 'aamarpay'])) {
            $baseUrl = config('app.url');
            
            switch ($paymentMethod) {
                case 'bkash':
                    return $baseUrl . '/bkash/checkout-url/create?order_id=' . $order->id;
                case 'shurjopay':
                    return $baseUrl . '/shurjopay/checkout?order_id=' . $order->id;
                case 'uddoktapay':
                    return $baseUrl . '/uddoktapay/checkout?order_id=' . $order->id;
                case 'aamarpay':
                    return $baseUrl . '/aamarpay/checkout?order_id=' . $order->id;
            }
        }

        return null;
    }
}
