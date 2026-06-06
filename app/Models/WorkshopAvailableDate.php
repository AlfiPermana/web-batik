<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkshopAvailableDate extends Model
{
    use HasFactory;

    protected $table = 'workshop_available_dates';

    protected $fillable = [
        'workshop_id',
        'date',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function schedules()
    {
        return $this->hasMany(WorkshopSlotSchedule::class, 'available_date_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->toDateString())->orderBy('date');
    }

    public function scopeForWorkshop($query, $workshopId)
    {
        return $query->where('workshop_id', $workshopId);
    }

    // Get date formatted
    public function getDateFormatted(): string
    {
        return $this->date->translatedFormat('l, d F Y');
    }

    // Check if date has available slots
    public function hasAvailableSlots(): bool
    {
        return $this->schedules()
            ->where('status', 'available')
            ->where('is_cancelled', false)
            ->exists();
    }

    // Get all available time slots for this date
    public function getAvailableSlots()
    {
        return $this->schedules()
            ->where('status', 'available')
            ->where('is_cancelled', false)
            ->with('timeSlot')
            ->orderBy('id')
            ->get();
    }
}
