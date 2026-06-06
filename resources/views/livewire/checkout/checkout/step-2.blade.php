<div class="bg-white rounded-lg border border-gray-200 p-6">
    <h2 class="text-xl font-bold mb-1" style="color: #8B4513;">
        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
        </svg>
        Pilih Metode Pengiriman
    </h2>
    <p class="text-gray-600 text-xs mb-4">Pilih salah satu metode pengiriman untuk pesanan Anda</p>

    @if (empty($shippingOptions))
        <div class="text-center py-12">
            @if ($loadingShippingOptions)
                <svg class="w-8 h-8 animate-spin mx-auto mb-4 text-[#8B4513]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-gray-500 mb-2 text-sm font-semibold">Memuat opsi pengiriman...</p>
                <p class="text-gray-400 text-xs">Menghubungi server pengiriman...</p>
            @else
                <p class="text-gray-500 mb-4 text-sm">
                    @if (!$district)
                        Silakan lengkapi data alamat (termasuk kelurahan) di step 1 terlebih dahulu
                    @else
                        Tidak ada opsi pengiriman tersedia
                    @endif
                </p>
            @endif
        </div>
    @else
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-2">Pilih Jasa Pengiriman</label>
            <select wire:model.live="selectedShippingService"
                    class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-[#8B4513] focus:border-transparent">
                <option value="">-- Pilih Metode Pengiriman --</option>
                @foreach ($shippingOptions as $index => $option)
                    <option value="{{ json_encode(['courier' => $option['courier'], 'service' => $option['service'], 'cost' => $option['cost']]) }}">
                        {{ $option['name'] }} - {{ $option['service'] }} (Rp {{ number_format($option['cost'], 0, ',', '.') }}) - Est: {{ $option['estimation'] }}
                    </option>
                @endforeach
            </select>
            
            @if ($selectedShippingService)
                <div class="mt-4 p-4 rounded-lg" style="background-color: #FDF8F3; border: 2px solid #8B4513;">
                    <h3 class="font-semibold text-gray-900 text-sm" style="color: #8B4513;">
                        {{ $selectedShippingService['name'] ?? $selectedShippingService['courier'] }} - {{ $selectedShippingService['service'] }}
                    </h3>
                    <p class="text-xs text-gray-600 mt-1">{{ $selectedShippingService['description'] ?? '' }}</p>
                    @if (isset($selectedShippingService['estimation']))
                        <p class="text-xs text-gray-500 mt-1">Estimasi: {{ $selectedShippingService['estimation'] }}</p>
                    @endif
                    <p class="font-bold text-base mt-2" style="color: #8B4513;">
                        Biaya: Rp {{ number_format($selectedShippingService['cost'] ?? 0, 0, ',', '.') }}
                    </p>
                </div>
            @endif
        </div>
    @endif
</div>
