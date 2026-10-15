<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ResellerFraudController extends Controller
{
    /**
     * Display manual fraud check page for reseller.
     *
     * @return \Illuminate\View\View
     */
    public function manualFraudCheckPage()
    {
        $user = Auth::guard('admin')->user();

        // Verify reseller
        if (!$user || (!$user->hasRole('reseller') && $user->role !== 'reseller')) {
            return redirect()->route('reseller.dashboard');
        }

        return view('reseller.fraud.manual_check', compact('user'));
    }

    /**
     * Perform manual fraud check.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function manualFraudCheck(Request $request)
    {
        $user = Auth::guard('admin')->user();

        // Verify reseller
        if (!$user || (!$user->hasRole('reseller') && $user->role !== 'reseller')) {
            return redirect()->route('reseller.dashboard');
        }

        $mobile = $request->input('mobile');

        if (!$mobile) {
            return back()->with('error', 'দয়া করে একটি মোবাইল নাম্বার লিখুন');
        }

        $apiKey = config('services.bdcourier.api_key');
        if (!$apiKey) {
            return back()->with('error', 'BDCourier API Key সেট করা নেই (.env → BDCOURIER_API_KEY)');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ])->timeout(20)->post('https://api.bdcourier.com/courier-check', [
                'phone' => $mobile,
            ]);

            $res = $response->json();

            if (($res['status'] ?? '') !== 'success') {
                return back()->with('error', $res['message'] ?? 'Courier check ব্যর্থ হয়েছে');
            }

            $data    = $res['data'] ?? [];
            $reports = $res['reports'] ?? [];

            return view('reseller.fraud.manual_check', compact('mobile', 'data', 'reports', 'user'));
        } catch (\Exception $e) {
            return back()->with('error', 'API Error: ' . $e->getMessage());
        }
    }
}
