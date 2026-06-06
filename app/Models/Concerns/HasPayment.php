<?php
// app/Models/Concerns/HasPayment.php

namespace App\Models\Concerns;

trait HasPayment
{
    /**
     * Generate unique merchant reference for Tripay
     */
    public function getPaymentMerchantRef(): string
    {
        if ($this instanceof \App\Models\Order) {
            return 'ORDER-' . $this->id . '-' . strtoupper(substr(md5($this->created_at), 0, 6));
        }
        if ($this instanceof \App\Models\WorkshopBooking) {
            return $this->booking_number; // ✅ sudah unik & konsisten
        }
        throw new \Exception('Model tidak didukung untuk pembayaran.');
    }

    /**
     * Get customer info
     */
    public function getPaymentCustomerName(): string
    {
        return $this->customer_name ?? ($this->user?->name ?? 'Guest');
    }

    public function getPaymentCustomerEmail(): string
    {
        return $this->customer_email ?? ($this->user?->email ?? '');
    }

    public function getPaymentCustomerPhone(): string
    {
        return $this->customer_phone ?? '';
    }

    /**
     * Get items for Tripay invoice (max 100 chars per item name)
     */
    public function getPaymentItems(float $amount, string $type = 'deposit'): array
    {
        if ($this instanceof \App\Models\Order) {
            return $this->items->map(function ($item) {
                return [
                    'name' => substr($item->product->name ?? 'Product', 0, 100),
                    'price' => (int) round($item->price),
                    'quantity' => (int) $item->quantity,
                ];
            })->toArray();
        }

        if ($this instanceof \App\Models\WorkshopBooking) {
            $title = $this->workshopDate?->workshop?->title ?? 'Workshop';
            $date = $this->workshopDate?->date?->format('d M Y') ?? '';
            $desc = match ($type) {
                'full' => "Pelunasan Workshop: {$title}",
                'remaining' => "Sisa Workshop: {$title}",
                default => "DP Workshop: {$title}",
            };

            return [[
                'name' => substr("{$desc} ({$date})", 0, 100),
                'price' => (int) round($amount), // ← ambil dari parameter, bukan $this->deposit_amount!
                'quantity' => 1,
            ]];
        }
        return [];
    }
}