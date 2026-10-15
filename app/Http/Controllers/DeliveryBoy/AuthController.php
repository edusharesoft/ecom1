<?php



namespace App\Http\Controllers\DeliveryBoy;



use App\Http\Controllers\Controller;

use App\Helpers\SmsHelper;

use App\Models\DeliveryBoy;

use App\Models\GeneralSetting;

use Carbon\Carbon;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Session;



class AuthController extends Controller

{

    private const SESSION_PHONE    = 'delivery_reset_phone';

    private const SESSION_VERIFIED = 'delivery_reset_verified_at';

    private const OTP_EXPIRE_MIN   = 10;

    private const RESET_WINDOW_MIN = 30;



    public function showLogin()

    {

        return view('delivery.auth.login');

    }



    public function login(Request $request)

    {

        $request->validate([

            'phone'    => 'required|string',

            'password' => 'required',

        ]);



        $phone = $this->normalizePhone($request->phone);

        $boy   = DeliveryBoy::where('phone', $phone)->where('status', 1)->first();



        if (! $boy || ! Hash::check($request->password, $boy->password)) {

            return back()->withErrors(['phone' => 'ফোন বা পাসওয়ার্ড ভুল।'])->withInput();

        }



        Auth::guard('delivery_boy')->login($boy, $request->boolean('remember'));



        return redirect()->intended(route('delivery.dashboard'));

    }



    public function logout(Request $request)

    {

        Auth::guard('delivery_boy')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();



        return redirect()->route('delivery.login');

    }



    public function showForgotPasswordForm()

    {

        return view('delivery.auth.forgot-password');

    }



    /** মোবাইলে OTP পাঠান */

    public function sendResetOtp(Request $request)

    {

        $request->validate([

            'phone' => 'required|string|max:30',

        ]);



        $phone = $this->normalizePhone($request->phone);

        $boy   = DeliveryBoy::where('phone', $phone)->where('status', 1)->first();



        if (! $boy) {

            return back()->with('error', 'এই নম্বরে সক্রিয় একাউন্ট পাওয়া যায়নি।')->withInput();

        }



        $otp = $this->generateOtp();



        DB::table('delivery_boy_password_resets')->updateOrInsert(

            ['phone' => $boy->phone],

            [

                'token'      => Hash::make($otp),

                'created_at' => now(),

            ]

        );



        $site = GeneralSetting::where('status', 1)->first();

        $siteName = $site->name ?? config('app.name', 'Delivery');



        $message = "Dear {$boy->name}!\r\nYour password reset OTP is {$otp}\r\nValid for " . self::OTP_EXPIRE_MIN . " minutes.\r\n{$siteName}";



        $sent = SmsHelper::sendForPasswordReset($boy->phone, $message);



        if (! $sent) {

            Log::warning('delivery_boy_reset_otp_sms_failed', ['phone' => $boy->phone]);



            return back()->with('error', 'SMS পাঠানো যায়নি। অ্যাডমিনে SMS Gateway সক্রিয় ও API Key আছে কিনা দেখুন (Order বা Forgot Password টগল চালু করুন)।')->withInput();

        }



        Session::put(self::SESSION_PHONE, $boy->phone);

        Session::forget(self::SESSION_VERIFIED);



        return redirect()

            ->route('delivery.password.verify')

            ->with('success', 'OTP আপনার মোবাইলে পাঠানো হয়েছে।');

    }



    public function showVerifyOtpForm(Request $request)

    {

        $phone = Session::get(self::SESSION_PHONE);



        if (! $phone) {

            return redirect()

                ->route('delivery.password.request')

                ->with('error', 'আগে মোবাইল নম্বর দিন।');

        }



        return view('delivery.auth.verify-otp', [

            'maskedPhone' => $this->maskPhone($phone),

        ]);

    }



    public function verifyOtp(Request $request)

    {

        $request->validate([

            'otp' => 'required|digits:6',

        ]);



        $phone = Session::get(self::SESSION_PHONE);



        if (! $phone) {

            return redirect()

                ->route('delivery.password.request')

                ->with('error', 'সেশন শেষ। আবার OTP অনুরোধ করুন।');

        }



        $row = DB::table('delivery_boy_password_resets')->where('phone', $phone)->first();



        if (! $row || ! $this->otpRowValid($row) || ! Hash::check($request->otp, $row->token)) {

            return back()->withErrors(['otp' => 'OTP সঠিক নয় বা মেয়াদ শেষ।'])->withInput();

        }



        Session::put(self::SESSION_VERIFIED, now()->toDateTimeString());



        return redirect()

            ->route('delivery.password.reset')

            ->with('success', 'OTP যাচাই হয়েছে। নতুন পাসওয়ার্ড সেট করুন।');

    }



