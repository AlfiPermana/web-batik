<x-layouts.app title="{{ $order->order_number }}">
<div class="min-h-screen bg-gradient-to-b from-amber-50 to-gray-50">
    
    <!-- Header Section -->
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $order->order_number }}</h1>
                    <p class="mt-2 text-gray-600">Detail pesanan Anda</p>
                </div>
                <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 text-amber-600 hover:text-amber-700 font-semibold hover:bg-amber-50 rounded-lg transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="space-y-6">

            <!-- Order Status Card -->
            <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Status Pesanan
                </h2>

                <!-- Current Status Badge -->
                <div id="order-status-badge" class="mb-6 p-4 rounded-lg inline-block" style="
                    @switch($order->status)
                        @case('pending')
                            background-color: #fef3c7; border: 1px solid #fde68a;
                        @break
                        @case('processing')
                            background-color: #dbeafe; border: 1px solid #bfdbfe;
                        @break
                        @case('shipped')
                            background-color: #e9d5ff; border: 1px solid #d8b4fe;
                        @break
                        @case('delivered')
                            background-color: #dcfce7; border: 1px solid #bbf7d0;
                        @break
                        @case('cancelled')
                            background-color: #fee2e2; border: 1px solid #fecaca;
                        @break
                    @endswitch
                ">
                    <span id="order-status-label" class="text-sm font-bold" style="
                        @switch($order->status)
                            @case('pending')
                                color: #92400e;
                            @break
                            @case('processing')
                                color: #0c4a6e;
                            @break
                            @case('shipped')
                                color: #5b21b6;
                            @break
                            @case('delivered')
                                color: #15803d;
                            @break
                            @case('cancelled')
                                color: #7f1d1d;
                            @break
                        @endswitch
                    ">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>

                <!-- Order Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600">Tanggal Pesanan</p>
                        <p class="font-semibold text-gray-900 mt-1">{{ $order->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Total Pesanan</p>
                        <p class="font-bold text-lg text-amber-600 mt-1">Rp {{ number_format($order->total ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Status Pembayaran</p>
                        <div id="payment-status-badge" class="mt-1 inline-block px-3 py-1 rounded-full text-xs font-semibold"
                             style="
                                 @switch($order->payment_status)
                                     @case('paid')
                                     @case('confirmed')
                                         background-color: #dcfce7; color: #15803d;
                                     @break
                                     @case('unpaid')
                                     @case('pending')
                                         background-color: #fef3c7; color: #92400e;
                                     @break
                                     @case('expired')
                                         background-color: #ffedd5; color: #9a3412;
                                     @break
                                     @case('refunded')
                                         background-color: #dbeafe; color: #1e40af;
                                     @break
                                     @case('failed')
                                         background-color: #fee2e2; color: #7f1d1d;
                                     @break
                                     @default
                                         background-color: #f3f4f6; color: #374151;
                                 @endswitch
                             ">
                            <span id="payment-status-label">{{ match($order->payment_status) {
                                'unpaid', 'pending' => 'Menunggu Pembayaran',
                                'paid', 'confirmed' => 'Lunas',
                                'expired' => 'Kadaluarsa',
                                'failed' => 'Gagal',
                                'refunded' => 'Dikembalikan',
                                default => ucfirst($order->payment_status ?? 'unknown'),
                            } }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items Card -->
            <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    Item Pesanan
                </h2>

                @php $displayItems = $order->display_items; @endphp
                <div class="space-y-4">
                    @forelse ($displayItems as $item)
                        <div class="flex gap-4 pb-4 border-b border-gray-200 last:border-b-0">
                            <!-- Product Image -->
                            @php
                                // ProductImage column is `photo` (bukan `image_path`)
                                $imgPath = $item->product?->images?->first()?->photo ?? $item->product?->photo;
                            @endphp

                            @if ($imgPath)
                                <img src="{{ asset('storage/' . $imgPath) }}"
                                     alt="{{ $item->product?->title ?? 'Produk' }}"
                                     class="w-24 h-24 object-cover rounded-lg flex-shrink-0">
                            @else
                                <div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif

                            <!-- Product Details -->
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 text-base">{{ $item->product->title ?? 'Produk' }}</h3>
                                @if (!empty($item->is_recovered))
                                    <p class="text-xs text-amber-700 mt-1">Item dipulihkan dari data subtotal order</p>
                                @endif
                                @if ($item->productSize)
                                    <p class="text-sm text-gray-600 mt-1">Ukuran: {{ $item->productSize->size }}</p>
                                @endif
                                
                                <div class="mt-4 space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Harga Satuan</span>
                                        <span class="font-semibold text-gray-900">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Jumlah</span>
                                        <span class="font-semibold text-gray-900">{{ $item->quantity }} pcs</span>
                                    </div>
                                    <div class="flex justify-between text-sm pt-2 border-t border-gray-200">
                                        <span class="font-semibold text-gray-900">Subtotal</span>
                                        <span class="font-bold text-amber-600">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">Tidak ada item dalam pesanan ini</p>
                    @endforelse
                </div>
            </div>

            <!-- Shipping Address Card -->
            <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Alamat Pengiriman
                </h2>

                @php $addr = $order->shipping_address @endphp
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-gray-600">Penerima</p>
                        <p class="font-semibold text-gray-900">{{ $addr['full_name'] ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">No. Telepon</p>
                        <p class="font-semibold text-gray-900">{{ $addr['phone_number'] ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Alamat</p>
                        <p class="font-semibold text-gray-900">{{ $addr['address'] ?? '-' }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-gray-600 text-xs">Provinsi</p>
                            <p class="font-semibold text-gray-900">{{ $addr['province'] ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-xs">Kota</p>
                            <p class="font-semibold text-gray-900">{{ $addr['city'] ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-gray-600 text-xs">Kecamatan</p>
                            <p class="font-semibold text-gray-900">{{ $addr['district'] ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-xs">Kelurahan</p>
                            <p class="font-semibold text-gray-900">{{ $addr['subdistrict'] ?? '-' }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-gray-600">Kode Pos</p>
                        <p class="font-semibold text-gray-900">{{ $addr['postal_code'] ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Shipping & Payment Info Card -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Shipping Info -->
                <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Pengiriman
                    </h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-gray-600">Layanan</p>
                            <p class="font-semibold text-gray-900 mt-1">{{ $order->shipping_service ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Biaya</p>
                            <p class="font-bold text-amber-600 mt-1">Rp {{ number_format($order->shipping_cost ?? 0, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h4m4 0h4M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        Pembayaran
                    </h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-gray-600">Metode</p>
                            <p class="font-semibold text-gray-900 mt-1">{{ $order->payment_method ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary Card -->
            <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    Ringkasan Pesanan
                </h3>

                <div class="space-y-3 pb-4 border-b border-gray-200">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="text-gray-900 font-semibold">Rp {{ number_format($order->subtotal ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Biaya Pengiriman</span>
                        <span class="text-gray-900 font-semibold">Rp {{ number_format($order->shipping_cost ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="mt-4 pt-4">
                    <div class="flex justify-between items-baseline">
                        <span class="text-lg font-semibold text-gray-900">Total</span>
                        <span class="text-3xl font-bold text-amber-600">Rp {{ number_format($order->total ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</x-layouts.app>

@if(in_array($order->payment_status, ['unpaid', 'pending'], true))
<script>
document.addEventListener('DOMContentLoaded', () => {
    const orderId = @json($order->id);
    const paymentStatusBadge = document.getElementById('payment-status-badge');
    const paymentStatusLabel = document.getElementById('payment-status-label');
    const orderStatusBadge = document.getElementById('order-status-badge');
    const orderStatusLabel = document.getElementById('order-status-label');

    const paymentStyles = {
        paid: { bg: '#dcfce7', color: '#15803d', label: 'Lunas' },
        confirmed: { bg: '#dcfce7', color: '#15803d', label: 'Lunas' },
        unpaid: { bg: '#fef3c7', color: '#92400e', label: 'Menunggu Pembayaran' },
        pending: { bg: '#fef3c7', color: '#92400e', label: 'Menunggu Pembayaran' },
        expired: { bg: '#ffedd5', color: '#9a3412', label: 'Kadaluarsa' },
        refunded: { bg: '#dbeafe', color: '#1e40af', label: 'Dikembalikan' },
        failed: { bg: '#fee2e2', color: '#7f1d1d', label: 'Gagal' },
    };

    const orderStyles = {
        pending: { bg: '#fef3c7', border: '#fde68a', color: '#92400e', label: 'Pending' },
        processing: { bg: '#dbeafe', border: '#bfdbfe', color: '#0c4a6e', label: 'Processing' },
        shipped: { bg: '#e9d5ff', border: '#d8b4fe', color: '#5b21b6', label: 'Shipped' },
        delivered: { bg: '#dcfce7', border: '#bbf7d0', color: '#15803d', label: 'Delivered' },
        cancelled: { bg: '#fee2e2', border: '#fecaca', color: '#7f1d1d', label: 'Cancelled' },
    };

    const applyStatuses = (data) => {
        const payment = paymentStyles[data.payment_status] || { bg: '#f3f4f6', color: '#374151', label: data.payment_status || 'Unknown' };
        const order = orderStyles[data.status] || { bg: '#f3f4f6', border: '#d1d5db', color: '#374151', label: data.status || 'Unknown' };

        paymentStatusBadge.style.backgroundColor = payment.bg;
        paymentStatusBadge.style.color = payment.color;
        paymentStatusLabel.textContent = payment.label;

        orderStatusBadge.style.backgroundColor = order.bg;
        orderStatusBadge.style.border = `1px solid ${order.border}`;
        orderStatusLabel.style.color = order.color;
        orderStatusLabel.textContent = order.label;

        if (['paid', 'confirmed', 'failed', 'expired', 'refunded'].includes(data.payment_status)) {
            clearInterval(window.__orderTripayPoller);
        }
    };

    const pollStatus = async () => {
        try {
            const response = await fetch(`/api/payment/${orderId}/status`, {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            });

            if (!response.ok) return;

            const data = await response.json();
            if (!data || !data.payment_status) return;

            applyStatuses(data);
        } catch (error) {
            console.error('Failed to sync order payment status', error);
        }
    };

    pollStatus();
    window.__orderTripayPoller = setInterval(pollStatus, 5000);
});
</script>
@endif
