<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Workshop;
use App\Models\WorkshopAvailableDate;
use App\Models\WorkshopSlotSchedule;

echo "=== UPDATE WORKSHOP TEST DATA ===\n\n";

$workshop = Workshop::find(4);

// 1. Add new future date
echo "1. Adding future dates...\n";
$futureDates = [
    now()->addDays(5)->format('Y-m-d'),  // 5 days from now
    now()->addDays(10)->format('Y-m-d'), // 10 days from now
];

foreach ($futureDates as $date) {
    $exists = WorkshopAvailableDate::where('workshop_id', $workshop->id)
        ->where('date', $date)
        ->exists();
    
    if (!$exists) {
        $availDate = WorkshopAvailableDate::create([
            'workshop_id' => $workshop->id,
            'date' => $date,
        ]);
        
        // Auto-create schedules
        $timeSlots = $workshop->timeSlots()->where('is_active', true)->get();
        foreach ($timeSlots as $slot) {
            WorkshopSlotSchedule::create([
                'workshop_id' => $workshop->id,
                'time_slot_id' => $slot->id,
                'available_date_id' => $availDate->id,
                'date' => $date,
                'max_capacity' => $slot->max_capacity,
                'booked_count' => 0,
                'status' => 'available',
            ]);
        }
        
        echo "   ✓ Added: {$date}\n";
    } else {
        echo "   ℹ Already exists: {$date}\n";
    }
}

// 2. Show updated schedules
echo "\n2. All Schedules (Future Only):\n";
$schedules = $workshop->slotSchedules()
    ->where('date', '>=', now()->format('Y-m-d'))
    ->orderBy('date')
    ->get();

foreach ($schedules as $sch) {
    $slot = $sch->timeSlot;
    echo "   - {$sch->date}: {$slot->name} ({$slot->start_time}-{$slot->end_time})\n";
}

// 3. Test API response
echo "\n3. API Response Format:\n";
$apiData = $workshop->slotSchedules()
    ->with('timeSlot')
    ->where('status', '!=', 'fully_booked')
    ->where('date', '>=', now()->format('Y-m-d'))
    ->orderBy('date')
    ->orderBy('time_slot_id')
    ->get()
    ->map(function ($schedule) {
        $dateStr = is_string($schedule->date) 
            ? $schedule->date 
            : $schedule->date->format('Y-m-d');
        
        $startTime = is_string($schedule->timeSlot->start_time)
            ? substr($schedule->timeSlot->start_time, 0, 5)
            : $schedule->timeSlot->start_time->format('H:i');
        
        $endTime = is_string($schedule->timeSlot->end_time)
            ? substr($schedule->timeSlot->end_time, 0, 5)
            : $schedule->timeSlot->end_time->format('H:i');
        
        return [
            'id' => $schedule->id,
            'date' => $dateStr,
            'date_formatted' => $dateStr . ' (Kamis)',
            'slot_name' => $schedule->timeSlot->name,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'booked_count' => $schedule->booked_count,
            'status' => $schedule->status,
            'label' => sprintf(
                '%s - %s %s-%s',
                $dateStr,
                $schedule->timeSlot->name,
                $startTime,
                $endTime
            )
        ];
    });

echo json_encode($apiData, JSON_PRETTY_PRINT);

echo "\n=== UPDATE COMPLETE ===\n";
?>
