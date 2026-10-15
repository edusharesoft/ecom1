<?php

namespace App\Services\Ads;

use App\Models\AdsAnalyticsSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TikTokAdsService
{
    private const API_BASE = 'https://business-api.tiktok.com/open_api/v1.3';

    public function getInsights(): array
    {
        $setting = AdsAnalyticsSetting::getByPlatform('tiktok');
        if (!$setting || !$setting->access_token || !$setting->ad_account_id) {
            return $this->emptyResponse('TikTok Ads');
        }

        try {
            $advertiserId = $setting->ad_account_id;
            $today = now()->format('Y-m-d');

            $response = Http::withHeaders([
                'Access-Token' => $setting->access_token,
                'Content-Type' => 'application/json',
            ])->get(self::API_BASE . '/report/integrated/get/', [
                'advertiser_id' => $advertiserId,
                'report_type' => 'BASIC',
                'dimensions' => json_encode(['stat_time_day']),
                'data_level' => 'AUCTION_ADVERTISER',
                'start_date' => $today,
                'end_date' => $today,
                'metrics' => json_encode(['spend', 'clicks', 'impressions', 'reach', 'conversion']),
            ]);

            if (!$response->successful()) {
                $body = $response->json();
                throw new \Exception($body['message'] ?? $response->body());
            }

            $data = $response->json();
            $spend = 0;
            $clicks = 0;
            $impressions = 0;
            $reach = 0;
            $conversions = 0;

            foreach ($data['data']['list'] ?? [] as $row) {
                $m = $row['metrics'] ?? [];
                $spend += (float) ($m['spend'] ?? 0);
                $clicks += (int) ($m['clicks'] ?? 0);
                $impressions += (int) ($m['impressions'] ?? 0);
                $reach += (int) ($m['reach'] ?? 0);
                $conversions += (int) ($m['conversion'] ?? 0);
            }

            return [
                'success' => true,
                'platform' => 'TikTok Ads',
                'spend' => round($spend, 2),
                'clicks' => $clicks,
                'impressions' => $impressions,
                'reach' => $reach,
                'conversions' => $conversions,
                'currency' => 'USD',
            ];
        } catch (\Throwable $e) {
            Log::error('TikTok Ads API Error: ' . $e->getMessage());
            return $this->errorResponse('TikTok Ads', $e->getMessage());
        }
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
