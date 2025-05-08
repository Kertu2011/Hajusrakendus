<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\WeatherController; 

Route::get('/', function () {
    return Inertia::render('Index');
})->name('home');

Route::get('/weather', [WeatherController::class, 'show'])->name('weather');

use App\Http\Controllers\MarkerController;
Route::put('/map/{marker}', [MarkerController::class, 'update'])->name('map.update');
Route::delete('/map/{marker}', [MarkerController::class, 'destroy'])->name('map.destroy');
Route::get('/map/{marker}/edit', [MarkerController::class, 'edit'])->name('map.edit');
Route::get('/map/create', [MarkerController::class, 'create'])->name('map.create');
Route::post('/map', [MarkerController::class, 'store'])->name('map.store');
Route::get('/map', [MarkerController::class, 'index'])->name('map.index');


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';