<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\ResellerLandingPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Brian2694\Toastr\Facades\Toastr;

class LandingPageController extends Controller
{
    public function index()
    {
        $user = Auth::guard('admin')->user();
        $landing = ResellerLandingPage::where('user_id', $user->id)->first();

        if (!$landing) {
            $slug = ResellerLandingPage::generateSlug($user->shop_name ?? $user->name ?? 'store', null);
            $landing = ResellerLandingPage::create([
                'user_id' => $user->id,
                'slug' => $slug,
                'title' => $user->shop_name ?? $user->name,
                'is_active' => 1,
            ]);
        }

        $landingUrl = url('/r/' . $landing->slug);
        return view('reseller.landing.index', compact('user', 'landing', 'landingUrl'));
    }

    public function store(Request $request)
    {
        $user = Auth::guard('admin')->user();

        $request->validate([
            'slug' => 'required|string|max:100|regex:/^[a-z0-9\-]+$/',
            'custom_domain' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'tagline' => 'nullable|string|max:500',
            'scrolling_text' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:500',
            'facebook_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'whatsapp_url' => 'nullable|string|max:255',
            'youtube_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'facebook_pixel_id' => 'nullable|string|max:50',
            'gtm_id' => 'nullable|string|max:50',
            'tiktok_pixel_id' => 'nullable|string|max:50',
            'facebook_capi_access_token' => 'nullable|string|max:1000',
            'favicon' => 'nullable|file|mimes:jpeg,png,jpg,webp,ico|max:512',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'slider_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $landing = ResellerLandingPage::where('user_id', $user->id)->first();
        if (!$landing) {
            $landing = new ResellerLandingPage(['user_id' => $user->id]);
        }

        // Slug uniqueness (exclude current user)
        $exists = ResellerLandingPage::where('slug', $request->slug)
            ->where('user_id', '!=', $user->id)
            ->exists();
        if ($exists) {
            Toastr::error('এই ইউআরএল অন্য কারও ব্যবহৃত। অন্য ইউআরএল চেষ্টা করুন।', 'Error');
            return back()->withInput();
        }

        $basePath = 'public/uploads/reseller/landing/' . $user->id . '/';
        if (!File::exists($basePath)) {
            File::makeDirectory($basePath, 0755, true);
        }

        $landing->slug = Str::slug($request->slug);
        $landing->custom_domain = $request->custom_domain ? trim($request->custom_domain) : null;
        $landing->title = $request->title;
        $landing->tagline = $request->tagline;
        $landing->scrolling_text = $request->scrolling_text ? trim($request->scrolling_text) : null;
        $landing->phone = $request->phone;
        $landing->email = $request->email;
        $landing->address = $request->address;
        $landing->facebook_url = $request->facebook_url ? trim($request->facebook_url) : null;
        $landing->twitter_url = $request->twitter_url ? trim($request->twitter_url) : null;
        $landing->whatsapp_url = $request->whatsapp_url ? trim($request->whatsapp_url) : null;
        $landing->youtube_url = $request->youtube_url ? trim($request->youtube_url) : null;
        $landing->instagram_url = $request->instagram_url ? trim($request->instagram_url) : null;
        $landing->show_newsletter_footer = $request->has('show_newsletter_footer') ? 1 : 0;
        $landing->show_social_footer = $request->has('show_social_footer') ? 1 : 0;
        $landing->facebook_pixel_id = $request->facebook_pixel_id ? trim($request->facebook_pixel_id) : null;
        $landing->gtm_id = $request->gtm_id ? trim($request->gtm_id) : null;
        $landing->tiktok_pixel_id = $request->tiktok_pixel_id ? trim($request->tiktok_pixel_id) : null;
        $landing->facebook_capi_access_token = $request->facebook_capi_access_token ? trim($request->facebook_capi_access_token) : null;

        if ($request->hasFile('favicon')) {
            if ($landing->favicon && File::exists($landing->favicon)) {
                File::delete($landing->favicon);
            }
            $landing->favicon = $this->uploadFavicon($request->file('favicon'), $basePath . 'favicon.');
        }

        if ($request->hasFile('logo')) {
            if ($landing->logo && File::exists($landing->logo)) {
                File::delete($landing->logo);
            }
            $landing->logo = $this->uploadImage($request->file('logo'), $basePath . 'logo.');
        }

        if ($request->hasFile('banner_image')) {
            if ($landing->banner_image && File::exists($landing->banner_image)) {
                File::delete($landing->banner_image);
            }
            $landing->banner_image = $this->uploadImage($request->file('banner_image'), $basePath . 'banner.');
        }

        if ($request->hasFile('slider_images')) {
            $sliderPaths = $landing->slider_images ?? [];
            foreach ($request->file('slider_images') as $idx => $file) {
                if ($file && $file->isValid()) {
                    $path = $this->uploadImage($file, $basePath . 'slider_' . $idx . '.');
                    $sliderPaths[] = $path;
                }
            }
            if (!empty($sliderPaths)) {
                $landing->slider_images = $sliderPaths;
            }
        }

        if ($request->has('remove_slider_index') && is_array($request->remove_slider_index)) {
            $sliders = $landing->slider_images ?? [];
            foreach ($request->remove_slider_index as $idx) {
                if (isset($sliders[$idx]) && File::exists($sliders[$idx])) {
                    File::delete($sliders[$idx]);
                    unset($sliders[$idx]);
                }
            }
            $landing->slider_images = array_values($sliders);
        }

        $landing->is_active = $request->has('is_active') ? 1 : 0;
        $landing->save();

        Toastr::success('ল্যান্ডিং পেজ সেভ হয়েছে।', 'Success');
        return redirect()->route('reseller.landing.index');
    }

    private function uploadFavicon($file, string $prefix): string
    {
        $path = dirname($prefix) . '/';
        $ext = strtolower($file->getClientOriginalExtension()) ?: 'png';
        if (!in_array($ext, ['ico', 'png', 'jpg', 'jpeg', 'webp'])) {
            $ext = 'png';
        }
        $name = basename($prefix) . time() . '.' . $ext;
        $fullPath = $path . $name;
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
        if ($ext === 'ico') {
            $file->move($path, $name);
            return $fullPath;
        }
        $img = Image::make($file->getRealPath());
        $img->resize(64, 64, function ($c) {
            $c->aspectRatio();
            $c->upsize();
        });
        $img->encode($ext === 'jpg' ? 'jpeg' : $ext, 90);
        $img->save($fullPath);
        return $fullPath;
    }

    private function uploadImage($file, string $prefix): string
    {
        $path = dirname($prefix) . '/';
        $name = basename($prefix) . time() . '.webp';
        $fullPath = $path . $name;
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
        $img = Image::make($file->getRealPath());
        $img->encode('webp', 85);
        $img->resize(1920, 1080, function ($c) {
            $c->aspectRatio();
            $c->upsize();
        });
        $img->save($fullPath);
        return $fullPath;
    }
}
