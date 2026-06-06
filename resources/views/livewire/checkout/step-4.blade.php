<div class="bg-white rounded-lg border border-gray-200 p-6">
    <h2 class="text-xl font-bold mb-1" style="color: #8B4513;">
        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Review Pesanan Anda
    </h2>
    <p class="text-gray-600 text-xs mb-4">Periksa kembali detail pesanan Anda sebelum mengkonfirmasi</p>

    <!-- Shipping Address -->
    <div class="mb-4 pb-4 border-b border-gray-200">
        <h3 class="font-semibold text-gray-900 mb-2 text-sm">Alamat Pengiriman</h3>
        <p class="text-xs text-gray-600 leading-relaxed">
            <strong>{{ $fullName }}</strong><br>
            {{ $address }}<br>
            @if ($subdistrict) {{ $subdistrict }}, @endif
            @if ($district) {{ $district }}, @endif
            @if ($city) {{ $city }}, @endif
            @if ($province) {{ $province }} @endif
            @if ($postalCode) {{ $postalCode }} @endif
            <br>
            {{ $phoneNumber }}
        </p>
    </div>

    <!-- Shipping Method -->
    @if ($selectedShippingService)
        <div class="mb-4 pb-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900 mb-2 text-sm">Metode Pengiriman</h3>
            <p class="text-xs text-gray-600 leading-relaxed">
                <strong>{{ $selectedShippingService['name'] ?? $selectedShippingService['courier'] }} - {{ $selectedShippingService['service'] }}</strong><br>
                {{ $selectedShippingService['description'] }}<br>
                <span class="text-gray-500">Estimasi: {{ $selectedShippingService['etd'] ?? 'N/A' }}</span>
            </p>
        </div>
    @endif

    <!-- Payment Method -->
    @if ($selectedPaymentMethod)
        <div class="mb-4 pb-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900 mb-2 text-sm">Metode Pembayaran</h3>
            <p class="text-xs text-gray-600 leading-relaxed">
                <strong>{{ $selectedPaymentMethod['name'] }}</strong><br>
                {{ $selectedPaymentMethod['description'] }}
            </p>
        </div>
    @endif

    <!-- Order Items -->
    <div class="bg-gray-50 rounded p-4 mb-4">
        <h3 class="font-semibold text-gray-900 mb-3 text-sm">Rincian Pesanan ({{ count($cartItems) }} produk)</h3>
        <div class="space-y-2">
            @foreach ($cartItems as $item)
                <div class="flex gap-2 py-2 border-b border-gray-200 last:border-0">
                    @if ($item['product']['photo'])
                        <img src="{{ asset('storage/' . $item['product']['photo']) }}" alt="{{ $item['product']['title'] }}"
                             class="w-12 h-12 object-cover rounded flex-shrink-0">
                    @else
                        <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center flex-shrink-0">
                            <span class="text-xs text-gray-500">No Img</span>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 text-xs">{{ substr($item['product']['title'], 0, 30) }}{{ strlen($item['product']['title']) > 30 ? '...' : '' }}</p>
                        <p class="text-xs text-gray-600">{{ $item['size']['size'] }} | x{{ $item['quantity'] }}</p>
                        <p class="text-xs font-semibold text-gray-900 mt-1">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Price Breakdown -->
    <div class="bg-gray-50 rounded p-4 space-y-2">
        <div class="flex justify-between text-xs">
            <span class="text-gray-700">Subtotal:</span>
            <span class="font-semibold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between text-xs">
            <span class="text-gray-700">Biaya Pengiriman:</span>
            <span class="font-semibold">Rp {{ number_format($shippingCost, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between text-sm pt-2 border-t border-gray-200 font-semibold">
            <span class="text-gray-900">Total Pembayaran:</span>
            <span style="color: #8B4513;">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
        </div>
    </div>
</div>
