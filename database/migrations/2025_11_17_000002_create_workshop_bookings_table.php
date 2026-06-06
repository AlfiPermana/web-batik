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
        Schema::create('workshop_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('workshop_date_id')->constrained('workshop_dates')->onDelete('cascade');
            
            $table->string('booking_number')->unique();
            
            // Customer Information
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            
            // Booking Details
            $table->integer('num_participants');
            $table->decimal('total_price', 12, 2);
            $table->decimal('deposit_amount', 12, 2);
            $table->decimal('remaining_amount', 12, 2);
            
            // Status
            $table->string('status')->default('pending'); // pending, confirmed, cancelled, completed
            $table->string('payment_status')->default('pending'); // pending, deposit_paid, fully_paid
            
            // Additional Info
            $table->text('special_requests')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->json('reminders_sent')->nullable();
            
            // Completion & Cancellation
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('user_id');
            $table->index('workshop_date_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index('booking_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_bookings');
    }
};
