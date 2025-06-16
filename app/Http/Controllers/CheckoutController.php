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
     * Processes the mock payment status (success/cancel).
     */
    public function processPayment(Request $request)
    {
        Stripe::setApiKey('sk_test_your_test_secret_key_here'); // Use your Stripe test secret key

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => ['name' => 'Test Product'],
                    'unit_amount' => 500, // €5.00
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
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
