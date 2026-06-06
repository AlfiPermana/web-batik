<div class="space-y-6">
    <div class="flex items-end justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold" style="font-family: 'Playfair Display', serif; color: #8B4513;">{{ __('Wishlist') }}</h1>
            <p class="mt-1 text-sm text-gray-600" style="font-family: 'Poppins', sans-serif;">{{ __('Produk yang kamu simpan untuk dibeli nanti') }}</p>
        </div>
    </div>

    @if($items->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($items as $item)
                @php
                    $product = $item->product;
                    $imgPath = $product?->images?->first()?->photo ?? $product?->photo;
                @endphp
                <div class="rounded-xl border border-amber-200/70 bg-white overflow-hidden shadow-sm">
                    <a href="{{ route('landing.product.detail', $product->id) }}" class="block">
                        <div class="aspect-square bg-gray-100 overflow-hidden">
                            @if($imgPath)
                                <img src="{{ asset('storage/' . $imgPath) }}"
                                     alt="{{ $product->title }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">No Image</div>
                            @endif
                        </div>
                    </a>

                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <a href="{{ route('landing.product.detail', $product->id) }}" class="block">
                                    <p class="font-semibold text-gray-900 dark:text-white truncate">{{ $product->title }}</p>
                                </a>
                                <p class="text-sm font-bold mt-1" style="color:#8B4513;">Rp {{ number_format($product->amount, 0, ',', '.') }}</p>
                            </div>
                            <livewire:toggle-wishlist :product-id="$product->id" :compact="true" :key="'wl-toggle-' . $item->id" />
                        </div>

                        <div class="mt-4 flex items-center justify-between gap-2">
                            <livewire:add-to-cart :product-id="$product->id" :compact="true" :key="'wl-atc-' . $product->id" />
                            <a href="{{ route('landing.product.detail', $product->id) }}"
                               class="text-sm font-semibold hover:underline" style="color:#8B4513;">
                                {{ __('Lihat Produk') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div>
            {{ $items->links(data: ['scrollTo' => false]) }}
        </div>
    @else
        <div class="rounded-xl border-2 border-dashed border-amber-200/70 bg-amber-50/40 py-10 sm:py-12 text-center">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900" style="font-family: 'Poppins', sans-serif;">{{ __('Wishlist masih kosong') }}</h3>
            <p class="mt-1 text-sm text-gray-600" style="font-family: 'Poppins', sans-serif;">{{ __('Tambahkan produk dari halaman shop') }}</p>
            <a href="{{ route('landing.shop') }}" class="mt-4 inline-block px-5 py-2.5 rounded-lg text-white font-semibold transition-all hover:shadow" style="background-color:#8B4513; font-family: 'Poppins', sans-serif;">
                {{ __('Browse Products') }}
            </a>
        </div>
    @endif
</div>
