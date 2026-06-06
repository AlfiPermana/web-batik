<x-layouts.landing title="Shop - Batik Giri Alam Gumelem Wetan">
    <!-- Header Section -->
    <section class="py-16 bg-[#f5f1e8]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-4">
                <h1 class="text-3xl md:text-4xl font-bold mb-4"
                    style="font-family: 'Playfair Display', serif; color: #8B4513;">
                    Produk Batik Giri Alam
                </h1>
                <p class="text-gray-700 text-sm md:text-base max-w-2xl mx-auto"
                    style="font-family: 'Poppins', sans-serif;">
                    Jelajahi koleksi tradisional kami dari seni dengan beragam motif khas<br class="hidden md:block">
                    dan filosofi mendalam
                </p>
            </div>
        </div>
    </section>

    <!-- Products Grid -->
    <section class="py-12" style="background-color: #fafaf8;">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                @foreach ($products as $product)
                    <div class="group shadow-md hover:shadow-lg transition-shadow duration-300 rounded-lg overflow-hidden">
                        <!-- Product Image -->
                        <a href="{{ route('landing.product.detail', $product->id) }}"
                            class="overflow-hidden rounded-lg mb-4 block relative">
                            <div class="aspect-square bg-gray-100 overflow-hidden">
                                @if ($product->images->count() > 0)
                                    <img src="{{ asset('storage/' . $product->images->first()->photo) }}"
                                        alt="{{ $product->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <img src="{{ asset('storage/' . $product->photo) }}" alt="{{ $product->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @endif
                            </div>
                        </a>

                        <!-- Product Info -->
                        <div class="p-4 flex flex-col gap-2">
                            <!-- Title -->
                            <a href="{{ route('landing.product.detail', $product->id) }}" class="block">
                                <h3 class="text-gray-800 font-medium text-sm"
                                    style="font-family: 'Poppins', sans-serif; color: #8B4513;">
                                    {{ $product->title }}
                                </h3>
                            </a>

                            <!-- Price and Button Row -->
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-gray-900 font-semibold text-sm"
                                    style="font-family: 'Poppins', sans-serif;">
                                    Rp {{ number_format($product->amount, 0, ',', '.') }}
                                </p>
                                <div class="flex items-center gap-2">
                                    <livewire:toggle-wishlist :product-id="$product->id" :compact="true" :key="'wl-' . $product->id" />
                                    <livewire:add-to-cart :product-id="$product->id" :compact="true" :key="'atc-' . $product->id" />
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12">
                {{ $products->links() }}
            </div>
        </div>
    </section>
</x-layouts.landing>
