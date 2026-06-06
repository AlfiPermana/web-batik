<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WorkshopDateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create workshop dates for testing
        DB::table('workshop_dates')->insertOrIgnore([
            [
                'id' => 1,
                'workshop_id' => 1,
                'date' => Carbon::now()->addDays(7)->format('Y-m-d'), // 7 days from now
                'start_time' => '09:00:00',
                'end_time' => '12:00:00',
                'capacity' => 30,
                'current_bookings' => 0,
                'is_active' => true,
                'is_cancelled' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'workshop_id' => 1,
                'date' => Carbon::now()->addDays(14)->format('Y-m-d'), // 14 days from now
                'start_time' => '14:00:00',
                'end_time' => '17:00:00',
                'capacity' => 30,
                'current_bookings' => 0,
                'is_active' => true,
                'is_cancelled' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 3,
                'workshop_id' => 1,
                'date' => Carbon::now()->addDays(21)->format('Y-m-d'), // 21 days from now
                'start_time' => '10:00:00',
                'end_time' => '13:00:00',
                'capacity' => 30,
                'current_bookings' => 0,
                'is_active' => true,
                'is_cancelled' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        $this->command->info('Workshop dates seeded successfully!');
    }
}
