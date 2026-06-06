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
        Schema::create('workshop_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('workshop_bookings')->onDelete('cascade');
            
            $table->decimal('amount', 12, 2);
            $table->string('type'); // deposit, remaining, full
            $table->string('payment_method'); // bank_transfer, ewallet, credit_card
            $table->string('payment_status')->default('pending'); // pending, confirmed, failed, refunded
            
            $table->string('reference_number')->nullable();
            $table->string('proof_file_path')->nullable();
            
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('booking_id');
            $table->index('payment_status');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_payments');
    }
};
