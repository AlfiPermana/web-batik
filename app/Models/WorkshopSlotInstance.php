<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkshopSlotInstance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'workshop_slot_instances';

    protected $fillable = [
        'workshop_id',
        'time_slot_id',
        'date',
        'available_slots',
        'booked_slots',
        'status',
        'notes',
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

    public function bookings()
    {
        return $this->hasMany(WorkshopBooking::class, 'workshop_slot_instance_id');
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

    public function scopeFullyBooked($query)
    {
        return $query->where('status', 'fully_booked');
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeForWorkshop($query, $workshopId)
    {
        return $query->where('workshop_id', $workshopId);
    }

    // Methods
    /**
     * Get remaining slots
     */
    public function getRemainingSlots(): int
    {
        return $this->available_slots - $this->booked_slots;
    }

    /**
     * Check if slot is available for booking
     */
    public function isAvailableForBooking(): bool
    {
        return !$this->is_cancelled && $this->status !== 'fully_booked' && $this->getRemainingSlots() > 0;
    }

    /**
     * Book slots
     */
    public function bookSlots(int $numParticipants): bool
    {
        if ($numParticipants > $this->getRemainingSlots()) {
            return false;
        }

        $this->booked_slots += $numParticipants;
        
        // Update status based on remaining slots
        if ($this->getRemainingSlots() === 0) {
            $this->status = 'fully_booked';
        } elseif ($this->booked_slots > 0 && $this->status === 'available') {
            $this->status = 'on_book';
        }

        return $this->save();
    }

    /**
     * Cancel booking (free up slots)
     */
    public function cancelBooking(int $numParticipants): bool
    {
        $this->booked_slots = max(0, $this->booked_slots - $numParticipants);
        
        // Update status
        if ($this->booked_slots === 0) {
            $this->status = 'available';
        } else {
            $this->status = 'on_book';
        }

        return $this->save();
    }

    /**
     * Get formatted time range
     */
    public function getTimeRangeAttribute(): string
    {
        return $this->timeSlot->start_time . ' - ' . $this->timeSlot->end_time;
    }
}
