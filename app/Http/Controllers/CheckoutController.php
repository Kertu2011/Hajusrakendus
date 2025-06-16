<?php
// app/Http/Controllers/CheckoutController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator; // Import Validator
use Stripe\Stripe;
use Stripe\Checkout\Session;

class CheckoutController extends Controller
{
    /**
     * Display the checkout form.
     */
    public function index()
    {
        // Cart data is managed on the frontend.
        // This controller just renders the checkout page.
        return Inertia::render('Checkout/Index');
    }

    /**
     * Receives checkout form data and cart, then shows mock payment confirmation.
     */
    public function initiatePayment(Request $request)
    {
        // Basic validation for user data
        // The cart_items validation is more for ensuring the structure is somewhat okay
        $validator = Validator::make($request->all(), [
            'user_info.firstName' => 'required|string|max:255',
            'user_info.lastName' => 'required|string|max:255',
            'user_info.email' => 'required|email|max:255',
            'user_info.phone' => 'required|string|max:50',
            'cart_items' => 'required|array',
            'cart_items.*.id' => 'required|integer', // Basic check for item structure
            'cart_items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            // Redirect back with errors. Inertia will automatically handle this.
            // Consider sending a flash message for a better UX if redirecting to a generic error page or back to form
            return Redirect::route('checkout.index')->withErrors($validator)->withInput();
        }

        // No complex order persistence needed.
        // We'll pass the submitted data to the mock payment confirmation page.
        return Inertia::render('Checkout/MockPaymentConfirmation', [
            'checkoutData' => $request->all(), // Contains 'user_info' and 'cart_items'
        ]);
    }

    /**
     * Processes the real Stripe Checkout Session.
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'cart_items' => 'required|array',
            'cart_items.*.name' => 'required|string',
            'cart_items.*.price' => 'required|integer', // price in cents
            'cart_items.*.quantity' => 'required|integer|min:1',
            'user_info.email' => 'required|email'
        ]);

        $cartItems = $request->input('cart_items');

        // Set your actual Stripe test key
        Stripe::setApiKey('sk_test_51RaW5yHGluQtUOBXj6NKAXFqR5xG5yq7I0pnGLLtluF7G2hmT5MFSZpPUmTlRJMKlVXp3qGjCtq9GtOdyMPzfbbI00zqMZSN4O');

        $lineItems = [];
        foreach ($cartItems as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item['name'],
                    ],
                    'unit_amount' => $item['price'], // must be in cents
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

        return redirect($session->url);
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
