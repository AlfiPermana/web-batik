<?php

namespace App\Services;

use App\Models\WorkshopBooking;
use App\Models\WorkshopPayment;
use App\Services\payment\PaymentService;
use Illuminate\Support\Facades\Log;

class WorkshopPaymentSyncService
{
    private PaymentService $paymentService;

    public function __construct(?PaymentService $paymentService = null)
    {
        $this->paymentService = $paymentService ?? new PaymentService();
    }

    public function syncBooking(WorkshopBooking $booking): WorkshopBooking
    {
        $booking->loadMissing(['payments', 'slotSchedule']);

        foreach ($booking->payments->sortByDesc('id') as $payment) {
            if ($payment->payment_status === 'pending' && !empty($payment->reference_number)) {
                $this->syncPayment($payment);
            }
        }

        return $booking->fresh(['payments', 'slotSchedule.timeSlot', 'workshopAvailableDate.workshop']);
    }

    public function syncPayment(WorkshopPayment $payment): WorkshopPayment
    {
        if ($payment->payment_status !== 'pending' || empty($payment->reference_number)) {
            return $payment;
        }

        $statusResponse = $this->paymentService->getPaymentStatus((string) $payment->reference_number);
        if (!($statusResponse['success'] ?? false) || !is_array($statusResponse['data'] ?? null)) {
            return $payment;
        }

        $tripayData = $statusResponse['data'];
        $newStatus = $this->mapTripayStatus($tripayData['status'] ?? null);

        $notes = [];
        if (is_string($payment->notes) && $payment->notes !== '') {
            $decoded = json_decode($payment->notes, true);
            if (is_array($decoded)) {
                $notes = $decoded;
            }
        }

        $notes['tripay_sync'] = array_merge($tripayData, [
            'synced_at' => now()->toDateTimeString(),
        ]);

        $updateData = [
            'notes' => json_encode($notes),
        ];

        if ($payment->payment_status !== $newStatus) {
            $updateData['payment_status'] = $newStatus;
        }

        if ($newStatus === 'confirmed' && empty($payment->verified_at)) {
            $updateData['verified_at'] = now();
        }

        if (count($updateData) > 0) {
            $payment->update($updateData);
            $payment->refresh();
        }

        if ($payment->booking) {
            $this->syncBookingAggregate($payment->booking->fresh(['payments', 'slotSchedule']));
        }

        Log::info('Workshop payment synced with Tripay', [
            'payment_id' => $payment->id,
            'booking_id' => $payment->booking_id,
            'reference_number' => $payment->reference_number,
            'payment_status' => $payment->payment_status,
        ]);

        return $payment;
    }

    private function syncBookingAggregate(WorkshopBooking $booking): void
    {
        $payments = $booking->payments;

        $confirmedFull = $payments->first(function ($payment) {
            return $payment->payment_status === 'confirmed'
                && in_array($payment->type, ['full', 'remaining'], true);
        });

        $confirmedDeposit = $payments->first(function ($payment) {
            return $payment->payment_status === 'confirmed' && $payment->type === 'deposit';
        });

        $negativePayment = $payments->sortByDesc('id')->first(function ($payment) {
            return in_array($payment->payment_status, ['failed', 'expired', 'refunded'], true);
        });

        $updateData = [];

        if ($confirmedFull) {
            $updateData = [
                'status' => 'confirmed',
                'payment_status' => 'fully_paid',
                'payment_date' => $booking->payment_date ?? ($confirmedFull->verified_at ?? now()),
                'remaining_amount' => 0,
            ];
            $this->markSlotPaid($booking);
        } elseif ($confirmedDeposit) {
            $updateData = [
                'status' => 'confirmed',
                'payment_status' => 'deposit_paid',
                'payment_date' => $booking->payment_date ?? ($confirmedDeposit->verified_at ?? now()),
            ];
            $this->markSlotPaid($booking);
        } elseif ($negativePayment) {
            $updateData = [
                'payment_status' => $negativePayment->payment_status,
            ];

            if (!in_array($booking->status, ['completed', 'cancelled'], true)) {
                $updateData['status'] = 'pending';
            }

            $this->releaseSlotIfNeeded($booking);
        } else {
            $updateData = [
                'payment_status' => 'pending',
            ];

            if (!in_array($booking->status, ['completed', 'cancelled'], true)) {
                $updateData['status'] = 'pending';
            }

            $this->releaseSlotIfNeeded($booking);
        }

        $booking->fill($updateData);
        if ($booking->isDirty()) {
            $booking->save();
        }
    }

    private function markSlotPaid(WorkshopBooking $booking): void
    {
        $schedule = $booking->slotSchedule;
        if ($schedule && $schedule->status !== 'PAID') {
            $schedule->update(['status' => 'PAID']);
        }
    }

    private function releaseSlotIfNeeded(WorkshopBooking $booking): void
    {
        if (in_array($booking->status, ['completed', 'cancelled'], true)) {
            return;
        }

        $hasConfirmedPayment = $booking->payments->contains(function ($payment) {
            return $payment->payment_status === 'confirmed';
        });

        if ($hasConfirmedPayment) {
            return;
        }

        $schedule = $booking->slotSchedule;
        if ($schedule && $schedule->booked_count > 0) {
            $schedule->cancelParticipants((int) $booking->num_participants);
        }
    }

    private function mapTripayStatus(?string $tripayStatus): string
    {
        return match (strtoupper((string) $tripayStatus)) {
            'PAID' => 'confirmed',
            'UNPAID' => 'pending',
            'FAILED' => 'failed',
            'EXPIRED' => 'expired',
            'REFUND', 'REFUNDED' => 'refunded',
            default => 'pending',
        };
    }
}
