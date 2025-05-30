<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController; 
use App\Http\Controllers\MarkerController;
use App\Http\Controllers\PostController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/weather', [WeatherController::class, 'getWeather']); 
