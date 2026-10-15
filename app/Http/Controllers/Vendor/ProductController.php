<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
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
use App\Models\Vendor;
use Toastr;
use File;
use Illuminate\Support\Str;
use DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
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
        $vendorId = Auth::user()->vendor_id;

        if (!$vendorId) {
            Toastr::error('Vendor profile not found', 'Error');
            return redirect()->route('vendor.dashboard');
        }

        $query = Product::where('vendor_id', $vendorId)
            ->orderBy('id','DESC')
            ->with('image','category');

        if ($request->keyword) {
            $query->where('name', 'LIKE', '%' . $request->keyword . "%");
        }

        $data = $query->paginate(10);
        $vendor = Vendor::findOrFail($vendorId);
        return view('vendor.products.index', compact('data', 'vendor'));
    }

    // ================================
    // CREATE
    // ================================
    public function create()
    {
        $vendorId = Auth::user()->vendor_id;

        if (!$vendorId) {
            Toastr::error('Vendor profile not found', 'Error');
            return redirect()->route('vendor.dashboard');
        }

        $vendor = Vendor::findOrFail($vendorId);

        // Check if vendor is verified
        if ($vendor->verification_status != 'approved') {
            Toastr::error('Please verify your account first to upload products. Upload your Voter ID card and self image for verification.', 'Account Not Verified');
            return redirect()->route('vendor.verification.index');
        }

        return view('vendor.products.create', [
            'vendor'     => $vendor,
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
        $vendorId = Auth::user()->vendor_id;

        if (!$vendorId) {
            Toastr::error('Vendor profile not found', 'Error');
            return redirect()->route('vendor.dashboard');
        }

        $vendor = Vendor::findOrFail($vendorId);

        // Check if vendor is verified
        if ($vendor->verification_status != 'approved') {
            Toastr::error('Please verify your account first to upload products. Upload your Voter ID card and self image for verification.', 'Account Not Verified');
            return redirect()->route('vendor.verification.index');
        }

        $this->validate($request, [
            'name'           => 'required',
            'category_id'    => 'required',
            'new_price'      => 'nullable|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'stock'          => 'nullable|integer|min:0',
            'description'    => 'required',
            'advance_amount' => 'nullable|numeric|min:0',

            'product_type'        => 'required|in:physical,digital',
            'digital_file'        => 'nullable|file|max:51200', // 50MB
            'download_limit'      => 'nullable|integer|min:1',
            'download_expire_days'=> 'nullable|integer|min:1',
            
            // Variant fields (optional)
            'variant_price'       => 'nullable|array',
            'variant_price.*.color_id' => 'nullable|exists:colors,id',
            'variant_price.*.size_id'  => 'nullable|exists:sizes,id',
            'variant_price.*.price'    => 'nullable|numeric|min:0',
            'variant_price.*.stock'   => 'nullable|integer|min:0',
            'proSize'                => 'nullable|array',
            'proColor'                => 'nullable|array',
            
            // Wholesale fields
            'is_wholesale'        => 'nullable',
            'wholesale_price'    => 'nullable|array',
            'wholesale_price.*.min_quantity' => 'nullable|integer|min:1',
            'wholesale_price.*.max_quantity' => 'nullable|integer|min:1',
            'wholesale_price.*.wholesale_price' => 'nullable|numeric|min:0',
        ]);

        $last_id = Product::max('id') + 1;

        // proSize, proColor, image, meta_image, variant_price, variant_image, wholesale_price, digital_file বাদ
        $input = $request->except([
            'image',
            'meta_image',
            'variant_price',
            'variant_image',
            'wholesale_price',
            'digital_file',
            'proSize',
            'proColor',
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

        // Set vendor_id automatically
        $input['vendor_id'] = $vendorId;

        // প্রাইস ও স্টক অপশনাল – না দিলে ০
        $input['new_price']      = $request->filled('new_price') ? $request->new_price : 0;
        $input['purchase_price'] = $request->filled('purchase_price') ? $request->purchase_price : 0;
        $input['stock']          = $request->filled('stock') ? (int) $request->stock : 0;

        // PRODUCT TYPE
        $isDigital = $request->product_type === 'digital';
        $input['is_digital'] = $isDigital ? 1 : 0;

        if ($isDigital) {
            $input['advance_amount'] = 0; // ডিজিটাল হলে advance লাগবে না
        } else {
            $input['advance_amount'] = $request->advance_amount ?? 0;
        }

        // Slug & video
        $input['slug']      = strtolower(preg_replace('/[\/\s]+/', '-', $request->name.'-'.$last_id));
        $input['pro_video'] = $this->getYouTubeVideoId($request->pro_video);

        // Status flags
        $input['status']          = $request->status ? 1 : 0;
        $input['approval_status'] = 'pending'; // Vendor products need admin approval
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
            $metaImg  = $request->file('meta_image');
            $metaName = time().'-meta-'.$metaImg->getClientOriginalName();
            $metaPath = 'public/uploads/product/meta/';
            $metaImg->move($metaPath, $metaName);
            $input['meta_image'] = $metaPath.$metaName;
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

        // সাইজ ও কালার অপশনাল – দিলে attach, না দিলে বাদ
        if ($request->proSize && is_array($request->proSize) && count($request->proSize) > 0) {
            $product->sizes()->attach($request->proSize);
        }
        if ($request->proColor && is_array($request->proColor) && count($request->proColor) > 0) {
            $product->colors()->attach($request->proColor);
        }

        // PRODUCT IMAGES
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $img) {
                $name = time().'-'.$img->getClientOriginalName();
                $name = strtolower(preg_replace('/\s+/', '-', $name));
                $path = 'public/uploads/product/';
                $img->move($path, $name);

                Productimage::create([
                    'product_id' => $product->id,
                    'image'      => $path.$name,
                ]);
            }

            // যদি meta_image সেট করা না থাকে, প্রথম ইমেজকে meta_image করো
            if (empty($product->meta_image) && $product->images()->first()) {
                $product->update(['meta_image' => $product->images()->first()->image]);
            }
        }

        // VARIANT IMAGES (from Product Variants - variant_image[row][image])
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
                    $name = time().'-'.uniqid().'-'.$file->getClientOriginalName();
                    $name = strtolower(preg_replace('/\s+/', '-', $name));
                    $path = 'public/uploads/product/';
                    $file->move($path, $name);
                    $savedFiles[$imageRow] = $path.$name;
                }
                Productimage::create([
                    'product_id' => $product->id,
                    'image'      => $savedFiles[$imageRow],
                    'color_id'   => $colorId,
                    'size_id'    => $sizeId ?: null,
                ]);
            }
        }

        // VARIANT PRICES
        if ($request->variant_price && is_array($request->variant_price)) {
            foreach ($request->variant_price as $variant) {
                // Skip if neither color nor size is selected
                if (empty($variant['color_id']) && empty($variant['size_id'])) {
                    continue;
                }
                
                // Convert empty string to null
                $colorId = !empty($variant['color_id']) ? $variant['color_id'] : null;
                $sizeId = !empty($variant['size_id']) ? $variant['size_id'] : null;
                
                ProductVariantPrice::create([
                    'product_id' => $product->id,
                    'color_id'   => $colorId,
                    'size_id'    => $sizeId,
                    'price'      => !empty($variant['price']) ? $variant['price'] : 0,
                    'stock'      => !empty($variant['stock']) ? $variant['stock'] : 0,
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

        Toastr::success('Product created successfully! It will be published after admin approval.');
        return redirect()->route('vendor.products.index');
    }

    // ================================
    // EDIT
    // ================================
    public function edit($id)
    {
        $vendorId = Auth::user()->vendor_id;
        $vendor = Vendor::findOrFail($vendorId);
        $edit = Product::where('vendor_id', $vendorId)
            ->with(['images.color','images.size','variantPrices'])
            ->findOrFail($id);

        // Group variant prices by color_id, price, and stock (combine multiple sizes)
        $groupedVariants = [];
        $variantPrices = $edit->variantPrices;
        
        foreach ($variantPrices as $variant) {
            $key = ($variant->color_id ?? 'no_color') . '_' . $variant->price . '_' . $variant->stock;
            
            if (!isset($groupedVariants[$key])) {
                $groupedVariants[$key] = [
                    'color_id' => $variant->color_id,
                    'size_ids' => [],
                    'price' => $variant->price,
                    'stock' => $variant->stock,
                ];
            }
            
            if ($variant->size_id) {
                $groupedVariants[$key]['size_ids'][] = $variant->size_id;
            }
        }
        
        // Convert to indexed array for view
        $groupedVariants = array_values($groupedVariants);

        return view('vendor.products.edit', [
            'vendor'        => $vendor,
            'edit_data'     => $edit,
            'categories'    => Category::where('parent_id', 0)->where('status', 1)->with('childrenCategories')->get(),
            'subcategory'   => Subcategory::where('category_id', $edit->category_id)->get(),
            'childcategory' => Childcategory::where('subcategory_id', $edit->subcategory_id)->get(),
            'brands'        => Brand::where('status', 1)->get(),
            'totalsizes'    => Size::where('status', 1)->get(),
            'totalcolors'   => Color::where('status', 1)->get(),
            'selectcolors'  => Productcolor::where('product_id', $id)->get(),
            'selectsizes'   => Productsize::where('product_id', $id)->get(),
            'wholesalePrices' => ProductWholesalePrice::where('product_id', $id)->get(),
            'groupedVariants' => $groupedVariants,
        ]);
    }

    // ================================
    // UPDATE
    // ================================
    public function update(Request $request)
    {
        $vendorId = Auth::user()->vendor_id;
        $product = Product::where('vendor_id', $vendorId)->findOrFail($request->id);

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
            
            // Variant fields (optional)
            'variant_price'       => 'nullable|array',
            'variant_price.*.color_id' => 'nullable|exists:colors,id',
            'variant_price.*.size_id'  => 'nullable|exists:sizes,id',
            'variant_price.*.price'    => 'nullable|numeric|min:0',
            'variant_price.*.stock'   => 'nullable|integer|min:0',
            'proSize'                => 'nullable|array',
            'proColor'                => 'nullable|array',
            
            // Wholesale fields
            'is_wholesale'        => 'nullable',
            'wholesale_price'    => 'nullable|array',
            'wholesale_price.*.min_quantity' => 'nullable|integer|min:1',
            'wholesale_price.*.max_quantity' => 'nullable|integer|min:1',
            'wholesale_price.*.wholesale_price' => 'nullable|numeric|min:0',
        ]);

        $input = $request->except([
            'image',
            'meta_image',
            'variant_price',
            'variant_image',
            'wholesale_price',
            'digital_file',
            'proSize',
            'proColor',
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

        // Ensure vendor_id doesn't change
        $input['vendor_id'] = $vendorId;

        // If product was approved and vendor edits it, set back to pending for admin review
        if ($product->approval_status == 'approved') {
            $input['approval_status'] = 'pending';
        }

        // PRODUCT TYPE
        $isDigital = $request->product_type === 'digital';
        $input['is_digital'] = $isDigital ? 1 : 0;

        if ($isDigital) {
            $input['advance_amount'] = 0;
        } else {
            $input['advance_amount'] = $request->advance_amount ?? 0;
        }

        // প্রাইস ও স্টক অপশনাল – আপডেটে না দিলে ০
        $input['new_price']      = $request->filled('new_price') ? $request->new_price : 0;
        $input['purchase_price'] = $request->filled('purchase_price') ? $request->purchase_price : 0;
        $input['stock']          = $request->filled('stock') ? (int) $request->stock : 0;

        // Slug & flags
        $input['slug']            = strtolower(preg_replace('/[\/\s]+/', '-', $request->name.'-'.$product->id));
        $input['status']          = $request->status ? 1 : 0;
        $input['topsale']         = $request->topsale ? 1 : 0;
        $input['feature_product'] = $request->feature_product ? 1 : 0;
        $input['pro_video']       = $this->getYouTubeVideoId($request->pro_video);
        
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
            $metaImg  = $request->file('meta_image');
            $metaName = time().'-meta-'.$metaImg->getClientOriginalName();
            $metaPath = 'public/uploads/product/meta/';
            $metaImg->move($metaPath, $metaName);
            $input['meta_image'] = $metaPath.$metaName;
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
            }
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

        // NEW IMAGES
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $img) {
                $name = time().'-'.$img->getClientOriginalName();
                $name = strtolower(preg_replace('/\s+/', '-', $name));
                $path = 'public/uploads/product/';
                $img->move($path, $name);

                Productimage::create([
                    'product_id' => $product->id,
                    'image'      => $path.$name,
                ]);
            }
        }

        // VARIANT IMAGES (from Product Variants)
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
                    $name = time().'-'.uniqid().'-'.$file->getClientOriginalName();
                    $name = strtolower(preg_replace('/\s+/', '-', $name));
                    $path = 'public/uploads/product/';
                    $file->move($path, $name);
                    $savedFiles[$imageRow] = $path.$name;
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

        Toastr::success('Product updated successfully! If approval status was pending, it will need admin approval again.');
        return redirect()->route('vendor.products.index');
    }

    // ================================
    // DELETE / IMAGE DELETE
    // ================================
    public function destroy(Request $request)
    {
        $vendorId = Auth::user()->vendor_id;
        $product = Product::where('vendor_id', $vendorId)->findOrFail($request->hidden_id);

        // digital ফাইল থাকলে ডিলিট
        if ($product->digital_file && Storage::disk('private')->exists($product->digital_file)) {
            Storage::disk('private')->delete($product->digital_file);
        }

        $product->delete();
        Toastr::success('Product deleted successfully');
        return redirect()->back();
    }

    public function imgdestroy(Request $request)
    {
        $vendorId = Auth::user()->vendor_id;
        $img = Productimage::findOrFail($request->id);

        // Verify the image belongs to vendor's product
        $product = Product::where('vendor_id', $vendorId)->find($img->product_id);
        if (!$product) {
            Toastr::error('Unauthorized action', 'Error');
            return redirect()->back();
        }

        $imagePath = $img->image;
        $productId = $img->product_id;

        // Delete all Productimage rows with same image path (variant images saved per size share same path)
        $allSame = Productimage::where('product_id', $productId)->where('image', $imagePath)->get();

        // Try delete from public disk if stored via storage/app/public/...
        $possiblePublicPath = str_replace('storage/', '', $imagePath);
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
    // YOUTUBE VIDEO HELPER
    // ================================
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
}
