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
        Schema::create('workshop_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_id')->constrained('workshop')->onDelete('cascade');
            
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            
            $table->integer('capacity')->default(30);
            $table->integer('current_bookings')->default(0);
            
            $table->boolean('is_active')->default(true);
            $table->boolean('is_cancelled')->default(false);
            $table->string('cancellation_reason')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('workshop_id');
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_dates');
    }
};
