<?php

return [
    'apiCredentials' => [
        'username' => 'env("MERCHANT_USERNAME")',
        'password' => env("MERCHANT_PASSWORD"),
        'prefix' => env("MERCHANT_PREFIX"),
        'return_url' => env("MERCHANT_RETURN_URL"),
        'cancel_url' => env("MERCHANT_CANCEL_URL"),
        'base_url' => env("ENGINE_URL"),
    ],
    
    
];


// MERCHANT_USERNAME="sp_sandbox"
// MERCHANT_PASSWORD="pyyk97hu&6u6"
// MERCHANT_PREFIX="NOK"
// MERCHANT_RETURN_URL="https://ecom.websolutionit.com/payment-success"
// MERCHANT_CANCEL_URL="https://ecom.websolutionit.com/payment-cancel"
// ENGINE_URL="https://sandbox.shurjopayment.com" 