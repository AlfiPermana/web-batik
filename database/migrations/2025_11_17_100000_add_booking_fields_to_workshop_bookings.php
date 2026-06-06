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
            // Add new columns for direct booking without workshop_date_id
            $table->date('workshop_date')->nullable()->after('customer_phone');
            $table->string('workshop_name')->nullable()->after('workshop_date');
            $table->time('start_time')->nullable()->after('workshop_name');
            $table->time('end_time')->nullable()->after('start_time');
            $table->text('address')->nullable()->after('end_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshop_bookings', function (Blueprint $table) {
            $table->dropColumn(['workshop_date', 'workshop_name', 'start_time', 'end_time', 'address']);
        });
    }
};
