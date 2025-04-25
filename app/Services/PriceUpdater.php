<?php

namespace App\Services;

use App\Models\Gadget;
use App\Models\Price;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Log;

class PriceUpdater
{
    private $ebayService;
    protected $parsers;

    public function __construct(EbayService $ebayService, GadgetParsersService $parsers)
    {
        $this->ebayService = $ebayService;
        $this->parsers = $parsers;
    }

    /**
     * Main update process: runs all price updaters for each gadget.
     */
    public function updatePrices()
    {
        Log::info("🚀 Starting price update...");

        $gadgets = Gadget::all();
        Log::info("🔍 Gadgets found: " . $gadgets->count());

        $updated = 0;

        foreach ($gadgets as $gadget) {
            Log::info("📌 Processing: {$gadget->name} (ID: {$gadget->id})");

            $updated += $this->updateFromAliExpress($gadget);
            $updated += $this->updateFromEbay($gadget);
            $updated += $this->parsers->updateFromProm($gadget);
            $updated += $this->parsers->updateFromOlx($gadget);
        }

        Log::info("✅ Finished! Total records updated: " . $updated);
        return "Updated {$updated} records";
    }

    /**
     * Simplifies the search query for external APIs.
     */
    private function simplifyQuery(string $name): string
    {
        $name = preg_replace('/\([^)]*\)/', '', $name); // remove parentheses
        $name = preg_replace('/-\s*.+$/', '', $name);    // remove suffixes
        $name = preg_replace('/\d{4}$/', '', $name);     // remove trailing year
        return trim($name);
    }

    /**
     * Fetches and saves prices from eBay for the given gadget.
     */
    public function updateFromEbay(Gadget $gadget)
    {
        $query = $this->simplifyQuery($gadget->name);

        $data = $this->ebayService->makeRequest('/buy/browse/v1/item_summary/search', [
            'q' => $query,
            'limit' => 7
        ]);

        // Fallback if no results found
        if (empty($data['itemSummaries'])) {
            $fallbackQuery = explode(' ', $query)[0] ?? $gadget->name;
            Log::warning("⚠️ No results for '{$query}', trying fallback: '{$fallbackQuery}'");

            $data = $this->ebayService->makeRequest('/buy/browse/v1/item_summary/search', [
                'q' => $fallbackQuery,
                'limit' => 7
            ]);
        }

        if (empty($data['itemSummaries'])) {
            Log::warning("❗ No results on eBay for: {$gadget->name}");
            return 0;
        }

        $updated = 0;

        foreach ($data['itemSummaries'] as $item) {
            $imageUrl = $item['image']['imageUrl'] ??
                        $item['thumbnailImages'][0]['imageUrl'] ??
                        $item['additionalImages'][0]['imageUrl'] ??
                        null;

            Log::info("✅ eBay item found:", [
                'title' => $item['title'] ?? '—',
                'price' => $item['price']['value'] ?? 'n/a',
                'link' => $item['itemWebUrl'] ?? 'n/a',
                'image' => $imageUrl
            ]);

            $updated += $this->savePrice(
                $gadget->id,
                'eBay',
                $item['title'] ?? 'Untitled',
                (float)($item['price']['value'] ?? 0),
                $item['itemWebUrl'] ?? '#',
                $imageUrl
            );
        }

        return $updated;
    }

    /**
     * Fetches simplified eBay price list for preview or manual use.
     */
    public function fetchEbayPrices(string $query)
    {
        Log::info("🛰️ eBay API request for: " . $query);

        $response = $this->ebayService->makeRequest('/buy/browse/v1/item_summary/search', [
            'q' => $query,
            'limit' => 5
        ]);

        if (empty($response['itemSummaries'])) {
            Log::warning("❗ Empty result from eBay for '{$query}'");
            return [];
        }

        return collect($response['itemSummaries'])->map(function ($item) {
            return [
                'title' => $item['title'] ?? 'Untitled',
                'price' => $item['price']['value'] ?? 0.00,
                'currency' => $item['price']['currency'] ?? 'USD',
                'link' => $item['itemWebUrl'] ?? '#'
            ];
        })->toArray();
    }

