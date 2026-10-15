<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\ProductVariantPrice;
use Gloudemans\Shoppingcart\Facades\Cart;
use Brian2694\Toastr\Facades\Toastr;
use DB;

class ResellerCartController extends Controller
{
    /**
     * Add product to cart with reseller price
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addToCart(Request $request)
    {
        $user = Auth::guard('admin')->user();

        // Verify reseller
        if (!$user || (!$user->hasRole('reseller') && $user->role !== 'reseller')) {
            Toastr::error('আপনি রিসেলার নন', 'Error');
            return redirect()->back();
        }

        // Check verification status
        if ($user->verification_status !== 'approved') {
            Toastr::error('আপনার একাউন্ট এখনও ভেরিফাই হয়নি। অর্ডার করার জন্য আপনার একাউন্ট ভেরিফাই করা আবশ্যক।', 'Account Not Verified');
            return redirect()->route('reseller.verification.index');
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'nullable|integer|min:1',
            'product_size' => 'nullable|integer',
            'product_color' => 'nullable|integer',
        ]);

        $product = Product::with(['image', 'sizes', 'colors'])->find($request->product_id);

        if (!$product) {
            Toastr::error('প্রোডাক্ট পাওয়া যায়নি', 'Error');
            return redirect()->back();
        }

        // Check if product has reseller_price
        if (!$product->reseller_price) {
            Toastr::error('এই প্রোডাক্টের জন্য রিসেলার প্রাইস নেই', 'Error');
            return redirect()->back();
        }

        $qty = $request->qty ?? 1;

        // Get variant price if size/color is selected
        $variantPrice = null;
        $variantPriceId = null;
        $resellerPrice = (float) $product->reseller_price;
        
        if ($request->product_size || $request->product_color) {
            $variantPrice = ProductVariantPrice::where('product_id', $product->id)
                ->when($request->product_color, function($q) use ($request) {
                    $q->where('color_id', $request->product_color);
                })
                ->when($request->product_size, function($q) use ($request) {
                    $q->where('size_id', $request->product_size);
                })
                ->first();
            
            if ($variantPrice) {
                // If variant has specific price, use it; otherwise use product reseller_price
                // Note: Variant prices are usually for regular customers, 
                // but we can use reseller_price as base and adjust if needed
                $variantPriceId = $variantPrice->id;
                
                // Check stock for variant
                if ($variantPrice->stock < $qty) {
                    Toastr::error('এই সাইজ/কালরের জন্য পর্যাপ্ত স্টক নেই', 'Error');
                    return redirect()->back();
                }
            } else {
                // Variant not found, check if size/color exists for this product
                if ($request->product_size && !$product->sizes->contains('id', $request->product_size)) {
                    Toastr::error('এই সাইজ এই প্রোডাক্টের জন্য নেই', 'Error');
                    return redirect()->back();
                }
                if ($request->product_color && !$product->colors->contains('id', $request->product_color)) {
                    Toastr::error('এই কালর এই প্রোডাক্টের জন্য নেই', 'Error');
                    return redirect()->back();
                }
            }
        } else {
            // Check stock for main product
            if ($product->stock < $qty) {
                Toastr::error('এই প্রোডাক্ট স্টকে নেই', 'Error');
                return redirect()->back();
            }
        }

        // Get product image
        $productImage = optional($product->image)->image 
            ?? DB::table('productimages')->where('product_id', $product->id)->value('image')
            ?? 'public/uploads/default.webp';

        // Add to cart with reseller_price
        Cart::instance('shopping')->add([
            'id'   => $product->id,
            'name' => $product->name,
            'qty'  => $qty,
            'price'=> $resellerPrice, // Use reseller_price
            'options' => [
                'slug'           => $product->slug,
                'image'          => $productImage,
                'old_price'      => (float) ($product->old_price ?? 0),
                'new_price'      => (float) ($product->new_price ?? 0),
                'reseller_price' => $resellerPrice, // Store for reference
                'purchase_price' => (float) ($product->purchase_price ?? 0),
                'advance_amount' => (float) ($product->advance_amount ?? 0),
                'is_digital'     => (int) ($product->is_digital ?? 0),
                'is_reseller_order' => true, // Flag to identify reseller orders
                'product_size'   => $request->product_size ?? null,
                'product_color'  => $request->product_color ?? null,
                'variant_price_id' => $variantPriceId,
            ],
        ]);

        Toastr::success('প্রোডাক্ট কার্টে যোগ করা হয়েছে', 'Success!');

        // If order_now is set, redirect to reseller checkout
        if ($request->has('order_now')) {
            return redirect()->route('reseller.checkout');
        }

        return redirect()->back();
    }

    /**
     * Add to cart via AJAX
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addToCartAjax(Request $request)
    {
        $user = Auth::guard('admin')->user();

        // Verify reseller
        if (!$user || (!$user->hasRole('reseller') && $user->role !== 'reseller')) {
            return response()->json([
                'success' => false,
                'message' => 'আপনি রিসেলার নন'
            ], 403);
        }

        // Check verification status
        if ($user->verification_status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'আপনার একাউন্ট এখনও ভেরিফাই হয়নি'
            ], 403);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'nullable|integer|min:1',
            'product_size' => 'nullable|integer',
            'product_color' => 'nullable|integer',
        ]);

        $product = Product::with(['image', 'sizes', 'colors'])->find($request->product_id);

        if (!$product || !$product->reseller_price) {
            return response()->json([
                'success' => false,
                'message' => 'প্রোডাক্ট পাওয়া যায়নি বা রিসেলার প্রাইস নেই'
            ], 404);
        }

        $qty = $request->qty ?? 1;

        // Get variant price if size/color is selected
        $variantPrice = null;
        $variantPriceId = null;
        $resellerPrice = (float) $product->reseller_price;
        
        if ($request->product_size || $request->product_color) {
            $variantPrice = ProductVariantPrice::where('product_id', $product->id)
                ->when($request->product_color, function($q) use ($request) {
                    $q->where('color_id', $request->product_color);
                })
                ->when($request->product_size, function($q) use ($request) {
                    $q->where('size_id', $request->product_size);
                })
                ->first();
            
            if ($variantPrice) {
                $variantPriceId = $variantPrice->id;
                
                // Check stock for variant
                if ($variantPrice->stock < $qty) {
                    return response()->json([
                        'success' => false,
                        'message' => 'এই সাইজ/কালরের জন্য পর্যাপ্ত স্টক নেই'
                    ], 400);
                }
            }
        } else {
            // Check stock for main product
            if ($product->stock < $qty) {
                return response()->json([
                    'success' => false,
                    'message' => 'স্টকে নেই'
                ], 400);
            }
        }

        $productImage = optional($product->image)->image 
            ?? DB::table('productimages')->where('product_id', $product->id)->value('image')
            ?? 'public/uploads/default.webp';

        Cart::instance('shopping')->add([
            'id'   => $product->id,
            'name' => $product->name,
            'qty'  => $qty,
            'price'=> $resellerPrice,
            'options' => [
                'slug'           => $product->slug,
                'image'          => $productImage,
                'old_price'      => (float) ($product->old_price ?? 0),
                'new_price'      => (float) ($product->new_price ?? 0),
                'reseller_price' => $resellerPrice,
                'purchase_price' => (float) ($product->purchase_price ?? 0),
                'advance_amount' => (float) ($product->advance_amount ?? 0),
                'is_digital'     => (int) ($product->is_digital ?? 0),
                'is_reseller_order' => true,
                'product_size'   => $request->product_size ?? null,
                'product_color'  => $request->product_color ?? null,
                'variant_price_id' => $variantPriceId,
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'প্রোডাক্ট কার্টে যোগ করা হয়েছে',
            'cart_count' => Cart::instance('shopping')->count()
        ]);
    }
}
