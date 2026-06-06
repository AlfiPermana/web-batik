<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ubah enum untuk menambah 'PAID'
        DB::statement("ALTER TABLE `workshop_slot_schedules` MODIFY COLUMN `status` ENUM('available', 'on_book', 'fully_booked', 'cancelled', 'PAID') DEFAULT 'available'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `workshop_slot_schedules` MODIFY COLUMN `status` ENUM('available', 'on_book', 'fully_booked', 'cancelled') DEFAULT 'available'");
    }
};
