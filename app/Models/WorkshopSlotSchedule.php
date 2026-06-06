<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkshopSlotSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'workshop_slot_schedules';

    protected $fillable = [
        'workshop_id',
        'time_slot_id',
        'available_date_id',
        'date',
        'max_capacity',
        'booked_count',
        'status',
        'is_cancelled',
        'cancellation_reason',
    ];

    protected $casts = [
        'date' => 'date',
        'is_cancelled' => 'boolean',
    ];

    // Relationships
    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function timeSlot()
    {
        return $this->belongsTo(WorkshopTimeSlot::class, 'time_slot_id');
    }

    public function availableDate()
    {
        return $this->belongsTo(WorkshopAvailableDate::class, 'available_date_id');
    }

    public function bookings()
    {
        return $this->hasMany(WorkshopBooking::class, 'workshop_slot_schedule_id');
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')->where('is_cancelled', false);
    }

    public function scopeOnBook($query)
    {
        return $query->where('status', 'on_book');
    }

    public function scopeForWorkshop($query, $workshopId)
    {
        return $query->where('workshop_id', $workshopId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    // Methods
    /**
     * Get remaining capacity
     */
    public function getRemainingCapacity(): int
    {
        return $this->max_capacity - $this->booked_count;
    }

    /**
     * Check if can book
     * Note: Number of participants is unlimited - no max_capacity check
     */
    public function canBook($numParticipants = 1): bool
    {
        // New system: 1 schedule = 1 booking window.
        // Only allow booking when schedule is explicitly available.
        return !$this->is_cancelled
            && $this->status === 'available'
            && $numParticipants > 0;
    }

    /**
     * Book participants
     * Note: booked_count is just a counter, NOT enforced as capacity limit
     */
    public function bookParticipants(int $numParticipants): bool
    {
        if (!$this->canBook($numParticipants)) {
            return false;
        }

        $this->booked_count += $numParticipants;
        
        // Update status - only based on cancellation, NOT on capacity
        if (!$this->is_cancelled && $this->status !== 'cancelled') {
            $this->status = 'on_book';  // Mark as having bookings
        }

        return $this->save();
    }

    /**
     * Cancel booking (reduce booking count)
     */
    public function cancelParticipants(int $numParticipants): bool
    {
        $this->booked_count = max(0, $this->booked_count - $numParticipants);
        
        // Update status - only reset if no more bookings
        if ($this->booked_count === 0 && !$this->is_cancelled) {
            $this->status = 'available';
        }

        return $this->save();
    }

    /**
     * Get formatted time range
     */
    public function getTimeRangeFormatted(): string
    {
        return $this->timeSlot->getTimeRangeFormatted();
    }

    /**
     * Get date formatted
     */
    public function getDateFormatted(): string
    {
        return $this->date->translatedFormat('l, d F Y');
    }
}
