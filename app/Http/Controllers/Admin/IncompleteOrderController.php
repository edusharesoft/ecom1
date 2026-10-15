<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\IncompleteOrder;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Shipping;
use App\Support\DeliveryLocation;
use App\Support\IncompleteOrderPayload;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class IncompleteOrderController extends Controller
{
    public function index()
    {
        $orders = IncompleteOrder::latest()->paginate(25);

        return view('backEnd.incomplete_orders.index', compact('orders'));
    }

    /**
     * @deprecated ফ্রন্টএন্ড চেকআউট এখন FrontendController::storeIncompleteOrder ব্যবহার করে
     */
    public function store(Request $request)
    {
        return app(\App\Http\Controllers\Frontend\FrontendController::class)
            ->storeIncompleteOrder($request);
    }

    public function accept($id)
    {
        $incomplete = IncompleteOrder::findOrFail($id);

        DB::beginTransaction();

        try {
            $items = $incomplete->line_items;
            $meta  = $incomplete->checkout_meta;

            if ($items === []) {
                Toastr::error('এই ইনকমপ্লিট অর্ডারে কোন প্রোডাক্ট নেই!', 'Error');

                return redirect()->route('admin.incomplete-orders.index');
            }

            $subtotal = IncompleteOrderPayload::subtotalFromLineItems($items);
            $discount = (float) ($meta['discount'] ?? 0);

            $divisionId = ! empty($meta['division_id']) ? (int) $meta['division_id'] : null;
            $districtId = ! empty($meta['district_id']) ? (int) $meta['district_id'] : null;
            $upazilaId  = ! empty($meta['upazila_id']) ? (int) $meta['upazila_id'] : null;

            if ($divisionId && $districtId && $upazilaId
                && ! DeliveryLocation::validateChain($divisionId, $districtId, $upazilaId)) {
                $divisionId = $districtId = $upazilaId = null;
            }

            $shippingAmount = isset($meta['shipping_charge'])
                ? (float) $meta['shipping_charge']
                : ($districtId ? DeliveryLocation::chargeForDistrictId($districtId) : 0);

            $grandTotal = $incomplete->total_amount > 0
                ? (float) $incomplete->total_amount
                : max(0, $subtotal + $shippingAmount - $discount);

            $baseName  = $incomplete->name ?: 'Customer';
            $slugValue = Str::slug($baseName).'-'.rand(1000, 9999);
            $phone     = IncompleteOrderPayload::normalizePhone($incomplete->phone) ?: ($incomplete->phone ?: '00000000000');

            $customer = null;
            if ($phone !== '' && $phone !== '00000000000') {
                $customer = Customer::firstOrCreate(
                    ['phone' => $phone],
                    [
                        'name'     => $baseName,
                        'slug'     => $slugValue,
                        'password' => bcrypt((string) rand(111111, 999999)),
                        'verify'   => 1,
                        'status'   => 'active',
                    ]
                );
            }

            if (! $customer) {
                $customer = Customer::create([
                    'name'     => $baseName,
                    'slug'     => $slugValue,
                    'phone'    => $phone,
                    'password' => bcrypt((string) rand(111111, 999999)),
                    'verify'   => 1,
                    'status'   => 'active',
                ]);
            }

            $order                  = new Order();
            $order->invoice_id      = rand(11111, 99999);
            $order->amount          = $grandTotal;
            $order->discount        = $discount;
            $order->shipping_charge = $shippingAmount;
            $order->customer_id     = $customer->id;
            $order->order_status    = 1;
            $order->note            = null;
            $order->order_note      = $meta['order_note'] ?? null;
            $order->payment_status  = 'pending';
            $order->save();

            $shipping              = new Shipping();
            $shipping->order_id    = $order->id;
            $shipping->customer_id = $customer->id;
            $shipping->name        = $incomplete->name;
            $shipping->phone       = $phone;
            $shipping->address     = $incomplete->address;
            $shipping->division_id = $divisionId;
            $shipping->district_id = $districtId;
            $shipping->upazila_id  = $upazilaId;
            $shipping->area        = ($divisionId && $districtId && $upazilaId)
                ? DeliveryLocation::shippingLabel($divisionId, $districtId, $upazilaId)
                : ($meta['location_label'] ?? 'N/A');
            $shipping->save();

            $payment                 = new Payment();
            $payment->order_id       = $order->id;
            $payment->customer_id    = $customer->id;
            $payment->payment_method = 'Cash On Delivery';
            $payment->amount         = $grandTotal;
            $payment->payment_status = 'pending';
            $payment->save();

            foreach ($items as $item) {
                $productId = $item['id'] ?? null;
                $product   = $productId ? Product::find($productId) : null;
                $qty       = (int) ($item['qty'] ?? 1);

                $detail                   = new OrderDetails();
                $detail->order_id         = $order->id;
                $detail->product_id       = $productId;
                $detail->product_name     = $item['name'] ?? ($product->name ?? 'Product');
                $detail->purchase_price   = $product->purchase_price ?? 0;
                $detail->product_discount = 0;
                $detail->sale_price       = (float) ($item['price'] ?? ($product->new_price ?? 0));
                $detail->qty              = $qty;
                $detail->product_color    = $item['color_id'] ?? null;
                $detail->product_size     = $item['size_id'] ?? null;
                $detail->variant_price_id = $item['variant_price_id'] ?? null;
                $detail->save();

                if ($product) {
                    if (Schema::hasColumn('products', 'stock')) {
                        $product->stock = max(0, (int) $product->stock - $qty);
                        $product->save();
                    } elseif (Schema::hasColumn('products', 'qty')) {
                        $product->qty = max(0, (int) $product->qty - $qty);
                        $product->save();
                    } elseif (Schema::hasColumn('products', 'quantity')) {
                        $product->quantity = max(0, (int) $product->quantity - $qty);
                        $product->save();
                    }
                }
            }

            $incomplete->delete();

            DB::commit();

            Toastr::success('ইনকমপ্লিট অর্ডার রেগুলার অর্ডারে রূপান্তর হয়েছে।', 'Success');

            return redirect()->route('admin.order.edit', $order->invoice_id);
        } catch (\Throwable $e) {
            DB::rollBack();
            Toastr::error('রূপান্তর ব্যর্থ: '.$e->getMessage(), 'Error');

            return redirect()->route('admin.incomplete-orders.index');
        }
    }

    public function destroy($id)
    {
        IncompleteOrder::findOrFail($id)->delete();

        Toastr::success('ইনকমপ্লিট অর্ডার মুছে ফেলা হয়েছে।', 'Success');

        return redirect()->route('admin.incomplete-orders.index');
    }
}
