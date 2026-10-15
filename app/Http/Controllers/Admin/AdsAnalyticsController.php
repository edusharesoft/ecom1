<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdsAnalyticsSetting;
use App\Models\ContactMessage;
use App\Models\Expense;
use App\Services\Ads\FacebookAdsService;
use App\Services\Ads\GoogleAdsService;
use App\Services\Ads\TikTokAdsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Brian2694\Toastr\Facades\Toastr;

class AdsAnalyticsController extends Controller
{
    public function __construct(
        protected FacebookAdsService $facebookAds,
        protected GoogleAdsService $googleAds,
        protected TikTokAdsService $tiktokAds
    ) {}

    /**
     * লাইভ এডস ড্যাশবোর্ড - Facebook, Google, TikTok Ads + মেসেজ ও ডলার খরচ
     */
    public function dashboard(Request $request)
    {
        $refresh = $request->boolean('refresh');
        $cacheKey = 'ads_analytics_dashboard';
        $cacheMinutes = $refresh ? 0 : 5;

        $data = $cacheMinutes > 0
            ? Cache::remember($cacheKey, $cacheMinutes * 60, fn () => $this->fetchAllData())
            : $this->fetchAllData();

        return view('backEnd.ads_analytics.dashboard', $data);
    }

    /**
     * Facebook Ads only - separate page
     */
    public function facebook(Request $request)
    {
        $refresh = $request->boolean('refresh');
        $cacheKey = 'ads_analytics_facebook';
        $data = $refresh ? $this->fetchFacebookOnly() : Cache::remember($cacheKey, 300, fn () => $this->fetchFacebookOnly());
        return view('backEnd.ads_analytics.facebook', $data);
    }

    /**
     * Google Ads only - separate page
     */
    public function google(Request $request)
    {
        $refresh = $request->boolean('refresh');
        $cacheKey = 'ads_analytics_google';
        $data = $refresh ? $this->fetchGoogleOnly() : Cache::remember($cacheKey, 300, fn () => $this->fetchGoogleOnly());
        return view('backEnd.ads_analytics.google', $data);
    }

    /**
     * TikTok Ads only - separate page
     */
    public function tiktok(Request $request)
    {
        $refresh = $request->boolean('refresh');
        $cacheKey = 'ads_analytics_tiktok';
        $data = $refresh ? $this->fetchTikTokOnly() : Cache::remember($cacheKey, 300, fn () => $this->fetchTikTokOnly());
        return view('backEnd.ads_analytics.tiktok', $data);
    }

    private function fetchFacebookOnly(): array
    {
        $fb = $this->facebookAds->getInsights();
        return ['facebook' => $fb, 'platform' => 'Facebook Ads'];
    }

    private function fetchGoogleOnly(): array
    {
        $google = $this->googleAds->getInsights();
        return ['google' => $google, 'platform' => 'Google Ads'];
    }

    private function fetchTikTokOnly(): array
    {
        $tiktok = $this->tiktokAds->getInsights();
        return ['tiktok' => $tiktok, 'platform' => 'TikTok Ads'];
    }

    /**
     * AJAX - Live data refresh
     */
    public function liveData(Request $request)
    {
        $data = $this->fetchAllData();
        return response()->json($data);
    }

    private function fetchAllData(): array
    {
        $fb = $this->facebookAds->getInsights();
        $google = $this->googleAds->getInsights();
        $tiktok = $this->tiktokAds->getInsights();

        // মেসেজ কাউন্ট (ওয়েবসাইট কন্টাক্ট মেসেজ)
        $totalMessages = ContactMessage::count();
        $todayMessages = ContactMessage::whereDate('created_at', Carbon::today())->count();
        $unreadMessages = ContactMessage::where('status', '!=', 'read')->count();

        // মোট এড স্পেন্ড (আজকের)
        $totalAdSpendToday = ($fb['spend'] ?? 0) + ($google['spend'] ?? 0) + ($tiktok['spend'] ?? 0);

        // মোট খরচ (এক্সপেন্স টেবিল থেকে - আজকের)
        $todayExpenses = Expense::whereDate('expense_date', Carbon::today())->sum('amount');
        $monthlyExpenses = Expense::whereYear('expense_date', Carbon::now()->year)
            ->whereMonth('expense_date', Carbon::now()->month)
            ->sum('amount');

        return [
            'facebook' => $fb,
            'google' => $google,
            'tiktok' => $tiktok,
            'totalMessages' => $totalMessages,
            'todayMessages' => $todayMessages,
            'unreadMessages' => $unreadMessages,
            'totalAdSpendToday' => round($totalAdSpendToday, 2),
            'todayExpenses' => $todayExpenses,
            'monthlyExpenses' => $monthlyExpenses,
        ];
    }

    /**
     * API সেটিংস পেজ
     */
    public function settings()
    {
        $settings = AdsAnalyticsSetting::all()->keyBy('platform');
        return view('backEnd.ads_analytics.settings', compact('settings'));
    }

    /**
     * সেটিংস সেভ
     */
    public function saveSettings(Request $request)
    {
        $platforms = ['facebook', 'google', 'tiktok'];

        foreach ($platforms as $platform) {
            $prefix = "{$platform}_";
            $isActive = $request->boolean("{$prefix}is_active");

            $setting = AdsAnalyticsSetting::firstOrNew(['platform' => $platform]);
            $setting->is_active = $isActive;

            if ($platform === 'facebook') {
                $setting->access_token = $request->input("{$prefix}access_token");
                $setting->ad_account_id = $request->input("{$prefix}ad_account_id");
                $setting->app_id = $request->input("{$prefix}app_id");
                $setting->app_secret = $request->input("{$prefix}app_secret");
            }

            if ($platform === 'google') {
                $setting->ad_account_id = $request->input("{$prefix}ad_account_id"); // Customer ID
                $setting->client_id = $request->input("{$prefix}client_id");
                $setting->client_secret = $request->input("{$prefix}client_secret");
                $setting->refresh_token = $request->input("{$prefix}refresh_token");
            }

            if ($platform === 'tiktok') {
                $setting->access_token = $request->input("{$prefix}access_token");
                $setting->ad_account_id = $request->input("{$prefix}advertiser_id");
            }

            $setting->save();
        }

        AdsAnalyticsSetting::forgetCache();
        Toastr::success('Settings saved successfully', 'Success');
        return redirect()->route('admin.ads_analytics.settings');
    }
}
