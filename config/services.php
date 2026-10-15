<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'facebook' => [
        'access_token' => env('FACEBOOK_ACCESS_TOKEN'),
        'pixel_id' => env('FACEBOOK_PIXEL_ID'),
        'test_event_code' => env('FACEBOOK_TEST_EVENT_CODE'),
        'app_id' => env('FACEBOOK_APP_ID'),
        'app_secret' => env('FACEBOOK_APP_SECRET'),
    ],

    'bdcourier' => [
        'api_key' => env('BDCOURIER_API_KEY'),
    ],

    /*
    | Steadfast public tracking link — %s = tracking_code (ইউআরএল টেম্পলেট ওভাররাইড: .env থেকে STEADFAST_PUBLIC_TRACK_URL)
    */
    'steadfast' => [
        'public_track_url_pattern' => env('STEADFAST_PUBLIC_TRACK_URL', 'https://steadfast.com.bd/t/%s'),
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
        'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
        'timeout' => (int) env('GEMINI_TIMEOUT', 60),
    ],
    'shatajpay' => [
        'gateway_key' => env('SHATAJ_PAY_GATEWAY_KEY'),
        'app_secret' => env('SHATAJ_PAY_APP_SECRET'),
        'app_key' => env('SHATAJ_PAY_APP_KEY'),
        'username' => env('SHATAJ_PAY_USERNAME'),
        'password' => env('SHATAJ_PAY_PASSWORD'),
        'getway_mode' => env('SHATAJ_PAY_MODE', 'production'),
    ],
];
