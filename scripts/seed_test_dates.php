<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\WorkshopAvailableDate;
use App\Models\WorkshopSlotSchedule;

// Add test dates for workshop 1
$workshopId = 1;
$testDates = ['2025-12-20', '2025-12-21', '2025-12-22', '2025-12-23', '2025-12-25'];

foreach ($testDates as $date) {
    $existing = WorkshopAvailableDate::where('workshop_id', $workshopId)
        ->where('date', $date)
        ->exists();
    
    if (!$existing) {
        $availableDate = WorkshopAvailableDate::create([
            'workshop_id' => $workshopId,
            'date' => $date,
        ]);
        
        // Create schedules for all active time slots
        $timeSlots = \App\Models\Workshop::find($workshopId)->timeSlots()->where('is_active', true)->get();
        foreach ($timeSlots as $slot) {
            WorkshopSlotSchedule::create([
                'workshop_id' => $workshopId,
                'time_slot_id' => $slot->id,
                'available_date_id' => $availableDate->id,
                'date' => $date,
                'max_capacity' => $slot->max_capacity,
                'booked_count' => 0,
                'status' => 'available',
                'is_cancelled' => false,
            ]);
        }
        
        echo "Added date: $date with " . $timeSlots->count() . " schedules\n";
    } else {
        echo "Date $date already exists\n";
    }
}

echo "\nDone!\n";
?>
