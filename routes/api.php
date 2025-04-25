<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EbayController;

Route::get('/ebay/items', [EbayController::class, 'getEbayItems']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
