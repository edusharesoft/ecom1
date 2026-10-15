<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Courierapi;

class RedXService
{
    protected $baseUrl;
    protected $token;
    protected $isSandbox;

    public function __construct()
    {
        $redxConfig = Courierapi::where(['status' => 1, 'type' => 'redx'])->first();
        
        if ($redxConfig) {
            $url = rtrim($redxConfig->url, '/');
            // Add https:// if not present
            if (!preg_match('/^https?:\/\//', $url)) {
                $url = 'https://' . $url;
            }
            $this->baseUrl = $url;
            
            // Clean token - remove Bearer prefix if already present, trim whitespace
            $token = trim($redxConfig->token ?? '');
            $token = preg_replace('/^Bearer\s+/i', '', $token); // Remove Bearer prefix if exists
            $this->token = $token;
            
            $this->isSandbox = strpos($this->baseUrl, 'sandbox') !== false;
        }
    }
    
    /**
     * Check if service is properly configured
     *
     * @return bool
     */
    public function isConfigured()
    {
        return !empty($this->token) && !empty($this->baseUrl);
    }
    
    /**
     * Get configuration status
     *
     * @return array
     */
    public function getConfigStatus()
    {
        return [
            'configured' => $this->isConfigured(),
            'has_token' => !empty($this->token),
            'has_base_url' => !empty($this->baseUrl),
            'base_url' => $this->baseUrl ?? 'Not set',
            'is_sandbox' => $this->isSandbox ?? false
        ];
    }

