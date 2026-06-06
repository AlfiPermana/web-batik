<?php

namespace App\Models;

use App\Models\Concerns\HasPayment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkshopBooking extends Model
{
    use HasFactory, HasPayment, SoftDeletes;

    protected $table = 'workshop_bookings';

    // ✅ DIPERBAIKI: Hapus kolom virtual dari $fillable (tidak ada di DB)
    protected $fillable = [
        'user_id',
        'workshop_available_date_id',
        'workshop_slot_schedule_id',
        'booking_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'num_participants',
        'total_price',
        'deposit_amount',
        'remaining_amount',
        'status',
        'payment_status',
        'special_requests',
        'payment_date',
        'reminders_sent',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'num_participants' => 'integer',
        'total_price' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'reminders_sent' => 'array',
        'payment_date' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workshopDate(): BelongsTo
    {
        return $this->belongsTo(WorkshopDate::class);
    }

    public function workshopAvailableDate(): BelongsTo
    {
        return $this->belongsTo(WorkshopAvailableDate::class, 'workshop_available_date_id');
    }

    public function slotSchedule(): BelongsTo
    {
        return $this->belongsTo(WorkshopSlotSchedule::class, 'workshop_slot_schedule_id');
    }

    public function workshop()
    {
        return $this->workshopAvailableDate?->workshop() ?? $this->workshopDate->workshop();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(WorkshopPayment::class, 'booking_id'); // ✅ DIPERBAIKI: Tambahkan 'booking_id'
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(WorkshopReminder::class, 'booking_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopePendingPayment($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeDepositPaid($query)
    {
        return $query->where('payment_status', 'deposit_paid');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS (VIRTUAL ATTRIBUTES)
    |--------------------------------------------------------------------------
    */

    /**
     * Total amount that has been paid (only confirmed payments)
     * ⚠️ DISABLED: Use getTotalPaidMethod() instead to avoid infinite recursion
     */
    // public function getTotalPaidAttribute(): float
    // {
    //     return (float) $this->payments()
    //         ->where('payment_status', 'confirmed')
    //         ->sum('amount');
    // }

    /**
     * Remaining amount to be paid (computed in real-time)
     * ⚠️ DISABLED: Use database column instead to avoid accessors conflict
     */
    // public function getRemainingAmountAttribute(): float
    // {
    //     return max(0, (float) $this->total_price - $this->total_paid);
    // }

    /**
     * Computed payment status based on actual payments
     * ⚠️ DISABLED: Use payment_status column directly to avoid accessor issues
     */
    // public function getComputedPaymentStatusAttribute(): string
    // {
    //     if ($this->isFullyPaid()) {
    //         return 'fully_paid';
    //     }
    //     if ($this->isDepositPaid()) {
    //         return 'deposit_paid';
    //     }
    //     return 'pending';
    // }

    /**
     * Virtual attribute: Workshop title
     */
    public function getWorkshopNameAttribute(): string
    {
        return $this->workshopDate?->workshop?->title ?? $this->attributes['workshop_name'] ?? 'Workshop';
    }

    /**
     * Virtual attribute: Formatted workshop date
     */
    public function getWorkshopDateFormattedAttribute(): string
    {
        return $this->workshopDate?->date?->format('d F Y') ?? $this->attributes['workshop_date'] ?? '-';
    }

    /**
     * Virtual attribute: Workshop address
     */
    public function getAddressAttribute(): string
    {
        return $this->workshopDate?->workshop?->address ?? $this->attributes['address'] ?? 'Alamat tidak tersedia';
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Generate booking number (existing logic, improved)
     */
    public static function generateBookingNumber(): string
    {
        $prefix = 'WS-' . now()->format('Ym');
        $latestBooking = self::where('booking_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        $nextNumber = 1;
        if ($latestBooking) {
            $lastNumber = intval(substr($latestBooking->booking_number, -6));
            $nextNumber = $lastNumber + 1;
        }

        return $prefix . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Check if booking is confirmed
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Check if deposit is paid (based on stored status — for backward compatibility)
     * @deprecated Use `isDepositPaidReal()` for real-time check
     */
    public function isDepositPaid(): bool
    {
        return in_array($this->payment_status, ['deposit_paid', 'fully_paid']);
    }

    /**
     * Real-time check: Is at least one deposit payment confirmed?
     */
    public function isDepositPaidReal(): bool
    {
        return $this->payments()
            ->where('type', 'deposit')
            ->where('payment_status', 'confirmed')
            ->exists();
    }

    /**
     * Check if fully paid (based on stored status — for backward compatibility)
     * @deprecated Use `isFullyPaidReal()` for real-time check
     */
    public function isFullyPaid(): bool
    {
        return $this->payment_status === 'fully_paid';
    }

    /**
     * Real-time check: Is fully paid?
     * ⚠️ SAFE: Does not use accessor
     */
    public function isFullyPaidReal(): bool
    {
        $totalPaid = $this->payments()
            ->where('payment_status', 'confirmed')
            ->sum('amount');
        return $totalPaid >= $this->total_price;
    }

    /**
     * Check if payment is overdue
     */
    public function isPaymentOverdue(): bool
    {
        return $this->payment_status === 'pending' && 
               $this->created_at->addDays(3)->isPast();
    }

    /**
     * Check if reminder was sent
     */
    public function hasReminderSent(string $type): bool
    {
        $remindersSent = $this->reminders_sent ?? [];
        return in_array($type, $remindersSent);
    }

    /**
     * Add sent reminder
     */
    public function addSentReminder(string $type): void
    {
        $remindersSent = $this->reminders_sent ?? [];
        if (!in_array($type, $remindersSent)) {
            $remindersSent[] = $type;
            $this->update(['reminders_sent' => $remindersSent]);
        }
    }

    /**
     * Get total paid amount (legacy method)
     * ⚠️ SAFE: Does not use accessor, queries directly
     * @deprecated
     */
    public function getTotalPaidAmount(): float
    {
        return (float) $this->payments()
            ->where('payment_status', 'confirmed')
            ->sum('amount');
    }

    /**
     * Sync stored `payment_status` with computed value
     * Call manually after webhook/payment confirmation
     */
    public function refreshPaymentStatus(): self
    {
        $computed = $this->computed_payment_status;
        if ($this->payment_status !== $computed) {
            $this->update(['payment_status' => $computed]);
        }
        return $this;
    }
}