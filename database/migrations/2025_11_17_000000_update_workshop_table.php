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
        Schema::table('workshop', function (Blueprint $table) {
            // Add new columns for workshop system
            if (!Schema::hasColumn('workshop', 'capacity')) {
                $table->integer('capacity')->default(30)->after('description');
            }
            if (!Schema::hasColumn('workshop', 'location')) {
                $table->string('location')->nullable()->after('capacity');
            }
            if (!Schema::hasColumn('workshop', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('location');
            }
            if (!Schema::hasColumn('workshop', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->after('is_active');
            }
            if (!Schema::hasColumn('workshop', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshop', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropForeign(['created_by']);
            $table->dropColumn([
                'capacity',
                'location',
                'is_active',
                'created_by',
            ]);
        });
    }
};
