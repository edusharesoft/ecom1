<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Brian2694\Toastr\Facades\Toastr;

class SettingsController extends Controller
{
    /**
     * Display reseller settings page.
     */
    public function index()
    {
        $user = Auth::guard('admin')->user();
        
        // Get customer record for phone
        $customer = Customer::where('email', $user->email)->first();

        return view('reseller.settings', compact('user', 'customer'));
    }

    /**
     * Update reseller profile information.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::guard('admin')->user();

        $customer = Customer::where('email', $user->email)->first();
        $customerId = $customer ? $customer->id : null;

        $request->validate([
            'name' => 'required|string|max:255',
            'shop_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20|unique:customers,phone,' . $customerId,
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->shop_name = $request->shop_name;

        // Handle profile image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->image && File::exists($user->image)) {
                File::delete($user->image);
            }

            $image = $request->file('image');
            $imageName = time() . '-reseller-profile-' . $user->id . '.webp';
            $imagePath = 'public/uploads/reseller/profile/';
            
            // Create directory if not exists
            if (!File::exists($imagePath)) {
                File::makeDirectory($imagePath, 0755, true);
            }

            // Resize and convert to webp
            $img = Image::make($image->getRealPath());
            $img->encode('webp', 90);
            $img->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $img->save($imagePath . $imageName);
            
            $user->image = $imagePath . $imageName;
        }

        $user->save();

        // Update or create customer record
        $customer = Customer::where('email', $user->email)->first();
        if ($customer) {
            $customer->name = $request->name;
            $customer->email = $request->email;
            $customer->phone = $request->phone;
            $customer->save();
        } else {
            // Create customer record if doesn't exist
            Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => $user->password, // Use existing password
                'status' => 'active',
                'verify' => 1,
            ]);
        }

        Toastr::success('Profile updated successfully!', 'Success');
        return redirect()->route('reseller.settings');
    }

    /**
     * Update reseller password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::guard('admin')->user();

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            Toastr::error('Current password is incorrect', 'Error');
            return redirect()->back()->withInput();
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Update customer password if exists
        $customer = Customer::where('email', $user->email)->first();
        if ($customer) {
            $customer->password = bcrypt($request->password);
            $customer->save();
        }

        Toastr::success('Password updated successfully!', 'Success');
        return redirect()->route('reseller.settings');
    }
}
