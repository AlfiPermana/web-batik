<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Admin User Seeder with custom credentials support
 * 
 * Usage:
 *   php artisan db:seed --class=AdminUserSeeder --env=local
 * 
 * Or update environment variables:
 *   ADMIN_EMAIL=your@email.com
 *   ADMIN_PASSWORD=yourpassword
 *   ADMIN_NAME="Your Name"
 */
class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get credentials from config or use defaults
        $adminEmail = env('ADMIN_EMAIL', 'admin@batik.local');
        $adminPassword = env('ADMIN_PASSWORD', 'password123');
        $adminName = env('ADMIN_NAME', 'Admin');

        // Check if admin already exists
        if (User::where('email', $adminEmail)->exists()) {
            echo "⚠️  Admin user already exists: {$adminEmail}\n";
            return;
        }

        // Create admin user
        $admin = User::create([
            'name' => $adminName,
            'email' => $adminEmail,
            'role' => 'admin',
            'password' => Hash::make($adminPassword),
            'email_verified_at' => now(),
        ]);

        echo "✓ Admin user created:\n";
        echo "  Name: {$admin->name}\n";
        echo "  Email: {$admin->email}\n";
        echo "  Role: admin\n";
        echo "  Password: {$adminPassword}\n\n";

        // Create test customer user if CUSTOMER_EMAIL env exists
        $customerEmail = env('CUSTOMER_EMAIL');
        if ($customerEmail && !User::where('email', $customerEmail)->exists()) {
            $customerPassword = env('CUSTOMER_PASSWORD', 'customer123');
            $customerName = env('CUSTOMER_NAME', 'Test Customer');

            $customer = User::create([
                'name' => $customerName,
                'email' => $customerEmail,
                'role' => 'customer',
                'password' => Hash::make($customerPassword),
                'email_verified_at' => now(),
            ]);

            echo "✓ Test customer created:\n";
            echo "  Name: {$customer->name}\n";
            echo "  Email: {$customer->email}\n";
            echo "  Password: {$customerPassword}\n";
        }
    }
}

