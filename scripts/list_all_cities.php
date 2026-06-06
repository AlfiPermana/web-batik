<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$service = new \App\Services\RajaOngkirService();

// Get all cities from Jawa Tengah (province ID 8)
echo "=== DAFTAR KOTA DI JAWA TENGAH ===\n";
try {
    $cities = $service->getCities(8);
    $formattedCities = $service->formatCities($cities);
    
    echo "Total: " . count($formattedCities) . " kota\n\n";
    foreach ($formattedCities as $city) {
        echo "ID: {$city['id']} | Name: {$city['name']}\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
