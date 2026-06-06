<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class PaymentApiController extends Controller
{
    private function mapTripayPaymentStatus(?string $tripayStatus): string
    {
        return match (strtoupper((string) $tripayStatus)) {
            'PAID' => 'paid',
            'UNPAID' => 'unpaid',
            'FAILED' => 'failed',
            'EXPIRED' => 'expired',
            'REFUND', 'REFUNDED' => 'refunded',
            default => 'pending',
        };
    }

    private function syncOrderWithTripay(Order $order): Order
    {
        if (!in_array($order->payment_status, ['unpaid', 'pending'], true) || empty($order->tripay_reference)) {
            return $order;
        }

        $paymentService = new \App\Services\payment\PaymentService();
        $statusResponse = $paymentService->getPaymentStatus((string) $order->tripay_reference);

        if (!($statusResponse['success'] ?? false) || !is_array($statusResponse['data'] ?? null)) {
            return $order;
        }

        $tripayData = $statusResponse['data'];
        $paymentStatus = $this->mapTripayPaymentStatus($tripayData['status'] ?? null);

        $currentTripay = is_array($order->tripay_response)
            ? $order->tripay_response
            : (is_string($order->tripay_response) ? json_decode($order->tripay_response, true) : []);

        $mergedTripay = array_merge($currentTripay ?: [], $tripayData);

        $updateData = [
            'payment_status' => $paymentStatus,
            'tripay_reference' => $tripayData['reference'] ?? $order->tripay_reference,
            'tripay_response' => $mergedTripay,
        ];

        if ($paymentStatus === 'paid' && empty($order->paid_at)) {
            $paidAtRaw = $tripayData['paid_at'] ?? null;
            if (is_numeric($paidAtRaw)) {
                $updateData['paid_at'] = Carbon::createFromTimestamp((int) $paidAtRaw);
            } elseif (is_string($paidAtRaw) && $paidAtRaw !== '') {
                $updateData['paid_at'] = Carbon::parse($paidAtRaw);
            } else {
                $updateData['paid_at'] = now();
            }
        }

        $order->update($updateData);

        if ($paymentStatus === 'paid' && $order->status === 'pending') {
            $order->updateOrderStatus('processing', 'Pembayaran dikonfirmasi otomatis dari cek status Tripay');
        }

        if ($paymentStatus === 'paid') {
            $this->deductOrderStocksIfNeeded((int) $order->id);
        }

        return $order->fresh();
    }

    private function deductOrderStocksIfNeeded(int $orderId): void
    {
        try {
            DB::transaction(function () use ($orderId) {
                /** @var Order|null $lockedOrder */
                $lockedOrder = Order::query()->whereKey($orderId)->lockForUpdate()->first();
                if (!$lockedOrder || $lockedOrder->payment_status !== 'paid' || !empty($lockedOrder->stock_deducted_at)) {
                    return;
                }

                $items = $lockedOrder->items()->select(['product_size_id', 'quantity'])->get();
                if ($items->isEmpty()) {
                    $lockedOrder->update(['stock_deducted_at' => now()]);
                    return;
                }

                $qtyBySize = $items->groupBy('product_size_id')->map(function ($rows) {
                    return (int) $rows->sum('quantity');
                });

                foreach ($qtyBySize as $sizeId => $qty) {
                    $size = ProductSize::query()->whereKey((int) $sizeId)->lockForUpdate()->first();
                    if (!$size) {
                        throw new \RuntimeException("Product size tidak ditemukan: {$sizeId}");
                    }

                    $currentStock = (int) ($size->stock ?? 0);
                    if ($currentStock < (int) $qty) {
                        throw new \RuntimeException("Stok tidak cukup untuk size_id={$sizeId}. stock={$currentStock}, qty={$qty}");
                    }

                    $size->decrement('stock', (int) $qty);
                }

                $lockedOrder->update(['stock_deducted_at' => now()]);
            });
        } catch (\Throwable $e) {
            Log::error('Failed to deduct stock from Tripay status sync', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Generate Tripay checkout URL
     */
    public function generateTripayCheckout(Request $request)
    {
        try {
            $orderId = $request->query('order_id');
            $amount = $request->query('amount');

            if (!$orderId || !$amount) {
                return response()->json(['error' => 'Missing order_id or amount'], 400);
            }

            $order = Order::find($orderId);
            if (!$order) {
                return response()->json(['error' => 'Order not found'], 404);
            }

            // Verify user owns this order
            if ($order->user_id !== Auth::id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Call Tripay API to create transaction
            $merchantCode = config('services.tripay.merchant_code');
            $apiKey = config('services.tripay.api_key');
            $privateKey = config('services.tripay.private_key');

            $payload = [
                'method' => 'all', // Allow all methods
                'merchant_ref' => $order->order_number,
                'amount' => (int) $amount,
                'customer_name' => $order->shipping_address['full_name'] ?? $order->shipping_address['name'] ?? Auth::user()->name,
                'customer_email' => Auth::user()->email,
                'customer_phone' => $order->shipping_address['phone_number'] ?? $order->shipping_address['phone'] ?? Auth::user()->phone,
                'order_items' => $order->items->map(fn($item) => [
                    'name' => $item->product->title,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ])->toArray(),
                'return_url' => route('payment.checkout', $order->id),
                'callback_url' => route('tripay.webhook'),
                'signature' => hash_hmac('sha256', $merchantCode . $order->order_number . $amount, $privateKey),
            ];

            // Make request to Tripay API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post(config('services.tripay.payment_url'), $payload);

            if (!$response->successful()) {
                Log::error('Tripay API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json(['error' => 'Failed to create payment'], 500);
            }

            $data = $response->json();

            // Check if response contains success indicator
            if ($data['success'] === false) {
                Log::error('Tripay Response Error', $data);
                return response()->json(['error' => $data['message'] ?? 'Payment creation failed'], 400);
            }

            // Store Tripay reference + full response in order
            if (isset($data['data']['reference'])) {
                $order->update([
                    'tripay_reference' => $data['data']['reference'],
                    'tripay_response' => $data['data'] ?? null,
                ]);
            }

            return response()->json([
                'success' => true,
                'checkout_url' => $data['data']['checkout_url'] ?? $data['checkout_url'] ?? null,
                'reference' => $data['data']['reference'] ?? null,
                'expired_time' => $data['data']['expired_time'] ?? null,
                'expired_at' => $data['data']['expired_at'] ?? null,
                'payment_details' => $data['data'] ?? null,
            ]);

        } catch (\Exception $e) {
            Log::error('Payment Generation Error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(Request $request, Order $order)
    {
        try {
            // Verify user owns this order
            if ($order->user_id !== Auth::id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $order = $this->syncOrderWithTripay($order);

            $tripay = is_array($order->tripay_response)
                ? $order->tripay_response
                : (is_string($order->tripay_response) ? json_decode($order->tripay_response, true) : null);

            $expiresAt = null;
            try {
                if (is_array($tripay) && !empty($tripay['expired_time']) && is_numeric($tripay['expired_time'])) {
                    $expiresAt = Carbon::createFromTimestamp((int) $tripay['expired_time']);
                } elseif (is_array($tripay) && !empty($tripay['expired_at']) && is_string($tripay['expired_at'])) {
                    $expiresAt = Carbon::parse($tripay['expired_at']);
                }
            } catch (\Throwable $e) {
                $expiresAt = null;
            }

            if (in_array($order->payment_status, ['unpaid', 'pending'], true) && $expiresAt && $expiresAt->isPast()) {
                $order->update(['payment_status' => 'expired']);
                $order->refresh();
            }

            $tripayData = is_array($tripay) ? $tripay : null;

            $amount = (is_array($tripayData) && is_numeric($tripayData['amount'] ?? null))
                ? (int) $tripayData['amount']
                : (int) $order->total;

            // Admin fee (Tripay): prefer fee_customer; fallback to total_fee/fee_merchant; last resort diff
            $fee = 0;
            if (is_array($tripayData)) {
                $feeCustomer = $tripayData['fee_customer'] ?? null;
                $totalFee = $tripayData['total_fee'] ?? null;
                $feeMerchant = $tripayData['fee_merchant'] ?? null;

                if (is_numeric($feeCustomer) && (int) $feeCustomer > 0) {
                    $fee = (int) $feeCustomer;
                } elseif (is_numeric($totalFee) && (int) $totalFee > 0) {
                    $fee = (int) $totalFee;
                } elseif (is_numeric($feeMerchant) && (int) $feeMerchant > 0) {
                    $fee = (int) $feeMerchant;
                } else {
                    $baseTotal = (int) $order->subtotal + (int) $order->shipping_cost - (int) ($order->discount ?? 0);
                    $fee = max(0, $amount - $baseTotal);
                }
            }

            $canContinue = in_array($order->payment_status, ['unpaid', 'pending'], true)
                && (!$expiresAt || $expiresAt->isFuture());

            return response()->json([
                'payment_status' => $order->payment_status,
                'status' => $order->status,
                'total' => (int) $order->total,
                'amount' => $amount,
                'fee' => $fee,
                'reference' => $tripayData['reference'] ?? $order->tripay_reference,
                'checkout_url' => $tripayData['checkout_url'] ?? null,
                'payment_name' => $tripayData['payment_name'] ?? ($tripayData['method_name'] ?? null),
                'pay_code' => $tripayData['pay_code'] ?? null,
                'qr_url' => $tripayData['qr_url'] ?? null,
                'expired_time' => $tripayData['expired_time'] ?? null,
                'expired_at' => $expiresAt?->format('d-m-Y H:i:s'),
                'tripay' => $tripayData,
                'can_continue_payment' => $canContinue,
            ]);

        } catch (\Exception $e) {
            Log::error('Payment Status Error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * (My Orders) Create / recreate Tripay transaction for an Order using selected method.
     */
    public function processOrderPayment(Request $request, Order $order)
    {
        try {
            if ($order->user_id !== Auth::id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $method = (string) $request->input('payment_method', '');
            if ($method === '') {
                return response()->json(['error' => 'Missing payment_method'], 400);
            }

            // Prevent processing if already final
            if (in_array($order->payment_status, ['paid', 'confirmed', 'failed', 'expired', 'refunded'], true)) {
                return response()->json(['error' => 'Payment status is not eligible for processing'], 400);
            }

            $tripay = is_array($order->tripay_response)
                ? $order->tripay_response
                : (is_string($order->tripay_response) ? json_decode($order->tripay_response, true) : null);

            // If there is an existing deadline and it is already past, mark expired
            $expiresAt = null;
            try {
                if (is_array($tripay) && !empty($tripay['expired_time']) && is_numeric($tripay['expired_time'])) {
                    $expiresAt = Carbon::createFromTimestamp((int) $tripay['expired_time']);
                } elseif (is_array($tripay) && !empty($tripay['expired_at']) && is_string($tripay['expired_at'])) {
                    $expiresAt = Carbon::parse($tripay['expired_at']);
                }
            } catch (\Throwable $e) {
                $expiresAt = null;
            }

            if (in_array($order->payment_status, ['unpaid', 'pending'], true) && $expiresAt && $expiresAt->isPast()) {
                $order->update(['payment_status' => 'expired']);
                return response()->json(['error' => 'Payment expired'], 400);
            }

            $order->loadMissing('items.product');

            $items = $order->items->map(fn($item) => [
                'name' => $item->product?->title ?? 'Produk',
                'quantity' => (int) $item->quantity,
                'price' => (int) $item->price,
            ])->toArray();

            if ((int) $order->shipping_cost > 0) {
                $items[] = [
                    'name' => 'Shipping Cost',
                    'quantity' => 1,
                    'price' => (int) $order->shipping_cost,
                ];
            }

            $addr = $order->shipping_address ?? [];
            $customerName = $addr['full_name'] ?? $addr['name'] ?? (Auth::user()->name ?? 'Customer');
            $customerPhone = $addr['phone_number'] ?? $addr['phone'] ?? (Auth::user()->phone ?? '');

            $paymentService = new \App\Services\payment\PaymentService();
            $resp = $paymentService->createPayment([
                'merchant_ref' => $order->order_number,
                'amount' => (int) $order->total,
                'method' => $method,
                'customer_name' => $customerName,
                'customer_email' => Auth::user()->email,
                'customer_phone' => $customerPhone,
                'items' => $items,
                'return_url' => route('payment.checkout', $order->id),
            ]);

            if (!($resp['success'] ?? false) || empty($resp['data']['checkout_url'])) {
                Log::error('Tripay create payment failed', [
                    'order_id' => $order->id,
                    'response' => $resp,
                ]);
                return response()->json(['error' => $resp['message'] ?? 'Failed to create payment'], 500);
            }

            $data = $resp['data'];

            // Fee calculation (same approach as checkout)
            $amount = (int) ($data['amount'] ?? $order->total);
            $feeCustomer = $data['fee_customer'] ?? null;
            $totalFee = $data['total_fee'] ?? null;
            $feeMerchant = $data['fee_merchant'] ?? null;

            if (is_numeric($feeCustomer) && (int) $feeCustomer > 0) {
                $fee = (int) $feeCustomer;
            } elseif (is_numeric($totalFee) && (int) $totalFee > 0) {
                $fee = (int) $totalFee;
            } elseif (is_numeric($feeMerchant) && (int) $feeMerchant > 0) {
                $fee = (int) $feeMerchant;
            } else {
                $baseTotal = (int) $order->subtotal + (int) $order->shipping_cost - (int) ($order->discount ?? 0);
                $fee = max(0, $amount - $baseTotal);
            }

            // Persist
            $order->update([
                'tripay_reference' => $data['reference'] ?? $order->tripay_reference,
                'tripay_response' => $data,
                'payment_status' => in_array($order->payment_status, ['unpaid', 'pending'], true) ? 'pending' : $order->payment_status,
            ]);

            // Format expired_at for UI
            $expiredAtLabel = null;
            try {
                if (!empty($data['expired_time']) && is_numeric($data['expired_time'])) {
                    $expiredAtLabel = Carbon::createFromTimestamp((int) $data['expired_time'])->format('d-m-Y H:i:s');
                } elseif (!empty($data['expired_at'])) {
                    $expiredAtLabel = Carbon::parse($data['expired_at'])->format('d-m-Y H:i:s');
                }
            } catch (\Throwable $e) {
                $expiredAtLabel = null;
            }

            return response()->json([
                'success' => true,
                'reference' => $data['reference'] ?? null,
                'checkout_url' => $data['checkout_url'] ?? null,
                'amount' => $amount,
                'fee' => $fee,
                'expired_time' => $data['expired_time'] ?? null,
                'expired_at' => $expiredAtLabel,
                'payment_details' => $data,
            ]);

        } catch (\Exception $e) {
            Log::error('Order Payment Process Error', [
                'order_id' => $order->id ?? null,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
