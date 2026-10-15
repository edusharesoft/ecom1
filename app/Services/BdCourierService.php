<?php

namespace App\Services;

use App\Models\GeneralSetting;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BdCourierService
{
    /**
     * BD Courier Bearer টোকেন: প্রথমে অ্যাডমিন ফ্রড সেটিংস (`general_settings.fraud_api_key`),
     * খালি থাকলে `.env` → `BDCOURIER_API_KEY` (`config('services.bdcourier.api_key')`)।
     */
    public static function resolveApiKey(): ?string
    {
        try {
            $gs = GeneralSetting::first();
            $db = $gs ? trim((string) ($gs->fraud_api_key ?? '')) : '';
            if ($db !== '') {
                return $db;
            }
        } catch (\Throwable $e) {
            Log::warning('BdCourierService::resolveApiKey DB read failed', ['error' => $e->getMessage()]);
        }

        $env = trim((string) config('services.bdcourier.api_key'));

        return $env !== '' ? $env : null;
    }

    /**
     * BD Courier `courier-check` API — ফোন অনুযায়ী কুরিয়ার রেশিও / ফ্রড সারাংশ।
     *
     * @param  bool  $syncToOrders  true হলে ওই ফোনের শিপিংযুক্ত অর্ডারে pathao/redx/steadfast/fraud ফিল্ড আপডেট
     * @return array{success: bool, message: string, payload: ?array}
     */
    public static function fetchCourierCheck(string $phone, bool $syncToOrders = true): array
    {
        $phone = trim($phone);
        if ($phone === '') {
            return ['success' => false, 'message' => 'Mobile number missing', 'payload' => null];
        }

        $apiKey = self::resolveApiKey();
        if (!$apiKey) {
            return ['success' => false, 'message' => 'BD Courier API কী নেই। অ্যাডমিন → ফ্রড সেটিংসে কী দিন, অথবা .env এ BDCOURIER_API_KEY সেট করুন।', 'payload' => null];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ])->timeout(20)->post('https://api.bdcourier.com/courier-check', [
                'phone' => $phone,
            ]);

            $res = $response->json();

            if (($res['status'] ?? '') !== 'success') {
                return [
                    'success' => false,
                    'message' => $res['message'] ?? 'Courier check ব্যর্থ হয়েছে',
                    'payload' => is_array($res) ? $res : null,
                ];
            }

            if ($syncToOrders) {
                self::applyCourierPayloadToOrders($phone, $res['data'] ?? []);
            }

            return ['success' => true, 'message' => $res['message'] ?? '', 'payload' => $res];
        } catch (\Throwable $e) {
            Log::warning('BdCourierService::fetchCourierCheck failed', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'message' => 'API Error: ' . $e->getMessage(), 'payload' => null];
        }
    }

    /**
     * @deprecated Use fetchCourierCheck($phone, true)
     */
    public static function fetchAndSyncOrders(string $phone): array
    {
        return self::fetchCourierCheck($phone, true);
    }

    protected static function applyCourierPayloadToOrders(string $phone, array $cData): void
    {
        $orders = Order::whereHas('shipping', static function ($q) use ($phone): void {
            $q->where('phone', $phone);
        })->get();

        foreach ($orders as $order) {
            $order->pathao_success    = $cData['pathao']['success_parcel'] ?? 0;
            $order->pathao_cancel     = $cData['pathao']['cancelled_parcel'] ?? 0;
            $order->pathao_rate       = $cData['pathao']['success_ratio'] ?? 0;

            $order->redx_success      = $cData['redx']['success_parcel'] ?? 0;
            $order->redx_cancel       = $cData['redx']['cancelled_parcel'] ?? 0;
            $order->redx_rate         = $cData['redx']['success_ratio'] ?? 0;

            $order->steadfast_success = $cData['steadfast']['success_parcel'] ?? 0;
            $order->steadfast_cancel  = $cData['steadfast']['cancelled_parcel'] ?? 0;
            $order->steadfast_rate    = $cData['steadfast']['success_ratio'] ?? 0;

            $order->fraud_success     = $cData['summary']['success_parcel'] ?? 0;
            $order->fraud_cancel      = $cData['summary']['cancelled_parcel'] ?? 0;
            $order->fraud_rate        = $cData['summary']['success_ratio'] ?? 0;

            $order->save();
        }
    }
}
