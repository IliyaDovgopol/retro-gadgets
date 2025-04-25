@extends('layouts.app')

@section('title', 'Контакти')

@section('content')
<div class="container my-5">
    <h1 class="mb-4">Зв'яжіться з нами</h1>

    <p class="mb-4">Маєте питання, пропозиції чи ідеї щодо ретро‑гаджетів? Напишіть нам — ми з радістю відповімо!</p>

    <div class="row g-4">
        <div class="col-md-6">
            <h5>Контактна інформація</h5>
            <ul class="list-unstyled">
                <li><strong>Email:</strong> <a href="mailto:support@retro-gadgets.com">support@retro-gadgets.com</a></li>
                <li><strong>Телефон:</strong> +380 (67) 998-53-96</li>
            </ul>
        </div>

        <div class="col-md-6">
            <h5>Ми в соцмережах</h5>
            <ul class="list-unstyled">
                <li><a href="#" class="text-decoration-none">Facebook</a></li>
                <li><a href="#" class="text-decoration-none">Instagram</a></li>
                <li><a href="#" class="text-decoration-none">Twitter</a></li>
            </ul>
        </div>
    </div>
</div>
@endsection
