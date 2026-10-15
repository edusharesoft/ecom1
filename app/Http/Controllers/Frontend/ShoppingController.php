<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Productprice;
use App\Models\Product;
use App\Models\ProductVariantPrice;
use App\Models\Color;
use App\Models\Size;
use App\Models\Coupon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Toastr;
use Cart;
use DB;
use Carbon\Carbon;
use Session;

class ShoppingController extends Controller
{

    /**
     * 🔹 কার্টে থাকা সব প্রোডাক্ট থেকে মোট Advance Amount বের করবে
     */
    public static function getCartAdvanceAmount()
    {
        $advance = 0;

        // ✅ First collect all product IDs to avoid N+1 query
        $productIds = Cart::instance('shopping')->content()
            ->pluck('id')
            ->unique()
            ->toArray();

        if (empty($productIds)) {
            return $advance;
        }

        // ✅ Load all products in a single query
        $products = Product::whereIn('id', $productIds)
            ->select('id', 'advance_amount')
            ->get()
            ->keyBy('id'); // Key by ID for fast lookup

        // ✅ Now iterate through cart items without additional queries
        foreach (Cart::instance('shopping')->content() as $item) {
            $product = $products->get($item->id);

            if ($product && $product->advance_amount > 0) {
                // Qty অনুযায়ী গুণ করব
                $advance += ($product->advance_amount * $item->qty);
            }
        }

        return $advance;
    }

    /**
     * ⭐ নতুন helper:
     * 🔹 কার্টে অন্তত একটি ডিজিটাল প্রোডাক্ট আছে কি না?
     */
    public static function hasDigitalProductInCart()
    {
        foreach (Cart::instance('shopping')->content() as $item) {
            if (!empty($item->options->is_digital) && $item->options->is_digital == 1) {
                return true;
            }
        }
        return false;
    }

    /**
     * ⭐ নতুন helper:
     * 🔹 কার্টে থাকা সব প্রোডাক্ট free delivery eligible কিনা check করবে
     * যদি সব প্রোডাক্ট free_delivery = 1 হয়, তাহলে shipping charge 0 হবে
     */
    public static function hasAllFreeDeliveryProducts()
    {
        $productIds = Cart::instance('shopping')->content()
            ->pluck('id')
            ->unique()
            ->toArray();

        if (empty($productIds)) {
            return false;
        }

        // Load all products in a single query
        $products = Product::whereIn('id', $productIds)
            ->select('id', 'free_delivery', 'is_digital')
            ->get()
            ->keyBy('id');

        // Check if all physical products have free_delivery = 1
        foreach (Cart::instance('shopping')->content() as $item) {
            $product = $products->get($item->id);
            
            // Digital products don't need shipping, so skip them
            if ($product && $product->is_digital == 1) {
                continue;
            }
            
            // If any physical product doesn't have free_delivery, return false
            if ($product && $product->free_delivery != 1) {
                return false;
            }
        }

        return true;
    }

    // 🟢 Add to cart (GET)
    public function addTocartGet($id, Request $request)
    {
        $qty = 1;
        $productInfo = Product::find($id);

        if (!$productInfo) {
            return response()->json(['error' => 'Product not found']);
        }

        $productImage = DB::table('productimages')
            ->where('product_id', $id)
            ->value('image') ?? 'public/uploads/default.webp';

        $cartinfo = Cart::instance('shopping')->add([
            'id'   => $productInfo->id,
            'name' => $productInfo->name,
            'qty'  => $qty,
            'price'=> (float) ($productInfo->new_price ?? $productInfo->old_price ?? 1),
            'options' => [
                'image'          => $productImage,
                'old_price'      => (float) ($productInfo->old_price ?? 0),
                'slug'           => $productInfo->slug,
                'purchase_price' => (float) ($productInfo->purchase_price ?? 0),

                // 🔥 Advance
                'advance_amount' => (float) ($productInfo->advance_amount ?? 0),

                // 🔥 Digital flag
                'is_digital'     => (int) ($productInfo->is_digital ?? 0),

                // 🔥 Free Delivery flag
                'free_delivery'  => (int) ($productInfo->free_delivery ?? 0),
            ],
        ]);

        return response()->json($cartinfo);
    }

