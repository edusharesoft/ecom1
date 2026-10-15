<?php

namespace App\Http\Controllers\Concerns;

use App\Helpers\SmsHelper;
use App\Models\GeneralSetting;
use App\Models\IncompleteOrder;
use App\Support\IncompleteOrderPayload;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

trait HandlesCheckoutOtp
{
    protected function checkoutOtpIsEnabled(): bool
    {
        $s = GeneralSetting::where('status', 1)->first();

        return $s && (int) ($s->checkout_otp_enabled ?? 0) === 1;
    }

    protected function checkoutOtpSessionKeys(string $channel): array
    {
        return [
            'pending' => "chkotp_{$channel}_pending",
            'code' => "chkotp_{$channel}_code",
            'phone' => "chkotp_{$channel}_phone",
            'expires' => "chkotp_{$channel}_expires",
            'sent_at' => "chkotp_{$channel}_sent_at",
            'draft' => "chkotp_{$channel}_draft",
        ];
    }

    protected function checkoutOtpStoreDraft(Request $request, string $channel): void
    {
        $keys = $this->checkoutOtpSessionKeys($channel);
        $existing = Session::get($keys['draft'], []);
        if (! is_array($existing)) {
            $existing = [];
        }

        $incoming = [
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'division_id' => $request->input('division_id'),
            'district_id' => $request->input('district_id'),
            'upazila_id' => $request->input('upazila_id'),
            'payment_method' => $request->input('payment_method'),
            'manual_trx_id' => $request->input('manual_trx_id'),
            'manual_sender_number' => $request->input('manual_sender_number'),
            'order_note' => $request->input('order_note'),
        ];

        foreach ($incoming as $key => $value) {
            if ($value !== null && $value !== '') {
                $existing[$key] = $value;
            }
        }

        Session::put($keys['draft'], $existing);
    }

    protected function checkoutOtpApplyDraft(Request $request, string $channel, bool $force = false): void
    {
        $draft = Session::get($this->checkoutOtpSessionKeys($channel)['draft'], []);
        if (! is_array($draft)) {
            return;
        }

        foreach ($draft as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if ($force || ! $request->filled($key)) {
                $request->merge([$key => $value]);
            }
        }
    }

    protected function checkoutOtpForget(string $channel, bool $cleanupIncomplete = false): void
    {
        if ($cleanupIncomplete) {
            $this->checkoutOtpCleanupIncompleteForChannel($channel);
        }

        foreach ($this->checkoutOtpSessionKeys($channel) as $sessionKey) {
            Session::forget($sessionKey);
        }
    }

    protected function checkoutOtpCleanupIncompleteForChannel(string $channel): void
    {
        $keys = $this->checkoutOtpSessionKeys($channel);
        $phone = Session::get($keys['phone']);
        if (! $phone) {
            return;
        }

        $localPhone = IncompleteOrderPayload::normalizePhone($phone);
        if ($localPhone !== '') {
            IncompleteOrder::where('phone', $localPhone)->delete();
        }
    }

    protected function checkoutOtpSweepExpired(string $channel): void
    {
        $keys = $this->checkoutOtpSessionKeys($channel);
        if (! Session::get($keys['pending'])) {
            return;
        }

        $exp = (int) Session::get($keys['expires'], 0);
        if ($exp > 0 && now()->timestamp > $exp) {
            $this->checkoutOtpForget($channel, true);
        }
    }

    protected function checkoutOtpWantsJson(Request $request, bool $force = false): bool
    {
        return $force || $request->ajax() || $request->expectsJson() || $request->wantsJson();
    }

    protected function checkoutOtpRedirectToCheckout(Request $request, string $channel, bool $preserveFlashedInput = false): \Illuminate\Http\RedirectResponse
    {
        if ($preserveFlashedInput) {
            return redirect()->back();
        }

        return redirect()->back()->withInput($request->except(['checkout_otp', 'checkout_otp_resend', '_token']));
    }

