<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class CheckoutController extends Controller
{
    /**
     * Display the checkout form.
     */
    public function index()
    {
        return Inertia::render('Checkout/Index');
    }

    /**
     * Receives checkout form data and cart, then redirects to Stripe for payment.
     */
    public function initiatePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_info.firstName' => 'required|string|max:255',
            'user_info.lastName' => 'required|string|max:255',
            'user_info.email' => 'required|email|max:255',
            'user_info.phone' => 'required|string|max:50',
            'cart_items' => 'required|array',
            'cart_items.*.id' => 'required|integer',
            'cart_items.*.name' => 'required|string',
            'cart_items.*.price' => 'required|numeric|min:0',
            'cart_items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return Redirect::route('checkout.index')->withErrors($validator)->withInput();
        }

        // Set your actual Stripe test key
        Stripe::setApiKey('sk_test_51RaW5yHGluQtUOBXj6NKAXFqR5xG5yq7I0pnGLLtluF7G2hmT5MFSZpPUmTlRJMKlVXp3qGjCtq9GtOdyMPzfbbI00zqMZSN4O');

        $lineItems = [];
        foreach ($request->input('cart_items') as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item['name'],
                    ],
                    'unit_amount' => (int)($item['price'] * 100), // Price in cents
                ],
                'quantity' => $item['quantity'],
            ];
        }

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'customer_email' => $request->input('user_info.email'),
            'success_url' => route('payment.success'),
            'cancel_url' => route('payment.failure'),
        ]);

        // Use Inertia::location() to perform a full-page redirect to an external website.
        // This avoids the CORS error by not using an XMLHttpRequest for the redirect.
        return Inertia::location($session->url);
    }

    /**
     * Display the payment success page.
     */
    public function success()
    {
        return Inertia::render('Payment/Success');
    }

    /**
     * Display the payment failure page.
     */
    public function failure()
    {
        return Inertia::render('Payment/Failure');
    }
}
