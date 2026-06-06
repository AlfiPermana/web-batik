<div class="bg-white rounded-lg border border-gray-200 p-6">
    <h2 class="text-xl font-bold mb-1" style="color: #8B4513;">
        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
        </svg>
        Pilih Metode Pembayaran
    </h2>
    <p class="text-gray-600 text-xs mb-4">Pilih salah satu metode pembayaran untuk melanjutkan</p>

    @if($paymentMethods && count($paymentMethods) > 0)
        <div class="mb-4">
            <select 
                wire:model.live="selectedPaymentMethod"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#8B4513] focus:border-transparent text-sm">
                <option value="">-- Pilih Metode Pembayaran --</option>
                @foreach($paymentMethods as $method)
                    <option value="{{ json_encode($method) }}">
                        {{ $method['name'] }}
                        @if($method['fee'] > 0)
                            (+Rp {{ number_format($method['fee'], 0, ',', '.') }})
                        @endif
                    </option>
                @endforeach
            </select>
        </div>

        @if($selectedPaymentMethod)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <img src="{{ $selectedPaymentMethod['icon'] }}" alt="{{ $selectedPaymentMethod['name'] }}" class="w-12 h-12 object-contain">
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900 text-sm">{{ $selectedPaymentMethod['name'] }}</h4>
                        <p class="text-xs text-gray-600 mt-1">{{ $selectedPaymentMethod['description'] }}</p>
                        @if($selectedPaymentMethod['fee'] > 0)
                            <p class="text-xs font-medium mt-2" style="color: #8B4513;">
                                Biaya administrasi: Rp {{ number_format($selectedPaymentMethod['fee'], 0, ',', '.') }}
                            </p>
                        @endif
                    </div>
                    <button 
                        type="button"
                        wire:click="selectPaymentMethod('')"
                        class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-xs text-yellow-800">
                    Pilih metode pembayaran di atas untuk melanjutkan ke langkah berikutnya
                </p>
            </div>
        @endif
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-sm text-yellow-800">Memproses metode pembayaran...</p>
        </div>
    @endif
</div>
