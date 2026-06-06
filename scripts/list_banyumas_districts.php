<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$service = new \App\Services\RajaOngkirService();

// Get all districts from city 591 (Banyumas)
echo "=== DAFTAR DISTRICT DI BANYUMAS (CITY 591) ===\n";
try {
    $districts = $service->getDistricts(591);
    $formatted = $service->formatDistricts($districts);
    
    echo "Total: " . count($formatted) . " districts\n\n";
    foreach ($formatted as $district) {
        echo "ID: {$district['id']} | Name: {$district['name']}\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
