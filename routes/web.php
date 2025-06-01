<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

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

Route::get('/me', function (Request $request) {return response()->json($request->user());});
Route::get('/blog', [PageController::class, 'blogIndex'])->name('blog.index');
Route::get('/blog/create', [PageController::class, 'blogCreate'])->name('blog.create')->middleware('auth');
Route::get('/blog/posts/{post}', [PageController::class, 'blogShow'])->name('blog.posts.show');
Route::get('/blog/posts/{post}/edit', [PageController::class, 'blogEdit'])->name('blog.posts.edit')->middleware('auth');

Route::put('/blog/posts/', [PostController::class, 'blogPost'])->name('blog.posts')->middleware('auth');
Route::post('/blog/posts/{post}', [PostController::class, 'blogEdit'])->name('blog.posts.update')->middleware('auth');
Route::delete('/blog/posts/{post}', [PostController::class, 'destroy'])->name('blog.posts.destroy')->middleware('auth');
Route::post('/blog/posts/{post}/comments', [CommentController::class, 'store'])->name('blog.posts.comments.store')->middleware('auth');
Route::delete('/blog/posts/{post}/comments/{comment}', [CommentController::class, 'destroy'])->middleware('auth'); // For admin deletion

Route::get('/products', [ProductController::class, 'productIndex'])->name('products.index');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/initiate-payment', [CheckoutController::class, 'initiatePayment'])->name('checkout.initiatePayment');
Route::post('/checkout/process-payment', [CheckoutController::class, 'processPayment'])->name('checkout.processPayment');

Route::get('/payment/success', [CheckoutController::class, 'success'])->name('payment.success');
Route::get('/payment/failure', [CheckoutController::class, 'failure'])->name('payment.failure');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
