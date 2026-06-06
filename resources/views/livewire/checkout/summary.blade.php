<div class="bg-white rounded-lg border border-gray-200 p-6 h-fit">
    <h3 class="text-lg font-bold mb-3" style="color: #8B4513;">Ringkasan Pesanan</h3>

    <div class="space-y-2 pb-3 border-b border-gray-200 mb-4">
        <div class="flex justify-between text-xs">
            <span class="text-gray-600">Subtotal</span>
            <span class="font-semibold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between text-xs">
            <span class="text-gray-600">Pengiriman</span>
            <span class="font-semibold">Rp {{ number_format($shippingCost, 0, ',', '.') }}</span>
        </div>
        @if ($discount > 0)
            <div class="flex justify-between text-xs">
                <span class="text-gray-600">Diskon</span>
                <span class="font-semibold text-green-600">-Rp {{ number_format($discount, 0, ',', '.') }}</span>
            </div>
        @endif
    </div>

    <div class="mb-4">
        <div class="flex justify-between items-baseline">
            <span class="text-gray-900 font-semibold text-sm">Total</span>
            <span class="text-xl font-bold" style="color: #8B4513;">
                Rp {{ number_format($totalAmount, 0, ',', '.') }}
            </span>
        </div>
    </div>

    <!-- Items List -->
    <div class="bg-gray-50 rounded p-3">
        <h4 class="text-xs font-semibold text-gray-900 mb-2">Produk ({{ count($cartItems) }})</h4>
        <div class="space-y-1 max-h-40 overflow-y-auto">
            @foreach ($cartItems as $item)
                <div class="text-xs text-gray-600 flex justify-between pb-1 border-b border-gray-200 last:border-0">
                    <div>
                        <p class="font-medium text-gray-900">{{ substr($item['product']['title'], 0, 20) }}{{ strlen($item['product']['title']) > 20 ? '...' : '' }}</p>
                        <p class="text-gray-500 text-xs">{{ $item['size']['size'] }}</p>
                    </div>
                    <p class="font-semibold">x{{ $item['quantity'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>
