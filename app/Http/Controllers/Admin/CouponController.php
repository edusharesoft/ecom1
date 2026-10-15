<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use Brian2694\Toastr\Facades\Toastr;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest()->get();
        return view('backEnd.coupon.index', compact('coupons'));
    }

    public function create()
    {
        return view('backEnd.coupon.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:coupons',
            'type' => 'required',
            'value' => 'required|numeric|min:1',
        ]);

        Coupon::create($request->all());
        Toastr::success('Coupon created successfully', 'Success');
        return redirect()->route('admin.coupons.index');
    }

    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);
        return view('backEnd.coupon.edit', compact('coupon'));
    }

    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->update($request->all());
        Toastr::success('Coupon updated successfully', 'Success');
        return redirect()->route('admin.coupons.index');
    }

    public function destroy($id)
    {
        Coupon::destroy($id);
        Toastr::success('Coupon deleted successfully', 'Success');
        return redirect()->back();
    }
}
