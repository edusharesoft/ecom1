<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\FacebookPageSetting;
use App\Services\FacebookPagePostService;

class ProductObserver
{
    public function created(Product $product): void
    {
        $setting = FacebookPageSetting::first();
        if (!$setting || !$setting->auto_post_new_products || !$setting->isConfigured()) {
            return;
        }
        try {
            app(FacebookPagePostService::class)->postProduct($product);
        } catch (\Throwable $e) {
            \Log::error('Facebook auto-post failed: ' . $e->getMessage());
        }
    }
}
