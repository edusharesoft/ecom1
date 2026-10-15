<?php

namespace App\Http\Middleware;

use App\Models\IncompleteOrder;
use App\Support\IncompleteOrderPayload;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class ClearAbandonedCheckoutOtp
{
    /** @var array<int, string> */
    private array $allowedPaths = [
        'customer/checkout',
        'customer/order-save',
        'customer/checkout-resend-otp',
        'customer/checkout-cancel-otp',
        'reseller/checkout',
        'reseller/checkout/resend-otp',
        'reseller/checkout/cancel-otp',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // চেকআউট পেজের ব্যাকগ্রাউন্ড AJAX (শিপিং, জেলা লোড ইত্যাদি) OTP সেশন মুছবে না
        if ($request->ajax() || $request->expectsJson() || $request->wantsJson()) {
            return $next($request);
        }

        $path = $request->path();

        $onCheckoutFlow = false;
        foreach ($this->allowedPaths as $allowed) {
            if ($path === $allowed) {
                $onCheckoutFlow = true;
                break;
            }
        }

        if (! $onCheckoutFlow) {
            foreach (['customer', 'reseller'] as $channel) {
                $pendingKey = "chkotp_{$channel}_pending";
                if (! Session::get($pendingKey)) {
                    continue;
                }

                $phone = Session::get("chkotp_{$channel}_phone");
                $localPhone = IncompleteOrderPayload::normalizePhone($phone);
                if ($localPhone !== '') {
                    IncompleteOrder::where('phone', $localPhone)->delete();
                }

                foreach (['pending', 'code', 'phone', 'expires', 'sent_at'] as $suffix) {
                    Session::forget("chkotp_{$channel}_{$suffix}");
                }
            }
        }

        return $next($request);
    }
}
