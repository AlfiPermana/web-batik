<div id="addToCartComponent-{{ $productId }}">
    <!-- DEBUG: Remove this after testing -->
    {{-- @dump(['productData' => $productData, 'authenticated' => auth()->check(), 'compact' => $compact]) --}}
    
    <!-- Add to Cart Button -->
    @if ($productData)
        @if (!auth()->check())
            <!-- Login Required Button -->
            @if ($compact)
                <a href="{{ route('login') }}"
                    class="inline-flex items-center justify-center p-3 rounded-full bg-gray-400 text-white hover:bg-gray-500 transition-colors"
                    style="font-family: 'Poppins', sans-serif;"
                    title="Harus login untuk menambahkan ke keranjang">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 4a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </a>
            @else
                <a href="{{ route('login') }}"
                    class="w-full bg-gray-400 text-white py-3 px-6 text-sm font-semibold hover:bg-gray-500 transition-colors rounded flex items-center justify-center gap-2 h-12"
                    style="font-family: 'Poppins', sans-serif;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 16l4-4m0 0l-4-4m4 4H4m12 0v4m0-8V4" />
                    </svg>
                    <span>Login untuk Membeli</span>
                </a>
            @endif
        @elseif ($compact)
            <!-- Icon Only (for Shop Listing) -->
            <button 
                id="addToCartBtn-{{ $productId }}"
                wire:click="addToCart"
                class="inline-flex items-center justify-center p-3 rounded-full bg-[#8B4513] text-white hover:bg-[#6B3410] transition-colors"
                style="font-family: 'Poppins', sans-serif;"
                wire:loading.attr="disabled"
                title="Tambahkan ke Keranjang">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 4a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </button>
        @else
            <!-- Full Width Button (for Product Detail) -->
            <button 
                id="addToCartBtn-{{ $productId }}"
                wire:click="addToCart"
                class="w-full bg-[#8B4513] text-white py-3 px-6 text-sm font-semibold hover:bg-[#6B3410] transition-colors rounded flex items-center justify-center gap-2 h-12"
                style="font-family: 'Poppins', sans-serif;"
                wire:loading.attr="disabled">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 4a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>Tambahkan ke Keranjang</span>
            </button>
        @endif
    @endif

    <!-- Modal (Disabled) -->
    @if (false)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="w-full max-w-md rounded-lg bg-white p-6">
                <!-- Modal Header -->
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">{{ __('Add to Cart') }}</h2>
                    <button 
                        wire:click="$toggle('showModal')"
                        class="text-gray-500 hover:text-gray-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Product Info -->
                <div class="mb-6 rounded-lg bg-gray-100 p-4">
                    <p class="text-sm text-gray-600">{{ __('Product') }}</p>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $product->title }}</h3>
                    <p class="mt-2 text-xl font-bold text-amber-600">
                        Rp {{ number_format($product->amount, 0, ',', '.') }}
                    </p>
                </div>

                <!-- Form -->
                <form wire:submit="addToCart" class="space-y-4">
                    <!-- Size Selection -->
                    <div id="addToCartComponent-{{ $productId }}">
                        <label for="size" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('Select Size') }} <span class="text-red-500">*</span>
                        </label>
                        <select 
                            wire:model="sizeId"
                            id="size"
                            class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 focus:border-amber-500 focus:ring-amber-500">
                            <option value="">{{ __('-- Choose Size --') }}</option>
                            @foreach ($sizes as $size)
                                <option value="{{ $size['id'] }}">
                                    {{ $size['size'] }} - Rp {{ number_format($size['price'], 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                        @error('sizeId')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Quantity Selection -->
                    <div id="addToCartComponent-{{ $productId }}">
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('Quantity') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center gap-3">
                            <button 
                                type="button"
                                wire:click="$set('quantity', Math.max(1, {{ $quantity }} - 1))"
                                class="rounded-md border border-gray-300 px-3 py-2 text-gray-700 hover:bg-gray-100">
                                −
                            </button>
                            <input 
                                type="number" 
                                wire:model.debounce="quantity"
                                id="quantity"
                                min="1"
                                max="100"
                                class="w-16 rounded-md border border-gray-300 px-3 py-2 text-center bg-white text-gray-900">
                            <button 
                                type="button"
                                wire:click="$set('quantity', Math.min(100, {{ $quantity }} + 1))"
                                class="rounded-md border border-gray-300 px-3 py-2 text-gray-700 hover:bg-gray-100">
                                +
                            </button>
                        </div>
                        @error('quantity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3 pt-6 w-full">
                        <button 
                            type="button"
                            wire:click="$toggle('showModal')"
                            class="flex-1 rounded-lg border border-gray-300 px-4 py-2 font-semibold text-gray-900 hover:bg-gray-50 transition-colors">
                            {{ __('Cancel') }}
                        </button>
                        <button 
                            type="submit"
                            class="flex-1 rounded-lg bg-[#8B4513] px-4 py-3 font-semibold text-white hover:bg-[#6B3410] transition-colors">
                            {{ __('Add to Cart') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
