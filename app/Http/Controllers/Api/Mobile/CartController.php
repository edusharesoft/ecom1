<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Cart as CartModel;
use App\Models\Size;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Get Cart Items
     * 
     * GET /api/v1/mobile/cart
     */
    public function index(Request $request)
    {
        $customer = $request->user();
        
        $cartItems = CartModel::where('customer_id', $customer->id)
            ->with(['product.image', 'size', 'color'])
            ->get();

        $subtotal = 0;
        $items = [];

        foreach ($cartItems as $item) {
            $product = $item->product;
            $itemTotal = $item->price * $item->quantity;
            $subtotal += $itemTotal;

            $items[] = [
                'id' => $item->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_slug' => $product->slug,
                'product_image' => $product->image ? url($product->image->image) : null,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'subtotal' => $itemTotal,
                'size_id' => $item->size_id,
                'color_id' => $item->color_id,
                'size_name' => $item->size ? $item->size->name : null,
                'color_name' => $item->color ? $item->color->name : null,
            ];
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Cart retrieved successfully',
            'data' => [
                'items' => $items,
                'subtotal' => $subtotal,
                'total_items' => $cartItems->sum('quantity'),
                'total_products' => $cartItems->count(),
            ]
        ], 200);
    }

    /**
     * Add to Cart
     * 
     * POST /api/v1/mobile/cart/add
     */
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'size_id' => 'nullable|exists:sizes,id',
            'color_id' => 'nullable|exists:colors,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $customer = $request->user();
        $product = Product::findOrFail($request->product_id);

        // Check stock
        if ($product->stock < $request->quantity) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient stock. Available: ' . $product->stock
            ], 400);
        }

        // Calculate price (check variant if size/color provided)
        $price = $product->new_price ?? $product->old_price ?? 0;
        
        if ($request->size_id && $request->color_id) {
            $variant = \App\Models\ProductVariantPrice::where('product_id', $product->id)
                ->where('size_id', $request->size_id)
                ->where('color_id', $request->color_id)
                ->first();
            
            if ($variant) {
                $price = $variant->price;
            }
        }

        // Check if item already exists in cart
        $existingCart = CartModel::where('customer_id', $customer->id)
            ->where('product_id', $product->id)
            ->where('size_id', $request->size_id)
            ->where('color_id', $request->color_id)
            ->first();

        if ($existingCart) {
            // Update quantity
            $newQuantity = $existingCart->quantity + $request->quantity;
            
            if ($product->stock < $newQuantity) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Insufficient stock. Available: ' . $product->stock
                ], 400);
            }

            $existingCart->update([
                'quantity' => $newQuantity,
                'price' => $price,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Cart updated successfully',
                'data' => [
                    'cart_item' => [
                        'id' => $existingCart->id,
                        'product_id' => $product->id,
                        'quantity' => $existingCart->quantity,
                        'price' => $existingCart->price,
                    ]
                ]
            ], 200);
        }

        // Create new cart item
        $cartItem = CartModel::create([
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'price' => $price,
            'size_id' => $request->size_id,
            'color_id' => $request->color_id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Product added to cart successfully',
            'data' => [
                'cart_item' => [
                    'id' => $cartItem->id,
                    'product_id' => $product->id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                ]
            ]
        ], 201);
    }

    /**
     * Update Cart Item
     * 
     * PUT /api/v1/mobile/cart/{id}
     */
    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $customer = $request->user();
        $cartItem = CartModel::where('id', $id)
            ->where('customer_id', $customer->id)
            ->with('product')
            ->firstOrFail();

        // Check stock
        if ($cartItem->product->stock < $request->quantity) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient stock. Available: ' . $cartItem->product->stock
            ], 400);
        }

        $cartItem->update([
            'quantity' => $request->quantity
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Cart item updated successfully',
            'data' => [
                'cart_item' => [
                    'id' => $cartItem->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'subtotal' => $cartItem->quantity * $cartItem->price,
                ]
            ]
        ], 200);
    }

    /**
     * Remove from Cart
     * 
     * DELETE /api/v1/mobile/cart/{id}
     */
    public function remove($id, Request $request)
    {
        $customer = $request->user();
        
        $cartItem = CartModel::where('id', $id)
            ->where('customer_id', $customer->id)
            ->firstOrFail();

        $cartItem->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Item removed from cart successfully'
        ], 200);
    }

    /**
     * Clear Cart
     * 
     * DELETE /api/v1/mobile/cart/clear
     */
    public function clear(Request $request)
    {
        $customer = $request->user();
        
        CartModel::where('customer_id', $customer->id)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Cart cleared successfully'
        ], 200);
    }

    /**
     * Get Cart Count
     * 
     * GET /api/v1/mobile/cart/count
     */
    public function count(Request $request)
    {
        $customer = $request->user();
        
        $totalItems = CartModel::where('customer_id', $customer->id)
            ->sum('quantity');
        
        $totalProducts = CartModel::where('customer_id', $customer->id)
            ->count();

        return response()->json([
            'status' => 'success',
            'message' => 'Cart count retrieved',
            'data' => [
                'total_items' => $totalItems,
                'total_products' => $totalProducts,
            ]
        ], 200);
    }
}
