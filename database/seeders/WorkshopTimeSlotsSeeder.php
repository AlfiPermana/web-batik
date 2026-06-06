<?php

namespace Database\Seeders;

use App\Models\Workshop;
use App\Models\WorkshopTimeSlot;
use App\Models\WorkshopSlotInstance;
use Illuminate\Database\Seeder;

class WorkshopTimeSlotsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing workshops
        $workshops = Workshop::all();

        foreach ($workshops as $workshop) {
            // Delete existing slots for this workshop
            WorkshopTimeSlot::where('workshop_id', $workshop->id)->delete();

            // Create time slots
            $slots = [
                [
                    'name' => 'Slot 1: Pagi',
                    'start_time' => '10:00:00',
                    'end_time' => '13:00:00',
                    'max_capacity' => 30,
                    'order' => 1,
                ],
                [
                    'name' => 'Slot 2: Siang',
                    'start_time' => '14:00:00',
                    'end_time' => '17:00:00',
                    'max_capacity' => 30,
                    'order' => 2,
                ],
                [
                    'name' => 'Slot 3: Malam',
                    'start_time' => '18:00:00',
                    'end_time' => '21:00:00',
                    'max_capacity' => 25,
                    'order' => 3,
                ],
            ];

            $createdSlots = [];
            foreach ($slots as $slotData) {
                $slot = WorkshopTimeSlot::create([
                    'workshop_id' => $workshop->id,
                    'name' => $slotData['name'],
                    'start_time' => $slotData['start_time'],
                    'end_time' => $slotData['end_time'],
                    'max_capacity' => $slotData['max_capacity'],
                    'order' => $slotData['order'],
                    'is_active' => true,
                ]);
                $createdSlots[] = $slot;
            }

            // Create slot instances for next 14 days
            for ($day = 0; $day < 14; $day++) {
                $date = now()->addDays($day)->format('Y-m-d');

                foreach ($createdSlots as $slot) {
                    // Skip if instance already exists
                    $existing = WorkshopSlotInstance::where([
                        'workshop_id' => $workshop->id,
                        'time_slot_id' => $slot->id,
                        'date' => $date,
                    ])->first();

                    if (!$existing) {
                        WorkshopSlotInstance::create([
                            'workshop_id' => $workshop->id,
                            'time_slot_id' => $slot->id,
                            'date' => $date,
                            'available_slots' => $slot->max_capacity,
                            'booked_slots' => 0,
                            'status' => 'available',
                        ]);
                    }
                }
            }

            $this->command->info("✓ Created time slots for workshop: {$workshop->title}");
        }
    }
}
