@extends('layouts.app')

@section('title', 'Ретро‑гаджети — колекція техніки минулого')

@section('content')
<div class="text-center py-5">
    <h1 class="display-4 fw-bold">Ретро‑гаджети</h1>
    <p class="lead text-muted">Колекція легендарної техніки з 70‑х, 80‑х, 90‑х і початку 2000‑х</p>
    <a href="{{ route('gadgets.index') }}" class="btn btn-primary btn-lg mt-3">
        Перейти в каталог
    </a>
</div>

@if(count($gadgets))
<div class="container mb-5">
    <h2 class="h4 mb-4 text-center">Випадкові гаджети</h2>
    <div class="row g-4">
		@foreach($gadgets as $item)
		@php
			$gadget = $item['gadget'];
			$imageUrl = $item['image'];
		@endphp
		<div class="col-6 col-md-3">
			<a href="{{ route('gadgets.show', $gadget->slug) }}" class="text-decoration-none text-dark">
				<div class="card h-100 border-0 shadow-sm">
					<img src="{{ $imageUrl }}"
						class="card-img-top p-3" style="height: 160px; object-fit: contain;">
					<div class="card-body text-center">
						<h6 class="card-title mb-1">{{ $gadget->name }}</h6>
						<small class="text-muted">{{ $gadget->year }}</small>
					</div>
				</div>
			</a>
		</div>
	@endforeach
    </div>
</div>
@endif

<div class="bg-light py-5 border-top">
    <div class="container">
        <div class="row text-center">
            <div class="col-md">
                <h5>⚙️ Автоматичне оновлення цін</h5>
                <p class="text-muted">Ціни з eBay, Prom, OLX — щодня</p>
            </div>
            <div class="col-md">
                <h5>📦 200+ пристроїв</h5>
                <p class="text-muted">Кожна сторінка — як міні-стаття</p>
            </div>
            <div class="col-md">
                <h5>📈 SEO‑структура</h5>
                <p class="text-muted">Оптимізовано для Google та каталогів</p>
            </div>
        </div>
    </div>
</div>
@endsection
