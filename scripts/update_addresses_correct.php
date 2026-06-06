<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Address;

echo "=== UPDATE ALAMAT YANG BENAR ===\n\n";

// Cari user rafli (ID: 2)
$user = User::find(2);
if (!$user) {
    echo "User rafli tidak ditemukan\n";
    exit;
}

echo "User: {$user->name} (ID: {$user->id})\n";

// Hapus semua alamat lama user ini
$oldAddresses = $user->addresses()->get();
echo "Menghapus " . $oldAddresses->count() . " alamat lama...\n";
foreach ($oldAddresses as $old) {
    $old->delete();
}

// Alamat baru 1: Test - Jatilawang (Default)
$address1 = new Address();
$address1->user_id = $user->id;
$address1->label = 'Test - Jatilawang';
$address1->full_name = 'Test Jatilawang';
$address1->phone_number = '081234567890';
$address1->province = 'Jawa Tengah';
$address1->city = 'Banyumas';
$address1->district = '6138'; // Simpan district ID langsung di sini
$address1->subdistrict = '';
$address1->address = 'Jl. Test No. 1, Jatilawang';
$address1->postal_code = '53172';
$address1->is_default = true;
$address1->save();

echo "✅ Alamat 1 dibuat: Test - Jatilawang (District ID: 6138)\n";

// Alamat baru 2: Test - Ajibarang
$address2 = new Address();
$address2->user_id = $user->id;
$address2->label = 'Test - Ajibarang';
$address2->full_name = 'Test Ajibarang';
$address2->phone_number = '081234567891';
$address2->province = 'Jawa Tengah';
$address2->city = 'Banyumas';
$address2->district = '6135'; // Simpan district ID langsung di sini
$address2->subdistrict = '';
$address2->address = 'Jl. Test No. 2, Ajibarang';
$address2->postal_code = '53173';
$address2->is_default = false;
$address2->save();

echo "✅ Alamat 2 dibuat: Test - Ajibarang (District ID: 6135)\n";

echo "\n=== VERIFIKASI ===\n";
$addresses = $user->addresses()->get();
foreach ($addresses as $addr) {
    echo "Alamat ID: {$addr->id}\n";
    echo "  Label: {$addr->label}\n";
    echo "  District: {$addr->district}\n";
    echo "  City: {$addr->city}\n";
    echo "  Default: " . ($addr->is_default ? 'Yes' : 'No') . "\n";
    echo "  ---\n";
}

echo "\nOrigin District ID: " . config('services.raja_ongkir.origin_district_id') . "\n";
echo "\nSEKARANG COBA REFRESH HALAMAN CHECKOUT ANDA!\n";
echo "District 6138 dan 6135 berbeda dengan origin 6134, jadi harusnya dapat banyak opsi pengiriman.\n";
