<?php

namespace App\Livewire\Dashboard;

use App\Models\Address;
use Livewire\Component;
use Illuminate\Support\Facades\Http;

class FormAlamat extends Component
{
    public $addressId = null;
    public $label = '';
    public $fullName = '';
    public $phoneNumber = '';
    public $province = '';
    public $provinceId = '';
    public $city = '';
    public $cityId = '';
    public $district = '';
    public $districtId = '';
    public $subdistrictId = '';
    public $subdistrict = '';
    public $address = '';
    public $postalCode = '';
    public $isDefault = false;

    public $provinces = [];
    public $cities = [];
    public $districts = [];
    public $subdistricts = [];

    protected $rules = [
        'label' => 'required|string|max:50',
        'fullName' => 'required|string|max:100',
        'phoneNumber' => 'required|numeric|digits_between:10,13',
        'provinceId' => 'required',
        'cityId' => 'required',
        'districtId' => 'required',
        'subdistrictId' => 'required',
        'address' => 'required|string|max:500',
        'postalCode' => 'nullable|string|max:10',
    ];

    public function mount($id = null)
    {
        $this->addressId = $id;
        $this->loadProvinces();

        if ($id) {
            $userAddress = Address::where('id', $id)
                ->where('user_id', auth()->id())
                ->first();

            if ($userAddress) {
                $this->label = $userAddress->label;
                $this->fullName = $userAddress->full_name;
                $this->phoneNumber = $userAddress->phone_number;
                $this->address = $userAddress->address;
                $this->postalCode = $userAddress->postal_code;
                $this->isDefault = $userAddress->is_default;

                $storedProvince = $userAddress->province;
                $storedCity = $userAddress->city;
                $storedDistrict = $userAddress->district;
                $storedSubdistrict = $userAddress->subdistrict;

                // Province ID
                if (is_numeric($storedProvince)) {
                    $this->provinceId = (string) $storedProvince;
                } else {
                    foreach ($this->provinces as $prov) {
                        if ($prov['name'] === $storedProvince) {
                            $this->provinceId = (string) $prov['id'];
                            break;
                        }
                    }
                }

                // City ID
                if ($this->provinceId) {
                    $this->loadCities($this->provinceId);

                    if (is_numeric($storedCity)) {
                        $this->cityId = (string) $storedCity;
                    } else {
                        foreach ($this->cities as $city) {
                            if ($city['name'] === $storedCity) {
                                $this->cityId = (string) $city['id'];
                                break;
                            }
                        }
                    }
                }

                // District ID
                if ($this->cityId) {
                    $this->loadDistricts($this->cityId);

                    if (is_numeric($storedDistrict)) {
                        $this->districtId = (string) $storedDistrict;
                    } else {
                        foreach ($this->districts as $dist) {
                            if ($dist['name'] === $storedDistrict) {
                                $this->districtId = (string) $dist['id'];
                                break;
                            }
                        }
                    }
                }

                // Subdistrict ID
                if ($this->districtId) {
                    $this->loadSubdistricts($this->districtId);

                    if (is_numeric($storedSubdistrict)) {
                        $this->subdistrictId = (string) $storedSubdistrict;
                    } else {
                        foreach ($this->subdistricts as $subdist) {
                            if ($subdist['name'] === $storedSubdistrict) {
                                $this->subdistrictId = (string) $subdist['id'];
                                break;
                            }
                        }
                    }
                }
            }
        }
    }

    public function loadProvinces()
    {
        try {
            $apiKey = config('services.raja_ongkir.api_key');
            $url = config('services.raja_ongkir.province_url');
            
            if (!$url) {
                \Log::error('SHIPPING_PROVINCE_URL is null', ['env_value' => env('SHIPPING_PROVINCE_URL')]);
                throw new \Exception('SHIPPING_PROVINCE_URL configuration is missing');
            }
            
            $response = Http::withHeaders([
                'key' => $apiKey,
            ])->timeout(10)->get($url);

            if ($response->successful()) {
                $data = $response->json('data') ?? [];
                $this->provinces = array_map(fn($item) => [
                    'id' => $item['id'],
                    'name' => $item['name'],
                ], $data);
            } else {
                // Suppress API error details, just log it
                \Log::warning('Raja Ongkir API Error', ['response' => $response->json()]);
            }
        } catch (\Exception $e) {
            \Log::warning('Raja Ongkir Connection Error', ['message' => $e->getMessage()]);
        }
    }

    public function updatedProvinceId($value)
    {
        $this->cityId = '';
        $this->districtId = '';
        $this->subdistrictId = '';
        $this->subdistrict = '';
        $this->cities = [];
        $this->districts = [];
        $this->subdistricts = [];

        // Set province name from ID
        if ($value) {
            foreach ($this->provinces as $prov) {
                if ((string)$prov['id'] === (string)$value) {
                    $this->province = $prov['name'];
                    \Log::info('Province updated', ['id' => $value, 'name' => $this->province]);
                    break;
                }
            }
            $this->loadCities($value);
        } else {
            $this->province = '';
        }
    }

