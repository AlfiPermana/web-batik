<?php
// Search for BOBOTSARI district
require __DIR__ . '/../vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$apiKey = env('SHIPPING_API_KEY');

// Get all cities first to find which city has BOBOTSARI
$citiesUrl = env('SHIPPING_CITY_URL');
$citiesUrl = str_replace('{province_id}', '33', $citiesUrl); // Jawa Tengah

echo "=== SEARCH BOBOTSARI ===\n";
echo "Getting cities in Jawa Tengah...\n\n";

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "key: $apiKey\r\n",
        'timeout' => 10,
    ]
]);

$response = @file_get_contents($citiesUrl, false, $context);
if ($response === false) {
    echo "❌ ERROR: Failed to get cities\n";
    exit;
}

$data = json_decode($response, true);
$cities = $data['data'] ?? [];

echo "Found " . count($cities) . " cities\n\n";

// Search each city for BOBOTSARI
foreach ($cities as $city) {
    $cityId = $city['id'];
    $cityName = $city['name'];
    
    $districtUrl = str_replace('{city_id}', $cityId, env('SHIPPING_DISTRICT_URL'));
    
    $response = @file_get_contents($districtUrl, false, $context);
    if ($response === false) continue;
    
    $data = json_decode($response, true);
    $districts = $data['data'] ?? [];
    
    foreach ($districts as $district) {
        if (strpos(strtoupper($district['name']), 'BOBOTS') !== false) {
            echo "✓ FOUND: {$district['name']}\n";
            echo "  - District ID: {$district['id']}\n";
            echo "  - City ID: {$cityId} ({$cityName})\n\n";
        }
    }
}
?>
