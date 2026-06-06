<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('workshop_bookings', function (Blueprint $table) {
            // Add new schedule_id column if not exists
            if (!Schema::hasColumn('workshop_bookings', 'workshop_slot_schedule_id')) {
                $table->unsignedBigInteger('workshop_slot_schedule_id')->nullable()->after('workshop_date_id');
                $table->foreign('workshop_slot_schedule_id')
                    ->references('id')
                    ->on('workshop_slot_schedules')
                    ->onDelete('cascade');
            }

            // Keep old column for backward compatibility (data migration)
            // It will be removed after successful data migration
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshop_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('workshop_bookings', 'workshop_slot_schedule_id')) {
                $table->dropForeign(['workshop_slot_schedule_id']);
                $table->dropColumn('workshop_slot_schedule_id');
            }
        });
    }
};