    public function loadCities($provinceId)
    {
        try {
            $baseUrl = config('services.raja_ongkir.city_url');
            $url = str_replace('{province_id}', $provinceId, $baseUrl);
            \Log::info('Loading cities', ['url' => $url, 'provinceId' => $provinceId]);
            
            $response = Http::withHeaders([
                'key' => config('services.raja_ongkir.api_key'),
            ])->timeout(10)->get($url);

            if ($response->successful()) {
                $cities = $response->json('data') ?? [];
                \Log::info('Cities fetched', ['count' => count($cities)]);
                
                $this->cities = array_map(fn($item) => [
                    'id' => $item['id'],
                    'name' => $item['name'],
                ], $cities);
                
                \Log::info('Cities loaded successfully', ['cities_count' => count($this->cities)]);
            } else {
                \Log::warning('Raja Ongkir API Error', ['status' => $response->status(), 'response' => $response->json()]);
            }
        } catch (\Exception $e) {
            \Log::warning('Raja Ongkir City Load Error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }
    }

    public function updatedCityId($value)
    {
        $this->districtId = '';
        $this->subdistrictId = '';
        $this->subdistrict = '';
        $this->districts = [];
        $this->subdistricts = [];

        // Set city name from ID
        if ($value) {
            foreach ($this->cities as $city) {
                if ((string)$city['id'] === (string)$value) {
                    $this->city = $city['name'];
                    \Log::info('City updated', ['id' => $value, 'name' => $this->city]);
                    break;
                }
            }
            $this->loadDistricts($value);
        } else {
            $this->city = '';
        }
    }

    public function loadDistricts($cityId)
    {
        try {
            $baseUrl = config('services.raja_ongkir.district_url');
            $url = str_replace('{city_id}', $cityId, $baseUrl);
            
            $response = Http::withHeaders([
                'key' => config('services.raja_ongkir.api_key'),
            ])->timeout(10)->get($url);

            if ($response->successful()) {
                $data = $response->json('data') ?? [];
                $this->districts = array_map(fn($item) => [
                    'id' => $item['id'],
                    'name' => $item['name'],
                ], $data);
            }
        } catch (\Exception $e) {
            \Log::warning('Raja Ongkir District Load Error', ['message' => $e->getMessage()]);
        }
    }

    public function updatedDistrictId($value)
    {
        $this->subdistrictId = '';
        $this->subdistrict = '';
        $this->subdistricts = [];

        // Set district name from ID
        if ($value) {
            foreach ($this->districts as $dist) {
                if ((string)$dist['id'] === (string)$value) {
                    $this->district = $dist['name'];
                    \Log::info('District updated', ['id' => $value, 'name' => $this->district]);
                    break;
                }
            }
            $this->loadSubdistricts($value);
        } else {
            $this->district = '';
        }
    }

    public function loadSubdistricts($districtId)
    {
        try {
            $baseUrl = config('services.raja_ongkir.subdistrict_url');
            $url = str_replace('{district_id}', $districtId, $baseUrl);
            
            $response = Http::withHeaders([
                'key' => config('services.raja_ongkir.api_key'),
            ])->timeout(10)->get($url);

            if ($response->successful()) {
                $data = $response->json('data') ?? [];
                $this->subdistricts = array_map(fn($item) => [
                    'id' => $item['id'],
                    'name' => $item['name'],
                ], $data);
            }
        } catch (\Exception $e) {
            \Log::warning('Raja Ongkir Subdistrict Load Error', ['message' => $e->getMessage()]);
        }
    }

    public function updatedSubdistrictId($value)
    {
        $this->subdistrictId = (string) $value;
    }

    public function saveAddress()
    {
        $this->validate();

        try {
            if ($this->addressId) {
                // Update existing
                $userAddress = Address::where('id', $this->addressId)
                    ->where('user_id', auth()->id())
                    ->first();

                if (!$userAddress) {
                    session()->flash('error', 'Alamat tidak ditemukan');
                    return;
                }

                $userAddress->update([
                    'label' => $this->label,
                    'full_name' => $this->fullName,
                    'phone_number' => $this->phoneNumber,
                    'province' => $this->provinceId,
                    'city' => $this->cityId,
                    'district' => $this->districtId,
                    'subdistrict' => $this->subdistrictId,
                    'address' => $this->address,
                    'postal_code' => $this->postalCode,
                    'is_default' => $this->isDefault,
                ]);

                session()->flash('success', '✓ Alamat berhasil diperbarui');
            } else {
                // Create new
                Address::create([
                    'user_id' => auth()->id(),
                    'label' => $this->label,
                    'full_name' => $this->fullName,
                    'phone_number' => $this->phoneNumber,
                    'province' => $this->provinceId,
                    'city' => $this->cityId,
                    'district' => $this->districtId,
                    'subdistrict' => $this->subdistrictId,
                    'address' => $this->address,
                    'postal_code' => $this->postalCode,
                    'is_default' => $this->isDefault,
                ]);

                session()->flash('success', '✓ Alamat berhasil ditambahkan');
            }

            // Redirect back to my-addresses
            redirect()->route('my-addresses');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.dashboard.form-alamat');
    }
}
