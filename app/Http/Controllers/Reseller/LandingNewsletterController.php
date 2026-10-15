<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\ResellerLandingPage;
use App\Models\ResellerLandingNewsletterSubscriber;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class LandingNewsletterController extends Controller
{
    public function subscribe(Request $request, string $slug)
    {
        $landing = ResellerLandingPage::where('slug', $slug)->where('is_active', 1)->first();
        if (!$landing || !($landing->show_newsletter_footer ?? true)) {
            return redirect()->back();
        }

        $request->validate([
            'email' => 'required|email',
        ]);

        $exists = ResellerLandingNewsletterSubscriber::where('reseller_landing_page_id', $landing->id)
            ->where('email', $request->email)
            ->exists();

        if ($exists) {
            Toastr::info('আপনি ইতিমধ্যে সাবস্ক্রাইব করেছেন!', 'Newsletter');
            return redirect()->back();
        }

        ResellerLandingNewsletterSubscriber::create([
            'reseller_landing_page_id' => $landing->id,
            'email' => $request->email,
        ]);

        Toastr::success('ন্যূজলেটার সাবস্ক্রিপশন সফল!', 'Success');
        return redirect()->back();
    }
}
