<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkshopTimeSlot extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'workshop_time_slots';

    protected $fillable = [
        'workshop_id',
        'name',
        'start_time',
        'end_time',
        'max_capacity',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function schedules()
    {
        return $this->hasMany(WorkshopSlotSchedule::class, 'time_slot_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('start_time');
    }

    // Get formatted time
    public function getTimeRangeAttribute(): string
    {
        return $this->start_time . ' - ' . $this->end_time;
    }

    public function getTimeRangeFormatted(): string
    {
        return substr($this->start_time, 0, 5) . ' - ' . substr($this->end_time, 0, 5);
    }
}
