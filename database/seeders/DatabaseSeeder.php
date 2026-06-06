<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        echo "\n=== SEEDING DATABASE ===\n\n";

        // 1. Create admin and test users
        $this->call(AdminUserSeeder::class);

        // 2. Create workshop test data
        $this->call(WorkshopSlotSeeder::class);

        echo "\n=== SEEDING COMPLETE ===\n";
        echo "\n📚 Resources created:\n";
        echo "  - Admin user (admin@batik.local / password123)\n";
        echo "  - Test customer (customer@example.com / customer123)\n";
        echo "  - Test workshop (Batik Workshop 101)\n";
        echo "  - 2 time slots (Pagi 10-13, Sore 14-17)\n";
        echo "  - 2 available dates (today + tomorrow)\n";
        echo "  - 4 schedules (2 dates × 2 slots)\n";
        echo "\n📖 Documentation:\n";
        echo "  - ADMIN_LOGIN_GUIDE.md\n";
        echo "  - WORKSHOP_SLOTS_FINAL_IMPLEMENTATION.md\n";
        echo "  - WORKSHOP_IMPLEMENTATION_STATUS.md\n";
        echo "\n✅ Ready to use!\n\n";
    }
}