    public function resendOtp(Request $request)

    {

        $phone = Session::get(self::SESSION_PHONE);



        if (! $phone) {

            return redirect()

                ->route('delivery.password.request')

                ->with('error', 'আগে মোবাইল নম্বর দিন।');

        }



        $boy = DeliveryBoy::where('phone', $phone)->where('status', 1)->first();



        if (! $boy) {

            Session::forget([self::SESSION_PHONE, self::SESSION_VERIFIED]);



            return redirect()

                ->route('delivery.password.request')

                ->with('error', 'একাউন্ট পাওয়া যায়নি।');

        }



        $otp = $this->generateOtp();



        DB::table('delivery_boy_password_resets')->updateOrInsert(

            ['phone' => $boy->phone],

            [

                'token'      => Hash::make($otp),

                'created_at' => now(),

            ]

        );



        $site = GeneralSetting::where('status', 1)->first();

        $siteName = $site->name ?? config('app.name', 'Delivery');



        $message = "Dear {$boy->name}!\r\nYour password reset OTP is {$otp}\r\nValid for " . self::OTP_EXPIRE_MIN . " minutes.\r\n{$siteName}";



        $sent = SmsHelper::sendForPasswordReset($boy->phone, $message);



        if (! $sent) {

            return back()->with('error', 'SMS পাঠানো যায়নি। পরে আবার চেষ্টা করুন।');

        }



        Session::forget(self::SESSION_VERIFIED);



        return back()->with('success', 'নতুন OTP পাঠানো হয়েছে।');

    }



    public function showResetForm(Request $request)

    {

        if (! $this->resetSessionValid()) {

            return redirect()

                ->route('delivery.password.request')

                ->with('error', 'প্রথমে OTP যাচাই করুন।');

        }



        return view('delivery.auth.reset-password', [

            'phone' => Session::get(self::SESSION_PHONE),

            'maskedPhone' => $this->maskPhone(Session::get(self::SESSION_PHONE)),

        ]);

    }



    public function resetPassword(Request $request)

    {

        if (! $this->resetSessionValid()) {

            return redirect()

                ->route('delivery.password.request')

                ->with('error', 'প্রথমে OTP যাচাই করুন।');

        }



        $request->validate([

            'password'              => 'required|string|min:6|confirmed',

            'password_confirmation' => 'required',

        ]);



        $phone = Session::get(self::SESSION_PHONE);

        $boy   = DeliveryBoy::where('phone', $phone)->where('status', 1)->first();



        if (! $boy) {

            $this->clearResetSession();



            return redirect()

                ->route('delivery.password.request')

                ->with('error', 'একাউন্ট পাওয়া যায়নি।');

        }



        $boy->password = Hash::make($request->password);

        $boy->save();



        DB::table('delivery_boy_password_resets')->where('phone', $phone)->delete();

        $this->clearResetSession();



        return redirect()

            ->route('delivery.login')

            ->with('success', 'নতুন পাসওয়ার্ড সেট হয়েছে। এখন লগইন করুন।');

    }



    private function resetSessionValid(): bool

    {

        $phone     = Session::get(self::SESSION_PHONE);

        $verifiedAt = Session::get(self::SESSION_VERIFIED);



        if (! $phone || ! $verifiedAt) {

            return false;

        }



        return Carbon::parse($verifiedAt)->addMinutes(self::RESET_WINDOW_MIN)->isFuture();

    }



    private function otpRowValid(object $row): bool

    {

        if (empty($row->created_at)) {

            return false;

        }



        return Carbon::parse($row->created_at)->addMinutes(self::OTP_EXPIRE_MIN)->isFuture();

    }



    private function clearResetSession(): void

    {

        Session::forget([self::SESSION_PHONE, self::SESSION_VERIFIED]);

    }



    private function normalizePhone(string $phone): string

    {

        return preg_replace('/\s+/', '', trim($phone));

    }



    private function generateOtp(): string

    {

        return (string) random_int(100000, 999999);

    }



    private function maskPhone(string $phone): string

    {

        $digits = preg_replace('/\D/', '', $phone);



        if (strlen($digits) < 6) {

            return '***';

        }



        return substr($digits, 0, 3) . str_repeat('*', max(3, strlen($digits) - 6)) . substr($digits, -3);

    }

}


