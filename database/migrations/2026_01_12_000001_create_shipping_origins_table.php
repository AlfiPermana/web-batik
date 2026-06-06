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
        Schema::create('shipping_origins', function (Blueprint $table) {
            $table->id();

            // RajaOngkir/Komerce identifiers
            $table->string('origin_city_id')->nullable();
            $table->string('origin_city_name')->nullable();
            $table->string('origin_district_id')->nullable();
            $table->string('origin_district_name')->nullable();

            // Optional admin sender address (for display / future use)
            $table->text('address')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_origins');
    }
};
