<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeoSettingController extends Controller
{
    /**
     * Show SEO settings edit form.
     */
    public function index()
    {
        // ধরে নিলাম টেবিল নাম `seo_settings` এবং আপনি টেবিলে একটিমাত্র row (id=1) রাখবেন
        $seo = DB::table('seo_settings')->first();

        return view('backEnd.seo_settings.index', compact('seo'));
    }

    /**
     * Update or create SEO settings.
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'meta_title' => 'nullable|string|max:255',
            'meta_tags' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'search_console_verification' => 'nullable|string|max:255',
        ]);

        // Upsert: যদি row না থাকে create করবে, না হলে update করবে
        DB::table('seo_settings')->updateOrInsert(
            ['id' => 1],
            array_merge($data, [
                'updated_at' => now(),
                'created_at' => DB::raw('IFNULL(created_at, NOW())')
            ])
        );

        return redirect()->back()->with('success', 'SEO settings saved successfully.');
    }
}
