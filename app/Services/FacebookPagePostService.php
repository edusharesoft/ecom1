<?php

namespace App\Services;

use App\Models\Product;
use App\Models\FacebookPageSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookPagePostService
{
    private const GRAPH_API = 'https://graph.facebook.com/v18.0';

    public function postProduct(Product $product): array
    {
        $setting = FacebookPageSetting::firstOrCreate();
        if (!$setting->isConfigured()) {
            return ['success' => false, 'message' => 'Facebook Page not configured. Go to Settings.'];
        }

        $productUrl = route('product', $product->id);
        $imageUrl = $this->getProductImageUrl($product);

        $template = $setting->post_template ?: "🛒 New Product!\n\n{name}\n\nPrice: ৳{price}\n\nOrder now: {link}";
        $message = str_replace(
            ['{name}', '{price}', '{link}', '{description}'],
            [
                $product->name,
                number_format($product->new_price ?? $product->purchase_price),
                $productUrl,
                \Str::limit(strip_tags($product->description ?? ''), 200),
            ],
            $template
        );

        // Post with photo (image gets more engagement)
        if ($imageUrl) {
            $response = Http::post(self::GRAPH_API . "/{$setting->page_id}/photos", [
                'url' => $imageUrl,
                'message' => $message,
                'access_token' => $setting->page_access_token,
            ]);
        } else {
            $response = Http::post(self::GRAPH_API . "/{$setting->page_id}/feed", [
                'message' => $message,
                'link' => $productUrl,
                'access_token' => $setting->page_access_token,
            ]);
        }

        $data = $response->json();

        if (!$response->successful()) {
            Log::error('Facebook Page Post Error', ['response' => $data, 'product_id' => $product->id]);
            return [
                'success' => false,
                'message' => $data['error']['message'] ?? 'Failed to post to Facebook',
            ];
        }

        $product->update(['facebook_posted_at' => now()]);

        return [
            'success' => true,
            'message' => 'Posted to Facebook successfully',
            'post_id' => $data['id'] ?? $data['post_id'] ?? null,
        ];
    }

    private function getProductImageUrl(Product $product): ?string
    {
        $product->loadMissing(['image', 'images', 'defaultImages']);
        $img = $product->image ?? $product->defaultImages->first() ?? $product->images->first();
        if (!$img || empty($img->image)) {
            return null;
        }
        $path = ltrim($img->image, '/');
        return url($path);
    }
}
