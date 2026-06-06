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
        Schema::table('workshop_bookings', function (Blueprint $table) {
            // Drop the old foreign key constraint
            $table->dropForeign(['workshop_date_id']);
        });

        // Rename column from workshop_date_id to workshop_available_date_id
        Schema::table('workshop_bookings', function (Blueprint $table) {
            $table->renameColumn('workshop_date_id', 'workshop_available_date_id');
        });

        // Add the new foreign key constraint
        Schema::table('workshop_bookings', function (Blueprint $table) {
            $table->foreign('workshop_available_date_id')
                ->references('id')
                ->on('workshop_available_dates')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshop_bookings', function (Blueprint $table) {
            // Drop the new foreign key constraint
            $table->dropForeign(['workshop_available_date_id']);
        });

        // Rename column back
        Schema::table('workshop_bookings', function (Blueprint $table) {
            $table->renameColumn('workshop_available_date_id', 'workshop_date_id');
        });

        // Add back the old foreign key constraint
        Schema::table('workshop_bookings', function (Blueprint $table) {
            $table->foreign('workshop_date_id')
                ->references('id')
                ->on('workshop_dates')
                ->onDelete('cascade');
        });
    }
};
