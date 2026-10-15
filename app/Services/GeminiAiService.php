<?php

namespace App\Services;

use App\Models\GeminiAiSetting;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use RuntimeException;

class GeminiAiService
{
    protected string $apiKey = '';

    protected string $model = 'gemini-2.5-flash';

    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';

    protected int $timeout = 60;

    /** @var array<int, string> */
    protected array $fallbackModels = [
        'gemini-2.5-flash',
        'gemini-2.5-flash-lite',
        'gemini-2.0-flash-lite',
        'gemini-2.0-flash',
    ];

    public function __construct()
    {
        $this->loadConfiguration();
    }

    protected function loadConfiguration(): void
    {
        $this->baseUrl = rtrim((string) config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta'), '/');

        $dbSetting = null;

        try {
            $dbSetting = Cache::remember('gemini_ai_settings', 3600, function () {
                return GeminiAiSetting::first();
            });
        } catch (\Throwable $e) {
            // Migration not run yet — fallback to .env
        }

        if ($dbSetting && $dbSetting->status && filled($dbSetting->api_key)) {
            $this->apiKey = (string) $dbSetting->api_key;
            $this->model = (string) ($dbSetting->model ?: config('services.gemini.model', 'gemini-2.5-flash'));
            $this->timeout = (int) ($dbSetting->timeout ?: config('services.gemini.timeout', 60));

            return;
        }

        $this->apiKey = (string) config('services.gemini.api_key', '');
        $this->model = (string) config('services.gemini.model', 'gemini-2.5-flash');
        $this->timeout = (int) config('services.gemini.timeout', 60);
    }

    public function generateResponse(string $prompt): string
    {
        $prompt = trim($prompt);

        if ($prompt === '') {
            throw new InvalidArgumentException('Prompt cannot be empty.');
        }

        if ($this->apiKey === '') {
            throw new RuntimeException('Gemini API key সেট করা নেই। Admin → API Integration → Gemini AI থেকে key দিন।');
        }

        return $this->executeGenerate([
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
        ]);
    }

    public function generateResponseWithApiKey(string $prompt, string $apiKey): string
    {
        $apiKey = trim($apiKey);

        if ($apiKey === '') {
            throw new InvalidArgumentException('API key cannot be empty.');
        }

        $originalKey = $this->apiKey;
        $this->apiKey = $apiKey;

        try {
            return $this->generateResponse($prompt);
        } finally {
            $this->apiKey = $originalKey;
        }
    }

    /**
     * @param  array<int, array{role: string, parts: array<int, array{text: string}>}>  $contents
     */
    public function chat(array $contents, ?string $systemInstruction = null): string
    {
        if ($contents === []) {
            throw new InvalidArgumentException('Chat contents cannot be empty.');
        }

        if ($this->apiKey === '') {
            throw new RuntimeException('Gemini API key সেট করা নেই। Admin → API Integration → Gemini AI থেকে key দিন।');
        }

        $payload = ['contents' => $contents];

        if ($systemInstruction !== null && trim($systemInstruction) !== '') {
            $payload['system_instruction'] = [
                'parts' => [['text' => trim($systemInstruction)]],
            ];
        }

        return $this->executeGenerate($payload, max($this->timeout, 90));
    }

  /**
     * @param  array<string, mixed>  $payload
     */
    protected function executeGenerate(array $payload, ?int $timeout = null): string
    {
        $timeout = $timeout ?? $this->timeout;
        $models  = array_values(array_unique(array_filter([
            $this->model,
            ...$this->fallbackModels,
        ])));

        $lastError = 'Gemini API request failed.';

        foreach ($models as $model) {
            $response = $this->postToModel($model, $payload, $timeout);

            if ($response->successful()) {
                $text = data_get($response->json(), 'candidates.0.content.parts.0.text');

                if (is_string($text) && trim($text) !== '') {
                    return trim($text);
                }

                $blockReason = data_get($response->json(), 'candidates.0.finishReason');
                throw new RuntimeException('Gemini খালি উত্তর দিয়েছে' . ($blockReason ? " ({$blockReason})" : '') . '।');
            }

            $lastError = $this->parseApiError($response, $model);

            Log::warning('Gemini model attempt failed', [
                'model'  => $model,
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);

            if (! in_array($response->status(), [404, 429], true)) {
                break;
            }
        }

        throw new RuntimeException($lastError);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function postToModel(string $model, array $payload, int $timeout): Response
    {
        $url = "{$this->baseUrl}/models/{$model}:generateContent";

        return Http::timeout($timeout)
            ->acceptJson()
            ->withHeaders([
                'Content-Type' => 'application/json',
                'x-goog-api-key' => $this->apiKey,
            ])
            ->post($url, $payload);
    }

    protected function parseApiError(Response $response, string $model): string
    {
        $status  = $response->status();
        $message = (string) (data_get($response->json(), 'error.message') ?: data_get($response->json(), 'error.status') ?: '');

        if ($status === 429 || str_contains(strtolower($message), 'quota')) {
            return "Gemini quota শেষ ({$model})। Google AI Studio-তে billing/quota চেক করুন, অথবা Model পরিবর্তন করে `gemini-2.5-flash` ব্যবহার করুন।";
        }

        if ($status === 404 || str_contains(strtolower($message), 'not found')) {
            return "Gemini model পাওয়া যায়নি: {$model}। Settings-এ `gemini-2.5-flash` বা `gemini-2.5-flash-lite` দিন।";
        }

        if ($status === 401 || $status === 403) {
            return 'Gemini API key ভুল বা অনুমোদিত নয়। নতুন key নিয়ে আবার সেভ করুন।';
        }

        if ($message !== '') {
            return "Gemini API error ({$status}): {$message}";
        }

        return "Gemini API request failed (HTTP {$status}).";
    }
}
