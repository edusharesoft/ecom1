<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Toastr;
use File;

class SettingsController extends Controller
{
    /**
     * Display vendor settings page.
     */
    public function index()
    {
        $user = Auth::user();
        $vendorId = $user->vendor_id;

        if (!$vendorId) {
            Toastr::error('Vendor profile not found', 'Error');
            return redirect()->route('vendor.dashboard');
        }

        $vendor = Vendor::findOrFail($vendorId);

        return view('vendor.settings', compact('vendor', 'user'));
    }

    /**
     * Update vendor shop information.
     */
    public function updateShopInfo(Request $request)
    {
        $user = Auth::user();
        $vendorId = $user->vendor_id;

        if (!$vendorId) {
            Toastr::error('Vendor profile not found', 'Error');
            return redirect()->route('vendor.settings');
        }

        $vendor = Vendor::findOrFail($vendorId);

        $request->validate([
            'shop_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:vendors,email,' . $vendor->id,
            'address' => 'nullable|string|max:500',
        ]);

        // Update vendor info
        $vendor->shop_name = $request->shop_name;
        $vendor->owner_name = $request->owner_name;
        $vendor->phone = $request->phone;
        $vendor->email = $request->email;
        $vendor->address = $request->address;

        // Update slug if shop name changed
        if ($vendor->isDirty('shop_name')) {
            $vendor->slug = Str::slug($request->shop_name . '-' . $vendor->id);
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($vendor->logo && file_exists(public_path($vendor->logo))) {
                @unlink(public_path($vendor->logo));
            }

            $logo = $request->file('logo');
            $logoName = time() . '-logo-' . $logo->getClientOriginalName();
            $logoPath = 'uploads/vendor/logo/';
            $logoFullPath = public_path($logoPath);
            
            // Create directory if not exists
            if (!file_exists($logoFullPath)) {
                File::makeDirectory($logoFullPath, 0755, true);
            }

            $logo->move($logoFullPath, $logoName);
            $vendor->logo = 'public/' . $logoPath . $logoName;
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            // Delete old banner if exists
            if ($vendor->banner && file_exists(public_path($vendor->banner))) {
                @unlink(public_path($vendor->banner));
            }

            $banner = $request->file('banner');
            $bannerName = time() . '-banner-' . $banner->getClientOriginalName();
            $bannerPath = 'uploads/vendor/banner/';
            $bannerFullPath = public_path($bannerPath);
            
            // Create directory if not exists
            if (!file_exists($bannerFullPath)) {
                File::makeDirectory($bannerFullPath, 0755, true);
            }

            $banner->move($bannerFullPath, $bannerName);
            $vendor->banner = 'public/' . $bannerPath . $bannerName;
        }

        $vendor->save();

        // Update user name and email
        $user->name = $request->owner_name;
        $user->email = $request->email;
        $user->save();

        Toastr::success('Shop information updated successfully!');
        return redirect()->route('vendor.settings');
    }

    /**
     * Update vendor profile information.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        // Handle profile image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->image && file_exists(public_path($user->image))) {
                @unlink(public_path($user->image));
            }

            $image = $request->file('image');
            $imageName = time() . '-profile-' . $image->getClientOriginalName();
            $imagePath = 'public/uploads/user/';
            
            // Create directory if not exists
            if (!file_exists($imagePath)) {
                File::makeDirectory($imagePath, 0755, true);
            }

            $image->move($imagePath, $imageName);
            $user->image = $imagePath . $imageName;
        }

        $user->save();

        Toastr::success('Profile updated successfully!');
        return redirect()->route('vendor.settings');
    }

    /**
     * Update vendor password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            Toastr::error('Current password is incorrect', 'Error');
            return redirect()->back()->withInput();
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        Toastr::success('Password updated successfully!');
        return redirect()->route('vendor.settings');
    }
}
