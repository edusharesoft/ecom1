<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\User;
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
        $user = Auth::guard('admin')->user();
        
        // Verify it's a reseller
        if ($user->role !== 'reseller' && !$user->hasRole('reseller')) {
            Toastr::error('Access denied', 'Error');
            return redirect()->route('reseller.dashboard');
        }

        return view('reseller.verification.index', compact('user'));
    }

    /**
     * Store verification documents
     */
    public function store(Request $request)
    {
        $user = Auth::guard('admin')->user();
        
        // Verify it's a reseller
        if ($user->role !== 'reseller' && !$user->hasRole('reseller')) {
            Toastr::error('Access denied', 'Error');
            return redirect()->route('reseller.dashboard');
        }

        $request->validate([
            'voter_id_front' => 'required|image|mimes:jpeg,jpg,png,webp|max:102400',
            'voter_id_back' => 'required|image|mimes:jpeg,jpg,png,webp|max:102400',
            'self_image' => 'required|image|mimes:jpeg,jpg,png,webp|max:102400',
        ]);

        // Upload Voter ID Front
        if ($request->hasFile('voter_id_front')) {
            $frontImage = $request->file('voter_id_front');
            $frontName = time() . '-voter-front-' . $user->id . '.webp';
            $frontPath = 'public/uploads/reseller/verification/';
            
            if (!File::exists($frontPath)) {
                File::makeDirectory($frontPath, 0755, true);
            }

            $img = Image::make($frontImage->getRealPath());
            $img->encode('webp', 90);
            $img->resize(800, 800, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $img->save($frontPath . $frontName);

            // Delete old image if exists
            if ($user->voter_id_front && File::exists($user->voter_id_front)) {
                File::delete($user->voter_id_front);
            }

            $user->voter_id_front = $frontPath . $frontName;
        }

        // Upload Voter ID Back
        if ($request->hasFile('voter_id_back')) {
            $backImage = $request->file('voter_id_back');
            $backName = time() . '-voter-back-' . $user->id . '.webp';
            $backPath = 'public/uploads/reseller/verification/';
            
            if (!File::exists($backPath)) {
                File::makeDirectory($backPath, 0755, true);
            }

            $img = Image::make($backImage->getRealPath());
            $img->encode('webp', 90);
            $img->resize(800, 800, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $img->save($backPath . $backName);

            // Delete old image if exists
            if ($user->voter_id_back && File::exists($user->voter_id_back)) {
                File::delete($user->voter_id_back);
            }

            $user->voter_id_back = $backPath . $backName;
        }

        // Upload Self Image
        if ($request->hasFile('self_image')) {
            $selfImage = $request->file('self_image');
            $selfName = time() . '-self-' . $user->id . '.webp';
            $selfPath = 'public/uploads/reseller/verification/';
            
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
            if ($user->self_image && File::exists($user->self_image)) {
                File::delete($user->self_image);
            }

            $user->self_image = $selfPath . $selfName;
        }

        // Update verification status
        $user->verification_status = 'pending';
        $user->verification_note = null;
        $user->save();

        Toastr::success('Verification documents uploaded successfully. Please wait for admin approval.', 'Success');
        return redirect()->route('reseller.verification.index');
    }
}
