<div class="w-full" style="font-family: 'Poppins', sans-serif;">
    <!-- Header -->
    <h1 class="text-3xl md:text-4xl font-bold mb-6 md:mb-8" style="font-family: 'Playfair Display', serif; color: #8B4513;">{{ __('Keranjang Belanja') }}</h1>

    @if (!$cart->isEmpty())
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Cart Table (Left Side) -->
            <div class="lg:col-span-7">
                <!-- Table Container with Horizontal Scroll -->
                <div class="bg-white rounded-lg border border-gray-200 overflow-x-auto overflow-y-hidden">
                    <table class="w-full min-w-max">
                        <!-- Table Header -->
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-2 md:px-4 py-2 md:py-3 text-left text-xs font-semibold text-gray-600">{{ __('Produk') }}</th>
                                <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-semibold text-gray-600">{{ __('Detail') }}</th>
                                <th class="px-2 md:px-4 py-2 md:py-3 text-center text-xs font-semibold text-gray-600">{{ __('Harga') }}</th>
                                <th class="px-2 md:px-4 py-2 md:py-3 text-center text-xs font-semibold text-gray-600">{{ __('Jumlah') }}</th>
                                <th class="px-2 md:px-4 py-2 md:py-3 text-right text-xs font-semibold text-gray-600">{{ __('Total') }}</th>
                                <th class="px-2 md:px-4 py-2 md:py-3 text-center text-xs font-semibold text-gray-600"></th>
                            </tr>
                        </thead>

                        <!-- Table Body -->
                        <tbody>
                            @foreach ($cartItems as $item)
                                @php
                                    $product = $item['product'];
                                    $size = $item['size'];
                                    $itemSubtotal = $item['price'] * $item['quantity'];
                                @endphp
                                <tr wire:key="cart-item-{{ $item['id'] }}" class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                    <!-- Product Image -->
                                    <td class="px-2 md:px-4 py-2 md:py-3">
                                        <div class="h-12 md:h-14 w-12 md:w-14 rounded bg-gray-100 overflow-hidden flex-shrink-0">
                                            @if ($product['photo'])
                                                <img src="{{ asset('storage/' . $product['photo']) }}" alt="{{ $product['title'] }}" class="h-full w-full object-cover">
                                            @else
                                                <div class="flex h-full w-full items-center justify-center text-gray-400">
                                                    <svg class="h-5 w-5 md:h-6 md:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Product Name & Variant -->
                                    <td class="hidden md:table-cell px-4 py-3">
                                        <div>
                                            <p class="font-semibold text-gray-900 text-xs">{{ $product['title'] }}</p>
                                            <p class="text-xs text-gray-600 mt-0.5">Variasi: {{ $size['size'] }}</p>
                                        </div>
                                    </td>

                                    <!-- Price -->
                                    <td class="px-2 md:px-4 py-2 md:py-3 text-center">
                                        <p class="text-xs font-medium text-gray-900">Rp</p>
                                        <p class="text-xs md:text-sm font-semibold text-gray-900 line-clamp-1">{{ number_format($item['price'], 0, ',', '.') }}</p>
                                    </td>

                                    <!-- Quantity Controls -->
                                    <td class="px-2 md:px-4 py-2 md:py-3">
                                        <div class="flex items-center justify-center gap-1">
                                            <button 
                                                wire:click="decrementQuantity({{ $item['id'] }})"
                                                wire:loading.attr="disabled"
                                                wire:target="decrementQuantity({{ $item['id'] }})"
                                                class="w-6 h-6 md:w-6 md:h-6 rounded border flex items-center justify-center text-xs font-bold transition-colors {{ $item['quantity'] <= 1 ? 'border-gray-200 bg-gray-100 text-gray-400 cursor-not-allowed' : 'border-gray-300 bg-white text-gray-600 hover:bg-gray-200' }}"
                                                {{ $item['quantity'] <= 1 ? 'disabled' : '' }}>
                                                −
                                            </button>
                                            <input 
                                                type="number" 
                                                wire:change="updateQuantity({{ $item['id'] }}, $event.target.value)"
                                                value="{{ $item['quantity'] }}"
                                                min="1"
                                                max="{{ $size ? ($size['stock'] ?? 100) : 100 }}"
                                                class="w-8 md:w-10 text-center border border-gray-300 rounded bg-white text-gray-900 font-semibold py-0.5 text-xs">
                                            <button 
                                                wire:click="incrementQuantity({{ $item['id'] }})"
                                                wire:loading.attr="disabled"
                                                wire:target="incrementQuantity({{ $item['id'] }})"
                                                class="w-6 h-6 md:w-6 md:h-6 rounded border flex items-center justify-center text-xs font-bold transition-colors {{ $item['quantity'] >= ($size ? ($size['stock'] ?? 0) : 0) ? 'border-gray-200 bg-gray-100 text-gray-400 cursor-not-allowed' : 'border-gray-300 bg-white text-gray-600 hover:bg-gray-200' }}"
                                                {{ $item['quantity'] >= ($size ? ($size['stock'] ?? 0) : 0) ? 'disabled' : '' }}>
                                                +
                                            </button>
                                        </div>
                                    </td>

                                    <!-- Subtotal -->
                                    <td class="px-2 md:px-4 py-2 md:py-3 text-right">
                                        <p class="text-xs font-medium text-gray-900">Rp</p>
                                        <p class="text-xs md:text-sm font-semibold text-gray-900 line-clamp-1">{{ number_format($itemSubtotal, 0, ',', '.') }}</p>
                                    </td>

                                    <!-- Delete Button -->
                                    <td class="px-2 md:px-4 py-2 md:py-3 text-center">
                                        <button 
                                            wire:click="removeItem({{ $item['id'] }})"
                                            class="text-red-400 hover:text-red-600 transition-colors">
                                            <svg class="h-4 w-4 md:h-4 md:w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Clear Cart Button -->
                <div class="mt-6 text-center">
                    <button 
                        wire:click="clearCart"
                        class="px-6 md:px-8 py-2 text-sm md:text-base border border-red-400 text-red-500 font-medium rounded hover:bg-red-50 transition-colors">
                        {{ __('Kosongkan Keranjang') }}
                    </button>
                </div>
            </div>

            <!-- Order Summary (Right Sidebar) -->
            <div class="lg:col-span-5 pl-0">
                <div class="bg-white rounded-lg border border-gray-200 p-4 md:p-6 lg:sticky lg:top-24 h-fit shadow-sm">
                    <h2 class="text-lg md:text-xl font-bold mb-4 md:mb-6" style="font-family: 'Playfair Display', serif; color: #8B4513;">{{ __('Ringkasan Pesanan') }}</h2>

                    <!-- Summary Details -->
                    <div class="space-y-3 md:space-y-4 pb-4 md:pb-6 border-b border-gray-200">
                        <div class="flex justify-between text-xs md:text-sm">
                            <span class="text-gray-700">{{ __('Subtotal') }}</span>
                            <span class="text-gray-900 font-medium">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-xs md:text-sm">
                            <span class="text-gray-700">{{ __('Pengiriman') }}</span>
                            <span class="text-red-600 font-medium text-xs">{{ __('Dihitung saat checkout') }}</span>
                        </div>
                        <div class="flex justify-between text-xs md:text-sm">
                            <span class="text-gray-700">{{ __('Diskon') }}</span>
                            <span class="text-gray-900 font-medium">-</span>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="py-4 md:py-6 mb-4 md:mb-6">
                        <div class="flex justify-between items-baseline gap-2 mb-1">
                            <span class="text-gray-900 font-semibold text-sm md:text-base">{{ __('Total') }}</span>
                            <span class="text-2xl md:text-3xl font-bold" style="color: #8B4513;">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-xs text-gray-600 mt-2">*{{ __('Belum termasuk biaya pengiriman') }}</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-2 md:space-y-3">
                        <a href="{{ route('checkout') }}" class="w-full py-2.5 md:py-3 rounded font-semibold text-white text-sm md:text-base transition-colors flex items-center justify-center gap-2" style="background-color: #8B4513;" onmouseover="this.style.backgroundColor='#6B3410'" onmouseout="this.style.backgroundColor='#8B4513'">
                            {{ __('Lanjut ke Checkout') }}
                            <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        <a href="{{ route('landing.shop') }}" class="block w-full py-2.5 md:py-3 rounded font-semibold text-center border border-gray-300 text-gray-900 text-sm md:text-base hover:bg-gray-50 transition-colors">
                            {{ __('Lanjut Belanja') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty Cart State -->
        <div class="rounded-lg border-2 border-dashed border-gray-300 py-16 text-center bg-gray-50">
            <svg class="mx-auto h-20 w-20 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <h3 class="mt-4 text-3xl font-bold" style="font-family: 'Playfair Display', serif; color: #8B4513;">{{ __('Keranjang Kosong') }}</h3>
            <p class="mt-2 text-gray-600">{{ __('Belum ada produk di keranjang Anda') }}</p>
            <a href="{{ route('landing.shop') }}" class="mt-8 inline-block rounded-lg py-3 px-8 font-semibold text-white transition-colors" style="background-color: #8B4513;" onmouseover="this.style.backgroundColor='#6B3410'" onmouseout="this.style.backgroundColor='#8B4513'">
                {{ __('Mulai Belanja') }}
            </a>
        </div>
    @endif
</div>
