<?php

namespace App\Models;

use App\Models\Concerns\HasPayment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Order extends Model
{
    use HasFactory;
    use HasPayment;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'payment_status',
        'subtotal',
        'shipping_cost',
        'discount',
        'total',
        'shipping_address',
        'shipping_service',
        'shipping_service_type',
        'shipping_method',
        'payment_method',
        'midtrans_transaction_id',
        'xendit_invoice_id',
        'tripay_reference',
        'tripay_response',
        'notes',
        'paid_at',
        'stock_deducted_at',
        'shipped_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'shipping_address' => 'array',
        'tripay_response' => 'array',
        'paid_at' => 'datetime',
        'stock_deducted_at' => 'datetime',
        'shipped_at' => 'datetime',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the status history for this order.
     */
    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber(): string
    {
        $date = now()->format('YmdHis');
        $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        return 'ORD-' . $date . $random;
    }

    /**
     * Get the formatted total.
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    /**
     * Get the formatted status.
     */
    public function getFormattedStatusAttribute(): string
    {
        return ucfirst($this->status);
    }

    public function getPaymentMethodNameAttribute($value): ?string
    {
        if (!empty($value)) {
            return $value;
        }

        return match ($this->payment_method) {
            'BCAVA' => 'BCA Virtual Account',
            'BNIVA' => 'BNI Virtual Account',
            'BRIVA' => 'BRI Virtual Account',
            'MANDIRIVA' => 'MANDIRI Virtual Account',
            'PERMATAVA' => 'PERMATA Virtual Account',
            'MUAMALATVA' => 'MUAMALAT Virtual Account',
            'CIMBVA' => 'CIMB Virtual Account',
            'SAMPOERNAVA' => 'Sahabat Sampoerna Virtual Account',
            'ALFAMART' => 'Alfamart',
            'INDOMARET' => 'Indomaret',
            'QRIS' => 'QRIS',
            'DANA' => 'DANA',
            'OVO' => 'OVO',
            'SHOPEEPAY' => 'ShopeePay',
            default => $this->payment_method,
        };
    }

    /**
     * Get formatted shipping address
     */
    public function getFormattedAddressAttribute(): string
    {
        $address = $this->shipping_address;
        if (!$address) return '';
        return ($address['address'] ?? '') . ', ' . ($address['city'] ?? '') . ' ' . ($address['postal_code'] ?? '');
    }

    /**
     * Update order status with history tracking
     */
    public function updateOrderStatus(string $newStatus, ?string $notes = null, ?int $changedBy = null): void
    {
        if ($newStatus === 'cancelled' && !$this->canBeCancelled()) {
            throw new \DomainException('Pesanan yang sudah dibayar tidak dapat dibatalkan. Refund harus diproses terlebih dahulu.');
        }

        $oldStatus = $this->status;

        // Update order status
        $this->update(['status' => $newStatus]);

        // Create status history record
        $this->statusHistories()->create([
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'notes' => $notes,
            'changed_by' => $changedBy,
        ]);
    }

    /**
     * Get status badge style
     */
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'label' => 'Pending'],
            'processing' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'Processing'],
            'shipped' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'label' => 'Shipped'],
            'delivered' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Delivered'],
            'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Cancelled'],
            default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => ucfirst($this->status)],
        };
    }

    public function isPaid(): bool
    {
        return in_array($this->payment_status, ['paid', 'confirmed'], true);
    }

    public function canBeCancelled(): bool
    {
        if ($this->isPaid() && $this->payment_status !== 'refunded') {
            return false;
        }

        return !in_array($this->status, ['delivered', 'cancelled'], true);
    }

    public function getDisplayItemsAttribute(): Collection
    {
        $items = $this->relationLoaded('items') ? $this->items : $this->items()->with(['product.images', 'productSize'])->get();
        if ($items->isNotEmpty()) {
            return $items;
        }

        $matchedSizes = ProductSize::query()
            ->with(['product.images'])
            ->where('price', $this->subtotal)
            ->get();

        if ($matchedSizes->count() !== 1) {
            return collect();
        }

        $size = $matchedSizes->first();

        return collect([
            (object) [
                'product' => $size->product,
                'productSize' => $size,
                'quantity' => 1,
                'price' => (float) $size->price,
                'subtotal' => (float) $size->price,
                'is_recovered' => true,
            ],
        ]);
    }
}
