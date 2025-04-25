<?php

namespace App\Services;

use App\Models\Gadget;
use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class GadgetService
{
    /**
     * Returns catalog page data with filters, sorting and cached categories.
     */
    public function getCatalogPage(array $filters = []): array
    {
        $categories = Cache::remember('gadget:categories', now()->addHours(6), fn() => Category::all());

        $minYear = Cache::remember('gadgets:min_year', now()->addDay(), fn() => Gadget::min('year') ?? 1970);
        $maxYear = Cache::remember('gadgets:max_year', now()->addDay(), fn() => Gadget::max('year') ?? now()->year);

        $query = Gadget::query()
            ->where('is_visible', true)
            ->with('category');

        // Filter by name, category and year
        if (!empty($filters['q'])) {
            $query->where('name', 'like', '%' . $filters['q'] . '%');
        }

        if (!empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        if (!empty($filters['year_from']) || !empty($filters['year_to'])) {
            $from = $filters['year_from'] ?? $minYear;
            $to = $filters['year_to'] ?? $maxYear;
            $query->whereBetween('year', [$from, $to]);
        }

        // Sort options
        switch ($filters['sort'] ?? 'created_desc') {
            case 'year_asc':   $query->orderBy('year');           break;
            case 'year_desc':  $query->orderByDesc('year');       break;
            case 'name_asc':   $query->orderBy('name');           break;
            case 'name_desc':  $query->orderByDesc('name');       break;
            default:           $query->orderByDesc('created_at'); break;
        }

        $gadgets = $query->paginate(12)->withQueryString();

        return compact('gadgets', 'categories', 'minYear', 'maxYear');
    }

    /**
     * Returns gadget with grouped price data (cached by slug).
     */
    public function getGadgetWithGroupedPrices(string $slug): array
    {
        return Cache::remember("gadget:{$slug}", now()->addMinutes(30), function () use ($slug) {
            $gadget = Gadget::where('slug', $slug)->firstOrFail();
            return $this->prepareGadgetData($gadget);
        });
    }

    /**
     * Returns gadget with grouped prices using model binding (cached).
     */
    public function getGadgetWithGroupedPricesByModel(Gadget $gadget): array
    {
        return Cache::remember("gadget:{$gadget->slug}", now()->addMinutes(30), function () use ($gadget) {
            return $this->prepareGadgetData($gadget);
        });
    }

    /**
     * Returns random visible gadgets with cached price data.
     */
    public function getRandomVisibleGadgets(int $limit = 8)
    {
        return Cache::remember("gadgets:random:{$limit}", now()->addMinutes(15), function () use ($limit) {
            return Gadget::with(['prices' => function ($query) {
                $query->whereNotNull('image_url')->orderBy('price');
            }])
                ->where('is_visible', true)
                ->inRandomOrder()
                ->take($limit)
                ->get();
        });
    }

    /**
     * Returns random gadgets for homepage preview with selected image.
     */
    public function getRandomGadgetsForHomepage(int $limit = 8): array
    {
        $gadgets = Gadget::where('is_visible', true)
            ->inRandomOrder()
            ->take($limit)
            ->get();

        return $gadgets->map(function ($gadget) {
            $data = $this->prepareGadgetData($gadget);
            return [
                'gadget' => $gadget,
                'image' => $data['ebayImages']->first() ?? $gadget->image_url ?? '/images/default-placeholder.png',
            ];
        })->toArray();
    }

    /**
     * Extracts and groups price data by source for a gadget.
     */
    private function prepareGadgetData(Gadget $gadget): array
    {
        $prices = $gadget->prices()
            ->whereNotNull('image_url')
            ->get()
            ->groupBy('source');

        return [
            'gadget' => $gadget,
            'ebayPrices' => $prices->get('eBay', collect())->take(5),
            'ebayImages' => $prices->get('eBay', collect())->pluck('image_url')->filter()->take(4),
            'promItems' => $prices->get('Prom', collect())->take(5),
            'olxItems' => $prices->get('OLX', collect())->take(5),
        ];
    }
}
