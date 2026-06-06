<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkshopDate extends Model
{
    protected $table = 'workshop_dates';

    protected $fillable = [
        'workshop_id',
        'date',
        'start_time',
        'end_time',
        'capacity',
        'current_bookings',
        'is_active',
        'is_cancelled',
        'cancellation_reason',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean',
        'is_cancelled' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the workshop that owns this date
     */
    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    /**
     * Get all bookings for this workshop date
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(WorkshopBooking::class);
    }

    /**
     * Get reminders for this workshop date
     */
    public function reminders(): HasMany
    {
        return $this->hasMany(WorkshopReminder::class);
    }

    /**
     * Get available capacity
     */
    public function getAvailableCapacity(): int
    {
        return $this->capacity - $this->current_bookings;
    }

    /**
     * Check if this date is full
     */
    public function isFull(): bool
    {
        return $this->current_bookings >= $this->capacity;
    }

    /**
     * Check if this date has passed
     */
    public function hasPassed(): bool
    {
        return now()->isAfter($this->date);
    }

    /**
     * Get formatted date time
     */
    public function getFormattedDateTime(): string
    {
        return $this->date->format('d M Y') . ' ' . $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i');
    }

    /**
     * Get days until workshop
     */
    public function daysUntil(): int
    {
        return now()->diffInDays($this->date);
    }
}
