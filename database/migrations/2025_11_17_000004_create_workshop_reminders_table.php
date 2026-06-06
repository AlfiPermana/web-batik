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
        Schema::create('workshop_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('workshop_bookings')->onDelete('cascade');
            $table->foreignId('workshop_date_id')->constrained('workshop_dates')->onDelete('cascade');
            
            $table->string('type'); // booking_confirmed, 7_days_before, 1_day_before, day_of, post_workshop
            $table->string('delivery_method'); // email, whatsapp, both
            
            $table->string('status')->default('pending'); // pending, sent, failed
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            
            $table->timestamp('scheduled_for')->useCurrent();
            
            $table->timestamps();
            
            // Indexes
            $table->index('booking_id');
            $table->index('workshop_date_id');
            $table->index('status');
            $table->index('scheduled_for');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_reminders');
    }
};
