<div class="bg-white rounded-lg border border-gray-200 p-6">
    <h2 class="text-xl font-bold mb-1" style="color: #8B4513;">
        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
        </svg>
        Pilih Metode Pengiriman
    </h2>
    <p class="text-gray-600 text-xs mb-4">Pilih salah satu metode pengiriman untuk pesanan Anda</p>

    <div>
        <label class="block text-xs font-semibold text-gray-700 mb-2">Pilih Jasa Pengiriman</label>
        
        @if ($loadingShippingOptions)
            <div class="w-full px-3 py-8 border border-gray-300 rounded text-sm text-center bg-gray-50">
                <div class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-[#8B4513]"></div>
                <span class="ml-2 text-gray-600">Memuat opsi pengiriman...</span>
            </div>
        @else
            <div class="flex gap-2 mb-2">
                <select wire:model.live="selectedShippingService"
                        class="flex-1 px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-[#8B4513] focus:border-transparent">
                    <option value="">-- Pilih Metode Pengiriman --</option>
                    @if (empty($this->shippingOptions))
                        <option value="" disabled>-- Tidak ada opsi pengiriman tersedia --</option>
                    @else
                        @foreach ($this->shippingOptions as $option)
                            <option value="{{ json_encode(['courier' => $option['courier'], 'service' => $option['service'], 'cost' => $option['cost']]) }}">
                                {{ $option['name'] }} - {{ $option['service'] }} (Rp {{ number_format($option['cost'], 0, ',', '.') }}) - Est: {{ $option['etd'] }}
                            </option>
                        @endforeach
                    @endif
                </select>
                <button wire:click="forceReloadShipping" 
                        class="px-3 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded text-sm"
                        title="Refresh opsi pengiriman">
                    🔄
                </button>
            </div>
            
            @if (count($this->shippingOptions) > 0)
                <p class="text-xs text-gray-500">{{ count($this->shippingOptions) }} opsi pengiriman tersedia</p>
            @endif
        @endif
        
        @if ($selectedShippingService)
            <div class="mt-4 p-4 rounded-lg" style="background-color: #FDF8F3; border: 2px solid #8B4513;">
                <h3 class="font-semibold text-gray-900 text-sm" style="color: #8B4513;">
                    {{ $selectedShippingService['name'] ?? $selectedShippingService['courier'] }} - {{ $selectedShippingService['service'] }}
                </h3>
                <p class="text-xs text-gray-600 mt-1">{{ $selectedShippingService['description'] ?? '' }}</p>
                @if (isset($selectedShippingService['etd']))
                    <p class="text-xs text-gray-500 mt-1">📦 Estimasi: {{ $selectedShippingService['etd'] }}</p>
                @endif
                <p class="font-bold text-base mt-2" style="color: #8B4513;">
                    Biaya: Rp {{ number_format($selectedShippingService['cost'] ?? 0, 0, ',', '.') }}
                </p>
            </div>
        @endif
    </div>
</div>
