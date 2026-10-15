<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

class ProductCatalogController extends Controller
{
    /**
     * Display product catalog for resellers.
     * Shows only products with reseller_price.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::guard('admin')->user();

        // Get products with reseller_price
        $query = Product::whereNotNull('reseller_price')
            ->where('status', 1)
            ->where('approval_status', 'approved')
            ->where('stock', '>', 0)
            ->with(['image', 'category', 'brand'])
            ->withCount(['sizes', 'colors'])
            ->orderBy('created_at', 'desc');

        // Search by product name
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'LIKE', "%{$keyword}%")
                  ->orWhere('product_code', 'LIKE', "%{$keyword}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by brand
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('reseller_price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('reseller_price', '<=', $request->max_price);
        }

        // Sort options
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('reseller_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('reseller_price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'profit_high':
                // Sort by profit (new_price - reseller_price) descending
                $query->orderByRaw('(new_price - reseller_price) DESC');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(20)->withQueryString();

        // Calculate profit for each product
        $products->getCollection()->transform(function($product) {
            $profit = $product->new_price - ($product->reseller_price ?? 0);
            $product->profit = $profit > 0 ? $profit : 0;
            return $product;
        });

        // Get filter options
        $categories = Category::where('parent_id', 0)
            ->where('status', 1)
            ->select('id', 'name')
            ->get();

        $brands = Brand::where('status', 1)
            ->select('id', 'name')
            ->get();

        // Price range
        $minPrice = Product::whereNotNull('reseller_price')
            ->where('status', 1)
            ->min('reseller_price');
        $maxPrice = Product::whereNotNull('reseller_price')
            ->where('status', 1)
            ->max('reseller_price');

        return view('reseller.products.catalog', compact(
            'user',
            'products',
            'categories',
            'brands',
            'minPrice',
            'maxPrice'
        ));
    }

    /**
     * Show single product details for reseller.
     *
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function show($slug)
    {
        $user = Auth::guard('admin')->user();

        $product = Product::where('slug', $slug)
            ->whereNotNull('reseller_price')
            ->where('status', 1)
            ->where('approval_status', 'approved')
            ->with([
                'image',
                'images',
                'category',
                'subcategory',
                'childcategory',
                'brand',
                'colors',
                'sizes',
                'variantPrices',
                'reviews'
            ])
            ->firstOrFail();

        // Calculate profit
        $profit = $product->new_price - ($product->reseller_price ?? 0);
        $product->profit = $profit > 0 ? $profit : 0;

        // Related products (same category with reseller_price)
        $relatedProducts = Product::whereNotNull('reseller_price')
            ->where('status', 1)
            ->where('approval_status', 'approved')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with('image')
            ->limit(8)
            ->get()
            ->map(function($p) {
                $profit = $p->new_price - ($p->reseller_price ?? 0);
                $p->profit = $profit > 0 ? $profit : 0;
                return $p;
            });

        return view('reseller.products.show', compact('user', 'product', 'relatedProducts'));
    }
}
