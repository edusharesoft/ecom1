<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\FacebookPageSetting;
use App\Services\FacebookPagePostService;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class FacebookPageController extends Controller
{
    public function settings()
    {
        $setting = FacebookPageSetting::firstOrCreate();
        return view('backEnd.facebook_page.settings', compact('setting'));
    }

    public function saveSettings(Request $request)
    {
        $setting = FacebookPageSetting::firstOrCreate();
        $setting->update([
            'page_id' => $request->page_id,
            'page_access_token' => $request->page_access_token ?: $setting->page_access_token,
            'page_name' => $request->page_name,
            'auto_post_new_products' => $request->boolean('auto_post_new_products'),
            'post_template' => $request->post_template,
        ]);
        Toastr::success('Settings saved', 'Success');
        return redirect()->route('admin.facebook_page.settings');
    }

    public function postProduct(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        $result = app(FacebookPagePostService::class)->postProduct($product);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result);
        }

        if ($result['success']) {
            Toastr::success($result['message'], 'Success');
        } else {
            Toastr::error($result['message'] ?? 'Failed', 'Error');
        }
        return redirect()->back();
    }
}
