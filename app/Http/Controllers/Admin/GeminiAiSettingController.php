<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeminiAiSetting;
use App\Services\GeminiAiService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GeminiAiSettingController extends Controller
{
    public function edit()
    {
        $setting = GeminiAiSetting::instance();

        return view('backEnd.settings.gemini_ai', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = GeminiAiSetting::instance();

        $request->validate([
            'api_key' => [
                $setting->hasApiKey() ? 'nullable' : 'required',
                'string',
                'max:2048',
            ],
            'model'   => 'nullable|string|max:100',
            'timeout' => 'nullable|integer|min:10|max:300',
            'customer_chat_welcome' => 'nullable|string|max:2000',
        ], [
            'api_key.required' => 'Gemini API key দিন।',
        ]);

        try {
            $data = [
                'model'   => $request->input('model', 'gemini-2.5-flash'),
                'timeout' => (int) $request->input('timeout', 60),
                'status'  => $request->boolean('status'),
                'customer_chat_enabled' => $request->boolean('customer_chat_enabled'),
                'customer_chat_welcome' => $request->input('customer_chat_welcome'),
            ];

            if ($request->filled('api_key')) {
                $data['api_key'] = trim((string) $request->api_key);
            }

            $setting->fill($data);
            $setting->save();

            Cache::forget('gemini_ai_settings');
            Cache::forget('gemini_chat_enabled');
            app(\App\Services\GeminiAdminContextService::class)->refreshContext();

            session()->flash('success', 'Gemini AI settings সফলভাবে সেভ হয়েছে।');
            Toastr::success('Gemini AI settings সফলভাবে সেভ হয়েছে।', 'Success');

            return redirect()->route('admin.gemini_ai.edit')->with('gemini_saved', true);
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->withErrors(['save' => 'সেটিংস সেভ ব্যর্থ: ' . $e->getMessage()]);
        }
    }

    public function test(Request $request, GeminiAiService $geminiAiService)
    {
        $request->validate([
            'prompt'  => 'nullable|string|max:500',
            'api_key' => 'nullable|string|max:2048',
        ]);

        try {
            $prompt = $request->input('prompt', 'Reply with only: API OK');
            $apiKey = trim((string) $request->input('api_key', ''));

            if ($apiKey !== '') {
                $response = $geminiAiService->generateResponseWithApiKey($prompt, $apiKey);
            } else {
                $response = $geminiAiService->generateResponse($prompt);
            }

            return response()->json([
                'success'  => true,
                'message'  => 'Gemini API connection successful.',
                'response' => $response,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
