<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use Brian2694\Toastr\Facades\Toastr;

class OrderRestrictionSettingController extends Controller
{
    public function index()
    {
        $data = GeneralSetting::first();
        return view('backEnd.order_restriction_setting.index', compact('data'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'order_limit_time' => 'required|integer|min:1',
            'order_limit_qty' => 'required|integer|min:1',
        ]);

        $setting = GeneralSetting::first();
        $setting->order_limit_time = $request->order_limit_time;
        $setting->order_limit_qty = $request->order_limit_qty;
        $setting->save();

        Toastr::success('Order restriction settings updated successfully', 'Success!');
        return redirect()->back();
    }
}
