<?php

namespace App\Livewire\Admin;

use App\Models\ShippingOrigin;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class ShippingOriginSettings extends Component
{
    public $currentOrigin = null;

    public $address = '';

    public $provinceId = '';
    public $cityId = '';
    public $districtId = '';

    /**
     * Courier codes enabled by admin (used by customer checkout).
     * Example: ['jne','jnt','sicepat']
     */
    public $enabledCouriers = [];

    /**
     * Courier catalog displayed in admin UI.
     */
    public $courierCatalog = [
        ['code' => 'jne', 'name' => 'JNE'],
        ['code' => 'tiki', 'name' => 'TIKI'],
        ['code' => 'pos', 'name' => 'POS Indonesia'],
        ['code' => 'jnt', 'name' => 'J&T Express'],
        ['code' => 'sicepat', 'name' => 'SiCepat'],
        ['code' => 'anteraja', 'name' => 'AnterAja'],
        ['code' => 'ninja', 'name' => 'Ninja Xpress'],
        ['code' => 'lion', 'name' => 'Lion Parcel'],
        ['code' => 'wahana', 'name' => 'Wahana'],
    ];

    public $provinces = [];
    public $cities = [];
    public $districts = [];

    public $loadingProvinces = false;
    public $loadingCities = false;
    public $loadingDistricts = false;

    public function mount(): void
    {
        $this->refreshCurrent();
        $this->loadProvinces();

        if ($this->currentOrigin) {
            $this->address = (string) ($this->currentOrigin->address ?? '');
            $this->enabledCouriers = is_array($this->currentOrigin->enabled_couriers)
                ? $this->currentOrigin->enabled_couriers
                : [];
        } else {
            // kosong = AUTO (ambil semua yang tersedia dari API)
            $this->enabledCouriers = [];
        }
    }

    public function refreshCurrent(): void
    {
        $this->currentOrigin = ShippingOrigin::current();
    }

    public function loadProvinces(): void
    {
        $this->loadingProvinces = true;

        try {
            $apiKey = config('services.raja_ongkir.api_key');
            $url = config('services.raja_ongkir.province_url');

            $response = Http::withHeaders(['key' => $apiKey])->timeout(10)->get($url);

            $data = $response->successful() ? ($response->json('data') ?? []) : [];

            $this->provinces = array_map(fn($item) => [
                'id' => (string) ($item['id'] ?? ''),
                'name' => (string) ($item['name'] ?? ''),
            ], $data);
        } finally {
            $this->loadingProvinces = false;
        }
    }

    public function updatedProvinceId($value): void
    {
        $this->cityId = '';
        $this->districtId = '';
        $this->cities = [];
        $this->districts = [];

        if (!$value) {
            return;
        }

        $this->loadingCities = true;

        try {
            $apiKey = config('services.raja_ongkir.api_key');
            $baseUrl = config('services.raja_ongkir.city_url');
            $url = str_contains($baseUrl, '{province_id}')
                ? str_replace('{province_id}', (string) $value, $baseUrl)
                : ($baseUrl . '/' . (string) $value);

            $response = Http::withHeaders(['key' => $apiKey])->timeout(10)->get($url);
            $data = $response->successful() ? ($response->json('data') ?? []) : [];

            $this->cities = array_map(fn($item) => [
                'id' => (string) ($item['id'] ?? ''),
                'name' => (string) ($item['name'] ?? ''),
            ], $data);
        } finally {
            $this->loadingCities = false;
        }
    }

    public function updatedCityId($value): void
    {
        $this->districtId = '';
        $this->districts = [];

        if (!$value) {
            return;
        }

        $this->loadingDistricts = true;

        try {
            $apiKey = config('services.raja_ongkir.api_key');
            $baseUrl = config('services.raja_ongkir.district_url');
            $url = str_contains($baseUrl, '{city_id}')
                ? str_replace('{city_id}', (string) $value, $baseUrl)
                : ($baseUrl . '/' . (string) $value);

            $response = Http::withHeaders(['key' => $apiKey])->timeout(10)->get($url);
            $data = $response->successful() ? ($response->json('data') ?? []) : [];

            $this->districts = array_map(fn($item) => [
                'id' => (string) ($item['id'] ?? ''),
                'name' => (string) ($item['name'] ?? ''),
            ], $data);
        } finally {
            $this->loadingDistricts = false;
        }
    }

    public function selectAllCouriers(): void
    {
        $this->enabledCouriers = array_values(array_map(fn ($c) => (string) ($c['code'] ?? ''), $this->courierCatalog));
        $this->enabledCouriers = array_values(array_filter($this->enabledCouriers));
    }

    public function clearCouriers(): void
    {
        $this->enabledCouriers = [];
    }

    public function save(): void
    {
        $this->validate([
            'provinceId' => ['required'],
            'cityId' => ['required'],
            'districtId' => ['required'],
            // kosong = AUTO (ambil semua yang tersedia dari API)
            'enabledCouriers' => ['nullable', 'array'],
            'enabledCouriers.*' => ['string'],
            'address' => ['nullable', 'string', 'max:1000'],
        ], [
            'provinceId.required' => 'Pilih provinsi terlebih dahulu.',
            'cityId.required' => 'Pilih kota/kabupaten terlebih dahulu.',
            'districtId.required' => 'Pilih kecamatan terlebih dahulu.',
        ]);

        $cityName = collect($this->cities)->firstWhere('id', (string) $this->cityId)['name'] ?? null;
        $districtName = collect($this->districts)->firstWhere('id', (string) $this->districtId)['name'] ?? null;

        $couriers = array_values(array_unique(array_filter(array_map(fn ($c) => strtolower(trim((string) $c)), (array) $this->enabledCouriers))));

        ShippingOrigin::query()->create([
            'origin_city_id' => (string) $this->cityId,
            'origin_city_name' => $cityName,
            'origin_district_id' => (string) $this->districtId,
            'origin_district_name' => $districtName,
            'address' => $this->address ?: null,
            // empty = AUTO (biarkan API mengembalikan semua kurir yang tersedia)
            'enabled_couriers' => $couriers ?: null,
        ]);

        ShippingOrigin::forgetCache();
        $this->refreshCurrent();

        $this->dispatch('notify', type: 'success', message: 'Alamat asal pengiriman berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.admin.shipping-origin-settings');
    }
}
