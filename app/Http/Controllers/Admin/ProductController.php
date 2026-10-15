<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\ImageOptimizer;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Productimage;
use App\Models\Productcolor;
use App\Models\Productsize;
use App\Models\ProductVariantPrice;
use App\Models\ProductWholesalePrice;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Childcategory;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Size;
use Toastr;
use File;
use Illuminate\Support\Str;
use DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use App\Services\GeminiAiService;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:product-list|product-create|product-edit|product-delete', ['only' => ['index','show']]);
        $this->middleware('permission:product-create', ['only' => ['create','store']]);
        $this->middleware('permission:product-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:product-delete', ['only' => ['destroy']]);
        $this->middleware('permission:product-create|product-edit', ['only' => ['generateAIDescription']]);
    }

    // ================================
    // AJAX: SUBCATEGORY
    // ================================
    public function getSubcategory(Request $request)
    {
        $sub = DB::table("subcategories")
            ->where("category_id", $request->category_id)
            ->pluck('subcategoryName', 'id');

        return response()->json($sub);
    }

    // ================================
    // AJAX: CHILDCATEGORY
    // ================================
    public function getChildcategory(Request $request)
    {
        $child = DB::table("childcategories")
            ->where("subcategory_id", $request->subcategory_id)
            ->pluck('childcategoryName', 'id');

        return response()->json($child);
    }

    // ================================
    // INDEX
    // ================================
    public function index(Request $request)
    {
        // Show only vendor products (all vendor products)
        $query = Product::whereNotNull('vendor_id')
            ->orderBy('id','DESC')
            ->with('image','category','vendor');

        if ($request->keyword) {
            $query->where('name', 'LIKE', '%' . $request->keyword . "%");
        }

        $data = $query->paginate(10);
        return view('backEnd.product.index', compact('data'));
    }

    // ================================
    // WHOLESALE PRODUCTS
    // ================================
    public function wholesale(Request $request)
    {
        // Show only wholesale products (is_wholesale = 1)
        $query = Product::where('is_wholesale', 1)
            ->orderBy('id','DESC')
            ->with('image','category','vendor','wholesalePrices');

        if ($request->keyword) {
            $query->where('name', 'LIKE', '%' . $request->keyword . "%");
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->status !== null) {
            $query->where('status', $request->status);
        }

        $data = $query->paginate(20);
        $categories = Category::where('parent_id', 0)->where('status', 1)->select('id', 'name')->get();
        
        return view('backEnd.product.wholesale', compact('data', 'categories'));
    }

    // ================================
    // CREATE
    // ================================
    public function create()
    {
        return view('backEnd.product.create', [
            'categories' => Category::where('parent_id', 0)->where('status', 1)->select('id', 'name')->with('childrenCategories')->get(),
            'brands'     => Brand::where('status', 1)->select('id', 'name')->get(),
            'colors'     => Color::where('status', 1)->get(),
            'sizes'      => Size::where('status', 1)->get(),
        ]);
    }

    // ================================
    // STORE
    // ================================
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'           => 'required',
            'category_id'    => 'required',
            'new_price'      => 'nullable|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'stock'          => 'nullable|integer|min:0',
            'description'    => 'required',
            'advance_amount' => 'nullable|numeric|min:0',
            'reseller_price' => 'nullable|numeric|min:0',

            'product_type'        => 'required|in:physical,digital',
            'digital_file'        => 'nullable|file|max:51200', // 50MB
            'download_limit'      => 'nullable|integer|min:1',
            'download_expire_days'=> 'nullable|integer|min:1',
            
            // Wholesale fields
            'is_wholesale'        => 'nullable',
            'wholesale_price'    => 'nullable|array',
            'wholesale_price.*.min_quantity' => 'nullable|integer|min:1',
            'wholesale_price.*.max_quantity' => 'nullable|integer|min:1',
            'wholesale_price.*.wholesale_price' => 'nullable|numeric|min:0',
        ]);

        $last_id = Product::max('id') + 1;

        // proSize, proColor, image, meta_image, variant_price, variant_image, digital_file বাদ
        $input = $request->except([
            'image',
            'image_color',
            'image_size',
            'meta_image',
            'variant_price',
            'variant_image',
            'digital_file',
            'proSize',
            'proColor',
            'pro_video_source',
            'pro_video_file',
        ]);

        foreach ($input as $key => $val) {
            if (is_array($val)) {
                // নেস্টেড অ্যারে হলে implode করবেন না (Array to string conversion এড়াতে)
                $allScalar = !array_filter($val, 'is_array');
                if ($allScalar) {
                    $input[$key] = implode(',', $val);
                } else {
                    unset($input[$key]);
                }
            }
        }

        // PRODUCT TYPE
        $isDigital = $request->product_type === 'digital';
        $input['is_digital'] = $isDigital ? 1 : 0;

        if ($isDigital) {
            $input['advance_amount'] = 0; // ডিজিটাল হলে advance লাগবে না
        } else {
            $input['advance_amount'] = $request->advance_amount ?? 0;
        }

        // Slug
        $input['slug'] = strtolower(preg_replace('/[\/\s]+/', '-', $request->name.'-'.$last_id));

        // VIDEO — YouTube or local upload
        $this->handleVideoInput($request, $input, null);

        // Price & stock optional – না দিলে ০ ধরা হবে
        $input['new_price']      = $request->filled('new_price') ? $request->new_price : 0;
        $input['purchase_price'] = $request->filled('purchase_price') ? $request->purchase_price : 0;
        $input['stock']          = $request->filled('stock') ? (int) $request->stock : 0;

        // Status flags
        $input['status']          = $request->status ? 1 : 0;
        $input['free_delivery']   = $request->free_delivery ? 1 : 0;
        $input['approval_status'] = 'approved'; // Admin created products are auto-approved
        $input['topsale']         = $request->topsale ? 1 : 0;
        $input['feature_product'] = $request->feature_product ? 1 : 0;
        $input['product_code']    = 'P' . str_pad($last_id, 4, '0', STR_PAD_LEFT);
        
        // Wholesale settings
        $input['is_wholesale'] = $request->is_wholesale ? 1 : 0;

        // SEO
        $input['meta_title']       = $request->meta_title ?? $request->name;
        $input['meta_description'] = $request->meta_description ?? Str::limit(strip_tags($request->description), 160);
        $input['meta_keywords']    = $request->meta_keywords ?? '';

        // META IMAGE UPLOAD
        if ($request->hasFile('meta_image')) {
            $input['meta_image'] = ImageOptimizer::store(
                $request->file('meta_image'),
                'public/uploads/product/meta/',
                ImageOptimizer::basenameFromOriginal($request->file('meta_image'), 'meta')
            );
        }

        // DIGITAL FILE UPLOAD
        if ($isDigital) {
            $input['download_limit']       = $request->download_limit ?? 5;
            $input['download_expire_days'] = $request->download_expire_days ?? 7;

            if ($request->hasFile('digital_file')) {
                $file = $request->file('digital_file');
                // storage/app/private/digital-products/...
                $path = $file->store('digital-products', 'private');
                $input['digital_file'] = $path;
            } else {
                $input['digital_file'] = null;
            }
        } else {
            $input['digital_file']        = null;
            $input['download_limit']      = null;
            $input['download_expire_days']= null;
        }

        // CREATE PRODUCT
        $product = Product::create($input);

        // সাইজ ও কালার অপশনাল – দিলে attach, না দিলে কিছু করব না
        if ($request->proSize && is_array($request->proSize) && count($request->proSize) > 0) {
            $product->sizes()->attach($request->proSize);
        }
        if ($request->proColor && is_array($request->proColor) && count($request->proColor) > 0) {
            $product->colors()->attach($request->proColor);
        }

        // PRODUCT IMAGES (with optional color/size per image)
        if ($request->hasFile('image')) {
            $imageColors = $request->image_color ?? [];
            $imageSizes  = $request->image_size ?? [];
            foreach ($request->file('image') as $idx => $img) {
                $imagePath = ImageOptimizer::store($img, 'public/uploads/product/');

                $colorId = $imageColors[$idx] ?? null;
                $sizeId  = $imageSizes[$idx] ?? null;

                Productimage::create([
                    'product_id' => $product->id,
                    'image'      => $imagePath,
                    'color_id'   => $colorId ?: null,
                    'size_id'    => $sizeId ?: null,
                ]);
            }

            // যদি meta_image সেট করা না থাকে, প্রথম ইমেজকে meta_image করো
            if (empty($product->meta_image) && $product->images()->first()) {
                $product->update(['meta_image' => $product->images()->first()->image]);
            }
        }

        // VARIANT PRICES
        if ($request->variant_price && is_array($request->variant_price)) {
            foreach ($request->variant_price as $variant) {
                // Skip if neither color nor size is selected
                if (empty($variant['color_id']) && empty($variant['size_id'])) {
                    continue;
                }
                
                ProductVariantPrice::create([
                    'product_id' => $product->id,
                    'color_id'   => $variant['color_id'] ?? null,
                    'size_id'    => $variant['size_id'] ?? null,
                    'price'      => $variant['price'] ?? 0,
                    'stock'      => $variant['stock'] ?? 0,
                ]);
            }
        }

        // VARIANT IMAGES (from Product Variants - variant_image[row_index][image], image_row links to row)
        if ($request->variant_price && is_array($request->variant_price)) {
            $savedFiles = [];
            $doneKeys = [];
            foreach ($request->variant_price as $idx => $vp) {
                $imageRow = $vp['image_row'] ?? $idx;
                $colorId = $vp['color_id'] ?? null;
                $sizeId = $vp['size_id'] ?? null;
                if (!$colorId) continue;
                $file = $request->file("variant_image.{$imageRow}.image");
                if (!$file) continue;
                $key = $colorId . '_' . ($sizeId ?: '0');
                if (isset($doneKeys[$key])) continue;
                $doneKeys[$key] = true;
                if (!isset($savedFiles[$imageRow])) {
                    $savedFiles[$imageRow] = ImageOptimizer::store($file, 'public/uploads/product/');
                }
                Productimage::create([
                    'product_id' => $product->id,
                    'image'      => $savedFiles[$imageRow],
                    'color_id'   => $colorId,
                    'size_id'    => $sizeId ?: null,
                ]);
            }
        }

        // WHOLESALE PRICING TIERS
        if ($input['is_wholesale'] && $request->wholesale_price && is_array($request->wholesale_price)) {
            foreach ($request->wholesale_price as $tier) {
                if (!empty($tier['min_quantity']) && !empty($tier['wholesale_price'])) {
                    ProductWholesalePrice::create([
                        'product_id'      => $product->id,
                        'min_quantity'    => $tier['min_quantity'],
                        'max_quantity'    => $tier['max_quantity'] ?? null,
                        'wholesale_price' => $tier['wholesale_price'],
                        'stock'           => $tier['stock'] ?? 0,
                    ]);
                }
            }
        }

        Toastr::success('Product created successfully!');
        return redirect()->route('products.index');
    }

    // ================================
    // SHOW
    // ================================
    public function show($id)
    {
        $product = Product::with([
            'image',
            'images',
            'category',
            'subcategory',
            'childcategory',
            'brand',
            'vendor',
            'colors',
            'sizes',
            'variantPrices',
            'wholesalePrices'
        ])->findOrFail($id);
            
        return view('backEnd.product.show', compact('product'));
    }

    // ================================
    // EDIT
    // ================================
    public function edit($id)
    {
        $edit = Product::with(['images.color','images.size','variantPrices'])->findOrFail($id);

        return view('backEnd.product.edit', [
            'edit_data'     => $edit,
            'categories'    => Category::where('parent_id', 0)->where('status', 1)->with('childrenCategories')->get(),
            'subcategory'   => Subcategory::where('category_id', $edit->category_id)->get(),
            'childcategory' => Childcategory::where('subcategory_id', $edit->subcategory_id)->get(),
            'brands'        => Brand::where('status', 1)->get(),
            'totalsizes'    => Size::where('status', 1)->get(),
            'totalcolors'   => Color::where('status', 1)->get(),
            'selectcolors'  => Productcolor::where('product_id', $id)->get(),
            'selectsizes'   => Productsize::where('product_id', $id)->get(),
            'wholesalePrices' => \App\Models\ProductWholesalePrice::where('product_id', $id)->get(),
        ]);
    }

    // ================================
    // UPDATE
    // ================================
    public function update(Request $request)
    {
        $this->validate($request, [
            'name'           => 'required',
            'category_id'    => 'required',
            'new_price'      => 'nullable|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'stock'          => 'nullable|integer|min:0',
            'description'    => 'required',
            'reseller_price' => 'nullable|numeric|min:0',

            'product_type'        => 'required|in:physical,digital',
            'digital_file'        => 'nullable|file|max:51200',
            'download_limit'      => 'nullable|integer|min:1',
            'download_expire_days'=> 'nullable|integer|min:1',
            
            // Wholesale fields
            'is_wholesale'        => 'nullable',
            'wholesale_price'    => 'nullable|array',
            'wholesale_price.*.min_quantity' => 'nullable|integer|min:1',
            'wholesale_price.*.max_quantity' => 'nullable|integer|min:1',
            'wholesale_price.*.wholesale_price' => 'nullable|numeric|min:0',
        ]);

        $product = Product::findOrFail($request->id);

        $input = $request->except([
            'image',
            'image_color',
            'image_size',
            'meta_image',
            'variant_price',
            'variant_image',
            'wholesale_price',
            'digital_file',
            'proSize',
            'proColor',
            'pro_video_source',
            'pro_video_file',
        ]);

        foreach ($input as $key => $val) {
            if (is_array($val)) {
                $allScalar = !array_filter($val, 'is_array');
                if ($allScalar) {
                    $input[$key] = implode(',', $val);
                } else {
                    unset($input[$key]);
                }
            }
        }

        // PRODUCT TYPE
        $isDigital = $request->product_type === 'digital';
        $input['is_digital'] = $isDigital ? 1 : 0;

        if ($isDigital) {
            $input['advance_amount'] = 0;
        } else {
            $input['advance_amount'] = $request->advance_amount ?? 0;
        }

        // Price & stock optional – আপডেটে না দিলে ০ ধরা হবে
        $input['new_price']      = $request->filled('new_price') ? $request->new_price : 0;
        $input['purchase_price'] = $request->filled('purchase_price') ? $request->purchase_price : 0;
        $input['stock']          = $request->filled('stock') ? (int) $request->stock : 0;

        // Slug & flags
        $input['slug']            = strtolower(preg_replace('/[\/\s]+/', '-', $request->name.'-'.$product->id));
        $input['status']          = $request->status ? 1 : 0;
        $input['topsale']         = $request->topsale ? 1 : 0;
        $input['free_delivery']   = $request->free_delivery ? 1 : 0;
        $input['feature_product'] = $request->feature_product ? 1 : 0;

        // VIDEO — YouTube or local upload
        $this->handleVideoInput($request, $input, $product);
        
        // Wholesale settings
        $input['is_wholesale'] = $request->is_wholesale ? 1 : 0;

        // SEO
        $input['meta_title']       = $request->meta_title ?? $request->name;
        $input['meta_description'] = $request->meta_description ?? $request->description;
        $input['meta_keywords']    = $request->meta_keywords ?? '';

        // META IMAGE UPDATE
        if ($request->hasFile('meta_image')) {
            if ($product->meta_image && file_exists($product->meta_image)) {
                @unlink($product->meta_image);
            }
            $input['meta_image'] = ImageOptimizer::store(
                $request->file('meta_image'),
                'public/uploads/product/meta/',
                ImageOptimizer::basenameFromOriginal($request->file('meta_image'), 'meta')
            );
        }

        // DIGITAL FILE UPDATE
        if ($isDigital) {
            $input['download_limit']       = $request->download_limit ?? $product->download_limit ?? 5;
            $input['download_expire_days'] = $request->download_expire_days ?? $product->download_expire_days ?? 7;

            if ($request->hasFile('digital_file')) {
                // পুরনো ফাইল ডিলিট
                if ($product->digital_file && Storage::disk('private')->exists($product->digital_file)) {
                    Storage::disk('private')->delete($product->digital_file);
                }

                $file = $request->file('digital_file');
                $path = $file->store('digital-products', 'private');
                $input['digital_file'] = $path;
            } // নতুন ফাইল না দিলে digital_file আগেরটাই থাকবে (update-এ key না পাঠালে unchanged)
        } else {
            // এখন যদি physical করে দাও, তাহলে digital ইনফো ডিলিট
            if ($product->digital_file && Storage::disk('private')->exists($product->digital_file)) {
                Storage::disk('private')->delete($product->digital_file);
            }
            $input['digital_file']        = null;
            $input['download_limit']      = null;
            $input['download_expire_days']= null;
        }

        // PRODUCT UPDATE
        $product->update($input);
        Cache::forget('product_details_' . $product->slug);

        // SIZE & COLOR
        $product->sizes()->sync($request->proSize ?? []);
        $product->colors()->sync($request->proColor ?? []);

        // NEW IMAGES (with optional color/size per image)
        if ($request->hasFile('image')) {
            $imageColors = $request->image_color ?? [];
            $imageSizes  = $request->image_size ?? [];
            foreach ($request->file('image') as $idx => $img) {
                $imagePath = ImageOptimizer::store($img, 'public/uploads/product/');

                $colorId = $imageColors[$idx] ?? null;
                $sizeId  = $imageSizes[$idx] ?? null;

                Productimage::create([
                    'product_id' => $product->id,
                    'image'      => $imagePath,
                    'color_id'   => $colorId ?: null,
                    'size_id'    => $sizeId ?: null,
                ]);
            }
        }

        // VARIANT IMAGES (from Product Variants - variant_image[row][image], image_row links to row)
        if ($request->variant_price && is_array($request->variant_price)) {
            $savedFiles = [];
            $doneKeys = [];
            foreach ($request->variant_price as $idx => $vp) {
                $imageRow = $vp['image_row'] ?? $idx;
                $colorId = $vp['color_id'] ?? null;
                $sizeId = $vp['size_id'] ?? null;
                if (!$colorId) continue;
                $file = $request->file("variant_image.{$imageRow}.image");
                if (!$file) continue;
                $key = $colorId . '_' . ($sizeId ?: '0');
                if (isset($doneKeys[$key])) continue;
                $doneKeys[$key] = true;
                if (!isset($savedFiles[$imageRow])) {
                    $savedFiles[$imageRow] = ImageOptimizer::store($file, 'public/uploads/product/');
                }
                Productimage::create([
                    'product_id' => $product->id,
                    'image'      => $savedFiles[$imageRow],
                    'color_id'   => $colorId,
                    'size_id'    => $sizeId ?: null,
                ]);
            }
        }

        // VARIANTS UPDATE
        ProductVariantPrice::where('product_id', $product->id)->delete();

        if ($request->variant_price && is_array($request->variant_price)) {
            foreach ($request->variant_price as $variant) {
                if (empty($variant['color_id']) && empty($variant['size_id'])) continue;

                ProductVariantPrice::create([
                    'product_id' => $product->id,
                    'color_id'   => $variant['color_id'] ?? null,
                    'size_id'    => $variant['size_id'] ?? null,
                    'price'      => $variant['price'] ?? 0,
                    'stock'      => $variant['stock'] ?? 0,
                ]);
            }
        }

        // WHOLESALE PRICING TIERS UPDATE
        ProductWholesalePrice::where('product_id', $product->id)->delete();

        if ($input['is_wholesale'] && $request->wholesale_price && is_array($request->wholesale_price)) {
            foreach ($request->wholesale_price as $tier) {
                if (!empty($tier['min_quantity']) && !empty($tier['wholesale_price'])) {
                    ProductWholesalePrice::create([
                        'product_id'      => $product->id,
                        'min_quantity'    => $tier['min_quantity'],
                        'max_quantity'    => $tier['max_quantity'] ?? null,
                        'wholesale_price' => $tier['wholesale_price'],
                        'stock'           => $tier['stock'] ?? 0,
                    ]);
                }
            }
        }

        Toastr::success('Product updated successfully!');
        return redirect()->route('products.index');
    }

    // ================================
    // DELETE / IMAGE DELETE
    // ================================
    public function destroy(Request $request)
    {
        $product = Product::findOrFail($request->hidden_id);

        // digital ফাইল থাকলে ডিলিট
        if ($product->digital_file && Storage::disk('private')->exists($product->digital_file)) {
            Storage::disk('private')->delete($product->digital_file);
        }

        // uploaded video থাকলে ডিলিট
        if ($product->pro_video_path && file_exists($product->pro_video_path)) {
            @unlink($product->pro_video_path);
        }

        $product->delete();
        Toastr::success('Product deleted successfully');
        return redirect()->back();
    }

    public function imgdestroy(Request $request)
    {
        $img = Productimage::findOrFail($request->id);
        $imagePath = $img->image;
        $productId = $img->product_id;

        // Delete all Productimage rows with same image path (variant images saved per size share same path)
        $allSame = Productimage::where('product_id', $productId)->where('image', $imagePath)->get();

        // Try delete from public disk if stored via storage/app/public/...
        $possiblePublicPath = str_replace('storage/', '', $imagePath); // if DB stores 'storage/uploads/...'
        if ($possiblePublicPath && Storage::disk('public')->exists($possiblePublicPath)) {
            Storage::disk('public')->delete($possiblePublicPath);
        } else {
            // fallback: if absolute or relative path present on filesystem
            if (file_exists(public_path($imagePath))) {
                @unlink(public_path($imagePath));
            } elseif (file_exists($imagePath)) {
                @unlink($imagePath);
            }
        }

        foreach ($allSame as $m) {
            $m->delete();
        }
        $product = Product::find($productId);
        if ($product) {
            Cache::forget('product_details_' . $product->slug);
        }

        Toastr::success('Image deleted successfully!');
        return redirect()->back();
    }

    // ================================
    // BULK ACTIONS (AJAX / POST)
    // ================================
    public function update_deals(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'status' => 'required|in:0,1',
        ]);

        Product::whereIn('id', $request->product_ids)->update(['topsale' => $request->status]);

        return response()->json(['status' => 'success', 'message' => 'Products updated successfully']);
    }

    public function update_status(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'status' => 'required|in:0,1',
        ]);

        Product::whereIn('id', $request->product_ids)->update(['status' => $request->status]);

        return response()->json(['status' => 'success', 'message' => 'Products status updated']);
    }

    // ================================
    // PENDING PRODUCTS (FOR APPROVAL)
    // ================================
    public function pending(Request $request)
    {
        $query = Product::where('approval_status', 'pending')
            ->orderBy('id','DESC')
            ->with('image','category','vendor');

        if ($request->keyword) {
            $query->where('name', 'LIKE', '%' . $request->keyword . "%");
        }

        $data = $query->paginate(10);
        return view('backEnd.product.pending', compact('data'));
    }

    // ================================
    // APPROVE PRODUCT
    // ================================
    public function approve(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->approval_status = 'approved';
        $product->save();

        Toastr::success('Product approved successfully!');
        return redirect()->back();
    }

    // ================================
    // REJECT PRODUCT
    // ================================
    public function reject(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:products,id',
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $product = Product::findOrFail($request->id);
        $product->approval_status = 'rejected';
        $product->save();

        // Store rejection reason if provided (you can add a rejection_reason column later)
        // $product->rejection_reason = $request->rejection_reason;
        // $product->save();

        Toastr::success('Product rejected successfully!');
        return redirect()->back();
    }

    // ================================
    // VIDEO HELPERS
    // ================================

    /**
     * Handle pro_video + pro_video_type + pro_video_path for store/update.
     * $product = null on create, Product instance on update.
     */
    private function handleVideoInput(Request $request, array &$input, $product): void
    {
        $videoType = $request->input('pro_video_source', 'youtube'); // 'youtube' or 'upload'

        if ($videoType === 'upload') {
            if ($request->hasFile('pro_video_file')) {
                // Delete old uploaded video if exists
                if ($product && $product->pro_video_path && file_exists($product->pro_video_path)) {
                    @unlink($product->pro_video_path);
                }

                $file      = $request->file('pro_video_file');
                $ext       = $file->getClientOriginalExtension();
                $fileName  = time() . '-video.' . $ext;
                $dir       = 'public/uploads/product/videos/';

                // Image pattern অনুসরণ করে — CWD (htdocs) relative path
                if (!is_dir($dir)) {
                    mkdir($dir, 0775, true);
                }

                $file->move($dir, $fileName);

                $input['pro_video']      = null;
                $input['pro_video_type'] = 'upload';
                $input['pro_video_path'] = $dir . $fileName;
            } else {
                // No new file — keep existing if update, else clear
                if ($product) {
                    unset($input['pro_video'], $input['pro_video_type'], $input['pro_video_path']);
                } else {
                    $input['pro_video']      = null;
                    $input['pro_video_type'] = null;
                    $input['pro_video_path'] = null;
                }
            }
        } else {
            // YouTube mode
            $ytId = $this->getYouTubeVideoId($request->input('pro_video'));

            // Delete old uploaded video if switching from upload to YouTube
            if ($product && $product->pro_video_path && file_exists($product->pro_video_path)) {
                @unlink($product->pro_video_path);
            }

            $input['pro_video']      = $ytId;
            $input['pro_video_type'] = $ytId ? 'youtube' : null;
            $input['pro_video_path'] = null;
        }
    }

    private function getYouTubeVideoId($input)
    {
        if (!$input) return null;

        // শুধু ১১ ক্যারেক্টারের ID হলে
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $input)) {
            return $input;
        }

        // পূর্ণ URL হলে
        preg_match(
            '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/',
            $input,
            $matches
        );

        return $matches[1] ?? null;
    }

    /**
     * Generate Bengali SEO product description via Gemini AI.
     */
    public function generateAIDescription(Request $request, GeminiAiService $geminiAiService)
    {
        $request->validate([
            'name'         => 'required_without:product_name|string|max:255',
            'product_name' => 'required_without:name|string|max:255',
            'category'     => 'required_without:category_id|string|max:255',
            'category_id'  => 'nullable|integer|exists:categories,id',
        ]);

        $productName = trim((string) ($request->input('name') ?: $request->input('product_name')));
        $categoryName = trim((string) $request->input('category', ''));

        if ($categoryName === '' && $request->filled('category_id')) {
            $categoryName = (string) (Category::where('id', $request->category_id)->value('name') ?? '');
        }

        if ($categoryName === '') {
            return response()->json([
                'success' => false,
                'message' => 'Category is required',
            ], 422);
        }

        $prompt = <<<PROMPT
You are an expert Bengali e-commerce copywriter and SEO specialist for the Bangladesh market.

Write a highly converting, SEO-optimized product description in Bengali for:

- Product name: {$productName}
- Category: {$categoryName}

Requirements:
- Language: Bengali (বাংলা) only
- Tone: trustworthy, persuasive, customer-friendly
- Length: 120-200 words
- Naturally include the product name and category-related keywords (no keyword stuffing)
- Highlight key benefits, use cases, and why customers should buy now
- Use simple HTML only: <p>, <ul>, <li>, <strong> (no <html>, <body>, or markdown)
- Do NOT include price, fake claims, or text outside the description
- Return ONLY the HTML description, nothing else
PROMPT;

        try {
            $description = trim($geminiAiService->generateResponse($prompt));

            return response()->json([
                'success'     => true,
                'description' => $description,
                'product_name'=> $productName,
                'category'    => $categoryName,
            ]);
        } catch (\Throwable $e) {
            Log::error('Gemini product description generation failed', [
                'product_name' => $productName,
                'category'     => $categoryName,
                'error'        => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
