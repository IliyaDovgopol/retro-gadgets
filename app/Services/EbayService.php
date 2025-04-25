<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EbayService
{
    private string $clientId;
    private string $clientSecret;

    public function __construct()
    {
        $this->clientId = env('EBAY_CLIENT_ID');
        $this->clientSecret = env('EBAY_CLIENT_SECRET');
    }

    /**
     * Returns cached eBay access token or updates it if expired
     */
    private function getAccessToken(): string
    {
        if (Cache::has('ebay_access_token')) {
            return Cache::get('ebay_access_token');
        }

        return $this->updateAccessToken();
    }

    /**
     * Requests a new eBay access token and stores it in cache
     */
    private function updateAccessToken(): string
    {
        Log::info("🔄 Updating eBay access token...");

        Cache::forget('ebay_access_token');

        $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
            ->asForm()
            ->post('https://api.ebay.com/identity/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
                'scope' => 'https://api.ebay.com/oauth/api_scope'
            ]);

        if ($response->failed()) {
            Log::error("❌ Failed to get eBay token: " . $response->body());
            return '';
        }

        $data = $response->json();
        $token = $data['access_token'] ?? '';

        if ($token) {
            $expiresIn = $data['expires_in'] ?? 7200;
            // Store token slightly shorter than actual TTL
            Cache::put('ebay_access_token', $token, now()->addSeconds($expiresIn - 60));
            Log::info("✅ eBay token updated successfully!");
        }

        return $token;
    }

    /**
     * Searches for items on eBay and maps result to simplified structure
     */
    public function getFilteredItems(string $query, int $limit = 5): \Illuminate\Support\Collection
    {
        $items = $this->makeRequest('/buy/browse/v1/item_summary/search', [
            'q' => $query,
            'limit' => $limit
        ]);

        return collect($items['itemSummaries'] ?? [])->map(function ($item) {
            return [
                'id' => $item['itemId'] ?? 'N/A',
                'title' => $item['title'] ?? 'Untitled',
                'price' => $item['price']['value'] ?? 'N/A',
                'currency' => $item['price']['currency'] ?? 'N/A',
                'image' => $item['image']['imageUrl'] ?? '',
                'link' => $item['itemWebUrl'] ?? '#',
                'seller' => $item['seller']['username'] ?? 'Unknown',
                'seller_feedback' => $item['seller']['feedbackPercentage'] ?? 'N/A',
                'condition' => $item['condition'] ?? 'Unknown',
                'shipping' => $item['shippingOptions'][0]['shippingCost']['value'] ?? 'N/A',
            ];
        });
    }

    /**
     * Performs an authenticated GET request to the eBay API
     */
    public function makeRequest(string $endpoint, array $queryParams = [])
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return ['error' => 'Failed to retrieve token'];
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer $token",
            'Content-Type' => 'application/json'
        ])->get("https://api.ebay.com$endpoint", $queryParams);

        if ($response->failed()) {
            Log::error("❌ eBay API request failed: " . $response->body());

            if ($response->status() === 401) {
                Log::warning("🔄 Token expired. Refreshing...");
                $this->updateAccessToken();
                return $this->makeRequest($endpoint, $queryParams);
            }

            return ['error' => 'eBay API request error'];
        }

        return $response->json();
    }
}
