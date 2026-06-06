<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Address extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'label',
        'full_name',
        'phone_number',
        'province',
        'city',
        'district',
        'subdistrict',
        'address',
        'postal_code',
        'is_default',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Get the user that owns the address.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the formatted full address
     */
    public function getFormattedAddressAttribute(): string
    {
        return "{$this->address}, {$this->subdistrictDisplay}, {$this->districtDisplay}, {$this->cityDisplay}, {$this->provinceDisplay} {$this->postal_code}";
    }

    public function getProvinceDisplayAttribute(): string
    {
        return $this->resolveProvinceName($this->province);
    }

    public function getCityDisplayAttribute(): string
    {
        return $this->resolveCityName($this->city, $this->province);
    }

    public function getDistrictDisplayAttribute(): string
    {
        return $this->resolveDistrictName($this->district, $this->city);
    }

    public function getSubdistrictDisplayAttribute(): string
    {
        return $this->resolveSubdistrictName($this->subdistrict, $this->district);
    }

    private function resolveProvinceName($province): string
    {
        if (empty($province) || !is_numeric($province)) {
            return (string) $province;
        }

        $cacheKey = 'ro:provinces:v1';

        // Jangan cache hasil kosong (bisa bikin ID tampil terus walau API balik normal)
        $provinces = Cache::get($cacheKey);
        if (!is_array($provinces) || empty($provinces)) {
            $provinces = [];
            try {
                $apiKey = config('services.raja_ongkir.api_key');
                $url = config('services.raja_ongkir.province_url');

                if (!empty($apiKey) && !empty($url)) {
                    $response = Http::withHeaders(['key' => $apiKey])->timeout(10)->get($url);
                    if ($response->successful()) {
                        $data = $response->json('data') ?? [];
                        if (!empty($data)) {
                            $provinces = $data;
                            Cache::put($cacheKey, $provinces, now()->addDays(14));
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning('resolveProvinceName failed', ['message' => $e->getMessage()]);
            }
        }

        $match = collect($provinces)->firstWhere('id', (string) $province);
        return (string) ($match['name'] ?? $province);
    }

    private function resolveCityName($city, $province): string
    {
        if (empty($city) || !is_numeric($city)) {
            return (string) $city;
        }
        if (empty($province) || !is_numeric($province)) {
            return (string) $city;
        }

        $cacheKey = 'ro:cities:v1:' . $province;

        // Jangan cache hasil kosong
        $cities = Cache::get($cacheKey);
        if (!is_array($cities) || empty($cities)) {
            $cities = [];
            try {
                $apiKey = config('services.raja_ongkir.api_key');
                $baseUrl = config('services.raja_ongkir.city_url');

                if (!empty($apiKey) && !empty($baseUrl)) {
                    $url = str_contains($baseUrl, '{province_id}')
                        ? str_replace('{province_id}', (string) $province, $baseUrl)
                        : ($baseUrl . '/' . $province);

                    $response = Http::withHeaders(['key' => $apiKey])->timeout(10)->get($url);
                    if ($response->successful()) {
                        $data = $response->json('data') ?? [];
                        if (!empty($data)) {
                            $cities = $data;
                            Cache::put($cacheKey, $cities, now()->addDays(14));
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning('resolveCityName failed', ['message' => $e->getMessage()]);
            }
        }

        $match = collect($cities)->firstWhere('id', (string) $city);
        return (string) ($match['name'] ?? $city);
    }

    private function resolveDistrictName($district, $city): string
    {
        if (empty($district) || !is_numeric($district)) {
            return (string) $district;
        }
        if (empty($city) || !is_numeric($city)) {
            return (string) $district;
        }

        $cacheKey = 'ro:districts:v1:' . $city;

        // Jangan cache hasil kosong
        $districts = Cache::get($cacheKey);
        if (!is_array($districts) || empty($districts)) {
            $districts = [];
            try {
                $apiKey = config('services.raja_ongkir.api_key');
                $baseUrl = config('services.raja_ongkir.district_url');

                if (!empty($apiKey) && !empty($baseUrl)) {
                    $url = str_contains($baseUrl, '{city_id}')
                        ? str_replace('{city_id}', (string) $city, $baseUrl)
                        : ($baseUrl . '/' . $city);

                    $response = Http::withHeaders(['key' => $apiKey])->timeout(10)->get($url);
                    if ($response->successful()) {
                        $data = $response->json('data') ?? [];
                        if (!empty($data)) {
                            $districts = $data;
                            Cache::put($cacheKey, $districts, now()->addDays(14));
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning('resolveDistrictName failed', ['message' => $e->getMessage()]);
            }
        }

        $match = collect($districts)->firstWhere('id', (string) $district);
        return (string) ($match['name'] ?? $district);
    }

    private function resolveSubdistrictName($subdistrict, $district): string
    {
        if (empty($subdistrict) || !is_numeric($subdistrict)) {
            return (string) $subdistrict;
        }
        if (empty($district) || !is_numeric($district)) {
            return (string) $subdistrict;
        }

        $cacheKey = 'ro:subdistricts:v1:' . $district;

        // Jangan cache hasil kosong
        $subdistricts = Cache::get($cacheKey);
        if (!is_array($subdistricts) || empty($subdistricts)) {
            $subdistricts = [];
            try {
                $apiKey = config('services.raja_ongkir.api_key');
                $baseUrl = config('services.raja_ongkir.subdistrict_url');

                if (!empty($apiKey) && !empty($baseUrl)) {
                    $url = str_contains($baseUrl, '{district_id}')
                        ? str_replace('{district_id}', (string) $district, $baseUrl)
                        : ($baseUrl . '/' . $district);

                    $response = Http::withHeaders(['key' => $apiKey])->timeout(10)->get($url);
                    if ($response->successful()) {
                        $data = $response->json('data') ?? [];
                        if (!empty($data)) {
                            $subdistricts = $data;
                            Cache::put($cacheKey, $subdistricts, now()->addDays(14));
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning('resolveSubdistrictName failed', ['message' => $e->getMessage()]);
            }
        }

        $match = collect($subdistricts)->firstWhere('id', (string) $subdistrict);
        return (string) ($match['name'] ?? $subdistrict);
    }
}
