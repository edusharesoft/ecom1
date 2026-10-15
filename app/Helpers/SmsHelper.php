<?php

namespace App\Helpers;

use App\Models\SmsGateway;

class SmsHelper
{
    /**
     * SMS পাঠাও — fully dynamic gateway support
     *
     * @param  string|array  $phone    একটি বা একাধিক নম্বর
     * @param  string        $message  SMS content
     * @param  array         $filters  SmsGateway query filters (e.g. ['order' => 1])
     * @return bool
     */
    public static function send($phone, string $message, array $filters = []): bool
    {
        $gateway = SmsGateway::where('status', 1);
        foreach ($filters as $col => $val) {
            $gateway->where($col, $val);
        }
        $gateway = $gateway->first();

        if (!$gateway || empty($gateway->api_key)) {
            return false;
        }

        // ── নম্বর normalize ──
        $numbers = is_array($phone) ? $phone : explode(',', $phone);
        $numbers = array_values(array_filter(array_map(
            fn($n) => self::normalizePhone(trim($n)),
            $numbers
        )));
        if (empty($numbers)) {
            return false;
        }
        $numberStr = implode(',', $numbers);

        // ── Dynamic parameter names (DB থেকে) ──
        $pApiKey   = $gateway->param_api_key  ?: 'api_key';
        $pPhone    = $gateway->param_phone    ?: 'number';
        $pMessage  = $gateway->param_message  ?: 'message';
        $pSenderid = $gateway->param_senderid ?: 'senderid';

        $senderid  = $gateway->senderid ?: ($gateway->serderid ?? '');

        // ── Request params build ──
        $params = [];

        // Extra static params (e.g. {"type":"text"})
        if (!empty($gateway->extra_params)) {
            $extra = is_array($gateway->extra_params)
                ? $gateway->extra_params
                : json_decode($gateway->extra_params, true);
            if (is_array($extra)) {
                $params = $extra;
            }
        }

        $params[$pApiKey]  = $gateway->api_key;
        $params[$pPhone]   = $numberStr;
        $params[$pMessage] = $message;
        if (!empty($senderid)) {
            $params[$pSenderid] = $senderid;
        }

        $baseUrl = rtrim($gateway->url ?: 'http://bulksmsbd.net/api/smsapi', '?&');
        $method  = strtoupper($gateway->method ?? 'GET');

        // ── cURL request ──
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        // Auth header
        $authType  = $gateway->auth_type ?? 'none';
        $authValue = $gateway->auth_value ?? '';
        $headers   = [];
        if ($authType === 'bearer' && !empty($authValue)) {
            $headers[] = 'Authorization: Bearer ' . $authValue;
        } elseif ($authType === 'basic' && !empty($authValue)) {
            $headers[] = 'Authorization: Basic ' . base64_encode($authValue);
        }
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_URL, $baseUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        } else {
            // GET
            curl_setopt($ch, CURLOPT_URL, $baseUrl . '?' . http_build_query($params));
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        }

        $response = curl_exec($ch);
        $err      = curl_error($ch);
        curl_close($ch);

        if ($err) {
            \Log::warning('SmsHelper cURL error: ' . $err);
            return false;
        }

        \Log::info('SmsHelper [' . ($gateway->gateway_name ?? 'Gateway') . '] response: ' . $response);

        // ── Success check ──
        $successKey = $gateway->success_check ?: '202';
        if (stripos($response, $successKey) !== false) {
            return true;
        }
        // JSON response_code check
        $decoded = json_decode($response, true);
        if (isset($decoded['response_code']) && (string)$decoded['response_code'] === $successKey) {
            return true;
        }
        return false;
    }

    /**
     * পাসওয়ার্ড রিসেট / OTP — গেটওয়ে ফলব্যাক:
     * 1) forget_pass চালু  2) order চালু  3) যেকোনো active গেটওয়ে
     */
    public static function sendForPasswordReset($phone, string $message): bool
    {
        foreach ([['forget_pass' => 1], ['order' => 1], []] as $filters) {
            if (self::send($phone, $message, $filters)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Admin notification numbers-এ SMS পাঠাও (DB এর admin_phone field থেকে)
     */
    public static function sendToAdmin(string $message): bool
    {
        $gateway = SmsGateway::where('status', 1)->first();
        if (!$gateway || empty($gateway->admin_phone)) {
            return false;
        }
        return self::send($gateway->admin_phone, $message);
    }

    /**
     * Test SMS — gateway connection check করো
     */
    public static function test(string $phone, string $message = 'Test SMS from your store.'): array
    {
        $gateway = SmsGateway::where('status', 1)->first();
        if (!$gateway) {
            return ['success' => false, 'message' => 'No active gateway found.'];
        }
        if (empty($gateway->api_key)) {
            return ['success' => false, 'message' => 'API Key is not configured.'];
        }

        $sent = self::send($phone, $message);
        return [
            'success' => $sent,
            'message' => $sent ? 'SMS sent successfully!' : 'SMS sending failed. Check API key and gateway config.',
            'gateway' => $gateway->gateway_name ?? 'Gateway',
        ];
    }

    /**
     * বাংলাদেশি নম্বর normalize → 880XXXXXXXXXX
     */
    private static function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) === 11 && str_starts_with($phone, '0')) {
            $phone = '88' . $phone;
        } elseif (strlen($phone) === 10) {
            $phone = '880' . $phone;
        }
        return strlen($phone) >= 10 ? $phone : '';
    }
}
