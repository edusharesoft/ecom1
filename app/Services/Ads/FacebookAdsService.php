<?php

namespace App\Services\Ads;

use App\Models\AdsAnalyticsSetting;
use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Fields\AdsInsightsFields;
use Illuminate\Support\Facades\Log;

class FacebookAdsService
{
    public function getInsights(): array
    {
        $setting = AdsAnalyticsSetting::getByPlatform('facebook');
        if (!$setting || !$setting->access_token || !$setting->ad_account_id) {
            return $this->emptyResponse('Facebook Ads');
        }

        try {
            Api::init(
                $setting->app_id ?? config('services.facebook.app_id'),
                $setting->app_secret ?? config('services.facebook.app_secret'),
                $setting->access_token
            );

            $accountId = str_starts_with($setting->ad_account_id, 'act_')
                ? $setting->ad_account_id
                : 'act_' . $setting->ad_account_id;

            $account = new AdAccount($accountId);
            $today = now()->format('Y-m-d');
            $params = [
                'time_range' => ['since' => $today, 'until' => $today],
                'fields' => [
                    AdsInsightsFields::IMPRESSIONS,
                    AdsInsightsFields::CLICKS,
                    AdsInsightsFields::SPEND,
                    AdsInsightsFields::REACH,
                    AdsInsightsFields::ACTIONS,
                ],
            ];

            $insights = $account->getInsights($params);

            $spend = 0;
            $clicks = 0;
            $impressions = 0;
            $reach = 0;
            $conversions = 0;

            foreach ($insights as $insight) {
                $spend += (float) ($insight->{AdsInsightsFields::SPEND} ?? 0);
                $clicks += (int) ($insight->{AdsInsightsFields::CLICKS} ?? 0);
                $impressions += (int) ($insight->{AdsInsightsFields::IMPRESSIONS} ?? 0);
                $reach += (int) ($insight->{AdsInsightsFields::REACH} ?? 0);
                $actions = $insight->{AdsInsightsFields::ACTIONS} ?? [];
                if (is_array($actions)) {
                    foreach ($actions as $a) {
                        if (($a['action_type'] ?? '') === 'purchase') {
                            $conversions += (int) ($a['value'] ?? 0);
                        }
                    }
                }
            }

            return [
                'success' => true,
                'platform' => 'Facebook Ads',
                'spend' => round($spend, 2),
                'clicks' => $clicks,
                'impressions' => $impressions,
                'reach' => $reach,
                'conversions' => $conversions,
                'currency' => 'USD',
            ];
        } catch (\Throwable $e) {
            Log::error('Facebook Ads API Error: ' . $e->getMessage());
            return $this->errorResponse('Facebook Ads', $e->getMessage());
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