    // 🟢 Apply coupon
    public function applyCoupon(Request $request)
    {
        $request->validate(['coupon_code' => 'required']);

        $coupon = Coupon::where('code', $request->coupon_code)
            ->where('status', 1)
            ->first();

        if (!$coupon) {
            Toastr::error('Invalid Coupon Code', 'Error');
            return redirect()->back();
        }

        $today = Carbon::now()->format('Y-m-d');

        if (($coupon->valid_from && $today < $coupon->valid_from) ||
            ($coupon->valid_to && $today > $coupon->valid_to)) {
            Toastr::error('Coupon expired or not valid yet', 'Error');
            return redirect()->back();
        }

        // subtotal() returns string like “1,200.00”
        $subtotal = floatval(
            preg_replace('/[^\d.]/', '', Cart::instance('shopping')->subtotal())
        );

        if ($coupon->min_purchase && $subtotal < $coupon->min_purchase) {
            Toastr::error("Minimum purchase ৳{$coupon->min_purchase} required", 'Error');
            return redirect()->back();
        }

        $discount = $coupon->type == 'percent'
            ? ($subtotal * ($coupon->value / 100))
            : $coupon->value;

        Session::put('coupon_code', $coupon->code);
        Session::put('discount', round($discount, 2));

        Toastr::success("Coupon Applied! You saved ৳" . round($discount, 2), 'Success');
        return redirect()->back();
    }

    // 🟢 Remove coupon
    public function removeCoupon()
    {
        Session::forget(['coupon_code', 'discount']);
        Toastr::success('Coupon removed successfully', 'Success');
        return redirect()->back();
    }

    // 🟢 Add to cart (POST) with variant support
    public function cart_store(Request $request)
    {
        $product = Product::with(['image', 'wholesalePrices'])->find($request->id);

        if (!$product) {
            Toastr::error('Product not found', 'Error!');
            return redirect()->back();
        }

        $qty = max(1, (int) ($request->qty ?? 1));
        $colorId = $request->filled('product_color') ? (int) $request->product_color : null;
        $sizeId  = $request->filled('product_size') ? (int) $request->product_size : null;

        $price = $product->resolveSalePrice($qty, $colorId, $sizeId);

        if ($price <= 0) {
            $price = (float) ($product->new_price ?? $product->old_price ?? 1);
        }

        // ✅ Fallback image
        $image = optional($product->image)->image
            ?? DB::table('productimages')->where('product_id', $product->id)->value('image')
            ?? 'public/uploads/default.webp';

        // ✅ Add to cart
        Cart::instance('shopping')->add([
            'id'   => $product->id,
            'name' => $product->name,
            'qty'  => $qty,
            'price'=> $price,
            'options' => [
                'slug'           => $product->slug,
                'image'          => $image,
                'old_price'      => (float) ($product->old_price ?? 0),
                'purchase_price' => (float) ($product->purchase_price ?? 0),
                'product_size'   => $request->product_size ?? null,
                'product_color'  => $request->product_color ?? null,
                'pro_unit'       => $request->pro_unit ?? null,

                // 🔥 Advance
                'advance_amount' => (float) ($product->advance_amount ?? 0),

                // 🔥 Digital flag
                'is_digital'     => (int) ($product->is_digital ?? 0),

                // 🔥 Free Delivery flag
                'free_delivery'  => (int) ($product->free_delivery ?? 0),
            ],
        ]);

        Toastr::success('Product added to cart successfully!', 'Success');

        // যদি ফর্ম থেকে "order_now" ক্লিক করা হয়ে থাকে, সরাসরি checkout
        if ($request->has('order_now')) {
            return redirect()->route('customer.checkout');
        }

        // নরমাল কেসে আগের পেইজে ফিরে যাবে
        return redirect()->back();
    }

