<?php

namespace App\Http\Controllers\DeliveryBoy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function edit()
    {
        $boy = Auth::guard('delivery_boy')->user();

        return view('delivery.profile.edit', compact('boy'));
    }

    public function update(Request $request)
    {
        $boy = Auth::guard('delivery_boy')->user();

        $request->validate([
            'name'     => 'required|string|max:191',
            'phone'    => 'required|string|max:20|unique:delivery_boys,phone,'.$boy->id,
            'email'    => 'nullable|email|unique:delivery_boys,email,'.$boy->id,
            'password' => 'nullable|string|min:6|confirmed',
            'image'    => 'nullable|image|max:2048',
        ]);

        $boy->name = $request->name;
        $boy->phone = preg_replace('/\s+/', '', $request->phone);
        $boy->email = $request->filled('email') ? $request->email : null;

        if ($request->filled('password')) {
            $boy->password = Hash::make($request->password);
        }

        if ($request->hasFile('image')) {
            $this->deleteStoredImageIfSafe($boy->image);
            $boy->image = $this->uploadImage($request->file('image'));
        }

        $boy->save();

        return redirect()->route('delivery.profile.edit')->with('success', 'প্রফাইল আপডেট হয়েছে।');
    }

    private function uploadImage(\Illuminate\Http\UploadedFile $file): string
    {
        $name = 'delivery_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
        $dir = 'public/uploads/delivery_boys';
        if (! is_dir(base_path($dir))) {
            mkdir(base_path($dir), 0755, true);
        }
        $file->move(base_path($dir), $name);

        return 'public/uploads/delivery_boys/'.$name;
    }

    private function deleteStoredImageIfSafe(?string $stored): void
    {
        if (! $stored || ! Str::startsWith($stored, 'public/uploads/delivery_boys/')) {
            return;
        }

        $full = base_path($stored);
        if (is_file($full)) {
            @unlink($full);
        }
    }
}
