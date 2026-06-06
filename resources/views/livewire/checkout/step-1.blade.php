<div class="bg-white rounded-lg border border-gray-200 p-6">
    <h2 class="text-xl font-bold mb-1" style="color: #8B4513;">
        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Pilih Alamat Pengiriman
    </h2>
    <p class="text-gray-600 text-xs mb-6">Pilih salah satu alamat yang sudah Anda buat</p>

    <div class="space-y-3">
        <!-- No addresses yet -->
        @if (count($userAddresses) == 0)
            <div class="bg-amber-50 border-2 border-amber-200 rounded-lg p-4 text-center">
                <p class="text-amber-800 text-sm mb-3">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Anda belum memiliki alamat tersimpan
                </p>
                <p class="text-amber-700 text-xs mb-4">
                    Silakan buat alamat terlebih dahulu di dashboard pengaturan Anda
                </p>
                <a href="{{ route('add-address') }}" class="inline-block px-4 py-2 bg-amber-600 text-white rounded text-sm hover:bg-amber-700 transition-colors">
                    Tambah Alamat
                </a>
            </div>
        @else
            <!-- Address Selection -->
            <div class="space-y-2 max-h-96 overflow-y-auto">
                @foreach ($userAddresses as $addr)
                    <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer transition-all"
                           style="border-color: {{ $selectedAddressId == $addr['id'] ? '#8B4513' : '#D1D5DB' }}; background-color: {{ $selectedAddressId == $addr['id'] ? 'rgba(139, 69, 19, 0.05)' : 'white' }}">
                        <input type="radio" wire:model.live="selectedAddressId" value="{{ $addr['id'] }}"
                               class="mt-1 w-4 h-4" style="accent-color: #8B4513;">
                        <div class="ml-4 flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-semibold text-gray-900">{{ $addr['label'] }}</span>
                                @if ($addr['is_default'])
                                    <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold text-white" style="background-color: #8B4513">Default</span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-600 mb-1">{{ $addr['full_name'] }} | {{ $addr['phone_number'] }}</div>
                            <div class="text-xs text-gray-500">
                                {{ $addr['address'] }}, {{ $addr['subdistrict'] }}, {{ $addr['district'] }}, {{ $addr['city'] }}, {{ $addr['province'] }}
                                @if (!empty($addr['postal_code']))
                                    {{ $addr['postal_code'] }}
                                @endif
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>

            <!-- Manage Address Link -->
            <div class="mt-4 pt-4 border-t border-gray-200">
                <a href="{{ route('my-addresses') }}" class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah atau Kelola Alamat Lain
                </a>
            </div>
        @endif
    </div>
</div>
