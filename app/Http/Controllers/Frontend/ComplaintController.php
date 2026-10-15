<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complaint;

class ComplaintController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'phone'       => 'required|string|max:20',
            'order_id'    => 'nullable|string|max:50',
            'description' => 'required|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // 🔹 Image upload to public/complaints
        $imagePath = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time().'_'.$image->getClientOriginalName();
            $image->move(public_path('complaints'), $imageName);

            $imagePath = 'complaints/'.$imageName;
        }

        // 🔹 Save complaint
        Complaint::create([
            'name'        => $request->name,
            'phone'       => $request->phone,
            'order_id'    => $request->order_id,
            'description' => $request->description,
            'image'       => $imagePath,
            'status'      => 'pending',
        ]);

        return back()->with('success', 'আপনার কমপ্লেইন সফলভাবে জমা হয়েছে');
    }
}
