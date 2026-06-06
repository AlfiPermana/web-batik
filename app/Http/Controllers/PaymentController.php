<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Show payment page for order
     */
    public function show(Order $order)
    {
        // Ensure user owns this order
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // If already paid, redirect to success page
        if ($order->payment_status === 'paid') {
            return redirect()->route('payment.success', $order->id)
                ->with('success', 'Pesanan sudah dibayar');
        }

        return view('checkout.payment', ['order' => $order]);
    }

    /**
     * Payment success handler
     */
    public function success(Order $order)
    {
        // Ensure user owns this order
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('checkout.success', ['order' => $order]);
    }

    /**
     * Payment failed handler
     */
    public function failed(Order $order)
    {
        // Ensure user owns this order
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('checkout.failed', ['order' => $order]);
    }
}
