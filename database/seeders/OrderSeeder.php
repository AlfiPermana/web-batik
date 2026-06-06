<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a customer user (if exists)
        $customer = User::where('role', 'customer')->first();

        if ($customer) {
            // Create sample orders for the customer
            Order::create([
                'user_id' => $customer->id,
                'total' => 150000,
                'status' => 'delivered',
                'shipping_address' => 'Jl. Merdeka No. 123, Jakarta, 12345',
                'shipping_method' => 'JNE Regular',
                'payment_method' => 'Bank Transfer',
                'notes' => 'Pesanan telah diterima dalam kondisi baik.',
                'created_at' => now()->subDays(30),
                'updated_at' => now()->subDays(30),
            ]);

            Order::create([
                'user_id' => $customer->id,
                'total' => 275000,
                'status' => 'shipped',
                'shipping_address' => 'Jl. Merdeka No. 123, Jakarta, 12345',
                'shipping_method' => 'JNE Express',
                'payment_method' => 'Credit Card',
                'notes' => 'Pesanan dalam perjalanan ke alamat Anda.',
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(5),
            ]);

            Order::create([
                'user_id' => $customer->id,
                'total' => 450000,
                'status' => 'processing',
                'shipping_address' => 'Jl. Merdeka No. 123, Jakarta, 12345',
                'shipping_method' => 'JNE Regular',
                'payment_method' => 'E-Wallet',
                'notes' => null,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(1),
            ]);

            Order::create([
                'user_id' => $customer->id,
                'total' => 99000,
                'status' => 'pending',
                'shipping_address' => 'Jl. Merdeka No. 123, Jakarta, 12345',
                'shipping_method' => null,
                'payment_method' => null,
                'notes' => 'Menunggu konfirmasi pembayaran.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
