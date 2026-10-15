<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GeminiAdminContextService;
use App\Services\GeminiAiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GeminiAdminChatController extends Controller
{
    protected const SESSION_KEY = 'gemini_admin_chat_history';

    protected const MAX_MESSAGES = 24;

    public function index()
    {
        $history = session(self::SESSION_KEY, []);

        return view('backEnd.gemini.chat', compact('history'));
    }

    public function send(Request $request, GeminiAiService $geminiAiService, GeminiAdminContextService $contextService)
    {
        $request->validate([
            'message' => 'required|string|max:4000',
        ]);

        $userMessage = trim($request->message);
        $history     = session(self::SESSION_KEY, []);

        $history[] = [
            'role' => 'user',
            'text' => $userMessage,
            'at'   => now()->format('Y-m-d H:i'),
        ];

        try {
            $contents = $this->toGeminiContents(array_slice($history, -self::MAX_MESSAGES));
            $system   = $contextService->buildSystemInstruction($userMessage);
            $reply    = $geminiAiService->chat($contents, $system);

            $history[] = [
                'role' => 'model',
                'text' => $reply,
                'at'   => now()->format('Y-m-d H:i'),
            ];

            session([self::SESSION_KEY => array_slice($history, -self::MAX_MESSAGES)]);

            return response()->json([
                'success' => true,
                'reply'   => $reply,
                'history' => session(self::SESSION_KEY),
            ]);
        } catch (\Throwable $e) {
            Log::error('Gemini admin chat failed', ['error' => $e->getMessage()]);

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

    public function refreshContext(GeminiAdminContextService $contextService)
    {
        $contextService->refreshContext();

        return response()->json([
            'success' => true,
            'message' => 'Site context refreshed',
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