    // 🟢 Update cart (color/size change)
    public function cart_update(Request $request)
    {
        $rowId    = $request->id;
        $cartItem = Cart::instance('shopping')->get($rowId);

        if ($cartItem) {
            Cart::instance('shopping')->update($rowId, [
                'options' => [
                    'product_size'   => $request->product_size ?: $cartItem->options->product_size,
                    'product_color'  => $request->product_color ?: $cartItem->options->product_color,
                    'slug'           => $cartItem->options->slug,
                    'image'          => $cartItem->options->image,
                    'old_price'      => $cartItem->options->old_price,
                    'purchase_price' => $cartItem->options->purchase_price,
                    'pro_unit'       => $cartItem->options->pro_unit,

                    // 🔥 পুরানো advance_amount টাকে রেখে দাও
                    'advance_amount' => $cartItem->options->advance_amount ?? 0,

                    // 🔥 Digital flag আগের মতোই থাকবে
                    'is_digital'     => $cartItem->options->is_digital ?? 0,

                    // 🔥 Free Delivery flag আগের মতোই থাকবে
                    'free_delivery'  => $cartItem->options->free_delivery ?? 0,
                ],
            ]);
        }

        return $this->cartFragmentView($request);
    }

    // 🟢 Remove from cart
    public function cart_remove(Request $request)
    {
        Cart::instance('shopping')->update($request->id, 0);
        return $this->cartFragmentView($request);
    }

    // 🟢 Increment quantity
    public function cart_increment(Request $request)
    {
        $item = Cart::instance('shopping')->get($request->id);
        if (!$item) {
            return $this->cartFragmentView($request);
        }

        $qty = $item->qty + 1;
        $this->syncCartItemPrice($request->id, $qty);

        return $this->cartFragmentView($request);
    }

    // 🟢 Decrement quantity
    public function cart_decrement(Request $request)
    {
        $item = Cart::instance('shopping')->get($request->id);
        if (!$item) {
            return $this->cartFragmentView($request);
        }

        $qty = max(1, $item->qty - 1);
        $this->syncCartItemPrice($request->id, $qty);

        return $this->cartFragmentView($request);
    }

    /**
     * কার্টের সব আইটেমে wholesale/variant প্রাইস আপডেট (চেকআউট লোডে)
     */
    public static function refreshCartWholesalePrices(): void
    {
        foreach (Cart::instance('shopping')->content() as $item) {
            $product = Product::with('wholesalePrices')->find($item->id);
            if (!$product) {
                continue;
            }

            $qty     = max(1, (int) $item->qty);
            $colorId = $item->options->color_id ?? null;
            $sizeId  = $item->options->size_id ?? null;
            $price   = $product->resolveSalePrice(
                $qty,
                $colorId ? (int) $colorId : null,
                $sizeId ? (int) $sizeId : null
            );

            if ($price <= 0) {
                continue;
            }

            Cart::instance('shopping')->update($item->rowId, [
                'qty'   => $qty,
                'price' => $price,
            ]);
        }
    }

    /**
     * কার্টে qty বদলালে wholesale/variant প্রাইস আপডেট
     */
    private function syncCartItemPrice(string $rowId, int $qty): void
    {
        $item = Cart::instance('shopping')->get($rowId);
        if (!$item) {
            return;
        }

        $product = Product::with('wholesalePrices')->find($item->id);
        if (!$product) {
            Cart::instance('shopping')->update($rowId, $qty);
            return;
        }

        $colorId = $item->options->color_id ?? null;
        $sizeId  = $item->options->size_id ?? null;
        $price   = $product->resolveSalePrice(
            $qty,
            $colorId ? (int) $colorId : null,
            $sizeId ? (int) $sizeId : null
        );

        if ($price <= 0) {
            $price = (float) ($item->price ?? $product->new_price ?? 1);
        }

        Cart::instance('shopping')->update($rowId, [
            'qty'   => $qty,
            'price' => $price,
        ]);
    }

    // 🟢 Cart count (header)
    public function cart_count(Request $request)
    {
        $data = Cart::instance('shopping')->count();
        return view('frontEnd.layouts.ajax.cart_count', compact('data'));
    }