    /**
     * Saves or updates price record in the database.
     */
    private function savePrice(int $gadgetId, string $source, string $productName, float $price, string $link, ?string $imageUrl = null)
    {
        $cleanedLink = strtok(trim($link), '?');
        $linkHash = md5($cleanedLink);

        $existingPrice = Price::where([
            'gadget_id' => $gadgetId,
            'source' => $source,
            'link_hash' => $linkHash,
        ])->first();

        if ($existingPrice) {
            $existingPrice->update([
                'product_name' => $productName,
                'price' => $price,
                'link' => $cleanedLink,
                'image_url' => $imageUrl,
                'updated_at' => now(),
            ]);
        } else {
            Price::create([
                'gadget_id' => $gadgetId,
                'source' => $source,
                'product_name' => $productName,
                'price' => $price,
                'link' => $cleanedLink,
                'link_hash' => $linkHash,
                'image_url' => $imageUrl,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return 1;
    }

    /**
     * Fetches and stores the best matching product from AliExpress.
     */
    public function updateFromAliExpress(Gadget $gadget)
    {
        $data = $this->fetchAliExpressPrices($gadget->name);

        if (!is_array($data) || empty($data)) {
            Log::warning("❗ No AliExpress results for: " . $gadget->name);
            return 0;
        }

        $keywords = array_filter(explode(' ', strtolower($gadget->name)), fn($word) => strlen($word) > 3);
        $filteredProducts = [];

        foreach ($data as $product) {
            if (!isset($product['sale_price'], $product['product_detail_url'], $product['product_title'], $product['product_main_image_url'])) {
                continue;
            }

            $titleLower = strtolower($product['product_title']);
            $matches = 0;

            foreach ($keywords as $keyword) {
                if (stripos($titleLower, $keyword) !== false) {
                    $matches++;
                }
            }

            if ($matches >= 2) {
                $filteredProducts[] = $product;
            }
        }

        if (empty($filteredProducts)) {
            Log::warning("⚠️ No relevant AliExpress items found for: " . $gadget->name);
            return 0;
        }

        usort($filteredProducts, fn($a, $b) => ($a['sale_price'] ?? PHP_INT_MAX) <=> ($b['sale_price'] ?? PHP_INT_MAX));
        $bestProduct = array_shift($filteredProducts);

        if (!isset($bestProduct['sale_price'], $bestProduct['product_detail_url'], $bestProduct['product_title'], $bestProduct['product_main_image_url'])) {
            Log::warning("⚠️ Invalid AliExpress response format.");
            return 0;
        }

        Log::info("✅ Selected AliExpress product for {$gadget->name}: ", $bestProduct);

        return $this->savePrice(
            $gadget->id,
            'AliExpress',
            $bestProduct['product_title'],
            $bestProduct['sale_price'],
            $bestProduct['product_detail_url'],
            $bestProduct['product_main_image_url'] ?? null
        );
    }

    /**
     * Sends a request to the AliExpress API via RapidAPI.
     */
    public function fetchAliExpressPrices(string $query)
    {
        Log::info("📡 Sending AliExpress RapidAPI request for: " . $query);

        $response = Http::withHeaders([
            'X-RapidAPI-Key' => env('ALIEXPRESS_RAPIDAPI_KEY'),
            'X-RapidAPI-Host' => 'free-aliexpress-api.p.rapidapi.com'
        ])->get('https://free-aliexpress-api.p.rapidapi.com/hot_products', [
            'q' => $query,
            'cat_id' => 7,
            'target_currency' => 'USD',
            'target_language' => 'EN',
            'page' => 1
        ]);

        if ($response->failed()) {
            Log::error("❌ AliExpress API error for '{$query}': " . $response->status());
            return [];
        }

        $data = $response->json() ?? [];
        Log::info("📩 AliExpress response for '{$query}':", $data);

        return $data;
    }
}
