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
        // Create workshop_time_slots table
        Schema::create('workshop_time_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_id')->constrained('workshop')->onDelete('cascade');
            $table->string('name')->comment('e.g., Slot 1: Pagi');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('max_capacity')->default(30)->comment('max peserta per slot');
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0)->comment('Display order');
            $table->timestamps();
            $table->softDeletes();
        });

        // Create workshop_available_dates table
        Schema::create('workshop_available_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_id')->constrained('workshop')->onDelete('cascade');
            $table->date('date')->comment('Tanggal workshop tersedia');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Unique: satu tanggal per workshop
            $table->unique(['workshop_id', 'date'], 'wad_unique_workshop_date');
        });

        // Create workshop_slot_schedules table (kombinasi date + time_slot)
        Schema::create('workshop_slot_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_id')->constrained('workshop')->onDelete('cascade');
            $table->foreignId('time_slot_id')->constrained('workshop_time_slots')->onDelete('cascade');
            $table->foreignId('available_date_id')->constrained('workshop_available_dates')->onDelete('cascade');
            $table->date('date')->comment('Redundant tapi untuk query efficiency');
            $table->integer('max_capacity')->comment('Copy dari time_slot, bisa di-override');
            $table->integer('booked_count')->default(0)->comment('Total peserta yang sudah booking');
            $table->enum('status', ['available', 'on_book', 'fully_booked', 'cancelled'])->default('available');
            $table->boolean('is_cancelled')->default(false);
            $table->string('cancellation_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Unique: satu slot per date per workshop
            $table->unique(['workshop_id', 'time_slot_id', 'available_date_id'], 'wss_unique_slot_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_slot_schedules');
        Schema::dropIfExists('workshop_available_dates');
        Schema::dropIfExists('workshop_time_slots');
    }
};
