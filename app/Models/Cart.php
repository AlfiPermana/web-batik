<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the cart.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all items in the cart.
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get cart subtotal (sum of all items price * quantity)
     */
    public function getSubtotalAttribute(): float
    {
        return (float) $this->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }

    /**
     * Get cart total (with tax if applicable)
     */
    public function getTotalAttribute(): float
    {
        return $this->subtotal; // For now, no tax. Can be extended
    }

    /**
     * Get total item count in cart
     */
    public function getItemCountAttribute(): int
    {
        return (int) $this->items->sum('quantity');
    }

    /**
     * Add or update item in cart
     */
    public function addItem(Product $product, ProductSize $size, int $quantity = 1): CartItem
    {
        // Check if item already exists in cart
        $item = $this->items()
            ->where('product_id', $product->id)
            ->where('product_size_id', $size->id)
            ->first();

        if ($item) {
            // Update quantity
            $item->update(['quantity' => $item->quantity + $quantity]);
            return $item;
        }

        // Create new item
        return $this->items()->create([
            'product_id' => $product->id,
            'product_size_id' => $size->id,
            'quantity' => $quantity,
            'price' => $size->price, // Snapshot price
        ]);
    }

    /**
     * Remove item from cart
     */
    public function removeItem(int $itemId): bool
    {
        return $this->items()->where('id', $itemId)->delete() > 0;
    }

    /**
     * Clear entire cart
     */
    public function clear(): bool
    {
        return $this->items()->delete() > 0;
    }

    /**
     * Check if cart is empty
     */
    public function isEmpty(): bool
    {
        return $this->items()->count() === 0;
    }
}
