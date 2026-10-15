<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\ImageOptimizer;
use App\Models\WholesaleProduct;
use App\Models\WholesaleProductImage;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Childcategory;
use App\Models\Brand;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Toastr;
use DB;

class WholesaleProductController extends Controller
{
    public function index(Request $request)
    {
        $query = WholesaleProduct::with(['category', 'vendor', 'image'])
            ->orderBy('id', 'DESC');

        if ($request->keyword) {
            $query->where('name', 'LIKE', '%' . $request->keyword . '%');
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->approval_status) {
            $query->where('approval_status', $request->approval_status);
        }

        $data = $query->paginate(15);
        return view('backEnd.wholesale_products.index', compact('data'));
    }

    public function create()
    {
        return view('backEnd.wholesale_products.create', [
            'categories' => Category::where('parent_id', 0)->where('status', 1)->select('id', 'name')->with('childrenCategories')->get(),
            'brands' => Brand::where('status', 1)->select('id', 'name')->get(),
            'vendors' => Vendor::where('status', 'active')->select('id', 'shop_name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'wholesale_price' => 'required|numeric|min:0',
            'retail_price' => 'nullable|numeric|min:0',
            'min_quantity' => 'required|integer|min:1',
            'stock' => 'required|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $input = $request->except(['image', 'meta_image']);
        
        // Generate slug
        $input['slug'] = Str::slug($request->name . '-' . time());
        
        // Generate product code
        $lastId = WholesaleProduct::max('id') ?? 0;
        $input['product_code'] = 'WP' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);
        
        // Set status
        $input['status'] = $request->status ? 1 : 0;
        $input['approval_status'] = 'approved'; // Admin created products are auto-approved
        $input['feature_product'] = $request->feature_product ? 1 : 0;
        $input['created_by'] = Auth::id();
        
        // SEO
        $input['meta_title'] = $request->meta_title ?? $request->name;
        $input['meta_description'] = $request->meta_description ?? Str::limit(strip_tags($request->description ?? ''), 160);
        $input['meta_keywords'] = $request->meta_keywords ?? '';

        // Meta image upload
        if ($request->hasFile('meta_image')) {
            $input['meta_image'] = ImageOptimizer::store(
                $request->file('meta_image'),
                'public/uploads/wholesale_products/meta/',
                ImageOptimizer::basenameFromOriginal($request->file('meta_image'), 'meta')
            );
        }

        $product = WholesaleProduct::create($input);

        // Upload images
        if ($request->hasFile('image')) {
            $sortOrder = 0;
            foreach ($request->file('image') as $img) {
                $imagePath = ImageOptimizer::store($img, 'public/uploads/wholesale_products/');

                WholesaleProductImage::create([
                    'wholesale_product_id' => $product->id,
                    'image' => $imagePath,
                    'sort_order' => $sortOrder++,
                ]);
            }

            // Set first image as meta_image if not set
            if (empty($product->meta_image) && $product->images()->first()) {
                $product->update(['meta_image' => $product->images()->first()->image]);
            }
        }

        Toastr::success('Wholesale product created successfully!', 'Success');
        return redirect()->route('admin.wholesale_products.index');
    }

    public function show($id)
    {
        $product = WholesaleProduct::with(['category', 'subcategory', 'childcategory', 'brand', 'vendor', 'images', 'creator'])
            ->findOrFail($id);
        return view('backEnd.wholesale_products.show', compact('product'));
    }

    public function edit($id)
    {
        $product = WholesaleProduct::with('images')->findOrFail($id);
        return view('backEnd.wholesale_products.edit', [
            'product' => $product,
            'categories' => Category::where('parent_id', 0)->where('status', 1)->select('id', 'name')->with('childrenCategories')->get(),
            'brands' => Brand::where('status', 1)->select('id', 'name')->get(),
            'vendors' => Vendor::where('status', 'active')->select('id', 'shop_name')->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'wholesale_price' => 'required|numeric|min:0',
            'retail_price' => 'nullable|numeric|min:0',
            'min_quantity' => 'required|integer|min:1',
            'stock' => 'required|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $product = WholesaleProduct::findOrFail($id);
        $input = $request->except(['image', 'meta_image']);

        // Update slug if name changed
        if ($request->name != $product->name) {
            $input['slug'] = Str::slug($request->name . '-' . $product->id);
        }

        // Set status
        $input['status'] = $request->status ? 1 : 0;
        $input['feature_product'] = $request->feature_product ? 1 : 0;

        // SEO
        $input['meta_title'] = $request->meta_title ?? $request->name;
        $input['meta_description'] = $request->meta_description ?? Str::limit(strip_tags($request->description ?? ''), 160);
        $input['meta_keywords'] = $request->meta_keywords ?? '';

        // Meta image update
        if ($request->hasFile('meta_image')) {
            if ($product->meta_image && file_exists(public_path($product->meta_image))) {
                unlink(public_path($product->meta_image));
            }

            $input['meta_image'] = ImageOptimizer::store(
                $request->file('meta_image'),
                'public/uploads/wholesale_products/meta/',
                ImageOptimizer::basenameFromOriginal($request->file('meta_image'), 'meta')
            );
        }

        $product->update($input);

        // Upload new images
        if ($request->hasFile('image')) {
            $maxSortOrder = $product->images()->max('sort_order') ?? -1;
            $sortOrder = $maxSortOrder + 1;

            foreach ($request->file('image') as $img) {
                $imagePath = ImageOptimizer::store($img, 'public/uploads/wholesale_products/');

                WholesaleProductImage::create([
                    'wholesale_product_id' => $product->id,
                    'image' => $imagePath,
                    'sort_order' => $sortOrder++,
                ]);
            }
        }

        Toastr::success('Wholesale product updated successfully!', 'Success');
        return redirect()->route('admin.wholesale_products.index');
    }

    public function destroy($id)
    {
        $product = WholesaleProduct::findOrFail($id);

        // Delete images
        foreach ($product->images as $image) {
            if (file_exists(public_path($image->image))) {
                unlink(public_path($image->image));
            }
            $image->delete();
        }

        // Delete meta image
        if ($product->meta_image && file_exists(public_path($product->meta_image))) {
            unlink(public_path($product->meta_image));
        }

        $product->delete();
        Toastr::success('Wholesale product deleted successfully!', 'Success');
        return back();
    }

    public function approve($id)
    {
        $product = WholesaleProduct::findOrFail($id);
        $product->approval_status = 'approved';
        $product->save();

        Toastr::success('Wholesale product approved successfully!', 'Success');
        return back();
    }

    public function reject($id)
    {
        $product = WholesaleProduct::findOrFail($id);
        $product->approval_status = 'rejected';
        $product->save();

        Toastr::success('Wholesale product rejected!', 'Success');
        return back();
    }

    // AJAX: Get subcategories
    public function getSubcategory(Request $request)
    {
        $sub = DB::table("subcategories")
            ->where("category_id", $request->category_id)
            ->pluck('subcategoryName', 'id');

        return response()->json($sub);
    }

    // AJAX: Get childcategories
    public function getChildcategory(Request $request)
    {
        $child = DB::table("childcategories")
            ->where("subcategory_id", $request->subcategory_id)
            ->pluck('childcategoryName', 'id');

        return response()->json($child);
    }
}
