<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ProductSize;
use App\Models\WorkshopPayment;
use App\Models\WorkshopSlotSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TripayWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('Tripay Webhook Raw', [
            'headers' => $request->headers->all(),
            'body' => $request->getContent(),
        ]);

        try {
            $signature = $request->header('X-Callback-Signature');
            $payload = $request->getContent();
            $secretKey = config('services.tripay.private_key');

            if (!$signature) {
                Log::error('Tripay webhook: X-Callback-Signature header missing');
                return response()->json(['error' => 'Invalid signature'], 403);
            }

            $computedSignature = hash_hmac('sha256', $payload, $secretKey);

            Log::info('Tripay signature verification', [
                'secret_key_set' => !empty($secretKey),
                'secret_key_length' => strlen($secretKey ?? ''),
                'received_signature' => $signature,
                'computed_signature' => $computedSignature,
                'payload_length' => strlen($payload),
                'payload_preview' => substr($payload, 0, 100),
            ]);

            if (!hash_equals($computedSignature, $signature)) {
                Log::warning('Tripay signature mismatch - check your private key in .env', [
                    'received' => $signature,
                    'computed' => $computedSignature,
                    'tip' => 'Ensure TRIPAY_PRIVATE_KEY in .env matches your Tripay account',
                ]);
                return response()->json(['error' => 'Invalid signature'], 403);
            }

            $data = $request->json()->all();
            $merchantRef = $data['merchant_ref'] ?? null;
            $reference = $data['reference'] ?? null;
            $status = $data['status'] ?? null;

            if (!$merchantRef || !$reference) {
                Log::error('Tripay webhook: missing merchant_ref or reference', $data);
                return response()->json(['error' => 'Invalid data'], 400);
            }

            Log::info('Tripay webhook data', [
                'merchant_ref' => $merchantRef,
                'reference' => $reference,
                'status' => $status,
            ]);

            $order = Order::where('order_number', $merchantRef)->first();
            if ($order) {
                Log::info('Found Order for webhook', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ]);

                $this->updateOrderPayment($order, $data, $status, $reference);

                return response()->json(['success' => true, 'type' => 'order'], 200);
            }

            $payment = WorkshopPayment::with('booking.payments', 'booking.slotSchedule')
                ->where('reference_number', $merchantRef)
                ->orWhere('reference_number', $reference)
                ->first();

            if ($payment) {
                Log::info('Found WorkshopPayment for webhook', [
                    'payment_id' => $payment->id,
                    'merchant_ref' => $merchantRef,
                    'reference' => $reference,
                ]);

                $this->updateWorkshopPayment($payment, $status);

                return response()->json(['success' => true, 'type' => 'workshop_payment'], 200);
            }

            Log::warning('No Order or WorkshopPayment found for webhook', [
                'merchant_ref' => $merchantRef,
                'reference' => $reference,
            ]);

            return response()->json(['success' => true, 'message' => 'No matching record found'], 200);
        } catch (\Exception $e) {
            Log::error('Tripay webhook exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['success' => true, 'error' => $e->getMessage()], 200);
        }
    }

    private function updateOrderPayment($order, array $data, ?string $status, ?string $reference): void
    {
        try {
            $paymentStatus = match ($status) {
                'PAID' => 'paid',
                'UNPAID' => 'unpaid',
                'FAILED' => 'failed',
                'EXPIRED' => 'expired',
                'REFUND', 'REFUNDED' => 'refunded',
                default => 'pending',
            };

            $updateData = [
                'payment_status' => $paymentStatus,
                'tripay_reference' => $reference,
                'tripay_response' => $data,
            ];

            if ($paymentStatus === 'paid') {
                $paidAtTimestamp = $data['paid_at'] ?? time();
                $updateData['paid_at'] = Carbon::createFromTimestamp((int) $paidAtTimestamp);
            }

            $order->update($updateData);

            if ($paymentStatus === 'paid' && $order->status === 'pending') {
                $order->updateOrderStatus('processing', 'Pembayaran dikonfirmasi otomatis dari Tripay webhook');
            }

            // Handle expired/failed: cancel the order regardless of current status
            // This fixes the race condition where frontend polling sets status=processing,
            // then EXPIRED webhook arrives and must override it.
            if (in_array($paymentStatus, ['expired', 'failed'], true)
                && !in_array($order->status, ['delivered', 'cancelled'], true)) {
                $order->updateOrderStatus(
                    'cancelled',
                    'Pembayaran ' . strtoupper($paymentStatus) . ' - pesanan dibatalkan otomatis oleh sistem'
                );

                Log::info('Order cancelled due to expired/failed payment', [
                    'order_id'        => $order->id,
                    'payment_status'  => $paymentStatus,
                    'previous_status' => $order->getOriginal('status'),
                ]);
            }

            Log::info('Order payment updated', [
                'order_id' => $order->id,
                'order_status' => $order->fresh()->status,
                'payment_status' => $paymentStatus,
                'reference' => $reference,
                'paid_at' => $updateData['paid_at'] ?? null,
            ]);

            if ($paymentStatus === 'paid') {
                $this->deductOrderStocksIfNeeded((int) $order->id);
            }
        } catch (\Exception $e) {
            Log::error('Error updating order: ' . $e->getMessage(), [
                'order_id' => $order->id ?? null,
            ]);
        }
    }

    private function deductOrderStocksIfNeeded(int $orderId): void
    {
        try {
            DB::transaction(function () use ($orderId) {
                $order = Order::query()->whereKey($orderId)->lockForUpdate()->first();
                if (!$order || $order->payment_status !== 'paid' || !empty($order->stock_deducted_at)) {
                    return;
                }

                $items = $order->items()->select(['product_size_id', 'quantity'])->get();
                if ($items->isEmpty()) {
                    $order->update(['stock_deducted_at' => now()]);
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

                $order->update(['stock_deducted_at' => now()]);
            });

            Log::info('Order stock deducted', ['order_id' => $orderId]);
        } catch (\Throwable $e) {
            Log::error('Failed to deduct order stock', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function updateWorkshopPayment(WorkshopPayment $payment, ?string $status): void
    {
        try {
            $newStatus = match ($status) {
                'PAID' => 'confirmed',
                'UNPAID' => 'pending',
                'FAILED' => 'failed',
                'EXPIRED' => 'expired',
                'REFUND', 'REFUNDED' => 'refunded',
                default => 'pending',
            };

            if ($payment->payment_status === $newStatus) {
                return;
            }

            $payment->update(['payment_status' => $newStatus]);

            Log::info('WorkshopPayment status updated', [
                'payment_id' => $payment->id,
                'status' => $newStatus,
            ]);

            $booking = $payment->booking?->fresh(['payments', 'slotSchedule']);
            if (!$booking) {
                return;
            }

            if ($newStatus === 'confirmed') {
                $nextBookingPaymentStatus = match ($payment->type) {
                    'deposit' => 'deposit_paid',
                    'remaining', 'full' => 'fully_paid',
                    default => $booking->payment_status ?? 'pending',
                };

                if (($booking->payment_status ?? null) === 'fully_paid') {
                    $nextBookingPaymentStatus = 'fully_paid';
                }

                $booking->update([
                    'status' => 'confirmed',
                    'payment_status' => $nextBookingPaymentStatus,
                    'payment_date' => now(),
                ]);

                if ($booking->slotSchedule && $booking->slotSchedule->status !== 'PAID') {
                    $booking->slotSchedule->update(['status' => 'PAID']);
                }

                Log::info('Workshop booking marked as paid/confirmed', [
                    'booking_id' => $booking->id,
                    'payment_type' => $payment->type,
                    'payment_status' => $nextBookingPaymentStatus,
                ]);

                return;
            }

            if (in_array($newStatus, ['failed', 'expired', 'refunded'], true)) {
                if (!in_array($booking->status, ['completed', 'cancelled'], true) && !$this->bookingHasConfirmedPayments($booking)) {
                    $booking->update([
                        'status' => 'pending',
                        'payment_status' => $newStatus,
                    ]);
                }

                $this->releaseWorkshopSlotIfNeeded($booking, $newStatus);
            }
        } catch (\Exception $e) {
            Log::error('Error updating workshop payment: ' . $e->getMessage());
        }
    }

    private function bookingHasConfirmedPayments($booking): bool
    {
        return $booking->payments->contains(function ($payment) {
            return $payment->payment_status === 'confirmed';
        });
    }

    private function releaseWorkshopSlotIfNeeded($booking, string $paymentStatus): void
    {
        if (!$booking || !$booking->workshop_slot_schedule_id) {
            return;
        }

        if ($this->bookingHasConfirmedPayments($booking) || in_array($booking->status, ['completed', 'cancelled'], true)) {
            Log::info('Workshop slot not released because booking is already protected', [
                'booking_id' => $booking->id,
                'payment_status' => $paymentStatus,
                'booking_status' => $booking->status,
            ]);
            return;
        }

        $schedule = $booking->slotSchedule ?: WorkshopSlotSchedule::find($booking->workshop_slot_schedule_id);
        if (!$schedule) {
            return;
        }

        $schedule->cancelParticipants((int) $booking->num_participants);

        Log::info('Workshop slot released after negative payment status', [
            'schedule_id' => $schedule->id,
            'booking_id' => $booking->id,
            'payment_status' => $paymentStatus,
        ]);
    }
}
