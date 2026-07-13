<x-layouts.landing title="{{ $product->title }} - Batik Giri Alam Gumelem Wetan">
    <!-- Breadcrumb -->
    <div style="background-color: #f5f1e8;" class="py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center text-sm text-gray-600">
                <a href="{{ route('landing.home') }}" class="hover:text-gray-900">Home</a>
                <span class="mx-2">/</span>
                <a href="{{ route('landing.shop') }}" class="hover:text-gray-900">Shop</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900">{{ $product->title }}</span>
            </div>
        </div>
    </div>

    <!-- Product Detail Section -->
    <section class="py-12" style="background-color: #fafaf8;">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                @php
                    $initialStock = (int) (($product->sizes->first()?->stock) ?? ($product->stock ?? 0));
                    $initialSizeId = $product->sizes->first()?->id;
                @endphp
                <!-- Left Column - Images -->
                <div>
                    <!-- Main Image Display -->
                    <div class="aspect-square bg-gray-100 mb-4 overflow-hidden relative">
                        <img id="mainImage"
                             src="{{ $product->images->count() > 0 ? asset('storage/' . $product->images->first()->photo) : asset('storage/' . $product->photo) }}"
                             alt="{{ $product->title }}"
                             class="w-full h-full object-cover transition duration-200 {{ $initialStock > 0 ? '' : 'out-of-stock-blur' }}">

                        <div id="outOfStockOverlay" class="absolute inset-0 pointer-events-none z-20 {{ $initialStock > 0 ? 'hidden' : '' }}">
                            <div class="absolute inset-0 bg-black/30"></div>
                            <div class="absolute z-10"
                                 style="top: 50%; left: 50%; width: 220%; transform: translate(-50%, -50%) rotate(-45deg); background: rgba(0,0,0,0.60); padding: 10px 0; color: #fff; text-align: center; font-size: 14px; font-weight: 700; letter-spacing: 0.08em; font-family: 'Poppins', sans-serif;">
                                STOK KOSONG
                            </div>
                        </div>
                    </div>

                    <!-- Image Gallery Thumbnails -->
                    @if($product->images->count() > 1)
                        <div class="grid grid-cols-4 gap-2">
                            @foreach($product->images as $image)
                                <div class="aspect-square bg-gray-100 overflow-hidden border-2 border-gray-200 cursor-pointer hover:border-gray-400 transition-colors thumbnail-image"
                                     onclick="changeMainImage('{{ asset('storage/' . $image->photo) }}', this)">
                                    <img src="{{ asset('storage/' . $image->photo) }}"
                                         alt="{{ $product->title }}"
                                         class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Right Column - Product Info -->
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold mb-3"
                        style="font-family: 'Playfair Display', serif; color: #8B4513;">
                        {{ $product->title }}
                    </h1>

                    <!-- Price -->
                    <div class="mb-4">
                        <p class="text-xl font-bold" style="font-family: 'Poppins', sans-serif; color: #8B4513;" id="displayPrice">
                            Rp {{ number_format($product->amount, 0, ',', '.') }}
                        </p>
                    </div>

                    <!-- Stock -->
                    <div class="mb-6">
                        <p class="text-sm" style="font-family: 'Poppins', sans-serif;">
                            <span class="text-gray-600">Stok tersedia:</span>
                            <span id="displayStock" class="font-semibold {{ $initialStock > 0 ? 'text-gray-900' : 'text-red-600' }}">
                                {{ $initialStock > 0 ? $initialStock . ' item' : 'Habis' }}
                            </span>
                        </p>
                        <input type="hidden" id="selectedSizeId" value="{{ $initialSizeId ?? '' }}">
                        <input type="hidden" id="selectedStock" value="{{ $initialStock }}">
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold mb-2" style="font-family: 'Poppins', sans-serif; color: #D2691E;">
                            Deskripsi
                        </h3>
                        <div class="text-sm text-gray-700 leading-relaxed product-description" style="font-family: 'Poppins', sans-serif;">
                            {!! $product->description !!}
                        </div>
                    </div>

                    <!-- Size Selection -->
                    @if($product->sizes->count() > 0)
                        <div class="mb-6">
                            <label class="block text-sm font-semibold mb-2" style="font-family: 'Poppins', sans-serif; color: #D2691E;">
                                Ukuran
                            </label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($product->sizes as $index => $size)
                                    <button type="button"
                                            class="size-option px-4 py-2 border-2 text-sm font-medium transition-colors {{ $index === 0 ? 'border-[#8B4513] bg-[#8B4513] text-white' : 'border-gray-300 bg-white text-gray-700 hover:border-gray-400' }}"
                                            data-size="{{ $size->size }}"
                                            data-size-id="{{ $size->id }}"
                                            data-price="{{ $size->price }}"
                                            data-stock="{{ (int) ($size->stock ?? 0) }}"
                                            onclick="selectSize(this)"
                                            style="font-family: 'Poppins', sans-serif;">
                                        {{ $size->size }}
                                    </button>
                                @endforeach
                            </div>
                            <input type="hidden" id="selectedSize" value="{{ $product->sizes->first()->size ?? '' }}">
                            <input type="hidden" id="selectedPrice" value="{{ $product->sizes->first()->price ?? $product->amount }}">
                        </div>
                    @endif

                    <!-- Jumlah -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-2" style="font-family: 'Poppins', sans-serif; color: #D2691E;">
                            Jumlah
                        </label>
                        <div class="flex items-center gap-3">
                            <button type="button" onclick="decreaseQuantity()"
                                    class="w-8 h-8 border border-gray-300 flex items-center justify-center hover:bg-gray-100 transition-colors"
                                    style="font-family: 'Poppins', sans-serif;">
                                -
                            </button>
                            <input type="number" id="quantity" value="1" min="1" readonly
                                   class="w-16 text-center border border-gray-300 py-1 text-sm"
                                   style="font-family: 'Poppins', sans-serif;">
                            <button type="button" onclick="increaseQuantity()"
                                    class="w-8 h-8 border border-gray-300 flex items-center justify-center hover:bg-gray-100 transition-colors"
                                    style="font-family: 'Poppins', sans-serif;">
                                +
                            </button>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3 mb-6 flex-col">
                        <div class="flex items-center gap-3">
                            <livewire:toggle-wishlist :product-id="$product->id" :key="'wl-detail-' . $product->id" />
                            <!-- Add to Cart Button -->
                            <div class="flex-1">
                                <livewire:add-to-cart :product-id="$product->id" :key="'atc-detail-' . $product->id" />
                            </div>
                        </div>

                        <!-- Back to Shop Button -->
                        <a href="{{ route('landing.shop') }}"
                           class="bg-gray-200 text-gray-900 py-3 px-6 text-center text-sm font-semibold hover:bg-gray-300 transition-colors rounded flex items-center justify-center gap-2 h-12"
                           style="font-family: 'Poppins', sans-serif;">
                            Kembali ke Shop
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Custom Styles for Rich Text -->
    <style>
        .out-of-stock-blur {
            filter: blur(6px);
        }

        .product-description ul {
            list-style-type: disc;
            padding-left: 1.5rem;
            margin: 1rem 0;
        }

        .product-description ol {
            list-style-type: decimal;
            padding-left: 1.5rem;
            margin: 1rem 0;
        }

        .product-description li {
            margin: 0.5rem 0;
            line-height: 1.5;
        }

        .product-description blockquote {
            border-left: 4px solid #d1d5db;
            padding-left: 1rem;
            margin: 1rem 0;
            font-style: italic;
            color: #6b7280;
        }

        .product-description strong {
            font-weight: 700;
            color: #111827;
        }

        .product-description em {
            font-style: italic;
        }

        .product-description p {
            margin: 0.5rem 0;
        }

        .product-description div {
            margin: 0.25rem 0;
        }

        .product-description h1,
        .product-description h2,
        .product-description h3,
        .product-description h4 {
            font-weight: 700;
            margin: 1rem 0 0.5rem 0;
        }

        .product-description a {
            color: #2563eb;
            text-decoration: underline;
        }
    </style>

    <!-- JavaScript for Interactive Features -->
    <script>
        // Change main image when thumbnail is clicked
        function changeMainImage(imageUrl, thumbnailElement) {
            document.getElementById('mainImage').src = imageUrl;

            // Update border styling
            document.querySelectorAll('.thumbnail-image').forEach(thumb => {
                thumb.classList.remove('border-black');
                thumb.classList.add('border-gray-200');
            });
            thumbnailElement.classList.remove('border-gray-200');
            thumbnailElement.classList.add('border-black');
        }

        function getSelectedStock() {
            const el = document.getElementById('selectedStock');
            const val = parseInt(el?.value ?? '0');
            return Number.isFinite(val) ? val : 0;
        }

        function updateOutOfStockOverlay(stock) {
            const overlayEl = document.getElementById('outOfStockOverlay');
            const imgEl = document.getElementById('mainImage');

            const s = parseInt(stock ?? 0);
            const inStock = Number.isFinite(s) && s > 0;

            if (overlayEl) {
                if (inStock) overlayEl.classList.add('hidden');
                else overlayEl.classList.remove('hidden');
            }

            if (imgEl) {
                if (inStock) imgEl.classList.remove('out-of-stock-blur');
                else imgEl.classList.add('out-of-stock-blur');
            }
        }

        function updateStockDisplay(stock) {
            const stockEl = document.getElementById('displayStock');
            if (!stockEl) return;

            const s = parseInt(stock ?? 0);
            if (Number.isFinite(s) && s > 0) {
                stockEl.textContent = `${s} item`;
                stockEl.classList.remove('text-red-600');
                stockEl.classList.add('text-gray-900');
            } else {
                stockEl.textContent = 'Habis';
                stockEl.classList.remove('text-gray-900');
                stockEl.classList.add('text-red-600');
            }

            updateOutOfStockOverlay(s);
        }

        function syncQuantityToLivewire() {
            const quantityInput = document.getElementById('quantity');
            if (!quantityInput) return;
            const qty = parseInt(quantityInput.value) || 1;

            if (window.Livewire) {
                if (typeof window.Livewire.dispatch === 'function') {
                    window.Livewire.dispatch('quantity-updated', { qty: qty, productId: {{ (int) $product->id }} });
                }
                
                // Hard-set to the AddToCart component directly for reliability
                const atcEl = document.getElementById('addToCartComponent-{{ (int) $product->id }}');
                const wireId = atcEl?.getAttribute('wire:id');
                if (wireId && typeof window.Livewire.find === 'function') {
                    const cmp = window.Livewire.find(wireId);
                    if (cmp && typeof cmp.set === 'function') {
                        cmp.set('quantity', qty);
                    } else if (cmp && cmp.$wire && typeof cmp.$wire.set === 'function') {
                        cmp.$wire.set('quantity', qty);
                    }
                }
            }
        }

        function clampQuantityToStock() {
            const stock = getSelectedStock();
            const quantityInput = document.getElementById('quantity');
            if (!quantityInput) return;

            let q = parseInt(quantityInput.value || '1');
            if (!Number.isFinite(q) || q < 1) q = 1;

            if (stock > 0 && q > stock) q = stock;
            quantityInput.value = q;
            syncQuantityToLivewire();
        }

        // Select size and update price
        function selectSize(button) {
            const size = button.dataset.size;
            const price = parseFloat(button.dataset.price);
            const stock = parseInt(button.dataset.stock ?? '0') || 0;
            const sizeId = button.dataset.sizeId;

            // Update selected values
            document.getElementById('selectedSize').value = size;
            document.getElementById('selectedPrice').value = price;
            const sizeIdEl = document.getElementById('selectedSizeId');
            if (sizeIdEl) sizeIdEl.value = sizeId ?? '';
            const stockEl = document.getElementById('selectedStock');
            if (stockEl) stockEl.value = String(stock);

            // Update price display
            document.getElementById('displayPrice').textContent =
                'Rp ' + new Intl.NumberFormat('id-ID').format(price);

            updateStockDisplay(stock);
            clampQuantityToStock();

            // Update button styling
            document.querySelectorAll('.size-option').forEach(btn => {
                btn.classList.remove('border-[#8B4513]', 'bg-[#8B4513]', 'text-white');
                btn.classList.add('border-gray-300', 'bg-white', 'text-gray-700');
            });
            button.classList.remove('border-gray-300', 'bg-white', 'text-gray-700');
            button.classList.add('border-[#8B4513]', 'bg-[#8B4513]', 'text-white');

            const addBtn = document.getElementById('addToCartBtn-{{ $product->id }}');
            if (addBtn) addBtn.disabled = stock <= 0;

            if (window.Livewire && sizeId) {
                const payload = {
                    productId: {{ (int) $product->id }},
                    sizeId: parseInt(sizeId)
                };

                if (typeof window.Livewire.dispatchTo === 'function') {
                    window.Livewire.dispatchTo('add-to-cart', 'product-size-selected', payload);
                } else if (typeof window.Livewire.dispatch === 'function') {
                    window.Livewire.dispatch('product-size-selected', payload);
                }

                // Hard-set ke komponen AddToCart di halaman ini (lebih reliable)
                const atcEl = document.getElementById('addToCartComponent-{{ (int) $product->id }}');
                const wireId = atcEl?.getAttribute('wire:id');
                if (wireId && typeof window.Livewire.find === 'function') {
                    const cmp = window.Livewire.find(wireId);
                    if (cmp && typeof cmp.set === 'function') {
                        cmp.set('sizeId', payload.sizeId);
                    } else if (cmp && cmp.$wire && typeof cmp.$wire.set === 'function') {
                        cmp.$wire.set('sizeId', payload.sizeId);
                    }
                }
            }
        }

        // Increase quantity
        function increaseQuantity() {
            const quantityInput = document.getElementById('quantity');
            let currentValue = parseInt(quantityInput.value);
            if (!Number.isFinite(currentValue) || currentValue < 1) currentValue = 1;

            const stock = getSelectedStock();
            if (stock > 0 && currentValue >= stock) return;

            quantityInput.value = currentValue + 1;
            syncQuantityToLivewire();
        }

        // Decrease quantity
        function decreaseQuantity() {
            const quantityInput = document.getElementById('quantity');
            let currentValue = parseInt(quantityInput.value);
            if (!Number.isFinite(currentValue) || currentValue <= 1) {
                quantityInput.value = 1;
                syncQuantityToLivewire();
                return;
            }
            quantityInput.value = currentValue - 1;
            syncQuantityToLivewire();
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateStockDisplay(getSelectedStock());
            clampQuantityToStock();
            const addBtn = document.getElementById('addToCartBtn-{{ $product->id }}');
            if (addBtn) addBtn.disabled = getSelectedStock() <= 0;
        });

        // Buy via WhatsApp
        function buyViaWhatsApp() {
            const productName = "{{ $product->title }}";
            const selectedSize = document.getElementById('selectedSize')?.value || '';
            const selectedPrice = parseFloat(document.getElementById('selectedPrice')?.value || '{{ $product->amount }}');
            const quantity = parseInt(document.getElementById('quantity')?.value || 1);

            // Calculate total price
            const totalPrice = selectedPrice * quantity;

            const formattedUnitPrice = new Intl.NumberFormat('id-ID').format(selectedPrice);
            const formattedTotalPrice = new Intl.NumberFormat('id-ID').format(totalPrice);

            // Construct WhatsApp message
            let message = `Halo, saya tertarik dengan produk berikut:\n\n`;
            message += `Nama: ${productName}\n`;
            if (selectedSize) {
                message += `Ukuran: ${selectedSize}\n`;
            }
            message += `Jumlah: ${quantity}\n`;
            message += `Harga per item: Rp ${formattedUnitPrice}\n`;
            message += `Total Harga: Rp ${formattedTotalPrice}\n\n`;
            message += `Apakah produk ini masih tersedia?`;

            // WhatsApp number (replace with actual number)
            const phoneNumber = '6285111230011'; // Ganti dengan nomor WhatsApp yang sebenarnya

            // Create WhatsApp URL
            const whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;

            // Open WhatsApp
            window.open(whatsappUrl, '_blank');
        }
    </script>
</x-layouts.landing>