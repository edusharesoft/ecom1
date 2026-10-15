<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Services\GeminiAiService;
use App\Services\GeminiCustomerContextService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GeminiCustomerChatController extends Controller
{
    protected const SESSION_KEY = 'gemini_customer_chat_history';

    protected const MAX_MESSAGES = 20;

    public function send(Request $request, GeminiAiService $geminiAiService, GeminiCustomerContextService $contextService)
    {
        if (! $contextService->isEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'লাইভ চ্যাট এখন সক্রিয় নেই।',
            ], 503);
        }

        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $userMessage = trim($request->message);
        $history     = session(self::SESSION_KEY, []);
        $customer    = Auth::guard('customer')->user();

        $history[] = [
            'role' => 'user',
            'text' => $userMessage,
            'at'   => now()->format('H:i'),
        ];

        try {
            $products = $contextService->searchProductsForMessage($userMessage);
            $contents = $this->toGeminiContents(array_slice($history, -self::MAX_MESSAGES));
            $system   = $contextService->buildSystemInstruction($userMessage, $customer);
            $reply    = $geminiAiService->chat($contents, $system);

            $history[] = [
                'role' => 'model',
                'text' => $reply,
                'at'   => now()->format('H:i'),
            ];

            session([self::SESSION_KEY => array_slice($history, -self::MAX_MESSAGES)]);

            return response()->json([
                'success'  => true,
                'reply'    => $reply,
                'products' => $products,
                'history'  => session(self::SESSION_KEY),
            ]);
        } catch (\Throwable $e) {
            Log::error('Gemini customer chat failed', ['error' => $e->getMessage()]);

            array_pop($history);
            session([self::SESSION_KEY => $history]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function clear()
    {
        session()->forget(self::SESSION_KEY);

        return response()->json(['success' => true]);
    }

    public function complaint(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'phone'       => 'required|string|max:20',
            'order_id'    => 'nullable|string|max:50',
            'description' => 'required|string|max:5000',
        ]);

        Complaint::create([
            'customer_id' => Auth::guard('customer')->id(),
            'name'        => $request->name,
            'phone'       => $request->phone,
            'order_id'    => $request->order_id,
            'description' => $request->description,
            'status'      => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'আপনার কমপ্লেইন সফলভাবে জমা হয়েছে। শীঘ্রই আমাদের টিম যোগাযোগ করবে।',
        ]);
    }

    /**
     * @param  array<int, array{role: string, text: string}>  $history
     * @return array<int, array{role: string, parts: array<int, array{text: string}>}>
     */
    protected function toGeminiContents(array $history): array
    {
        $contents = [];

        foreach ($history as $message) {
            $role = ($message['role'] ?? '') === 'model' ? 'model' : 'user';
            $text = trim((string) ($message['text'] ?? ''));

            if ($text === '') {
                continue;
            }

            $contents[] = [
                'role'  => $role,
                'parts' => [['text' => $text]],
            ];
        }

        if ($contents === [] || ($contents[0]['role'] ?? '') !== 'user') {
            throw new \RuntimeException('Invalid chat history.');
        }

        return $contents;
    }
}
