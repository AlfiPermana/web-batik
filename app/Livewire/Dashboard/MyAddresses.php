<?php

namespace App\Livewire\Dashboard;

use App\Models\Address;
use Livewire\Component;

class MyAddresses extends Component
{
    public $addresses = [];

    public function mount()
    {
        $this->loadAddresses();
    }

    public function loadAddresses()
    {
        $userAddresses = Address::where('user_id', auth()->id())
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $this->addresses = $userAddresses->map(fn($addr) => [
            'id' => $addr->id,
            'label' => $addr->label,
            'full_name' => $addr->full_name,
            'phone_number' => $addr->phone_number,
            'address' => $addr->address,
            'subdistrict' => $addr->subdistrict_display,
            'district' => $addr->district_display,
            'city' => $addr->city_display,
            'province' => $addr->province_display,
            'postal_code' => $addr->postal_code,
            'is_default' => $addr->is_default,
        ])->toArray();
    }

    public function setDefaultAddress($id)
    {
        $address = Address::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if ($address) {
            // Reset all addresses for user
            Address::where('user_id', auth()->id())->update(['is_default' => false]);
            // Set this one as default
            $address->update(['is_default' => true]);
            
            session()->flash('success', '✓ Alamat default berhasil diubah');
            $this->loadAddresses();
        }
    }

    public function deleteAddress($id)
    {
        $address = Address::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if ($address) {
            $address->delete();
            session()->flash('success', '✓ Alamat berhasil dihapus');
            $this->loadAddresses();
        }
    }

    public function render()
    {
        return view('livewire.dashboard.my-addresses');
    }
}
