<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\GeminiAiSetting;
use Illuminate\Support\Facades\Http;

$key = GeminiAiSetting::first()->api_key;
$response = Http::timeout(30)->get('https://generativelanguage.googleapis.com/v1beta/models', [
    'key' => $key,
]);
echo 'list HTTP ' . $response->status() . PHP_EOL;
if ($response->successful()) {
    foreach (data_get($response->json(), 'models', []) as $m) {
        $name = str_replace('models/', '', $m['name'] ?? '');
        if (str_contains($name, 'flash') || str_contains($name, '2.5') || str_contains($name, '2.0')) {
            echo $name . PHP_EOL;
        }
    }
} else {
    echo data_get($response->json(), 'error.message', $response->body()) . PHP_EOL;
}
