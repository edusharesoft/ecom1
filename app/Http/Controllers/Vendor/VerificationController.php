<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Brian2694\Toastr\Facades\Toastr;

class VerificationController extends Controller
{
    /**
     * Show verification form
     */
    public function index()
    {
        $vendorId = Auth::user()->vendor_id;
        if (!$vendorId) {
            Toastr::error('Vendor profile not found', 'Error');
            return redirect()->route('vendor.dashboard');
        }

        $vendor = Vendor::findOrFail($vendorId);
        return view('vendor.verification.index', compact('vendor'));
    }

    /**
     * Store verification documents
     */
    public function store(Request $request)
    {
        $vendorId = Auth::user()->vendor_id;
        if (!$vendorId) {
            Toastr::error('Vendor profile not found', 'Error');
            return redirect()->route('vendor.dashboard');
        }

        $request->validate([
            'voter_id_front' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048',
            'voter_id_back' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048',
            'self_image' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        $vendor = Vendor::findOrFail($vendorId);

        // Upload Voter ID Front
        if ($request->hasFile('voter_id_front')) {
            $frontImage = $request->file('voter_id_front');
            $frontName = time() . '-voter-front-' . $vendor->id . '.webp';
            $frontPath = 'public/uploads/vendor/verification/';
            
            if (!File::exists($frontPath)) {
                File::makeDirectory($frontPath, 0755, true);
            }

            $img = Image::make($frontImage->getRealPath());
            $img->encode('webp', 90);
            $img->resize(800, 600, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $img->save($frontPath . $frontName);

            // Delete old image if exists
            if ($vendor->voter_id_front && File::exists($vendor->voter_id_front)) {
                File::delete($vendor->voter_id_front);
            }

            $vendor->voter_id_front = $frontPath . $frontName;
        }

        // Upload Voter ID Back
        if ($request->hasFile('voter_id_back')) {
            $backImage = $request->file('voter_id_back');
            $backName = time() . '-voter-back-' . $vendor->id . '.webp';
            $backPath = 'public/uploads/vendor/verification/';
            
            if (!File::exists($backPath)) {
                File::makeDirectory($backPath, 0755, true);
            }

            $img = Image::make($backImage->getRealPath());
            $img->encode('webp', 90);
            $img->resize(800, 600, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $img->save($backPath . $backName);

            // Delete old image if exists
            if ($vendor->voter_id_back && File::exists($vendor->voter_id_back)) {
                File::delete($vendor->voter_id_back);
            }

            $vendor->voter_id_back = $backPath . $backName;
        }

        // Upload Self Image
        if ($request->hasFile('self_image')) {
            $selfImage = $request->file('self_image');
            $selfName = time() . '-self-' . $vendor->id . '.webp';
            $selfPath = 'public/uploads/vendor/verification/';
            
            if (!File::exists($selfPath)) {
                File::makeDirectory($selfPath, 0755, true);
            }

            $img = Image::make($selfImage->getRealPath());
            $img->encode('webp', 90);
            $img->resize(600, 600, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $img->save($selfPath . $selfName);

            // Delete old image if exists
            if ($vendor->self_image && File::exists($vendor->self_image)) {
                File::delete($vendor->self_image);
            }

            $vendor->self_image = $selfPath . $selfName;
        }

        // Update verification status
        $vendor->verification_status = 'pending';
        $vendor->verification_note = null;
        $vendor->save();

        Toastr::success('Verification documents uploaded successfully. Please wait for admin approval.', 'Success');
        return redirect()->route('vendor.dashboard');
    }
}
