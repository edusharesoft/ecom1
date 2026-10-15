<?php

namespace App\Services\Ads;

use App\Models\AdsAnalyticsSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleAdsService
{
    public function getInsights(): array
    {
        $setting = AdsAnalyticsSetting::getByPlatform('google');
        if (!$setting || !$setting->client_id || !$setting->client_secret || !$setting->refresh_token) {
            return $this->emptyResponse('Google Ads');
        }

        try {
            $accessToken = $this->getAccessToken($setting);
            if (!$accessToken) {
                return $this->errorResponse('Google Ads', 'Failed to refresh access token');
            }

            $customerId = preg_replace('/[^0-9]/', '', $setting->ad_account_id ?? '');
            if (empty($customerId)) {
                return $this->errorResponse('Google Ads', 'Ad Account ID (Customer ID) not set');
            }

            $today = now()->format('Y-m-d');
            $url = "https://googleads.googleapis.com/v16/customers/{$customerId}/googleAds:searchStream";

            $query = "SELECT metrics.cost_micros, metrics.clicks, metrics.impressions, metrics.conversions 
                      FROM campaign 
                      WHERE segments.date = '{$today}'";

            $response = Http::withToken($accessToken)
                ->post($url, [
                    'query' => $query,
                ]);

            if (!$response->successful()) {
                throw new \Exception($response->body());
            }

            $data = $response->json();
            $spend = 0;
            $clicks = 0;
            $impressions = 0;
            $conversions = 0;

            foreach ($data['results'] ?? [] as $row) {
                $m = $row['metrics'] ?? [];
                $spend += ((int) ($m['costMicros'] ?? 0)) / 1_000_000;
                $clicks += (int) ($m['clicks'] ?? 0);
                $impressions += (int) ($m['impressions'] ?? 0);
                $conversions += (float) ($m['conversions'] ?? 0);
            }

            return [
                'success' => true,
                'platform' => 'Google Ads',
                'spend' => round($spend, 2),
                'clicks' => $clicks,
                'impressions' => $impressions,
                'reach' => 0,
                'conversions' => (int) $conversions,
                'currency' => 'USD',
            ];
        } catch (\Throwable $e) {
            Log::error('Google Ads API Error: ' . $e->getMessage());
            return $this->errorResponse('Google Ads', $e->getMessage());
        }
    }

    private function getAccessToken(AdsAnalyticsSetting $setting): ?string
    {
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => $setting->client_id,
            'client_secret' => $setting->client_secret,
            'refresh_token' => $setting->refresh_token,
            'grant_type' => 'refresh_token',
        ]);

        if (!$response->successful()) {
            return null;
        }

        return $response->json('access_token');
    }

    private function emptyResponse(string $platform): array
    {
        return [
            'success' => false,
            'platform' => $platform,
            'spend' => 0,
            'clicks' => 0,
            'impressions' => 0,
            'reach' => 0,
            'conversions' => 0,
            'currency' => 'USD',
            'message' => 'API credentials not configured',
        ];
    }

    private function errorResponse(string $platform, string $message): array
    {
        return [
            'success' => false,
            'platform' => $platform,
            'spend' => 0,
            'clicks' => 0,
            'impressions' => 0,
            'reach' => 0,
            'conversions' => 0,
            'currency' => 'USD',
            'message' => $message,
        ];
    }
}
