<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Get Products List
     * 
     * GET /api/v1/mobile/products
     */
    public function index(Request $request)
    {
        $query = Product::where('status', 1)
            ->with(['image', 'category', 'brand'])
            ->orderBy('id', 'desc');

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('product_code', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by brand
        if ($request->has('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('new_price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('new_price', '<=', $request->max_price);
        }

        // Filter by stock
        if ($request->has('in_stock')) {
            $query->where('stock', '>', 0);
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('new_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('new_price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'latest':
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        // Pagination
        $perPage = $request->get('per_page', 20);
        $products = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Products retrieved successfully',
            'data' => [
                'products' => $products->items(),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ]
            ]
        ], 200);
    }

    /**
     * Get Product Details
     * 
     * GET /api/v1/mobile/products/{id}
     */
    public function show($id)
    {
        $product = Product::where('status', 1)
            ->with([
                'image',
                'images',
                'category',
                'subcategory',
                'childcategory',
                'brand',
                'sizes',
                'colors',
                'variantPrices',
                'reviews' => function($query) {
                    $query->where('status', 'approved')
                          ->with('customer:id,name')
                          ->orderBy('id', 'desc')
                          ->limit(10);
                }
            ])
            ->findOrFail($id);

        // Format product data
        $productData = [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'product_code' => $product->product_code,
            'description' => $product->description,
            'old_price' => $product->old_price,
            'new_price' => $product->new_price,
            'stock' => $product->stock,
            'is_digital' => $product->is_digital,
            'is_wholesale' => $product->is_wholesale,
            'free_delivery' => $product->free_delivery ?? false,
            'image' => $product->image ? url($product->image->image) : null,
            'images' => $product->images->map(function($img) {
                return url($img->image);
            }),
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name,
                'slug' => $product->category->slug,
            ] : null,
            'subcategory' => $product->subcategory ? [
                'id' => $product->subcategory->id,
                'name' => $product->subcategory->subcategoryName,
                'slug' => $product->subcategory->slug,
            ] : null,
            'brand' => $product->brand ? [
                'id' => $product->brand->id,
                'name' => $product->brand->name,
            ] : null,
            'sizes' => $product->sizes->map(function($size) {
                return [
                    'id' => $size->id,
                    'name' => $size->name,
                ];
            }),
            'colors' => $product->colors->map(function($color) {
                return [
                    'id' => $color->id,
                    'name' => $color->name,
                    'code' => $color->code ?? null,
                ];
            }),
            'variant_prices' => $product->variantPrices->map(function($variant) {
                return [
                    'id' => $variant->id,
                    'size_id' => $variant->size_id,
                    'color_id' => $variant->color_id,
                    'price' => $variant->price,
                    'stock' => $variant->stock,
                ];
            }),
            'reviews' => $product->reviews->map(function($review) {
                return [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'customer_name' => $review->customer->name ?? 'Anonymous',
                    'created_at' => $review->created_at->format('Y-m-d H:i:s'),
                ];
            }),
            'discount_percentage' => $product->old_price ? 
                round((($product->old_price - $product->new_price) / $product->old_price) * 100) : 0,
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Product retrieved successfully',
            'data' => $productData
        ], 200);
    }

    /**
     * Get Featured Products
     * 
     * GET /api/v1/mobile/products/featured
     */
    public function featured(Request $request)
    {
        $limit = $request->get('limit', 10);
        
        $products = Product::where('status', 1)
            ->where('feature_product', 1)
            ->with(['image', 'category'])
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Featured products retrieved',
            'data' => $products->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'old_price' => $product->old_price,
                    'new_price' => $product->new_price,
                    'stock' => $product->stock,
                    'image' => $product->image ? url($product->image->image) : null,
                    'category' => $product->category ? $product->category->name : null,
                ];
            })
        ], 200);
    }

    /**
     * Get Hot Deal Products
     * 
     * GET /api/v1/mobile/products/hot-deals
     */
    public function hotDeals(Request $request)
    {
        $limit = $request->get('limit', 10);
        
        $products = Product::where('status', 1)
            ->where('topsale', 1)
            ->where('stock', '>', 0)
            ->with(['image', 'category'])
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Hot deal products retrieved',
            'data' => $products->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'old_price' => $product->old_price,
                    'new_price' => $product->new_price,
                    'stock' => $product->stock,
                    'image' => $product->image ? url($product->image->image) : null,
                    'category' => $product->category ? $product->category->name : null,
                ];
            })
        ], 200);
    }

    /**
     * Get Products by Category
     * 
     * GET /api/v1/mobile/products/category/{categoryId}
     */
    public function byCategory($categoryId, Request $request)
    {
        $perPage = $request->get('per_page', 20);
        
        $products = Product::where('status', 1)
            ->where('category_id', $categoryId)
            ->with(['image', 'category'])
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Products retrieved successfully',
            'data' => [
                'products' => $products->items(),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ]
            ]
        ], 200);
    }
}
