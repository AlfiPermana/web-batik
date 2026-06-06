<?php

namespace App\Livewire\Settings;

use App\Models\Address as AddressModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Address extends Component
{
    public $user;
    public $addresses = [];
    public $showForm = false;
    public $editingAddressId = null;

    // Form Fields
    public $label = '';
    public $fullName = '';
    public $phoneNumber = '';
    public $province = '';
    public $city = '';
    public $district = '';
    public $subdistrict = '';
    public $address = '';
    public $postalCode = '';
    public $isDefault = false;

    // Raja Ongkir Dropdowns
    public $provinces = [];
    public $cities = [];
    public $districts = [];
    public $subdistricts = [];
    public $loadingCities = false;
    public $loadingDistricts = false;
    public $loadingSubdistricts = false;

    protected $rules = [
        'label' => 'required|string|max:50',
        'fullName' => 'required|string|max:255',
        'phoneNumber' => 'required|string|max:25',
        'province' => 'required|string',
        'city' => 'required|string',
        'district' => 'required|string',
        'subdistrict' => 'required|string',
        'address' => 'required|string|max:1024',
        'postalCode' => 'required|string|max:10',
    ];

    public function mount()
    {
        $this->user = Auth::user();
        $this->loadAddresses();
        $this->loadProvinces();
    }

    public function loadAddresses()
    {
        $this->addresses = $this->user->addresses()
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($addr) => [
                'id' => $addr->id,
                'label' => $addr->label,
                'full_name' => $addr->full_name,
                'phone_number' => $addr->phone_number,
                'province' => $addr->province_display,
                'city' => $addr->city_display,
                'district' => $addr->district_display,
                'subdistrict' => $addr->subdistrict_display,
                'address' => $addr->address,
                'postal_code' => $addr->postal_code,
                'is_default' => $addr->is_default,
                'created_at' => $addr->created_at,
                'updated_at' => $addr->updated_at,
            ])
            ->toArray();
    }

    public function loadProvinces()
    {
        try {
            $apiKey = config('services.raja_ongkir.api_key');
            $url = config('services.raja_ongkir.province_url');

            $response = Http::withHeaders([
                'key' => $apiKey,
            ])->get($url);

            if ($response->successful()) {
                $jsonData = $response->json();
                $data = $jsonData['data'] ?? [];

                $this->provinces = array_map(fn($item) => [
                    'id' => $item['id'],
                    'name' => $item['name'],
                ], $data);
            }
        } catch (\Exception $e) {
            Log::error('Error loading provinces: ' . $e->getMessage());
        }
    }

    public function updatedProvince($value)
    {
        if (!$value) {
            $this->cities = [];
            $this->city = '';
            $this->districts = [];
            $this->district = '';
            $this->subdistricts = [];
            $this->subdistrict = '';
            return;
        }

        $this->loadingCities = true;

        try {
            $baseUrl = config('services.raja_ongkir.city_url');
            $url = $baseUrl . '/' . $value;

            $response = Http::withHeaders([
                'key' => config('services.raja_ongkir.api_key'),
            ])->get($url);

            if ($response->successful()) {
                $jsonData = $response->json();
                $data = $jsonData['data'] ?? [];
                $this->cities = array_map(fn($item) => [
                    'id' => $item['id'],
                    'name' => $item['name'],
                ], $data);
                $this->city = '';
                $this->districts = [];
                $this->district = '';
                $this->subdistricts = [];
                $this->subdistrict = '';
            }
        } catch (\Exception $e) {
            Log::error('Error loading cities: ' . $e->getMessage());
        } finally {
            $this->loadingCities = false;
        }
    }

    public function updatedCity($value)
    {
        if (!$value) {
            $this->districts = [];
            $this->district = '';
            $this->subdistricts = [];
            $this->subdistrict = '';
            return;
        }

        $this->loadingDistricts = true;

        try {
            $baseUrl = config('services.raja_ongkir.district_url');
            $url = $baseUrl . '/' . $value;

            $response = Http::withHeaders([
                'key' => config('services.raja_ongkir.api_key'),
            ])->get($url);

            if ($response->successful()) {
                $jsonData = $response->json();
                $data = $jsonData['data'] ?? [];
                $this->districts = array_map(fn($item) => [
                    'id' => $item['id'],
                    'name' => $item['name'],
                ], $data);
                $this->district = '';
                $this->subdistricts = [];
                $this->subdistrict = '';
            }
        } catch (\Exception $e) {
            Log::error('Error loading districts: ' . $e->getMessage());
        } finally {
            $this->loadingDistricts = false;
        }
    }

    public function updatedDistrict($value)
    {
        if (!$value) {
            $this->subdistricts = [];
            $this->subdistrict = '';
            return;
        }

        $this->loadingSubdistricts = true;

        try {
            $baseUrl = config('services.raja_ongkir.subdistrict_url');
            $url = $baseUrl . '/' . $value;

            $response = Http::withHeaders([
                'key' => config('services.raja_ongkir.api_key'),
            ])->get($url);

            if ($response->successful()) {
                $jsonData = $response->json();
                $data = $jsonData['data'] ?? [];
                $this->subdistricts = array_map(fn($item) => [
                    'id' => $item['id'],
                    'name' => $item['name'],
                ], $data);
                $this->subdistrict = '';
            }
        } catch (\Exception $e) {
            Log::error('Error loading subdistricts: ' . $e->getMessage());
        } finally {
            $this->loadingSubdistricts = false;
        }
    }

    public function openForm()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editingAddressId = null;
        // Pre-fill dengan user profile data jika ada
        if ($this->user->customerProfile) {
            $profile = $this->user->customerProfile;
            $this->fullName = $this->user->name;
            $this->phoneNumber = $profile->phone_number ?? '';
        } else {
            $this->fullName = $this->user->name;
        }
    }

    public function editAddress($addressId)
    {
        $addressModel = AddressModel::findOrFail($addressId);
        
        $this->editingAddressId = $addressId;
        $this->label = $addressModel->label;
        $this->fullName = $addressModel->full_name;
        $this->phoneNumber = $addressModel->phone_number;
        $this->province = $addressModel->province;
        $this->city = $addressModel->city;
        $this->district = $addressModel->district;
        $this->subdistrict = $addressModel->subdistrict;
        $this->address = $addressModel->address;
        $this->postalCode = $addressModel->postal_code;
        $this->isDefault = $addressModel->is_default;

        $this->showForm = true;

        // Load cascading dropdowns based on saved values
        if ($this->province) {
            $this->updatedProvince($this->province);
        }
    }

    public function saveAddress()
    {
        $this->validate();

        if ($this->editingAddressId) {
            // Update existing address
            $addressModel = AddressModel::findOrFail($this->editingAddressId);
            
            // If setting as default, unset other default addresses
            if ($this->isDefault) {
                AddressModel::where('user_id', $this->user->id)
                    ->where('id', '!=', $this->editingAddressId)
                    ->update(['is_default' => false]);
            }

            $addressModel->update([
                'label' => $this->label,
                'full_name' => $this->fullName,
                'phone_number' => $this->phoneNumber,
                'province' => $this->province,
                'city' => $this->city,
                'district' => $this->district,
                'subdistrict' => $this->subdistrict,
                'address' => $this->address,
                'postal_code' => $this->postalCode,
                'is_default' => $this->isDefault,
            ]);

            $this->dispatch('notify', type: 'success', message: 'Alamat berhasil diperbarui');
        } else {
            // Create new address
            // If this is the first address or setting as default, make it default
            $isFirstAddress = $this->user->addresses()->count() === 0;
            
            if ($this->isDefault || $isFirstAddress) {
                // Unset other default addresses
                AddressModel::where('user_id', $this->user->id)
                    ->update(['is_default' => false]);
                $this->isDefault = true;
            }

            AddressModel::create([
                'user_id' => $this->user->id,
                'label' => $this->label,
                'full_name' => $this->fullName,
                'phone_number' => $this->phoneNumber,
                'province' => $this->province,
                'city' => $this->city,
                'district' => $this->district,
                'subdistrict' => $this->subdistrict,
                'address' => $this->address,
                'postal_code' => $this->postalCode,
                'is_default' => $this->isDefault,
            ]);

            $this->dispatch('notify', type: 'success', message: 'Alamat berhasil ditambahkan');
        }

        $this->loadAddresses();
        $this->closeForm();
    }

    public function deleteAddress($addressId)
    {
        $addressModel = AddressModel::findOrFail($addressId);
        
        // Don't allow deletion if it's the only address
        if ($this->user->addresses()->count() === 1) {
            $this->dispatch('notify', type: 'error', message: 'Anda harus memiliki minimal 1 alamat');
            return;
        }

        // If deleting default address, set another as default
        if ($addressModel->is_default) {
            $this->user->addresses()
                ->where('id', '!=', $addressId)
                ->first()
                ?->update(['is_default' => true]);
        }

        $addressModel->delete();
        $this->loadAddresses();
        $this->dispatch('notify', type: 'success', message: 'Alamat berhasil dihapus');
    }

    public function setDefaultAddress($addressId)
    {
        AddressModel::where('user_id', $this->user->id)
            ->update(['is_default' => false]);

        AddressModel::find($addressId)
            ->update(['is_default' => true]);

        $this->loadAddresses();
        $this->dispatch('notify', type: 'success', message: 'Alamat default telah diubah');
    }

    public function closeForm()
    {
        $this->showForm = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->label = '';
        $this->fullName = '';
        $this->phoneNumber = '';
        $this->province = '';
        $this->city = '';
        $this->district = '';
        $this->subdistrict = '';
        $this->address = '';
        $this->postalCode = '';
        $this->isDefault = false;
        $this->editingAddressId = null;
        $this->clearValidation();
    }

    public function render()
    {
        return view('livewire.settings.address');
    }
}
