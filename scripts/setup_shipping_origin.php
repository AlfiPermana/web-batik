<?php

/**
 * Setup Origin Configuration for Shipping
 * Script ini akan membantu menemukan dan mengatur origin district ID yang benar
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

echo "🚚 SHIPPING ORIGIN CONFIGURATION\n";
echo "===============================\n\n";

// Test 1: Check current configuration
echo "📋 Current Configuration:\n";
echo "-------------------------\n";

$currentOriginDistrictId = config('services.raja_ongkir.origin_district_id');
$currentOriginCityId = config('services.raja_ongkir.origin_city_id');

echo "Origin District ID: " . ($currentOriginDistrictId ?? 'NOT SET') . "\n";
echo "Origin City ID: " . ($currentOriginCityId ?? 'NOT SET') . "\n\n";

// Test 2: Find Purwokerto/Banyumas districts
echo "📋 Finding Origin District IDs:\n";
echo "------------------------------\n";

$apiKey = config('services.raja_ongkir.api_key');
$provinceUrl = config('services.raja_ongkir.province_url');
$cityUrl = config('services.raja_ongkir.city_url');
$districtUrl = config('services.raja_ongkir.district_url');

if (empty($apiKey)) {
    echo "❌ API Key not configured!\n";
    exit(1);
}

try {
    // Get all provinces
    $provinceResponse = \Illuminate\Support\Facades\Http::withHeaders(['key' => $apiKey])
        ->timeout(10)->get($provinceUrl);
    
    if (!$provinceResponse->successful()) {
        echo "❌ Failed to load provinces\n";
        exit(1);
    }
    
    $provinces = $provinceResponse->json('data', []);
    
    // Find Jawa Tengah
    $jawaTengah = null;
    foreach ($provinces as $province) {
        if (stripos($province['name'], 'jawa tengah') !== false) {
            $jawaTengah = $province;
            break;
        }
    }
    
    if (!$jawaTengah) {
        echo "❌ Jawa Tengah province not found\n";
        exit(1);
    }
    
    echo "✅ Found Jawa Tengah: {$jawaTengah['name']} (ID: {$jawaTengah['id']})\n";
    
    // Get cities in Jawa Tengah
    $cityResponse = \Illuminate\Support\Facades\Http::withHeaders(['key' => $apiKey])
        ->timeout(10)->get($cityUrl . '/' . $jawaTengah['id']);
    
    if (!$cityResponse->successful()) {
        echo "❌ Failed to load cities\n";
        exit(1);
    }
    
    $cities = $cityResponse->json('data', []);
    
    // Find Purwokerto and Banyumas
    $targetCities = [];
    foreach ($cities as $city) {
        $cityName = strtolower($city['name']);
        if (stripos($cityName, 'purwokerto') !== false || stripos($cityName, 'banyumas') !== false) {
            $targetCities[] = $city;
        }
    }
    
    echo "✅ Found " . count($targetCities) . " target cities:\n";
    foreach ($targetCities as $city) {
        echo "   - {$city['name']} (ID: {$city['id']})\n";
    }
    
    // Get districts for each target city
    $allDistricts = [];
    foreach ($targetCities as $city) {
        $districtResponse = \Illuminate\Support\Facades\Http::withHeaders(['key' => $apiKey])
            ->timeout(10)->get($districtUrl . '/' . $city['id']);
        
        if ($districtResponse->successful()) {
            $districts = $districtResponse->json('data', []);
            foreach ($districts as $district) {
                $districtName = strtolower($district['name']);
                
                // Look for specific districts
                if (stripos($districtName, 'purwokerto utara') !== false || 
                    stripos($districtName, 'purwokerto selatan') !== false ||
                    stripos($districtName, 'purwokerto timur') !== false ||
                    stripos($districtName, 'purwokerto barat') !== false ||
                    stripos($districtName, 'banyumas') !== false) {
                    
                    $allDistricts[] = [
                        'city_name' => $city['name'],
                        'city_id' => $city['id'],
                        'district_name' => $district['name'],
                        'district_id' => $district['id']
                    ];
                    
                    echo "   ✅ {$district['name']} (ID: {$district['id']})\n";
                }
            }
        }
    }
    
    echo "\n🎯 RECOMMENDED ORIGIN DISTRICTS:\n";
    echo "================================\n";
    
    foreach ($allDistricts as $district) {
        echo "City: {$district['city_name']} (ID: {$district['city_id']})\n";
        echo "District: {$district['district_name']} (ID: {$district['district_id']})\n";
        echo "Recommended for: {$district['district_name']}\n\n";
    }
    
    // Test 3: Generate .env configuration
    echo "📝 .ENV CONFIGURATION UPDATES:\n";
    echo "================================\n";
    
    echo "# Add these lines to your .env file:\n\n";
    
    // Use first Purwokerto district as default
    $recommendedDistrict = null;
    foreach ($allDistricts as $district) {
        if (stripos($district['district_name'], 'purwokerto') !== false) {
            $recommendedDistrict = $district;
            break;
        }
    }
    
    if ($recommendedDistrict) {
        echo "SHIPPING_ORIGIN_DISTRICT_ID={$recommendedDistrict['district_id']}\n";
        echo "SHIPPING_ORIGIN_CITY_ID={$recommendedDistrict['city_id']}\n\n";
        
        echo "# This will set origin to: {$recommendedDistrict['district_name']}, {$recommendedDistrict['city_name']}\n";
    }
    
    // Test 4: Test shipping calculation with recommended origin
    if ($recommendedDistrict) {
        echo "\n🧪 TESTING SHIPPING CALCULATION:\n";
        echo "==================================\n";
        
        $testDestinationDistrictId = '6134'; // Purwokerto Utara (test destination)
        $calculateCostUrl = config('services.raja_ongkir.calculate_cost_url');
        
        echo "Testing: {$recommendedDistrict['district_name']} → Purwokerto Utara\n";
        
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Key' => $apiKey,
            'Accept' => 'application/json',
        ])->asForm()->timeout(15)->post($calculateCostUrl, [
            'origin' => (int) $recommendedDistrict['district_id'],
            'destination' => (int) $testDestinationDistrictId,
            'weight' => 1000,
            'courier' => 'jne',
        ]);
        
        if ($response->successful()) {
            $data = $response->json();
            echo "✅ API Response Status: " . $response->status() . "\n";
            echo "✅ Success: " . ($data['success'] ?? 'N/A') . "\n";
            
            if (isset($data['data']) && count($data['data']) > 0) {
                echo "✅ Shipping Options Found: " . count($data['data']) . "\n";
                $firstOption = $data['data'][0];
                echo "✅ Sample Option: {$firstOption['name']} - {$firstOption['service']} (Rp " . number_format($firstOption['cost'], 0, ',', '.') . ")\n";
            } else {
                echo "❌ No shipping options returned\n";
            }
        } else {
            echo "❌ API Test Failed: " . $response->status() . "\n";
            echo "Response: " . $response->body() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n🎉 NEXT STEPS:\n";
echo "================\n";
echo "1. Copy the recommended .env configuration above\n";
echo "2. Add it to your .env file\n";
echo "3. Run: php artisan config:cache\n";
echo "4. Test checkout flow again\n\n";
