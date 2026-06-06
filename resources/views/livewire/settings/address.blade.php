<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Alamat Pengiriman</h1>
            <p class="mt-2 text-gray-600">Kelola alamat pengiriman Anda</p>
        </div>

        <!-- Add Address Button -->
        <div class="mb-8">
            @if (!$showForm)
                <button wire:click="openForm"
                        class="px-6 py-3 rounded-lg font-semibold text-white transition-colors"
                        style="background-color: #8B4513; hover:opacity-90">
                    + Tambah Alamat Baru
                </button>
            @endif
        </div>

        <!-- Address Form Modal -->
        @if ($showForm)
            <div class="fixed inset-0 bg-black bg-opacity-50 z-40 flex items-center justify-center">
                <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                    <!-- Form Header -->
                    <div class="sticky top-0 bg-gray-100 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-xl font-bold text-gray-900">
                            {{ $editingAddressId ? 'Edit Alamat' : 'Tambah Alamat Baru' }}
                        </h2>
                        <button wire:click="closeForm" class="text-gray-600 hover:text-gray-900 text-2xl">&times;</button>
                    </div>

                    <!-- Form Body -->
                    <div class="p-6 space-y-4">
                        <!-- Label -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Label Alamat</label>
                            <input type="text" wire:model="label" placeholder="Rumah, Kantor, etc"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-600 focus:border-transparent">
                            @error('label') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Full Name -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                            <input type="text" wire:model="fullName"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-600 focus:border-transparent">
                            @error('fullName') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Telepon</label>
                            <input type="tel" wire:model="phoneNumber"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-600 focus:border-transparent">
                            @error('phoneNumber') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Province Dropdown -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Provinsi</label>
                            <select wire:model="province"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-600 focus:border-transparent">
                                <option value="">-- Pilih Provinsi --</option>
                                @foreach ($provinces as $prov)
                                    <option value="{{ $prov['id'] }}">{{ $prov['name'] }}</option>
                                @endforeach
                            </select>
                            @error('province') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- City Dropdown -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Kota/Kabupaten</label>
                            <select wire:model="city" {{ $loadingCities ? 'disabled' : '' }}
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-600 focus:border-transparent">
                                <option value="">{{ $loadingCities ? '-- Loading --' : '-- Pilih Kota --' }}</option>
                                @foreach ($cities as $cty)
                                    <option value="{{ $cty['id'] }}">{{ $cty['name'] }}</option>
                                @endforeach
                            </select>
                            @error('city') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- District Dropdown -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Kecamatan</label>
                            <select wire:model="district" {{ $loadingDistricts ? 'disabled' : '' }}
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-600 focus:border-transparent">
                                <option value="">{{ $loadingDistricts ? '-- Loading --' : '-- Pilih Kecamatan --' }}</option>
                                @foreach ($districts as $dtrct)
                                    <option value="{{ $dtrct['id'] }}">{{ $dtrct['name'] }}</option>
                                @endforeach
                            </select>
                            @error('district') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Subdistrict Dropdown -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Kelurahan</label>
                            <select wire:model="subdistrict" {{ $loadingSubdistricts ? 'disabled' : '' }}
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-600 focus:border-transparent">
                                <option value="">{{ $loadingSubdistricts ? '-- Loading --' : '-- Pilih Kelurahan --' }}</option>
                                @foreach ($subdistricts as $subdst)
                                    <option value="{{ $subdst['id'] }}">{{ $subdst['name'] }}</option>
                                @endforeach
                            </select>
                            @error('subdistrict') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Address Detail -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Lengkap</label>
                            <textarea wire:model="address" rows="3" placeholder="Nomor rumah, nama jalan, detail lokasi"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-600 focus:border-transparent"></textarea>
                            @error('address') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Postal Code -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Kode Pos</label>
                            <input type="text" wire:model="postalCode"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-600 focus:border-transparent">
                            @error('postalCode') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Set as Default -->
                        <div class="flex items-center">
                            <input type="checkbox" wire:model="isDefault" id="isDefault" class="rounded">
                            <label for="isDefault" class="ml-2 text-sm text-gray-700">Jadikan alamat default</label>
                        </div>
                    </div>

                    <!-- Form Footer -->
                    <div class="bg-gray-100 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                        <button wire:click="closeForm"
                                class="px-6 py-2 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">
                            Batal
                        </button>
                        <button wire:click="saveAddress" wire:loading.attr="disabled"
                                class="px-6 py-2 rounded-lg font-semibold text-white transition-colors"
                                style="background-color: #8B4513">
                            <span wire:loading.remove>Simpan Alamat</span>
                            <span wire:loading><svg class="inline-block w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Menyimpan...</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Address List -->
        <div class="grid grid-cols-1 gap-4">
            @forelse ($addresses as $addr)
                <div class="bg-white rounded-lg shadow p-6 border-l-4"
                     style="border-color: {{ $addr['is_default'] ? '#8B4513' : '#D1D5DB' }}">
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ $addr['label'] }}</h3>
                            @if ($addr['is_default'])
                                <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-semibold text-white"
                                      style="background-color: #8B4513">Alamat Default</span>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            <button wire:click="editAddress({{ $addr['id'] }})"
                                    class="px-4 py-2 rounded-lg border border-amber-600 text-amber-600 font-semibold hover:bg-amber-50">
                                Edit
                            </button>
                            <button wire:click="deleteAddress({{ $addr['id'] }})"
                                    wire:confirm="Apakah Anda yakin ingin menghapus alamat ini?"
                                    class="px-4 py-2 rounded-lg border border-red-600 text-red-600 font-semibold hover:bg-red-50">
                                Hapus
                            </button>
                        </div>
                    </div>

                    <!-- Address Details -->
                    <div class="text-gray-700 space-y-1">
                        <p><strong>Nama:</strong> {{ $addr['full_name'] }}</p>
                        <p><strong>Telepon:</strong> {{ $addr['phone_number'] }}</p>
                        <p><strong>Alamat:</strong> {{ $addr['address'] }}</p>
                        <p><strong>Lokasi:</strong> {{ $addr['subdistrict'] }}, {{ $addr['district'] }}, {{ $addr['city'] }}, {{ $addr['province'] }} {{ $addr['postal_code'] }}</p>
                    </div>

                    <!-- Set as Default Button (if not default) -->
                    @if (!$addr['is_default'])
                        <div class="mt-4">
                            <button wire:click="setDefaultAddress({{ $addr['id'] }})"
                                    class="text-sm px-4 py-2 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">
                                Jadikan Default
                            </button>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <p class="text-gray-600 mb-4">Belum ada alamat tersimpan</p>
                    <button wire:click="openForm"
                            class="px-6 py-2 rounded-lg font-semibold text-white transition-colors"
                            style="background-color: #8B4513">
                        Tambah Alamat Pertama
                    </button>
                </div>
            @endforelse
        </div>
    </div>
</div>
