<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ResellerLandingPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;

class LandingProductController extends Controller
{
    protected function getLanding(): ResellerLandingPage
    {
        $landing = ResellerLandingPage::where('user_id', Auth::guard('admin')->id())->first();
        if (!$landing) {
            abort(404, 'ল্যান্ডিং পেজ সেট আপ করুন প্রথমে।');
        }
        return $landing;
    }

    public function index()
    {
        $user = Auth::guard('admin')->user();
        $landing = $this->getLanding();
        $landing->load('landingProducts.image');

        $products = $landing->landingProducts()->orderByPivot('created_at', 'desc')->get();

        return view('reseller.landing.products', compact('user', 'landing', 'products'));
    }

    public function addForm()
    {
        $user = Auth::guard('admin')->user();
        $landing = $this->getLanding();

        $availableProducts = Product::whereNotNull('reseller_price')
            ->where('reseller_price', '>', 0)
            ->where('status', 1)
            ->whereNotIn('id', function ($query) use ($landing) {
                $query->select('product_id')
                    ->from('reseller_landing_products')
                    ->where('reseller_landing_page_id', $landing->id);
            })
            ->when(function ($q) {
                if (\Schema::hasColumn('products', 'approval_status')) {
                    $q->where('approval_status', 'approved');
                }
            })
            ->with('image')
            ->orderBy('name')
            ->get();

        return view('reseller.landing.add-products', compact('user', 'landing', 'availableProducts'));
    }

    public function add(Request $request)
    {
        $landing = $this->getLanding();

        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'custom_price' => 'required|numeric|min:0',
        ]);

        $product = Product::where('id', $request->product_id)
            ->whereNotNull('reseller_price')
            ->where('reseller_price', '>', 0)
            ->where('status', 1)
            ->first();

        if (!$product) {
            Toastr::error('প্রোডাক্টটি যোগ করা যাবে না।', 'Error');
            return back();
        }

        $exists = $landing->landingProducts()->where('product_id', $product->id)->exists();
        if ($exists) {
            Toastr::warning('প্রোডাক্টটি ইতিমধ্যে রয়েছে।', 'Warning');
            return back();
        }

        $landing->landingProducts()->attach($product->id, [
            'custom_price' => $request->custom_price,
        ]);

        Toastr::success('প্রোডাক্ট যোগ হয়েছে।', 'Success');
        return redirect()->route('reseller.landing.products');
    }

    public function updatePrice(Request $request)
    {
        $landing = $this->getLanding();

        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'custom_price' => 'required|numeric|min:0',
        ]);

        $updated = $landing->landingProducts()->updateExistingPivot($request->product_id, [
            'custom_price' => $request->custom_price,
        ]);

        if ($updated) {
            Toastr::success('প্রাইস আপডেট হয়েছে।', 'Success');
        } else {
            Toastr::error('প্রোডাক্টটি আপনার ল্যান্ডিং পেজে নেই।', 'Error');
        }

        return back();
    }

    public function remove(Request $request, int $productId)
    {
        $landing = $this->getLanding();
        $landing->landingProducts()->detach($productId);
        Toastr::success('প্রোডাক্ট রিমুভ হয়েছে।', 'Success');
        return redirect()->route('reseller.landing.products');
    }
}
