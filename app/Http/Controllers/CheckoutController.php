<?php
// app/Http/Controllers/CheckoutController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator; // Import Validator

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
        $request->validate([
            'status' => 'required|string|in:success,cancel',
            // 'cart_items' and 'user_info' are passed through but not strictly re-validated here
            // as they were validated in initiatePayment or come from trusted frontend state
        ]);

        if ($request->input('status') === 'success') {
            // No DB interaction for order needed for this mock.
            // Cart will be cleared on the frontend (Success.vue).
            return Redirect::route('payment.success')->with('success', 'Makse õnnestus! Teie tellimus on esitatud.');
        } else {
            // Products remain in cart (frontend state).
            return Redirect::route('payment.failure')->with('error', 'Makse tühistati või ebaõnnestus. Teie tooted on endiselt ostukorvis.');
        }
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
