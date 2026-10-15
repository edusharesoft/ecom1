<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ErrorLogController extends Controller
{
    public function index()
    {
        $logDir  = storage_path('logs');
        $logPath = $logDir . '/laravel.log';
        $content = '';
        $exists  = File::exists($logPath);
        $writable = false;
        $message = '';

        if (!is_dir($logDir)) {
            if (@mkdir($logDir, 0775, true)) {
                $message = 'storage/logs ফোল্ডার তৈরি হয়েছে।';
            } else {
                $message = 'storage/logs ফোল্ডার তৈরি করা যায়নি। পারমিশন চেক করুন।';
            }
        }

        if (!$exists && is_dir($logDir)) {
            if (@file_put_contents($logPath, '') !== false) {
                $exists = true;
                $message = 'laravel.log ফাইল তৈরি হয়েছে।';
            } else {
                $message = 'laravel.log ফাইল তৈরি করা যায়নি। storage/logs ফোল্ডারে লিখার পারমিশন দিন।';
            }
        }

        if ($exists) {
            $writable = is_writable($logPath);
            $content = File::get($logPath);
            $lines = explode("\n", $content);
            $lines = array_slice($lines, -500);
            $content = implode("\n", $lines);
        }

        $logChannel = config('logging.default');
        $logLevel   = config('logging.channels.single.level', env('LOG_LEVEL', 'debug'));
        $configCached = file_exists(base_path('bootstrap/cache/config.php'));

        return view('backEnd.error-log.index', [
            'content'      => $content,
            'exists'       => $exists,
            'path'         => $logPath,
            'writable'     => $writable,
            'message'      => $message,
            'logChannel'   => $logChannel,
            'logLevel'     => $logLevel,
            'configCached' => $configCached,
        ]);
    }

    public function create()
    {
        $logDir  = storage_path('logs');
        $logPath = $logDir . '/laravel.log';

        if (!is_dir($logDir)) {
            @mkdir($logDir, 0775, true);
        }

        if (!File::exists($logPath)) {
            @file_put_contents($logPath, '[' . date('Y-m-d H:i:s') . "] Log file created.\n");
        }

        return redirect()->route('error-log.index')->with('success', 'Log file তৈরি/চেক করা হয়েছে।');
    }

    /**
     * টেস্ট লগ এন্ট্রি লিখে যাচাই করুন লগিং কাজ করছে কিনা
     */
    public function testLog()
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        try {
            Log::info("[TEST] Error log page থেকে টেস্ট লগ - {$timestamp}");
            Log::error("[TEST] টেস্ট error লগ - {$timestamp}");
            return redirect()->route('error-log.index')->with('success', 'টেস্ট লগ লেখা হয়েছে। পেজ রিফ্রেশ করুন।');
        } catch (\Throwable $e) {
            return redirect()->route('error-log.index')->with('error', 'লগ লেখা যায়নি: ' . $e->getMessage());
        }
    }
}
