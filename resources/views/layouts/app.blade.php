<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Каталог ретро-гаджетів')</title>

    {{-- Base styles --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/lux/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    {{-- Additional styles from child views --}}
    @stack('styles')
</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
		<div class="container">
			{{-- Brand --}}
			<a class="navbar-brand fw-semibold" href="{{ route('home') }}">Retro&nbsp;Gadgets</a>

			{{-- Toggle --}}
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse"
					data-bs-target="#mainNavbar" aria-controls="mainNavbar"
					aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>

			{{-- Navigation + Search --}}
			<div class="collapse navbar-collapse" id="mainNavbar">
				<ul class="navbar-nav me-auto mb-2 mb-lg-0">

					{{-- Catalog link --}}
					<li class="nav-item">
						<a class="nav-link {{ request()->routeIs('gadgets.index') ? 'active' : '' }}"
						href="{{ route('gadgets.index') }}">Каталог</a>
					</li>

					{{-- Dropdown: categories --}}
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="{{ route('gadgets.index') }}" id="catDropdown" role="button"
						data-bs-toggle="dropdown" aria-expanded="false">
							Категорії
						</a>
						<ul class="dropdown-menu" aria-labelledby="catDropdown">
							@foreach(\App\Models\Category::all() as $cat)
								<li>
									<a class="dropdown-item"
									href="{{ route('gadgets.index', ['category' => $cat->id]) }}">
										{{ $cat->name }}
									</a>
								</li>
							@endforeach
						</ul>
					</li>

					{{-- Static links --}}
					<li class="nav-item"><a class="nav-link" href="{{ route('about') }}">Про нас</a></li>
					<li class="nav-item"><a class="nav-link" href="{{ route('contacts') }}">Контакти</a></li>
				</ul>

				{{-- Search form --}}
				<form class="d-flex" action="{{ route('gadgets.index') }}" method="GET">
					{{-- Preserve existing filters --}}
					@foreach(request()->except('q') as $key => $val)
						<input type="hidden" name="{{ $key }}" value="{{ $val }}">
					@endforeach

					<input class="form-control me-2" type="search" name="q" placeholder="Пошук"
						value="{{ request('q') }}" aria-label="Search">
					<button class="btn btn-outline-primary" type="submit">Search</button>
				</form>
			</div>
		</div>
	</nav>

    <div class="container mt-4">
        @yield('content')
    </div>

	<footer class="bg-light border-top shadow-lg mt-5">
		<div class="container py-5">
			<div class="row">

				{{-- Branding --}}
				<div class="col-md-4 mb-4">
					<h5 class="fw-bold text-primary">RetroGadgets</h5>
					<p class="text-muted small">
						An online collection of iconic retro devices — from pagers to vintage consoles.
					</p>
				</div>

				{{-- Footer navigation --}}
				<div class="col-md-4 mb-4">
					<h6 class="fw-semibold text-dark">Навігація</h6>
					<ul class="list-unstyled text-muted small">
						<li><a href="{{ route('gadgets.index') }}" class="text-decoration-none text-muted">Каталог</a></li>
						<li><a href="{{ route('about') }}" class="text-decoration-none text-muted">Про нас</a></li>
						<li><a href="{{ route('contacts') }}" class="text-decoration-none text-muted">Контакти</a></li>
						<li><a href="{{ route('terms') }}" class="text-decoration-none text-muted">Умови використання</a></li>
						<li><a href="{{ route('privacy') }}" class="text-decoration-none text-muted">Конфіденційність</a></li>
					</ul>
				</div>

				{{-- Social or subscribe --}}
				<div class="col-md-4 mb-4">
					<h6 class="fw-semibold text-dark">Залишайся на звʼязку</h6>
					<p class="text-muted small">Follow us:</p>
					<div class="d-flex gap-2">
						<a href="#" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Telegram</a>
						<a href="#" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Instagram</a>
					</div>
				</div>
			</div>

			<hr>
			<div class="d-flex justify-content-between align-items-center text-muted small pt-2">
				<div>&copy; {{ date('Y') }} RetroGadgets. All rights reserved.</div>
				<div>Dovgopol Iliya</div>
			</div>
		</div>
	</footer>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Scripts from child views --}}
    @stack('scripts')
</body>
</html>
