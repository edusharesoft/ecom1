<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AuthTheme;
use Illuminate\Http\Request;

/**
 * Admin → Site Setting → Login & Registration Page
 * Background photo + YouTube video for the login and registration pages.
 */
class AuthThemeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:setting-list|setting-edit', ['only' => ['edit']]);
        $this->middleware('permission:setting-edit', ['only' => ['update']]);
    }

    public function edit()
    {
        return view('backEnd.settings.auth_theme', ['s' => AuthTheme::all()]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'login_bg' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'register_bg' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'login_video' => 'nullable|string|max:255',
            'register_video' => 'nullable|string|max:255',
        ]);

        foreach (['login_video', 'register_video'] as $key) {
            $url = trim((string) $request->input($key));
            if ($url !== '' && ! AuthTheme::youtubeId($url)) {
                return back()->withInput()->withErrors([$key => 'সঠিক YouTube লিংক দিন (যেমন https://www.youtube.com/watch?v=xxxxxxxxxxx)।']);
            }
        }

        $values = [
            'login_video' => trim((string) $request->input('login_video')),
            'register_video' => trim((string) $request->input('register_video')),
            'show_video' => $request->boolean('show_video') ? '1' : '0',
        ];

        $dir = base_path('public/uploads/auth');
        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        foreach (AuthTheme::KINDS as $kind) {
            $key = $kind.'_bg';
            $old = AuthTheme::get($key);
            if ($request->boolean('remove_'.$key)) {
                $this->deleteFile($old);
                $values[$key] = '';
            }
            if ($file = $request->file($key)) {
                $name = $kind.'-bg-'.time().'.'.strtolower($file->getClientOriginalExtension());
                $file->move($dir, $name);
                $this->deleteFile($old);
                $values[$key] = 'public/uploads/auth/'.$name;
            }
        }

        AuthTheme::save($values);

        return back()->with('success', 'লগইন ও রেজিস্ট্রেশন পেইজের সেটিংস সেভ হয়েছে।');
    }

    protected function deleteFile(?string $path): void
    {
        if ($path && str_starts_with($path, 'public/uploads/auth/') && is_file(base_path($path))) {
            @unlink(base_path($path));
        }
    }
}
