<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Update API URL (Main Site)
    |--------------------------------------------------------------------------
    |
    | Fallback যখন DB (general_settings.update_api_url) থেকে মান নেই।
    | প্রায়শই General Settings এ থেকে সেট করা হয়।
    |
    */

    'api_url' => env('UPDATE_API_URL', 'https://www.creativedesign.com.bd'),

    /*
    |--------------------------------------------------------------------------
    | Script Name
    |--------------------------------------------------------------------------
    |
    | Fallback যখন DB (general_settings.update_script_name) থেকে মান নেই।
    |
    */

    'script_name' => env('UPDATE_SCRIPT_NAME', 'Ecommerce Pro'),

    /*
    |--------------------------------------------------------------------------
    | Current Version
    |--------------------------------------------------------------------------
    |
    | Fallback যখন DB (general_settings.app_version) থেকে মান নেই।
    |
    */

    'current_version' => env('APP_VERSION', '1.0.0'),

];
