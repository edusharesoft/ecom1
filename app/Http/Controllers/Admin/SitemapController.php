<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Sitemap\SitemapGenerator;

class SitemapController extends Controller
{
    public function index()
    {
        return view('backEnd.sitemap.index');
    }

    public function generate()
    {
        // sitemap.xml মেইন ফোল্ডারে তৈরি হবে
        $path = base_path('sitemap.xml');

        SitemapGenerator::create(config('app.url'))
            ->writeToFile($path);

        return redirect()->back()->with('success', '✅ Sitemap generated successfully at project root!');
    }
}