    protected function checkoutOtpJson(bool $ok, string $message, string $type = 'success', int $status = 200): JsonResponse
    {
        return response()->json([
            'ok' => $ok,
            'message' => $message,
            'type' => $type,
        ], $status);
    }

    protected function normalizeBdCheckoutPhone(?string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', (string) $phone);
        if (strlen($phone) === 11 && str_starts_with($phone, '0')) {
            $phone = '88'.$phone;
        } elseif (strlen($phone) === 10) {
            $phone = '880'.$phone;
        }

        return $phone;
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|JsonResponse|null null = চালিয়ে অর্ডার প্রসেস করুন
     */
    protected function checkoutOtpGate(Request $request, string $channel, bool $consumeOnSuccess = true)
    {
        if (! $this->checkoutOtpIsEnabled()) {
            return null;
        }

        $this->checkoutOtpSweepExpired($channel);
        $keys = $this->checkoutOtpSessionKeys($channel);

        $normalized = $this->normalizeBdCheckoutPhone($request->input('phone'));

        if (Session::get($keys['pending'])) {
            if ($normalized !== Session::get($keys['phone'])) {
                $this->checkoutOtpForget($channel, true);
                Toastr::warning('মোবাইল নম্বর পরিবর্তিত হয়েছে। নতুন OTP পাঠানো হচ্ছে।');

                return $this->checkoutOtpIssueFresh($request, $channel, $normalized);
            }

            $request->validate([
                'checkout_otp' => 'required|digits:6',
            ], [
                'checkout_otp.required' => 'এসএমএসে পাঠানো OTP কোডটি লিখুন।',
                'checkout_otp.digits' => 'OTP ৬ ডিজিটের হতে হবে।',
            ]);

            $exp = (int) Session::get($keys['expires'], 0);
            if ($exp === 0 || now()->timestamp > $exp) {
                $this->checkoutOtpForget($channel, true);
                Toastr::error('OTP এর মেয়াদ শেষ। আবার চেষ্টা করুন।');

                return redirect()->back()->withInput();
            }

            if ((string) Session::get($keys['code']) !== trim((string) $request->input('checkout_otp'))) {
                Toastr::error('OTP ভুল। আবার চেষ্টা করুন।');

                return redirect()->back()->withInput($request->except('checkout_otp'));
            }

            if ($consumeOnSuccess) {
                $this->checkoutOtpForget($channel);
            }

            return null;
        }

        return $this->checkoutOtpIssueFresh($request, $channel, $normalized);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|JsonResponse
     */
    protected function checkoutOtpIssueFresh(Request $request, string $channel, string $normalizedPhone, bool $forceJson = false, bool $redirectToCheckout = false)
    {
        if ($normalizedPhone === '' || strlen($normalizedPhone) < 12) {
            $message = 'সঠিক মোবাইল নম্বর দিন।';
            if ($this->checkoutOtpWantsJson($request, $forceJson)) {
                return $this->checkoutOtpJson(false, $message, 'error', 422);
            }
            Toastr::error($message);

            return $redirectToCheckout
                ? $this->checkoutOtpRedirectToCheckout($request, $channel)
                : redirect()->back()->withInput();
        }

        $keys = $this->checkoutOtpSessionKeys($channel);
        $otp = (string) random_int(100000, 999999);
        $setting = GeneralSetting::where('status', 1)->first();
        $siteName = $setting->name ?? config('app.name');
        $msg = "আপনার অর্ডার ভেরিফিকেশন কোড: {$otp}। এটি কারও সাথে শেয়ার করবেন না। — {$siteName}";

        $sent = SmsHelper::send($normalizedPhone, $msg, ['order' => 1]);
        if (! $sent) {
            $message = 'এসএমএস পাঠানো যায়নি। SMS গেটওয়ে (অর্ডার টাইপ) সক্রিয় ও API চেক করুন।';
            if ($this->checkoutOtpWantsJson($request, $forceJson)) {
                return $this->checkoutOtpJson(false, $message, 'error', 500);
            }
            Toastr::error($message);

            return $redirectToCheckout
                ? $this->checkoutOtpRedirectToCheckout($request, $channel)
                : redirect()->back()->withInput();
        }

        Session::put($keys['pending'], true);
        Session::put($keys['code'], $otp);
        Session::put($keys['phone'], $normalizedPhone);
        Session::put($keys['expires'], now()->addMinutes(10)->timestamp);
        Session::put($keys['sent_at'], now()->timestamp);

        if (! $request->boolean('checkout_otp_resend')) {
            $this->checkoutOtpStoreDraft($request, $channel);
        }

        $message = 'আপনার মোবাইলে OTP পাঠানো হয়েছে। কোডটি বসিয়ে আবার অর্ডার নিশ্চিত করুন।';
        if ($this->checkoutOtpWantsJson($request, $forceJson)) {
            return $this->checkoutOtpJson(true, $message, 'success');
        }

        Toastr::success($message, 'OTP পাঠানো হয়েছে');

        if ($redirectToCheckout) {
            if ($request->boolean('checkout_otp_resend')) {
                return redirect()->back()->with('checkout_otp_cleared', true);
            }

            return $this->checkoutOtpRedirectToCheckout($request, $channel);
        }

        return redirect()->back()->withInput($request->except(['checkout_otp', 'checkout_otp_resend', '_token']));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|JsonResponse
     */
    protected function checkoutOtpResendForChannel(Request $request, string $channel, bool $forceJson = false)
    {
        if (! $this->checkoutOtpIsEnabled()) {
            $message = 'এই ফিচার বন্ধ আছে।';
            if ($this->checkoutOtpWantsJson($request, $forceJson)) {
                return $this->checkoutOtpJson(false, $message, 'error', 403);
            }
            Toastr::error($message);

            return $this->checkoutOtpRedirectToCheckout($request, $channel, true);
        }

        $this->checkoutOtpSweepExpired($channel);
        $keys = $this->checkoutOtpSessionKeys($channel);

        if (! Session::get($keys['pending'])) {
            $message = 'প্রথমে অর্ডার ফর্ম জমা দিন।';
            if ($this->checkoutOtpWantsJson($request, $forceJson)) {
                return $this->checkoutOtpJson(false, $message, 'warning', 422);
            }
            Toastr::warning($message);

            return $this->checkoutOtpRedirectToCheckout($request, $channel, true);
        }

        $last = (int) Session::get($keys['sent_at'], 0);
        if ($last && (now()->timestamp - $last) < 55) {
            $message = 'এক মিনিট অপেক্ষা করে আবার OTP চাইতে পারেন।';
            if ($this->checkoutOtpWantsJson($request, $forceJson)) {
                return response()->json([
                    'ok' => false,
                    'message' => $message,
                    'type' => 'info',
                    'retry_after' => 55 - (now()->timestamp - $last),
                ], 429);
            }
            Toastr::info($message);

            return $this->checkoutOtpRedirectToCheckout($request, $channel, true);
        }

        $sessionPhone = (string) Session::get($keys['phone'], '');
        if ($sessionPhone === '') {
            $message = 'প্রথমে অর্ডার ফর্ম জমা দিন।';
            if ($this->checkoutOtpWantsJson($request, $forceJson)) {
                return $this->checkoutOtpJson(false, $message, 'warning', 422);
            }
            Toastr::warning($message);

            return $this->checkoutOtpRedirectToCheckout($request, $channel, true);
        }

        return $this->checkoutOtpIssueFresh($request, $channel, $sessionPhone, $forceJson, true);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|JsonResponse
     */
    protected function checkoutOtpCancelForChannel(Request $request, string $channel)
    {
        $hadPending = (bool) Session::get($this->checkoutOtpSessionKeys($channel)['pending']);
        if ($hadPending) {
            $this->checkoutOtpForget($channel, true);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return $this->checkoutOtpJson(true, 'চেকআউট সেশন বাতিল হয়েছে।');
        }

        if ($hadPending) {
            Toastr::info('OTP ছাড়া চেকআউট বাতিল হয়েছে। আবার অর্ডার করতে ফর্ম পূরণ করুন।');
        }

        return redirect()->back()->withInput();
    }
}
