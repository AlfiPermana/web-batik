<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\CustomerProfile;
use App\Models\Address;

$user = User::where('role', 'customer')->first();

if (!$user) {
    echo "❌ User customer tidak ditemukan!\n";
    exit;
}

echo "=== SETTING UP PROFILE FOR USER: " . $user->name . " ===\n";

echo "\n📋 Creating Customer Profile...\n";

// Create or update customer profile
$profile = $user->customerProfile ?? new CustomerProfile();
$profile->user_id = $user->id;
$profile->phone_number = '085728961516';
$profile->address = 'TAYEM';
$profile->city = 'Cilacap';
$profile->province = 'Jawa Tengah';
$profile->postal_code = '53000';
$profile->save();

echo "✓ Customer Profile Created\n";
echo "  📱 Phone: " . $profile->phone_number . "\n";
echo "  🏠 Address: " . $profile->address . "\n";

echo "\n📍 Creating Saved Addresses...\n";

$addresses_data = [
    [
        'street' => 'Jl. Diponegoro No. 123',
        'city' => 'Cilacap',
        'province' => 'Jawa Tengah',
        'postal_code' => '53000',
        'full_address' => 'Jl. Diponegoro No. 123, Cilacap, Jawa Tengah 53000'
    ],
    [
        'street' => 'Jl. Sukarno Hatta No. 45',
        'city' => 'Banyumas',
        'province' => 'Jawa Tengah',
        'postal_code' => '53100',
        'full_address' => 'Jl. Sukarno Hatta No. 45, Banyumas, Jawa Tengah 53100'
    ],
    [
        'street' => 'TAYEM',
        'city' => 'Cilacap',
        'province' => 'Jawa Tengah',
        'postal_code' => '53000',
        'full_address' => 'TAYEM, Cilacap, Jawa Tengah 53000'
    ]
];

foreach ($addresses_data as $data) {
    $address = Address::create([
        'user_id' => $user->id,
        'street' => $data['street'],
        'city' => $data['city'],
        'province' => $data['province'],
        'postal_code' => $data['postal_code'],
        'full_address' => $data['full_address']
    ]);
    echo "✓ " . $address->full_address . "\n";
}

echo "\n✅ VERIFICATION\n";
$user->refresh();
echo "  User: " . $user->name . " (" . $user->email . ")\n";
echo "  Phone: " . $user->customerProfile->phone_number . "\n";
echo "  Total Addresses: " . $user->addresses()->count() . "\n";
?>