    /**
     * Create a new parcel
     *
     * @param array $data
     * @return array|null
     */
    public function createParcel(array $data)
    {
        // Check if token and baseUrl are set
        if (empty($this->token)) {
            Log::error('RedX Create Parcel Failed: Token not configured');
            return ['error' => 'RedX API token not configured'];
        }
        
        if (empty($this->baseUrl)) {
            Log::error('RedX Create Parcel Failed: Base URL not configured');
            return ['error' => 'RedX API base URL not configured'];
        }

        try {
            $url = $this->baseUrl . '/parcel';
            
            // Clean token - remove Bearer prefix if already present
            $token = trim($this->token);
            $token = preg_replace('/^Bearer\s+/i', '', $token); // Remove Bearer prefix if exists
            
            Log::info('RedX Create Parcel Request', [
                'url' => $url,
                'data' => $data,
                'token_preview' => substr($token, 0, 20) . '...',
                'token_length' => strlen($token),
                'is_sandbox' => $this->isSandbox
            ]);
            
            $response = Http::withHeaders([
                'API-ACCESS-TOKEN' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ])->post($url, $data);

            $statusCode = $response->status();
            $responseBody = $response->body();
            $responseJson = $response->json();

            if ($response->successful()) {
                Log::info('RedX Create Parcel Success', [
                    'status' => $statusCode,
                    'response' => $responseJson
                ]);
                return $responseJson;
            }

            Log::error('RedX Create Parcel Failed', [
                'status' => $statusCode,
                'response_body' => $responseBody,
                'response_json' => $responseJson,
                'request_data' => $data,
                'url' => $url
            ]);

            $errorMessage = $responseJson['message'] ?? $responseBody ?? 'Unknown error';
            
            // Provide helpful error messages
            if ($statusCode == 401) {
                if (strpos(strtolower($errorMessage), 'invalid signature') !== false || strpos(strtolower($errorMessage), 'invalid') !== false) {
                    $errorMessage = 'Invalid API token. Please check: 1) Token is correct, 2) Token matches environment (Sandbox token for Sandbox URL, Production token for Production URL), 3) Token is not expired.';
                } else {
                    $errorMessage = 'Authentication failed (401). Please verify your API token is correct and matches the selected environment.';
                }
            }
            
            return [
                'error' => 'API request failed',
                'status' => $statusCode,
                'message' => $errorMessage,
                'response_body' => $responseBody
            ];
        } catch (\Exception $e) {
            Log::error('RedX Create Parcel Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);
            return [
                'error' => 'Exception occurred',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Track a parcel
     *
     * @param string $trackingId
     * @return array|null
     */
    public function trackParcel(string $trackingId)
    {
        try {
            $response = Http::withHeaders([
                'API-ACCESS-TOKEN' => 'Bearer ' . $this->token,
            ])->get($this->baseUrl . '/parcel/track/' . $trackingId);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('RedX Track Parcel Failed', [
                'tracking_id' => $trackingId,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('RedX Track Parcel Exception', [
                'tracking_id' => $trackingId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get parcel details
     *
     * @param string $trackingId
     * @return array|null
     */
    public function getParcelDetails(string $trackingId)
    {
        try {
            $response = Http::withHeaders([
                'API-ACCESS-TOKEN' => 'Bearer ' . $this->token,
            ])->get($this->baseUrl . '/parcel/info/' . $trackingId);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('RedX Get Parcel Details Failed', [
                'tracking_id' => $trackingId,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('RedX Get Parcel Details Exception', [
                'tracking_id' => $trackingId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Update parcel status
     *
     * @param string $trackingId
     * @param string $status
     * @param string|null $reason
     * @return array|null
     */
    public function updateParcel(string $trackingId, string $status, ?string $reason = null)
    {
        try {
            $data = [
                'entity_type' => 'parcel-tracking-id',
                'entity_id' => $trackingId,
                'update_details' => [
                    'property_name' => 'status',
                    'new_value' => $status,
                ]
            ];

            if ($reason) {
                $data['update_details']['reason'] = $reason;
            }

            $response = Http::withHeaders([
                'API-ACCESS-TOKEN' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ])->patch($this->baseUrl . '/parcels', $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('RedX Update Parcel Failed', [
                'tracking_id' => $trackingId,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('RedX Update Parcel Exception', [
                'tracking_id' => $trackingId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get all areas
     *
     * @param int|null $postCode
     * @param string|null $districtName
     * @return array|null
     */
    public function getAreas(?int $postCode = null, ?string $districtName = null)
    {
        try {
            $url = $this->baseUrl . '/areas';
            
            $params = [];
            if ($postCode) {
                $params['post_code'] = $postCode;
            }
            if ($districtName) {
                $params['district_name'] = $districtName;
            }

            if (!empty($params)) {
                $url .= '?' . http_build_query($params);
            }

            $response = Http::withHeaders([
                'API-ACCESS-TOKEN' => 'Bearer ' . $this->token,
            ])->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('RedX Get Areas Failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('RedX Get Areas Exception', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Calculate parcel charge
     *
     * @param int $deliveryAreaId
     * @param int $pickupAreaId
     * @param float $cashCollectionAmount
     * @param int $weight Weight in grams
     * @return array|null
     */
    public function calculateCharge(int $deliveryAreaId, int $pickupAreaId, float $cashCollectionAmount, int $weight)
    {
        try {
            $url = $this->baseUrl . '/charge/charge_calculator?' . http_build_query([
                'delivery_area_id' => $deliveryAreaId,
                'pickup_area_id' => $pickupAreaId,
                'cash_collection_amount' => $cashCollectionAmount,
                'weight' => $weight
            ]);

            $response = Http::withHeaders([
                'API-ACCESS-TOKEN' => 'Bearer ' . $this->token,
            ])->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('RedX Calculate Charge Failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('RedX Calculate Charge Exception', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Create pickup store
     *
     * @param array $data
     * @return array|null
     */
    public function createPickupStore(array $data)
    {
        try {
            $response = Http::withHeaders([
                'API-ACCESS-TOKEN' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/pickup/store', $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('RedX Create Pickup Store Failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('RedX Create Pickup Store Exception', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get all pickup stores
     *
     * @return array|null
     */
    public function getPickupStores()
    {
        try {
            $response = Http::withHeaders([
                'API-ACCESS-TOKEN' => 'Bearer ' . $this->token,
            ])->get($this->baseUrl . '/pickup/stores');

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('RedX Get Pickup Stores Failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('RedX Get Pickup Stores Exception', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get pickup store details
     *
     * @param int $pickupStoreId
     * @return array|null
     */
    public function getPickupStoreDetails(int $pickupStoreId)
    {
        try {
            $response = Http::withHeaders([
                'API-ACCESS-TOKEN' => 'Bearer ' . $this->token,
            ])->get($this->baseUrl . '/pickup/store/info/' . $pickupStoreId);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('RedX Get Pickup Store Details Failed', [
                'pickup_store_id' => $pickupStoreId,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('RedX Get Pickup Store Details Exception', [
                'pickup_store_id' => $pickupStoreId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Map RedX status to order status
     *
     * @param string $redxStatus
     * @return int|null
     */
    public function mapStatusToOrderStatus(string $redxStatus)
    {
        $s = strtolower(trim(str_replace('_', '-', $redxStatus)));

        /** @var list<string> $delivered ডেলিভারড / সমতুল্য → Completed (৬) */
        $delivered = ['delivered', 'partial-delivered', 'partially-delivered', 'partial-delivered-completed'];

        /** @var list<string> $cancelReturn বাতিল রিটার্ন → Cancelled (১১) */
        $cancelReturn = [
            'returned', 'cancelled', 'canceled', 'parcel-cancelled', 'parcel-canceled',
            'parcel-returned', 'delivery-failed', 'delivery-cancelled',
        ];

        if (in_array($s, $delivered, true)) {
            return 6;
        }

        if (in_array($s, $cancelReturn, true)) {
            return 11;
        }

        // ট্রানজিট/হোল্ড — অর্ডার ৫ থাকলে ওভারওয়েট আপডেট দরকার নেই (Courier:check-status স্কিপ করে)
        return null;
    }
}
