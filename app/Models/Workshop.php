<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Workshop extends Model
{
    use HasFactory;

    protected $table = 'workshop';

    protected $fillable = [
        'title',
        'amount',
        'description',
        'capacity',
        'location',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all dates for this workshop
     */
    public function dates(): HasMany
    {
        return $this->hasMany(WorkshopDate::class);
    }

    /**
     * Get all time slots for this workshop
     */
    public function timeSlots(): HasMany
    {
        return $this->hasMany(WorkshopTimeSlot::class)->orderBy('order');
    }

    /**
     * Get all available dates for this workshop (NEW SYSTEM)
     */
    public function availableDates(): HasMany
    {
        return $this->hasMany(WorkshopAvailableDate::class);
    }

    /**
     * Get all slot schedules for this workshop (NEW SYSTEM)
     */
    public function slotSchedules(): HasMany
    {
        return $this->hasMany(WorkshopSlotSchedule::class);
    }

    /**
     * Alias for slotSchedules - used in views
     */
    public function schedules(): HasMany
    {
        return $this->slotSchedules();
    }

    /**
     * Get all slot instances for this workshop
     */
    public function slotInstances(): HasMany
    {
        return $this->hasMany(WorkshopSlotInstance::class);
    }

    /**
     * Get all bookings for this workshop (NEW SYSTEM)
     * Uses workshop_slot_schedules as the join table.
     */
    public function bookings(): HasManyThrough
    {
        return $this->hasManyThrough(
            WorkshopBooking::class,
            WorkshopSlotSchedule::class,
            'workshop_id',
            'workshop_slot_schedule_id',
            'id',
            'id'
        );
    }

    /**
     * Get active dates
     */
    public function activeDates()
    {
        return $this->dates()->where('is_active', true)->where('is_cancelled', false);
    }

    /**
     * Get upcoming dates
     */
    public function upcomingDates()
    {
        return $this->activeDates()->where('date', '>=', now()->toDateString())->orderBy('date');
    }

    /**
     * Get next available date
     */
    public function nextAvailableDate()
    {
        return $this->upcomingDates()
            ->where('current_bookings', '<', $this->capacity)
            ->first();
    }

    /**
     * Check if workshop is full
     */
    public function isFull(): bool
    {
        $available = $this->nextAvailableDate();
        return is_null($available);
    }

    /**
     * Get total capacity
     */
    public function getTotalCapacity(): int
    {
        return $this->capacity ?? 0;
    }

    /**
     * Get current bookings
     */
    public function getCurrentBookings(): int
    {
        return $this->bookings()->where('status', 'confirmed')->count();
    }

    /**
     * Scope: Get active workshops
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get workshops with dates
     */
    public function scopeWithDates($query)
    {
        return $query->with('activeDates');
    }
}
