<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeminiAiSetting extends Model
{
    protected $table = 'gemini_ai_settings';

    protected $guarded = [];

    protected $casts = [
        'status' => 'boolean',
        'customer_chat_enabled' => 'boolean',
    ];

    public static function instance(): self
    {
        return static::firstOrCreate(['id' => 1], [
            'model' => 'gemini-2.5-flash',
            'timeout' => 60,
            'status' => true,
            'customer_chat_enabled' => true,
        ]);
    }

    public function hasApiKey(): bool
    {
        return filled($this->api_key);
    }

    public function maskedApiKey(): ?string
    {
        if (! $this->hasApiKey()) {
            return null;
        }

        $key = (string) $this->api_key;
        $len = strlen($key);

        if ($len <= 8) {
            return str_repeat('•', $len);
        }

        return str_repeat('•', max(0, $len - 4)) . substr($key, -4);
    }
}
