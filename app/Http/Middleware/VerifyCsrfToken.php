<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     * পেমেন্ট গেটওয়ে থেকে redirect/callback এলে CSRF টোকেন থাকে না, তাই ৪১৯ এড়াতে এগুলো বাদ।
     *
     * @var array<int, string>
     */
    protected $except = [
        // AamarPay – গেটওয়ে থেকে রিডাইরেক্টে CSRF নেই, তাই ৪১৯ এড়াতে
        'aamarpay/*',
        'aamarpay/success',
        'aamarpay/fail',
        'aamarpay/cancel',
        'aamarpay/checkout',
        // UddoktaPay verify, IPN, cancel
        'uddoktapay/verify',
        'uddoktapay/ipn',
        'uddoktapay/cancel',
        // Shurjopay payment success/cancel
        'payment-success',
        'payment-cancel',
        // bKash callback
        'bkash/checkout-url/callback',
        // RedX Webhook
        'api/redx/webhook',
        'redx/webhook',
        // Steadfast Webhook
        'api/steadfast/webhook',
        'steadfast/webhook',
    ];

    /**
     * পেমেন্ট ক্যালব্যাক – যেকোনো পাথ/সাবফোল্ডার থেকে এলে ও ৪১৯ এড়াতে।
     */
    protected function inExceptArray($request)
    {
        $path = $request->path();

        // aamarpay – পাথের যেকোনো জায়গায় থাকলেই বাদ (সাবফোল্ডার/prefix সব ক্ষেত্রে)
        if (str_contains($path, 'aamarpay')) {
            return true;
        }
        if (str_contains($path, 'uddoktapay')) {
            return true;
        }
        if (str_contains($path, 'payment-success') || str_contains($path, 'payment-cancel')) {
            return true;
        }
        if (str_contains($path, 'bkash/checkout-url/callback')) {
            return true;
        }
        if (str_contains($path, 'steadfast/webhook')) {
            return true;
        }

        return parent::inExceptArray($request);
    }
}
