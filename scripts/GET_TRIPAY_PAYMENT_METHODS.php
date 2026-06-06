<?php
/**
 * Get Available Payment Methods from Tripay Merchant Account
 * 
 * Run in terminal:
 * php artisan tinker
 * 
 * Then paste:
 * include 'scripts/GET_TRIPAY_PAYMENT_METHODS.php';
 */

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

$apiKey = env('TRIPAY_API_KEY');
$merchantCode = env('TRIPAY_MERCHANT_CODE');

echo "\n=== CHECK AVAILABLE PAYMENT METHODS IN TRIPAY ===\n";
echo "Merchant: {$merchantCode}\n";
echo "API Key: " . substr($apiKey, 0, 15) . "...\n\n";

try {
    // Endpoint to get available methods
    $url = 'https://tripay.co.id/api-sandbox/merchant/payment-method';
    
    echo "Calling: {$url}\n\n";
    
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apiKey,
    ])->get($url);

    echo "Status: " . $response->status() . "\n\n";
    
    $data = $response->json();
    
    if ($response->successful() && isset($data['data'])) {
        echo "✅ SUCCESS - Available Methods:\n\n";
        
        $availableCodes = [];
        foreach ($data['data'] as $method) {
            $code = $method['code'] ?? 'N/A';
            $name = $method['name'] ?? 'N/A';
            $availableCodes[] = $code;
            
            echo "• {$code}\n";
            echo "  Name: {$name}\n";
            echo "  Type: " . ($method['type'] ?? 'N/A') . "\n";
            echo "\n";
        }
        
        // Check for specific methods
        echo "=== SUMMARY ===\n";
        echo (in_array('OVOBANK', $availableCodes) ? "✅" : "❌") . " OVOBANK\n";
        echo (in_array('DANACASH', $availableCodes) ? "✅" : "❌") . " DANACASH\n";
        echo (in_array('QRIS', $availableCodes) ? "✅" : "❌") . " QRIS\n";
        echo (in_array('BCAVA', $availableCodes) ? "✅" : "❌") . " BCAVA\n";
        
    } else {
        echo "❌ FAILED\n";
        echo "Response: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
    }
    
} catch (\Exception $e) {
    echo "❌ EXCEPTION\n";
    echo $e->getMessage() . "\n";
}
