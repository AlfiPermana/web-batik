<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\CustomerProfile;
use App\Models\Address;

$customers = User::where('role', 'customer')->get();

if ($customers->isEmpty()) {
    echo "❌ Tidak ada user dengan role customer!\n";
    exit;
}

echo "=== SETUP PROFILE & ADDRESSES UNTUK SEMUA CUSTOMER ===\n";
echo "Total customers: " . $customers->count() . "\n\n";

foreach ($customers as $user) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📝 User: " . $user->name . " (" . $user->email . ")\n";
    
    // Create or update customer profile
    $profile = $user->customerProfile ?? new CustomerProfile();
    $profile->user_id = $user->id;
    $profile->phone_number = '085728961516';
    $profile->address = 'TAYEM';
    $profile->city = 'Cilacap';
    $profile->province = 'Jawa Tengah';
    $profile->postal_code = '53000';
    $profile->save();
    
    echo "  ✓ Profile created\n";
    echo "    📱 Phone: " . $profile->phone_number . "\n";

    // Delete existing addresses for clean setup
    $user->addresses()->delete();
    
    $addresses_data = [
        [
            'label' => 'Rumah',
            'full_name' => $user->name,
            'phone_number' => '085728961516',
            'province' => 'Jawa Tengah',
            'city' => 'Cilacap',
            'district' => 'Cilacap',
            'subdistrict' => 'Cilacap',
            'address' => 'Jl. Diponegoro No. 123',
            'postal_code' => '53000'
        ],
        [
            'label' => 'Kantor',
            'full_name' => $user->name,
            'phone_number' => '085728961516',
            'province' => 'Jawa Tengah',
            'city' => 'Banyumas',
            'district' => 'Banyumas',
            'subdistrict' => 'Banyumas',
            'address' => 'Jl. Sukarno Hatta No. 45',
            'postal_code' => '53100'
        ],
        [
            'label' => 'Profil',
            'full_name' => $user->name,
            'phone_number' => '085728961516',
            'province' => 'Jawa Tengah',
            'city' => 'Cilacap',
            'district' => 'Cilacap',
            'subdistrict' => 'Cilacap',
            'address' => 'TAYEM',
            'postal_code' => '53000'
        ]
    ];

    echo "  ✓ Addresses created:\n";
    foreach ($addresses_data as $data) {
        $address = Address::create([
            'user_id' => $user->id,
            'label' => $data['label'],
            'full_name' => $data['full_name'],
            'phone_number' => $data['phone_number'],
            'province' => $data['province'],
            'city' => $data['city'],
            'district' => $data['district'],
            'subdistrict' => $data['subdistrict'],
            'address' => $data['address'],
            'postal_code' => $data['postal_code']
        ]);
        echo "    - [" . $address->label . "] " . $address->address . ", " . $address->city . "\n";
    }
    echo "\n";
}

echo "✅ SETUP SELESAI!\n";
echo "\nVerifikasi:\n";
$customers->fresh();
foreach (User::where('role', 'customer')->get() as $user) {
    echo "  " . $user->name . ": " . $user->addresses()->count() . " alamat, phone: " . $user->customerProfile->phone_number . "\n";
}
?>
