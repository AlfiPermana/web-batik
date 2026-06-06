<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkshopPayment extends Model
{
    protected $table = 'workshop_payments';

    protected $fillable = [
        'booking_id',
        'amount',
        'type',
        'payment_method',
        'payment_status',
        'reference_number',
        'proof_file_path',
        'verified_by',
        'verified_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the booking that owns this payment
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(WorkshopBooking::class, 'booking_id');
    }

    /**
     * Get the admin who verified this payment
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Scope: Get confirmed payments
     */
    public function scopeConfirmed($query)
    {
        return $query->where('payment_status', 'confirmed');
    }

    /**
     * Scope: Get pending payments
     */
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    /**
     * Scope: Get deposit payments
     */
    public function scopeDeposit($query)
    {
        return $query->where('type', 'deposit');
    }

    /**
     * Scope: Get remaining payments
     */
    public function scopeRemaining($query)
    {
        return $query->where('type', 'remaining');
    }

    /**
     * Check if payment is confirmed
     */
    public function isConfirmed(): bool
    {
        return $this->payment_status === 'confirmed';
    }

    /**
     * Check if payment proof exists
     */
    public function hasProof(): bool
    {
        return !is_null($this->proof_file_path);
    }
}