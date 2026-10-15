<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactMessage;

class ContactMessageController extends Controller
{
    public function store(Request $request)
    {
        // ✅ Correct validation (phone/address বাদ)
        $request->validate([
            'full_name' => 'required|string|max:255',
            'mobile'    => 'required|string|max:20',
            'email'     => 'nullable|email',
            'subject'   => 'nullable|string|max:255',
            'details'   => 'required|string',
        ]);

        // ✅ Save data
        ContactMessage::create([
            'full_name' => $request->full_name,
            'mobile'    => $request->mobile,
            'email'     => $request->email,
            'subject'   => $request->subject,
            'details'   => $request->details,
            'status'    => 0,
        ]);

        return back()->with('success', 'Your message has been sent successfully!');
    }
}
