<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\ResellerLandingPage;
use App\Models\ResellerLandingContactMessage;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class LandingContactController extends Controller
{
    public function show(string $slug)
    {
        $landing = ResellerLandingPage::where('slug', $slug)->where('is_active', 1)->first();
        if (!$landing) {
            abort(404);
        }
        return view('reseller.landing.contact', compact('landing'));
    }

    public function store(Request $request, string $slug)
    {
        $landing = ResellerLandingPage::where('slug', $slug)->where('is_active', 1)->first();
        if (!$landing) {
            abort(404);
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
            'subject' => 'nullable|string|max:255',
            'details' => 'required|string',
        ]);

        ResellerLandingContactMessage::create([
            'reseller_landing_page_id' => $landing->id,
            'full_name' => $request->full_name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'subject' => $request->subject,
            'details' => $request->details,
        ]);

        Toastr::success('আপনার মেসেজ পাঠানো হয়েছে। শীঘ্রই আমরা যোগাযোগ করব।', 'সফল');
        return redirect()->route('reseller.landing.contact', $slug);
    }
}
