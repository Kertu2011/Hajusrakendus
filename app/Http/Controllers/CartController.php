<?php
// app/Http/Controllers/CartController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    /**
     * Display the shopping cart.
     */
    public function index()
    {
        // Cart data is managed on the frontend.
        // This controller just renders the cart page.
        return Inertia::render('Cart/Index');
    }
}
