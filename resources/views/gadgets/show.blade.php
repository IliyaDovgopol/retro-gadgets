@extends('layouts.app')

@section('title', $gadget->name . ' – історія, огляд та де купити')

@section('content')
<article class="container" itemscope itemtype="https://schema.org/Product">
    {{-- SEO microdata --}}
    <meta itemprop="name" content="{{ $gadget->name }}">
    <meta itemprop="brand" content="{{ $gadget->brand ?? 'Невідомий бренд' }}">
    <meta itemprop="releaseDate" content="{{ $gadget->year }}">

    {{-- Page header --}}
    <header class="text-center mb-4">
        <h1 class="fw-bold fs-1">{{ $gadget->name }} – огляд, історія та ціни</h1>
        <p class="text-muted fs-5">Дата випуску: {{ $gadget->year }} | Категорія: {{ $gadget->category->name }}</p>
    </header>

    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
            <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a href="{{ route('home') }}" itemprop="item"><span itemprop="name">Головна</span></a>
                <meta itemprop="position" content="1">
            </li>
            <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a href="{{ route('gadgets.index') }}" itemprop="item"><span itemprop="name">Каталог</span></a>
                <meta itemprop="position" content="2">
            </li>
            <li class="breadcrumb-item active" aria-current="page" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <span itemprop="name">{{ $gadget->name }}</span>
                <meta itemprop="position" content="3">
            </li>
        </ol>
    </nav>

    <div class="row mt-5">
        <div class="col-lg-8 text-body">
            {{-- Intro text (optional) --}}
            @if($gadget->intro)
                <p class="lead fs-4 lh-lg mb-4">{{ $gadget->intro }}</p>
            @endif

            {{-- Image + quick specs --}}
            <div class="row mb-5">
                <div class="col-md-6">
                    <div class="product-image-container">
                        @php
                            $ebayPrice = $gadget->prices()->where('source', 'eBay')->orderBy('price')->first();
                            $imageUrl = $ebayPrice->image_url ?? $gadget->image_url ?? '/images/default-placeholder.png';
                        @endphp
                        <img src="{{ $imageUrl }}" class="img-fluid product-image" alt="{{ $gadget->name }}" itemprop="image">
                    </div>
                </div>

                <div class="col-md-6">
                    <h2 class="fs-3 mb-3">Що таке {{ $gadget->name }}?</h2>
                    <p class="fs-5 lh-lg mb-4" itemprop="description">{{ $gadget->description }}</p>

                    <h3 class="fs-4 mb-3">Основні особливості:</h3>
                    <ul class="fs-5 lh-lg mb-4">
                        <li class="mb-2"><strong>Рік випуску:</strong> {{ $gadget->year }}</li>
                        <li class="mb-2"><strong>Категорія:</strong> {{ $gadget->category->name }}</li>
                        <li class="mb-2"><strong>Історична цінність:</strong> {{ $gadget->legacy }}</li>
                        <li class="mb-2"><strong>Унікальні характеристики:</strong> {{ $gadget->unique_features }}</li>
                    </ul>
                </div>
            </div>

            {{-- History / Competition / Fun facts --}}
            <section class="mb-5">
                <h2 class="fs-3 mb-3">Історія {{ $gadget->name }}</h2>
                <p class="fs-5 lh-lg mb-4">{{ $gadget->history }}</p>

                <h3 class="fs-4 mb-3">Конкуренти на ринку</h3>
                <p class="fs-5 lh-lg mb-4">{{ $gadget->competition }}</p>

                <h3 class="fs-4 mb-3">Цікаві факти</h3>
                <div class="fact-box fs-5 lh-lg mb-4">
                    <ul class="mb-0">
                        @foreach(explode("\n", $gadget->fun_facts) as $fact)
                            @if(trim($fact))
                                <li>{{ $fact }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>

                <h3 class="fs-4 mb-3">Наслідки випуску та вплив</h3>
                <p class="fs-5 lh-lg">{{ $gadget->legacy }}</p>
            </section>

            {{-- Price chart (if available) --}}
            <section class="mb-5">
                <h2 class="fs-3 mb-3">Історія цін</h2>
                @if($gadget->price_history)
                    <div id="price-chart"></div>
                    <script>
                        const priceData = {!! json_encode($gadget->price_history) !!};
                    </script>
                @else
                    <p class="fs-6 lh-lg">Історія цін відсутня.</p>
                @endif
            </section>
        </div>

        {{-- Right column with purchase blocks --}}
        <aside class="col-lg-4">
            <section>
                <h2 class="fs-4 mb-4">Де купити {{ $gadget->name }}?</h2>

                {{-- eBay section --}}
                <div class="mb-4">
                    <img src="{{ $ebayImages[1] ?? '/images/default-placeholder.png' }}" alt="eBay товар" class="img-fluid rounded mb-2">
                    <h5 class="fs-6 fw-bold">eBay (USD)</h5>
                    <ul class="list-unstyled small">
                        @foreach($ebayPrices as $price)
                            <li class="mb-1">
                                <a href="{{ $price->link }}" target="_blank">
                                    {{ $price->product_name }} — ${{ number_format($price->price, 2) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Prom.ua section --}}
                <div class="mb-4">
                    <img src="{{ $ebayImages[2] ?? '/images/default-placeholder.png' }}" alt="Prom фото" class="img-fluid rounded mb-2">
                    <h5 class="fs-6 fw-bold">Prom.ua (грн)</h5>
                    <ul class="list-unstyled small">
                        @foreach($promItems as $price)
                            <li class="mb-1">
                                <a href="{{ $price->link }}" target="_blank">
                                    {{ $price->product_name }} — {{ number_format($price->price, 2) }} грн
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- OLX section --}}
                <div class="mb-4">
                    <img src="{{ $ebayImages[3] ?? '/images/default-placeholder.png' }}" alt="OLX фото" class="img-fluid rounded mb-2">
                    <h5 class="fs-6 fw-bold">OLX (грн)</h5>
                    <ul class="list-unstyled small">
                        @foreach($olxItems as $price)
                            <li class="mb-1">
                                <a href="{{ $price->link }}" target="_blank">
                                    {{ $price->product_name }} — {{ number_format($price->price, 2) }} грн
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </section>
        </aside>
    </div>

    {{-- Return link --}}
    <div class="text-center mt-5">
        <a href="{{ route('gadgets.index') }}" class="btn btn-secondary fs-6">Повернутися до каталогу</a>
    </div>

    {{-- Last price update info --}}
    @php
        $lastUpdatedPrice = $gadget->prices()->orderBy('updated_at', 'desc')->first();
        $lastUpdateDate = $lastUpdatedPrice ? $lastUpdatedPrice->updated_at->format('d.m.Y') : 'Невідомо';
    @endphp

    <p class="text-muted text-center mt-3 fs-7">Останнє оновлення товарів: {{ $lastUpdateDate }}</p>
</article>

{{-- Inline styling --}}
<style>
    .product-image-container {
        max-width: 100%;
        max-height: 500px;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
        padding: 20px;
    }

    .product-image {
        width: 100%;
        height: auto;
        max-height: 100%;
        object-fit: contain;
    }

    .fact-box {
        background: #f8f9fa;
        padding: 15px;
        border-left: 5px solid #007bff;
        margin: 20px 0;
    }
</style>
@endsection
