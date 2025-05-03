<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\WeatherController; 

Route::get('/', function () {
    return Inertia::render('Index');
})->name('home');

Route::get('/weather', [WeatherController::class, 'show'])->name('weather');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';