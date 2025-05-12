<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GadgetController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PriceScraperController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/gadgets', [GadgetController::class, 'index'])->name('gadgets.index');
Route::get('/gadgets/{gadget:slug}', [GadgetController::class, 'show'])->name('gadgets.show');
Route::get('/scrape-prices', [PriceScraperController::class, 'fetch'])->name('scrape.prices');
Route::view('/about', 'static.about')->name('about');
Route::view('/contacts', 'static.contacts')->name('contacts');
Route::view('/terms', 'static.terms')->name('terms');
Route::view('/privacy', 'static.privacy')->name('privacy');

Route::get('/debug-sentry', function () {
    throw new \Exception('Test Sentry exception');
});

Route::get('/internal/cron-run', function (\Illuminate\Http\Request $request) {
    if ($request->query('key') !== env('CRON_KEY')) {
        abort(403);
    }

    Log::info('🚀 cron-run route triggered');
    echo '✅ STARTED<br>';

    Artisan::call('schedule:run');

    echo '✅ FINISHED<br>';
    return 'OK';
});


Route::get('/internal/test-reveal', function (\Illuminate\Http\Request $request) {
    if ($request->query('key') !== env('CRON_KEY')) {
        abort(403);
    }

    $exitCode = Artisan::call('gadgets:reveal-one');
    return Artisan::output() ?: "Команда выполнена, exit code $exitCode";
});
