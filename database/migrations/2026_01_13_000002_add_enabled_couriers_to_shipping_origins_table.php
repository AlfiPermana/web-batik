<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipping_origins', function (Blueprint $table) {
            if (!Schema::hasColumn('shipping_origins', 'enabled_couriers')) {
                $table->json('enabled_couriers')->nullable()->after('address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('shipping_origins', function (Blueprint $table) {
            if (Schema::hasColumn('shipping_origins', 'enabled_couriers')) {
                $table->dropColumn('enabled_couriers');
            }
        });
    }
};
