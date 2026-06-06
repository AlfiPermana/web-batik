<?php

namespace Database\Seeders;

use App\Models\Workshop;
use App\Models\WorkshopTimeSlot;
use App\Models\WorkshopAvailableDate;
use App\Models\WorkshopSlotSchedule;
use Illuminate\Database\Seeder;

class WorkshopSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test workshop
        $workshop = Workshop::create([
            'title' => 'Batik Workshop 101',
            'description' => 'Learn traditional batik making',
            'amount' => 500000, // Rp 500k
            'location' => 'Jakarta',
            'capacity' => 100,
            'is_active' => true,
            'created_by' => null,
        ]);

        // Create time slots
        $slotMorning = WorkshopTimeSlot::create([
            'workshop_id' => $workshop->id,
            'name' => 'Slot 1: Pagi',
            'start_time' => '10:00:00',
            'end_time' => '13:00:00',
            'max_capacity' => 20,
            'is_active' => true,
            'order' => 1,
        ]);

        $slotAfternoon = WorkshopTimeSlot::create([
            'workshop_id' => $workshop->id,
            'name' => 'Slot 2: Sore',
            'start_time' => '14:00:00',
            'end_time' => '17:00:00',
            'max_capacity' => 20,
            'is_active' => true,
            'order' => 2,
        ]);

        // Create available dates
        $dateToday = WorkshopAvailableDate::create([
            'workshop_id' => $workshop->id,
            'date' => now()->toDateString(),
            'is_active' => true,
            'notes' => 'Test date today',
        ]);

        $dateTomorrow = WorkshopAvailableDate::create([
            'workshop_id' => $workshop->id,
            'date' => now()->addDay()->toDateString(),
            'is_active' => true,
            'notes' => 'Test date tomorrow',
        ]);

        // System auto-creates schedules when dates are added
        // But for test, we manually create them to verify
        WorkshopSlotSchedule::create([
            'workshop_id' => $workshop->id,
            'time_slot_id' => $slotMorning->id,
            'available_date_id' => $dateToday->id,
            'date' => $dateToday->date,
            'max_capacity' => $slotMorning->max_capacity,
            'booked_count' => 0,
            'status' => 'available',
        ]);

        WorkshopSlotSchedule::create([
            'workshop_id' => $workshop->id,
            'time_slot_id' => $slotAfternoon->id,
            'available_date_id' => $dateToday->id,
            'date' => $dateToday->date,
            'max_capacity' => $slotAfternoon->max_capacity,
            'booked_count' => 0,
            'status' => 'available',
        ]);

        WorkshopSlotSchedule::create([
            'workshop_id' => $workshop->id,
            'time_slot_id' => $slotMorning->id,
            'available_date_id' => $dateTomorrow->id,
            'date' => $dateTomorrow->date,
            'max_capacity' => $slotMorning->max_capacity,
            'booked_count' => 0,
            'status' => 'available',
        ]);

        WorkshopSlotSchedule::create([
            'workshop_id' => $workshop->id,
            'time_slot_id' => $slotAfternoon->id,
            'available_date_id' => $dateTomorrow->id,
            'date' => $dateTomorrow->date,
            'max_capacity' => $slotAfternoon->max_capacity,
            'booked_count' => 0,
            'status' => 'available',
        ]);

        echo "✓ Workshop test data created successfully\n";
    }
}
