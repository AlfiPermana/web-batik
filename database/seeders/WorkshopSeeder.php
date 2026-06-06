<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Workshop;
use App\Models\WorkshopTimeSlot;
use App\Models\WorkshopAvailableDate;
use App\Models\WorkshopSlotSchedule;

class WorkshopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create workshops
        $workshop1 = Workshop::firstOrCreate(
            ['id' => 1],
            [
                'title' => 'Mbatik Canting',
                'amount' => 500000,
                'description' => 'Pelajari teknik membatik canting tradisional',
                'location' => 'Cilacap, Jawa Tengah',
                'capacity' => 0,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        $workshop2 = Workshop::firstOrCreate(
            ['id' => 2],
            [
                'title' => 'Workshop Batik Advanced',
                'amount' => 350000,
                'description' => 'Teknik lanjutan dalam pembuatan batik',
                'location' => 'Cilacap, Jawa Tengah',
                'capacity' => 0,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        // Create time slots for workshop 1
        $slot1 = WorkshopTimeSlot::firstOrCreate(
            [
                'workshop_id' => 1,
                'start_time' => '09:00:00',
                'end_time' => '12:00:00',
            ],
            [
                'name' => 'Pagi (09:00 - 12:00)',
                'max_capacity' => 20,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        $slot2 = WorkshopTimeSlot::firstOrCreate(
            [
                'workshop_id' => 1,
                'start_time' => '13:00:00',
                'end_time' => '16:00:00',
            ],
            [
                'name' => 'Siang (13:00 - 16:00)',
                'max_capacity' => 20,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        // Create time slots for workshop 2
        $slot3 = WorkshopTimeSlot::firstOrCreate(
            [
                'workshop_id' => 2,
                'start_time' => '10:00:00',
                'end_time' => '14:00:00',
            ],
            [
                'name' => 'Siang (10:00 - 14:00)',
                'max_capacity' => 15,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        // Create available dates for workshop 1 (next 30 days)
        for ($i = 1; $i <= 5; $i++) {
            $date = Carbon::now()->addDays($i);
            
            $availableDate = WorkshopAvailableDate::firstOrCreate(
                [
                    'workshop_id' => 1,
                    'date' => $date->format('Y-m-d'),
                ],
                [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );

            // Create schedules for each time slot on this date
            foreach ([$slot1, $slot2] as $slot) {
                WorkshopSlotSchedule::firstOrCreate(
                    [
                        'workshop_id' => 1,
                        'time_slot_id' => $slot->id,
                        'available_date_id' => $availableDate->id,
                        'date' => $date->format('Y-m-d'),
                    ],
                    [
                        'max_capacity' => $slot->max_capacity,
                        'booked_count' => 0,
                        'status' => 'available',
                        'is_cancelled' => false,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                );
            }
        }

        // Create available dates for workshop 2
        for ($i = 2; $i <= 6; $i++) {
            $date = Carbon::now()->addDays($i);
            
            $availableDate = WorkshopAvailableDate::firstOrCreate(
                [
                    'workshop_id' => 2,
                    'date' => $date->format('Y-m-d'),
                ],
                [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );

            // Create schedule for time slot
            WorkshopSlotSchedule::firstOrCreate(
                [
                    'workshop_id' => 2,
                    'time_slot_id' => $slot3->id,
                    'available_date_id' => $availableDate->id,
                    'date' => $date->format('Y-m-d'),
                ],
                [
                    'max_capacity' => $slot3->max_capacity,
                    'booked_count' => 0,
                    'status' => 'available',
                    'is_cancelled' => false,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
        }

        $this->command->info('✅ Workshops, time slots, and available dates seeded successfully!');
    }
}
