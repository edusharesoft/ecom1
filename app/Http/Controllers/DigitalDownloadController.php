<?php

namespace App\Http\Controllers;

use App\Models\DigitalDownload;
use Illuminate\Support\Facades\Storage;

class DigitalDownloadController extends Controller
{
    public function download($token)
    {
        $download = DigitalDownload::where('token', $token)->firstOrFail();

        // ❌ এই কড়া চেকটা কমেন্ট করে দাও বা নিচের safe ভার্সন ব্যবহার করো
        /*
        if ($download->customer_id != auth('customer')->id()) {
            abort(403, 'You are not allowed to download this file.');
        }
        */

        // ✅ যদি তুমি এখনও চাও "অন্য কাস্টমার" ব্লক হোক কিন্তু guest কে allow করো:
        if (auth('customer')->check() && $download->customer_id != auth('customer')->id()) {
            abort(403, 'You are not allowed to download this file.');
        }

        // মেয়াদ চেক
        if ($download->expires_at && now()->greaterThan($download->expires_at)) {
            abort(403, 'Download link has expired.');
        }

        // ডাউনলোড লিমিট চেক
        if (!is_null($download->remaining_downloads) && $download->remaining_downloads <= 0) {
            abort(403, 'Download limit exceeded.');
        }

        // ডাউনলোড কাউন্ট কমাও
        if (!is_null($download->remaining_downloads)) {
            $download->decrement('remaining_downloads');
        }

        // ফাইল আছে কিনা চেক
        if (!Storage::disk('private')->exists($download->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('private')->download($download->file_path);
    }
}
