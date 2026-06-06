<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkshopReminder extends Model
{
    protected $table = 'workshop_reminders';

    protected $fillable = [
        'booking_id',
        'workshop_date_id',
        'type',
        'delivery_method',
        'status',
        'sent_at',
        'error_message',
        'scheduled_for',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'scheduled_for' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the booking associated with this reminder
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(WorkshopBooking::class, 'booking_id');
    }

    /**
     * Get the workshop date associated with this reminder
     */
    public function workshopDate(): BelongsTo
    {
        return $this->belongsTo(WorkshopDate::class);
    }

    /**
     * Scope: Get pending reminders
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Get sent reminders
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope: Get failed reminders
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope: Get reminders that should be sent now
     */
    public function scopeDueNow($query)
    {
        return $query->where('status', 'pending')
            ->where('scheduled_for', '<=', now());
    }

    /**
     * Scope: Get email reminders
     */
    public function scopeEmail($query)
    {
        return $query->whereIn('delivery_method', ['email', 'both']);
    }

    /**
     * Scope: Get WhatsApp reminders
     */
    public function scopeWhatsapp($query)
    {
        return $query->whereIn('delivery_method', ['whatsapp', 'both']);
    }

    /**
     * Check if reminder was sent successfully
     */
    public function wasSent(): bool
    {
        return $this->status === 'sent';
    }

    /**
     * Check if reminder failed
     */
    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if reminder is due
     */
    public function isDue(): bool
    {
        return $this->status === 'pending' && now()->isAfter($this->scheduled_for);
    }

    /**
     * Mark as sent
     */
    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    /**
     * Mark as failed
     */
    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);
    }
}