    // 🟢 Mobile cart count
    public function mobilecart_qty(Request $request)
    {
        $data = Cart::instance('shopping')->count();
        return view('frontEnd.layouts.ajax.mobilecart_qty', compact('data'));
    }

    // 🟢 Sidebar cart (for floating cart drawer)
    public function sidebarCart(Request $request)
    {
        $generalsetting = \App\Models\GeneralSetting::where('status', 1)->first();
        return view('frontEnd.layouts.ajax.sidebar-cart', compact('generalsetting'));
    }

    /**
     * Campaign ল্যান্ডিং: কার্টে একটিই লাইন, ভেরিয়েন্ট অনুযায়ী দাম।
     */
    public static function setCampaignCartProduct(Product $product, ?int $colorId = null, ?int $sizeId = null): void
    {
        Cart::instance('shopping')->destroy();

        $product->loadMissing(['image', 'wholesalePrices']);

        $qty        = 1;
        $finalPrice = $product->resolveSalePrice($qty, $colorId, $sizeId);
        if ($finalPrice <= 0) {
            $finalPrice = (float) ($product->new_price ?? $product->old_price ?? 1);
        }

        $variantPrice = null;
        if ($colorId || $sizeId) {
            $vq = ProductVariantPrice::where('product_id', $product->id);
            if ($colorId && $sizeId) {
                $variantPrice = (clone $vq)->where('color_id', $colorId)->where('size_id', $sizeId)->first();
            } elseif ($colorId) {
                $variantPrice = (clone $vq)->where('color_id', $colorId)->whereNull('size_id')->first();
            } else {
                $variantPrice = (clone $vq)->where('size_id', $sizeId)->whereNull('color_id')->first();
            }
        }

        $sizeName  = null;
        $colorName = null;
        if ($sizeId) {
            $sz = Size::find($sizeId);
            $sizeName = $sz ? ($sz->sizeName ?? $sz->size_name ?? null) : null;
        }
        if ($colorId) {
            $cl = Color::find($colorId);
            $colorName = $cl ? ($cl->getDisplayName() ?? $cl->colorName ?? $cl->color_name ?? null) : null;
        }

        Cart::instance('shopping')->add([
            'id'    => $product->id,
            'name'  => $product->name,
            'qty'   => $qty,
            'price' => $finalPrice,
            'options' => [
                'slug'             => $product->slug,
                'image'            => optional($product->image)->image ?? 'public/uploads/default.webp',
                'old_price'        => (float) ($product->old_price ?? 0),
                'purchase_price'   => (float) ($product->purchase_price ?? 0),

                'advance_amount' => (float) ($product->advance_amount ?? 0),

                'is_digital'     => (int) ($product->is_digital ?? 0),

                'free_delivery'  => (int) ($product->free_delivery ?? 0),

                'color_id'           => $colorId,
                'size_id'            => $sizeId,
                'product_size'       => $sizeName,
                'product_color'      => $colorName,
                'variant_price_id'   => $variantPrice->id ?? null,
                'is_wholesale'       => (int) ($product->is_wholesale ?? 0),
            ],
        ]);

        self::refreshCartWholesalePrices();
    }

    protected function cartFragmentView(Request $request): \Illuminate\Contracts\View\View
    {
        $name = $request->boolean('campaign')
            ? 'frontEnd.layouts.ajax.campaign-cart-table'
            : 'frontEnd.layouts.ajax.cart';

        return view($name);
    }

    // 🟢 Change product from campaign or offers
    public function changeProduct(Request $request)
    {
        $productId = $request->input('id');
        $colorId   = $request->filled('color_id') ? (int) $request->input('color_id') : null;
        $sizeId    = $request->filled('size_id') ? (int) $request->input('size_id') : null;

        $product = Product::with(['image', 'wholesalePrices'])->find($productId);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ]);
        }

        self::setCampaignCartProduct($product, $colorId, $sizeId);

        return view('frontEnd.layouts.ajax.campaign-cart-table');
    }
}
