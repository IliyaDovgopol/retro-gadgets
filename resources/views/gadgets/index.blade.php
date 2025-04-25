@extends('layouts.app')

@section('title', 'Каталог ретро‑гаджетів')

@section('content')

<nav aria-label="breadcrumb">
    <ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
        <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <a href="{{ route('home') }}" itemprop="item"><span itemprop="name">Головна</span></a>
            <meta itemprop="position" content="1">
        </li>
        <li class="breadcrumb-item active" aria-current="page" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <span itemprop="name">Каталог</span>
            <meta itemprop="position" content="2">
        </li>
    </ol>
</nav>

<div class="container-fluid mt-4">
    <div class="row">

        {{-- Filter sidebar --}}
        <aside class="col-12 col-md-3 mb-4">
            <div class="p-3 bg-light rounded shadow-sm position-sticky top-0">

                <form method="GET" id="filterForm">
                    {{-- Category filter --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Категорія</label>
                        <select name="category" class="form-select">
                            <option value="">Усі</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Year range slider --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Рік випуску</label>
                        <div id="yearRange"
                             data-min="{{ \App\Models\Gadget::min('year') ?? 1970 }}"
                             data-max="{{ \App\Models\Gadget::max('year') ?? now()->year }}">
                        </div>
                    </div>

                    {{-- Sorting options --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Сортувати</label>
                        <select name="sort" class="form-select">
                            <option value="created_desc" @selected(request('sort') == 'created_desc')>Нові спершу</option>
                            <option value="year_desc" @selected(request('sort') == 'year_desc')>Рік ↓</option>
                            <option value="year_asc" @selected(request('sort') == 'year_asc')>Рік ↑</option>
                            <option value="name_asc" @selected(request('sort') == 'name_asc')>A→Z</option>
                            <option value="name_desc" @selected(request('sort') == 'name_desc')>Z→A</option>
                        </select>
                    </div>

                    <button class="btn btn-primary w-100" type="submit">Застосувати</button>
                </form>

            </div>
        </aside>

        {{-- Gadget cards --}}
        <main class="col-12 col-md-9">
            <div class="row g-3">
                @php use Illuminate\Support\Str; @endphp
                @foreach($gadgets as $gadget)
                    @php
                        $USD_TO_UAH = 38;
                        $prices = $gadget->prices->map(fn ($p) => tap($p, function ($p) use ($USD_TO_UAH) {
                            $p->converted_price = in_array($p->source, ['eBay','AliExpress'])
                                ? $p->price * $USD_TO_UAH
                                : $p->price;
                        }));
                        $cheapestPrice = $prices->sortBy('converted_price')->first();
                        $ebayImage     = $gadget->prices()->where('source','eBay')->whereNotNull('image_url')->orderBy('price')->first()?->image_url;
                        $fallbackImage = $gadget->prices()->whereNotNull('image_url')->orderBy('price')->first()?->image_url;
                        $imageUrl = $ebayImage ?? $fallbackImage ?? $gadget->image_url ?? '/images/default-placeholder.png';
                    @endphp

                    <div class="col-6 col-lg-4 d-flex">
                        <div class="card custom-card h-100 d-flex flex-column">
                            <div class="image-container">
                                <img src="{{ $imageUrl }}" class="card-img-top img-fluid" alt="{{ $gadget->name }}">
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $gadget->name }} ({{ $gadget->year }})</h5>
                                <p class="card-text d-none d-md-block flex-grow-1">
                                    {{ Str::limit($gadget->description, 100) }}
                                </p>
                                @if($cheapestPrice)
                                    Від:
                                    {{ in_array($cheapestPrice->source, ['eBay','AliExpress'])
                                        ? '$'.number_format($cheapestPrice->price,2)
                                        : number_format($cheapestPrice->price,0).' грн' }}
                                @else
                                    Немає в наявності
                                @endif
                                <a href="{{ route('gadgets.show', $gadget->slug) }}"
                                   class="btn btn-outline-primary btn-sm mt-auto px-3 py-2">Детальніше</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $gadgets->withQueryString()->links() }}
            </div>
        </main>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/nouislider@15.7.0/dist/nouislider.min.css">

<style>
    /* Custom styling for range slider (yearRange) */
    #yearRange .noUi-connect,
    #yearRange .noUi-base {
        height:6px;
    }

    #yearRange .noUi-handle {
        width:14px;
        height:14px;
        top:-4px;
        border-radius:50%;
        border:2px solid #0d6efd;
        background:#fff;
        cursor:pointer;
    }

    #yearRange .noUi-handle::before,
    #yearRange .noUi-handle::after {
        display:none;
    }

    #yearRange .noUi-handle:focus-visible {
        outline:none;
        box-shadow:0 0 0 3px rgba(13,110,253,.25);
    }

    .image-container {
        width: 100%;
        height: 200px;
        overflow: hidden;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-top-left-radius: 0.375rem;
        border-top-right-radius: 0.375rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/nouislider@15.7.0/dist/nouislider.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('filterForm');
    const slider = document.getElementById('yearRange');
    if (!slider || !form) return;

    const minYear = parseInt(slider.dataset.min);
    const maxYear = parseInt(slider.dataset.max);

    // Read year values from URL
    const getQueryParam = (key, fallback) => {
        const url = new URL(window.location.href);
        const val = parseInt(url.searchParams.get(key));
        return isNaN(val) ? fallback : val;
    };

    const yearFrom = getQueryParam('year_from', minYear);
    const yearTo   = getQueryParam('year_to', maxYear);

    // Initialize range slider
    noUiSlider.create(slider, {
        start: [yearFrom, yearTo],
        connect: true,
        step: 1,
        range: { min: minYear, max: maxYear },
        tooltips: true,
        format: {
            to: v => parseInt(v),
            from: v => parseInt(v)
        }
    });

    // On form submit: add year range to query params
    form.addEventListener('submit', e => {
        const sliderVals = slider.noUiSlider.get();
        const url = new URL(window.location.href);
        const params = new URLSearchParams();

        // Add other form fields
        new FormData(form).forEach((value, key) => {
            if (value) params.set(key, value);
        });

        // Add year range manually
        params.set('year_from', Math.floor(sliderVals[0]));
        params.set('year_to', Math.floor(sliderVals[1]));

        window.location.href = `${url.pathname}?${params.toString()}`;
        e.preventDefault();
    });
});
</script>
@endpush
