<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaOngkirService
{
    private $apiKey;
    private $provinceUrl;
    private $cityUrl;
    private $districtUrl;
    private $subdistrictUrl;
    private $calculateCostUrl;

    public function __construct()
    {
        $this->apiKey = config('services.raja_ongkir.api_key');
        $this->provinceUrl = config('services.raja_ongkir.province_url');
        $this->cityUrl = config('services.raja_ongkir.city_url');
        $this->districtUrl = config('services.raja_ongkir.district_url');
        $this->subdistrictUrl = config('services.raja_ongkir.subdistrict_url');
        $this->calculateCostUrl = config('services.raja_ongkir.calculate_cost_url');
    }

    /**
     * Get all provinces
     */
    public function getProvinces()
    {
        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey,
            ])->timeout(10)->get($this->provinceUrl);

            if ($response->successful()) {
                return $response->json('data') ?? [];
            }

            Log::warning('Raja Ongkir getProvinces failed', ['status' => $response->status()]);
            return [];
        } catch (\Exception $e) {
            Log::error('Raja Ongkir getProvinces error', ['message' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Format provinces for display
     */
    public function formatProvinces($provinces)
    {
        return array_map(fn($item) => [
            'id' => $item['id'],
            'name' => $item['name'],
        ], $provinces);
    }

    /**
     * Get cities by province ID
     */
    public function getCities($provinceId)
    {
        try {
            $url = str_replace('{province_id}', $provinceId, $this->cityUrl);
            
            $response = Http::withHeaders([
                'key' => $this->apiKey,
            ])->timeout(10)->get($url);

            if ($response->successful()) {
                return $response->json('data') ?? [];
            }

            Log::warning('Raja Ongkir getCities failed', ['status' => $response->status(), 'province_id' => $provinceId]);
            return [];
        } catch (\Exception $e) {
            Log::error('Raja Ongkir getCities error', ['message' => $e->getMessage(), 'province_id' => $provinceId]);
            return [];
        }
    }

    /**
     * Format cities for display
     */
    public function formatCities($cities)
    {
        return array_map(fn($item) => [
            'id' => $item['id'],
            'name' => $item['name'],
        ], $cities);
    }

    /**
     * Get districts by city ID
     */
    public function getDistricts($cityId)
    {
        try {
            $url = str_replace('{city_id}', $cityId, $this->districtUrl);
            
            $response = Http::withHeaders([
                'key' => $this->apiKey,
            ])->timeout(10)->get($url);

            if ($response->successful()) {
                return $response->json('data') ?? [];
            }

            Log::warning('Raja Ongkir getDistricts failed', ['status' => $response->status(), 'city_id' => $cityId]);
            return [];
        } catch (\Exception $e) {
            Log::error('Raja Ongkir getDistricts error', ['message' => $e->getMessage(), 'city_id' => $cityId]);
            return [];
        }
    }

    /**
     * Format districts for display
     */
    public function formatDistricts($districts)
    {
        return array_map(fn($item) => [
            'id' => $item['id'],
            'name' => $item['name'],
        ], $districts);
    }

    /**
     * Get subdistricts by district ID
     */
    public function getSubdistricts($districtId)
    {
        try {
            $url = str_replace('{district_id}', $districtId, $this->subdistrictUrl);
            
            $response = Http::withHeaders([
                'key' => $this->apiKey,
            ])->timeout(10)->get($url);

            if ($response->successful()) {
                return $response->json('data') ?? [];
            }

            Log::warning('Raja Ongkir getSubdistricts failed', ['status' => $response->status(), 'district_id' => $districtId]);
            return [];
        } catch (\Exception $e) {
            Log::error('Raja Ongkir getSubdistricts error', ['message' => $e->getMessage(), 'district_id' => $districtId]);
            return [];
        }
    }

    /**
     * Format subdistricts for display
     */
    public function formatSubdistricts($subdistricts)
    {
        return array_map(fn($item) => [
            'id' => $item['id'],
            'name' => $item['name'],
        ], $subdistricts);
    }

    /**
     * Calculate shipping cost
     */
    public function getShippingCost($originDistrictId, $destinationDistrictId, $weight, $couriers = [])
    {
        try {
            $payload = [
                'origin' => $originDistrictId,
                'destination' => $destinationDistrictId,
                'weight' => $weight,
            ];

            // Add couriers (optional)
            // NOTE: API expects 'courier' (singular), not 'couriers'
            // If not provided, let API return what is available.
            if (!empty($couriers)) {
                $payload['courier'] = implode(':', $couriers);
            }


            Log::info('Raja Ongkir API Request', [
                'url' => $this->calculateCostUrl,
                'payload' => $payload
            ]);

            $response = Http::withHeaders([
                'key' => $this->apiKey,
            ])->timeout(10)->asForm()->post($this->calculateCostUrl, $payload);

            Log::info('Raja Ongkir API Response', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            if ($response->successful()) {
                $data = $response->json('data') ?? [];
                Log::info('Raja Ongkir Shipping Data Retrieved', [
                    'count' => count($data),
                    'data' => $data
                ]);
                return $data;
            }

            Log::warning('Raja Ongkir getShippingCost failed', [
                'status' => $response->status(),
                'response' => $response->json()
            ]);
            
            // Return fallback mock data saat API gagal atau limit exceeded
            Log::info('Using fallback mock shipping data due to API failure');
            return $this->getFallbackShippingData();
        } catch (\Exception $e) {
            Log::error('Raja Ongkir getShippingCost error', [
                'message' => $e->getMessage(),
                'origin' => $originDistrictId,
                'destination' => $destinationDistrictId,
                'weight' => $weight
            ]);
            
            // Return fallback mock data saat exception
            Log::info('Using fallback mock shipping data due to exception');
            return $this->getFallbackShippingData();
        }
    }

    /**
     * Get fallback/mock shipping data when API fails
     * Used when daily limit exceeded or API is down
     */
    private function getFallbackShippingData(): array
    {
        return [
            [
                'name' => 'Jalur Nugraha Ekakurir (JNE)',
                'code' => 'jne',
                'service' => 'REG',
                'description' => 'Layanan Reguler',
                'cost' => 69000,
                'etd' => '4-5 hari'
            ],
            [
                'name' => 'Citra Van Titipan Kilat (TIKI)',
                'code' => 'tiki',
                'service' => 'REG',
                'description' => 'Reguler Service',
                'cost' => 65000,
                'etd' => '2-3 hari'
            ],
            [
                'name' => 'POS Indonesia (POS)',
                'code' => 'pos',
                'service' => 'Pos Reguler',
                'description' => 'Layanan Reguler',
                'cost' => 60000,
                'etd' => '3-4 hari'
            ],
            [
                'name' => 'J&T Express',
                'code' => 'jnt',
                'service' => 'EZ',
                'description' => 'Express Zone',
                'cost' => 55000,
                'etd' => '2-3 hari'
            ],
            [
                'name' => 'SiCepat Express',
                'code' => 'sicepat',
                'service' => 'REG',
                'description' => 'Reguler',
                'cost' => 59000,
                'etd' => '2-3 hari'
            ],
        ];
    }

    /**
     * Format shipping costs for display
     * 
     * API returns flat array of service options with structure:
     * {
     *   "name": "Courier Name",
     *   "code": "courier_code",
     *   "service": "SERVICE_CODE",
     *   "description": "Service Description",
     *   "cost": 10000,
     *   "etd": "1 day"
     * }
     */
    public function formatShippingCosts($shippingData)
    {
        $formatted = [];

        foreach ($shippingData as $service) {
            $formatted[] = [
                'courier' => $service['code'] ?? 'unknown',
                'name' => $service['name'] ?? 'Unknown',
                'service' => $service['service'] ?? '',
                'description' => $service['description'] ?? '',
                'cost' => $service['cost'] ?? 0,
                'etd' => $service['etd'] ?? '',
                'display_name' => "{$service['name']} {$service['service']}",
            ];
        }

        return $formatted;
    }
}
