<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ResellerLandingPage;
use App\Models\Subcategory;

class PublicLandingController extends Controller
{
    protected function getLanding(string $slug)
    {
        $landing = ResellerLandingPage::where('slug', $slug)->where('is_active', 1)->first();
        if (!$landing) {
            abort(404);
        }
        return $landing;
    }

    protected function baseProductIds($landing)
    {
        return $landing->landingProducts()->pluck('product_id')->toArray();
    }

    protected function customPricesMap($landing): array
    {
        return $landing->landingProducts()->get()->pluck('pivot.custom_price', 'id')->map(fn ($v) => (float) $v)->toArray();
    }

    protected function productQueryForLanding($landing)
    {
        $productIds = $this->baseProductIds($landing);
        if (empty($productIds)) {
            return Product::whereRaw('1 = 0');
        }
        return Product::whereIn('id', $productIds)
            ->where('status', 1)
            ->when(function ($q) {
                if (\Schema::hasColumn('products', 'approval_status')) {
                    $q->where('approval_status', 'approved');
                }
            })
            ->with('image');
    }

    public function show(string $slug)
    {
        $landing = $this->getLanding($slug)->load('landingProducts');

        $productIds = $this->baseProductIds($landing);
        $products = collect();
        if (!empty($productIds)) {
            $products = Product::whereIn('id', $productIds)
                ->where('status', 1)
                ->when(function ($q) {
                    if (\Schema::hasColumn('products', 'approval_status')) {
                        $q->where('approval_status', 'approved');
                    }
                })
                ->with('image')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }

        $categories = Category::where('status', 1)->where('parent_id', 0)->with('subcategories')->orderBy('name')->get();
        $customPrices = $this->customPricesMap($landing);

        return view('reseller.landing.public', compact('landing', 'products', 'categories', 'customPrices'));
    }

    public function category(string $slug, string $categorySlug)
    {
        $landing = $this->getLanding($slug);
        $category = Category::where('slug', $categorySlug)->where('status', 1)->where('parent_id', 0)->first();
        if (!$category) {
            abort(404);
        }

        $products = $this->productQueryForLanding($landing)
            ->where('category_id', $category->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $categories = Category::where('status', 1)->where('parent_id', 0)->with('subcategories')->orderBy('name')->get();
        $customPrices = $this->customPricesMap($landing);

        return view('reseller.landing.category', compact('landing', 'category', 'products', 'categories', 'customPrices'));
    }

    public function subcategory(string $slug, string $subcategorySlug)
    {
        $landing = $this->getLanding($slug);
        $subcategory = Subcategory::with('category')->where('slug', $subcategorySlug)->where('status', 1)->first();
        if (!$subcategory) {
            abort(404);
        }

        $products = $this->productQueryForLanding($landing)
            ->where('subcategory_id', $subcategory->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $categories = Category::where('status', 1)->where('parent_id', 0)->with('subcategories')->orderBy('name')->get();
        $customPrices = $this->customPricesMap($landing);

        return view('reseller.landing.subcategory', compact('landing', 'subcategory', 'products', 'categories', 'customPrices'));
    }

    public function product(string $slug, string $productSlug)
    {
        $landing = $this->getLanding($slug);

        $productIds = $this->baseProductIds($landing);
        if (empty($productIds)) {
            abort(404);
        }

        $product = Product::whereIn('id', $productIds)
            ->where('slug', $productSlug)
            ->where('status', 1)
            ->with(['images', 'category', 'subcategory', 'brand'])
            ->first();

        if (!$product) {
            abort(404);
        }

        $relatedProducts = Product::whereIn('id', $productIds)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 1)
            ->with('image')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        $categories = Category::where('status', 1)->where('parent_id', 0)->with('subcategories')->orderBy('name')->get();
        $customPrices = $this->customPricesMap($landing);

        return view('reseller.landing.product', compact('landing', 'product', 'relatedProducts', 'categories', 'customPrices'));
    }
}
