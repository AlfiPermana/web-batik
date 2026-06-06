<div class="max-w-5xl mx-auto px-4 py-8">
    <!-- Back Button -->
    <a href="{{ route('my-addresses') }}" class="inline-flex items-center mb-8 font-medium transition" style="color: #8B4513;">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Kembali ke Alamat Saya
    </a>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <!-- Form Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2" style="color: #8B4513; font-family: 'Playfair Display', serif;">
            {{ $addressId ? 'Edit Alamat' : 'Tambah Alamat Baru' }}
        </h1>
        <p class="text-gray-600">Kelola informasi pengiriman Anda</p>
    </div>

    <!-- Form Card -->
    <form wire:submit="saveAddress" class="bg-white rounded-lg border border-gray-200 p-8">
        
        <!-- Section 1: Basic Info -->
        <div class="mb-8">
            <h2 class="text-lg font-bold mb-5" style="color: #8B4513;">Informasi Penerima</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Label -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Label</label>
                    <input type="text" wire:model="label" placeholder="Rumah, Kantor, dll"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:border-transparent transition" style="--tw-ring-color: #8B4513;">
                    @error('label')<span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>@enderror
                </div>

                <!-- Nama -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Penerima</label>
                    <input type="text" wire:model="fullName" placeholder="Nama lengkap"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:border-transparent transition" style="--tw-ring-color: #8B4513;">
                    @error('fullName')<span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>@enderror
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">No. Telepon</label>
                    <input type="tel" wire:model="phoneNumber" placeholder="08xx atau +62xx"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:border-transparent transition" style="--tw-ring-color: #8B4513;">
                    @error('phoneNumber')<span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>@enderror
                </div>

                <!-- Default Address -->
                <div class="flex items-center">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" wire:model="isDefault" class="w-4 h-4 rounded border-gray-300">
                        <span class="text-sm font-semibold text-gray-700">Jadikan alamat default</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="border-t my-8"></div>

        <!-- Section 2: Location -->
        <div class="mb-8">
            <h2 class="text-lg font-bold mb-5" style="color: #8B4513;">Lokasi</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Province -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Provinsi</label>
                    <select wire:model.live="provinceId"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:border-transparent transition" style="--tw-ring-color: #8B4513;">
                        <option value="">Pilih provinsi</option>
                        @foreach($provinces as $prov)
                            <option value="{{ $prov['id'] }}">{{ $prov['name'] }}</option>
                        @endforeach
                    </select>
                    @error('provinceId')<span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>@enderror
                </div>

                <!-- City -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kota/Kabupaten</label>
                    <select wire:model.live="cityId" {{ !$provinceId ? 'disabled' : '' }}
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:border-transparent transition disabled:bg-gray-100 disabled:cursor-not-allowed" style="--tw-ring-color: #8B4513;">
                        <option value="">{{ $provinceId ? 'Pilih kota' : 'Pilih provinsi dulu' }}</option>
                        @foreach($cities as $c)
                            <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                        @endforeach
                    </select>
                    @error('cityId')<span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>@enderror
                </div>

                <!-- District -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kecamatan</label>
                    <select wire:model.live="districtId" {{ !$cityId ? 'disabled' : '' }}
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:border-transparent transition disabled:bg-gray-100 disabled:cursor-not-allowed" style="--tw-ring-color: #8B4513;">
                        <option value="">{{ $cityId ? 'Pilih kecamatan' : 'Pilih kota dulu' }}</option>
                        @foreach($districts as $d)
                            <option value="{{ $d['id'] }}">{{ $d['name'] }}</option>
                        @endforeach
                    </select>
                    @error('districtId')<span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>@enderror
                </div>

                <!-- Subdistrict -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kelurahan</label>
                    <select wire:model.live="subdistrictId" {{ !$districtId ? 'disabled' : '' }}
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:border-transparent transition disabled:bg-gray-100 disabled:cursor-not-allowed" style="--tw-ring-color: #8B4513;">
                        <option value="">{{ $districtId ? 'Pilih kelurahan' : 'Pilih kecamatan dulu' }}</option>
                        @foreach($subdistricts as $s)
                            <option value="{{ $s['id'] }}">{{ $s['name'] }}</option>
                        @endforeach
                    </select>
                    @error('subdistrictId')<span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="border-t my-8"></div>

        <!-- Section 3: Address Details -->
        <div class="mb-8">
            <h2 class="text-lg font-bold mb-5" style="color: #8B4513;">Detail Alamat</h2>
            
            <div class="space-y-6">
                <!-- Alamat Lengkap -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Lengkap</label>
                    <textarea wire:model="address" rows="3" placeholder="Jalan, nomor rumah, blok, RT/RW, dll"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:border-transparent transition resize-none" style="--tw-ring-color: #8B4513;"></textarea>
                    @error('address')<span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>@enderror
                </div>

                <!-- Kode Pos -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kode Pos <span class="text-gray-500 font-normal">(Opsional)</span></label>
                    <input type="text" wire:model="postalCode" placeholder="12345"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:border-transparent transition" style="--tw-ring-color: #8B4513;">
                    @error('postalCode')<span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex gap-2 pt-4 border-t">
            <a href="{{ route('my-addresses') }}" 
               class="px-6 py-3 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition">
                Batal
            </a>
            <button type="submit" 
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50 cursor-not-allowed"
                    class="px-6 py-3 rounded-lg text-white font-semibold hover:shadow-lg transition ml-auto" style="background-color: #8B4513;">
                <span wire:loading.remove>{{ $addressId ? '✏️ Perbarui Alamat' : '✓ Simpan Alamat' }}</span>
                <span wire:loading>⏳ Memproses...</span>
            </button>
        </div>
    </form>
</div>
