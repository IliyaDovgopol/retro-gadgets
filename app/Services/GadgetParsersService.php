<?php

namespace App\Services;

use App\Models\Gadget;
use App\Models\Price;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Log;

class GadgetParsersService
{
    /**
     * Parses product data from Prom.ua and stores it in the database
     */
    public function updateFromProm(Gadget $gadget): int
    {
        $url = 'https://prom.ua/search?search_term=' . urlencode($gadget->name);

        try {
            $response = Http::timeout(20)->get($url);
            $html = $response->body();
        } catch (\Exception $e) {
            Log::warning("❗ Failed to fetch Prom page: " . $e->getMessage());
            return 0;
        }

        file_put_contents(storage_path('app/prom_test.html'), $html);

        $crawler = new Crawler($html);
        $updated = 0;

        $items = $crawler->filter('div[data-qaid="product_block"]');

        if ($items->count() === 0) {
            Log::warning("⚠️ No products found on Prom for '{$gadget->name}'");
            return 0;
        }

        $items->slice(0, 10)->each(function ($node) use (&$updated, $gadget) {
            $titleNode = $node->filter('span[data-qaid="product_name"]');
            $linkNode = $node->filter('a[data-qaid="product_link"]');
            $priceNode = $node->filter('div[data-qaid="product_price"] span');
            $imageNode = $node->filter('picture img');

            $title = $titleNode->count() ? $titleNode->text() : 'Untitled';
            $relativeLink = $linkNode->count() ? $linkNode->attr('href') : '#';
            $priceRaw = $priceNode->count() ? $priceNode->text() : '0';
            $price = (float) filter_var($priceRaw, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $image = $imageNode->count() ? $imageNode->attr('src') : null;

            $updated += $this->savePrice(
                $gadget->id,
                'Prom',
                $title,
                $price,
                'https://prom.ua' . $relativeLink,
                $image
            );
        });

        return $updated;
    }

    /**
     * Parses product data from OLX.ua and stores it in the database
     */
    public function updateFromOlx(Gadget $gadget): int
    {
        $url = 'https://www.olx.ua/list/q-' . urlencode($gadget->name) . '/';

        try {
            $response = Http::timeout(20)->get($url);
            $html = $response->body();
        } catch (\Exception $e) {
            Log::warning("❗ Failed to fetch OLX page: " . $e->getMessage());
            return 0;
        }

        file_put_contents(storage_path('app/olx_test.html'), $html);

        $crawler = new Crawler($html);
        $updated = 0;

        $items = $crawler->filter('div[data-cy="l-card"]');

        if ($items->count() === 0) {
            Log::warning("⚠️ No products found on OLX for '{$gadget->name}'");
            return 0;
        }

        $items->slice(0, 10)->each(function ($node) use (&$updated, $gadget) {
            $linkNode = $node->filter('a')->first();
            $link = $linkNode->count() ? $linkNode->attr('href') : '#';
            if ($link && strpos($link, 'http') !== 0) {
                $link = 'https://www.olx.ua' . $link;
            }

            $priceNode = $node->filter('p[data-testid="ad-price"]');
            $priceRaw = $priceNode->count() ? $priceNode->text() : '0';
            $price = (float) filter_var($priceRaw, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

            $imageNode = $node->filter('img');
            $image = $imageNode->count() ? $imageNode->attr('src') : null;

            // Skip placeholder images
            if (!$image || str_contains($image, 'no_thumbnail')) {
                return;
            }

            $updated += $this->savePrice(
                $gadget->id,
                'OLX',
                'OLX Product',
                $price,
                $link,
                $image
            );
        });

        return $updated;
    }

    /**
     * Saves or updates a product price record in the database
     */
    public function savePrice(
        int $gadgetId,
        string $source,
        string $productName,
        float $price,
        string $link,
        ?string $imageUrl = null
    ): int {
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
                'link_hash' => $linkHash,
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
     * Updates data from all available sources
     */
    public function updateAllSources(Gadget $gadget): int
    {
        $updated = 0;
        $updated += $this->updateFromProm($gadget);
        $updated += $this->updateFromOlx($gadget);
        return $updated;
    }
}
