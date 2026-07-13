<x-layouts.app title="{{ $order->order_number }}">
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900">{{ $order->order_number }}</h1>
                    <p class="mt-2 text-gray-600">Kelola detail pesanan dan perbarui status</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center px-4 py-2 text-amber-600 hover:text-amber-700 font-semibold hover:bg-amber-50 rounded-lg transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Alert Messages -->
        @if ($message = Session::get('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ $message }}</span>
            </div>
        @endif

        @if ($message = Session::get('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ $message }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Main Content -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Order Status Card -->
                <div class="bg-white rounded-lg shadow-md border border-gray-100">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">
                            <svg class="w-6 h-6 inline-block mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Status Pesanan
                        </h2>

                        <!-- Current Status Badge -->
                        <div class="mb-8 p-4 rounded-lg" style="
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
                            <span class="text-sm font-bold" style="
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
                                <svg class="w-4 h-4 inline-block mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16z" clip-rule="evenodd"></path></svg>
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>

                        <!-- Update Status Form -->
                        @php $canCancelOrder = $order->canBeCancelled(); @endphp
                        <form id="statusForm" method="POST" action="{{ route('admin.orders.updateStatus', $order) }}" class="space-y-5">
                            @csrf
                            @method('PATCH')

                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-3">
                                    <span class="text-red-500">*</span> Ubah Status Pesanan
                                </label>
                                <select id="statusSelect" name="status" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition bg-white"
                                    required>
                                    <option value="">-- Pilih Status Baru --</option>
                                    @foreach($statuses as $status)
                                        @if($status !== $order->status && ($status !== 'cancelled' || $canCancelOrder))
                                            <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <small class="text-gray-500 mt-2 block">
                                    Wajib dipilih untuk mengubah status
                                    @unless($canCancelOrder)
                                        . Status `cancelled` dinonaktifkan karena pesanan sudah lunas.
                                    @endunless
                                </small>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-3">Catatan (Opsional)</label>
                                <textarea id="notesTextarea" name="notes" rows="4" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition"
                                    placeholder="Tambahkan catatan perubahan status, misalnya: Barang sudah dikirim via JNE..."></textarea>
                                <small class="text-gray-500 mt-2 block">Kosongkan jika tidak ada catatan tambahan</small>
                            </div>

                            <div class="flex gap-3 pt-2">
                                <button type="submit" 
                                    style="background-color: #b45309; color: white; flex: 1; padding: 0.75rem 1rem; border-radius: 0.5rem; font-weight: 600; transition: background-color 0.2s;"
                                    onmouseover="this.style.backgroundColor='#92400e'"
                                    onmouseout="this.style.backgroundColor='#b45309'"
                                    onmousedown="this.style.backgroundColor='#78350f'"
                                    onmouseup="this.style.backgroundColor='#92400e'"
                                    class="flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Simpan Perubahan
                                </button>
                                <button type="reset" style="padding: 0.75rem 1.5rem; border: 1px solid #d1d5db; border-radius: 0.5rem; color: #374151; font-weight: 600; transition: background-color 0.2s;"
                                    onmouseover="this.style.backgroundColor='#f3f4f6'"
                                    onmouseout="this.style.backgroundColor='transparent'"
                                    class="transition duration-200">
                                    Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Order Items Card -->
                <div class="bg-white rounded-lg shadow-md border border-gray-100">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">
                            <svg class="w-6 h-6 inline-block mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 8m10 0l2-8m0 0h10M17 21a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Item Pesanan
                        </h2>

                        @php $displayItems = $order->display_items; @endphp
                        <div class="space-y-6 px-6 py-6">
                            @forelse($displayItems as $item)
                                <div class="bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition">
                                    <!-- Product Header -->
                                    <div class="flex items-start gap-4 mb-4">
                                        @php
                                            // ProductImage column is `photo` (bukan `image_path`)
                                            $imgPath = $item->product?->images?->first()?->photo ?? $item->product?->photo;
                                        @endphp

                                        @if($imgPath)
                                            <div class="flex-shrink-0">
                                                <img src="{{ asset('storage/' . $imgPath) }}" 
                                                     alt="{{ $item->product?->title ?? 'Produk' }}"
                                                     class="w-16 h-16 rounded-lg object-cover border border-gray-300">
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-bold text-gray-900 text-base mb-1">{{ $item->product?->title ?? 'Produk' }}</h3>
                                            @if($item->product->description)
                                                <p class="text-sm text-gray-600 line-clamp-2">{!! strip_tags($item->product->description) !!}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Product Details Grid -->
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4 pt-4 border-t border-gray-200">
                                        <!-- Ukuran -->
                                        <div>
                                            <p class="text-xs font-semibold text-gray-600 uppercase mb-1">Ukuran</p>
                                            <p class="text-sm font-bold text-gray-900">{{ $item->productSize?->size ?? '-' }}</p>
                                        </div>

                                        <!-- Jumlah -->
                                        <div>
                                            <p class="text-xs font-semibold text-gray-600 uppercase mb-1">Jumlah</p>
                                            <div style="background-color: #dbeafe; color: #0c4a6e; padding: 0.375rem 0.75rem; border-radius: 0.375rem; font-weight: 700; text-align: center; font-size: 0.875rem; display: inline-block;">
                                                {{ $item->quantity }} pcs
                                            </div>
                                        </div>

                                        <!-- Harga Satuan -->
                                        <div>
                                            <p class="text-xs font-semibold text-gray-600 uppercase mb-1">Harga Satuan</p>
                                            <p class="text-sm font-bold text-gray-900">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                        </div>

                                        <!-- Subtotal -->
                                        <div>
                                            <p class="text-xs font-semibold text-gray-600 uppercase mb-1">Subtotal</p>
                                            <p class="text-sm font-bold text-amber-600">Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</p>
                                        </div>
                                    </div>

                                    <!-- Total Summary -->
                                    <div class="bg-amber-50 border-l-4 border-amber-600 p-3 rounded-sm">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-700 font-semibold">Total Item:</span>
                                            <span class="text-lg font-bold text-amber-600">
                                                Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <p>Tidak ada item pesanan</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Status History Card -->
                <div class="bg-white rounded-lg shadow-md border border-gray-100">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">
                            <svg class="w-6 h-6 inline-block mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Riwayat Perubahan Status
                        </h2>

                        <div class="space-y-6">
                            @forelse($order->statusHistories as $history)
                                <div class="relative pb-6 last:pb-0">
                                    @if(!$loop->last)
                                        <div class="absolute left-3 top-12 bottom-0 w-0.5 bg-gray-200"></div>
                                    @endif
                                    <div class="relative flex gap-4">
                                        <div class="flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center" style="
                                            @switch($history->new_status)
                                                @case('pending')
                                                    background-color: #fbbf24; color: white;
                                                @break
                                                @case('processing')
                                                    background-color: #3b82f6; color: white;
                                                @break
                                                @case('shipped')
                                                    background-color: #a855f7; color: white;
                                                @break
                                                @case('delivered')
                                                    background-color: #22c55e; color: white;
                                                @break
                                                @case('cancelled')
                                                    background-color: #ef4444; color: white;
                                                @break
                                            @endswitch
                                        ">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        </div>
                                        <div class="flex-1 pt-1">
                                            <h4 class="font-bold text-gray-900">
                                                {{ ucfirst($history->new_status) }}
                                                @if($history->old_status)
                                                    <span class="text-sm text-gray-600 font-normal">(dari {{ ucfirst($history->old_status) }})</span>
                                                @endif
                                            </h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ $history->created_at->format('d M Y H:i') }}</p>
                                            @if($history->changedBy)
                                                <p class="text-sm text-gray-600">Oleh: <span class="font-semibold">{{ $history->changedBy->name }}</span></p>
                                            @endif
                                            @if($history->notes)
                                                <div class="mt-3 p-3 bg-amber-50 border-l-4 border-amber-600 rounded">
                                                    <p class="text-sm text-gray-700">{{ $history->notes }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-6">Belum ada riwayat perubahan status</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Information Cards -->
            <div class="space-y-6">
                
                <!-- Customer Info Card -->
                <div class="bg-white rounded-lg shadow-md border border-gray-100">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            <svg class="w-5 h-5 inline-block mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Customer
                        </h3>
                        <div class="space-y-4 text-sm">
                            <div>
                                <p class="text-gray-600">Nama</p>
                                <p class="font-semibold text-gray-900">{{ $order->user->name }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Email</p>
                                <p class="font-semibold text-gray-900 break-all text-xs">{{ $order->user->email }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">No. Telepon</p>
                                <p class="font-semibold text-gray-900">{{ $order->user->phone ?? 'Tidak tersedia' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Address Card -->
                <div class="bg-white rounded-lg shadow-md border border-gray-100">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            <svg class="w-5 h-5 inline-block mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Alamat Pengiriman
                        </h3>
                        <div class="space-y-3 text-sm">
                            @php $addr = $order->shipping_address @endphp
                            <div>
                                <p class="text-gray-600">Nama Penerima</p>
                                <p class="font-semibold text-gray-900">{{ $addr['full_name'] ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">No. Telepon</p>
                                <p class="font-semibold text-gray-900">{{ $addr['phone_number'] ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Alamat</p>
                                <p class="font-semibold text-gray-900 text-xs">{{ $addr['address'] ?? '-' }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <p class="text-gray-600 text-xs">Provinsi</p>
                                    <p class="font-semibold text-gray-900 text-sm">{{ $addr['province'] ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 text-xs">Kota</p>
                                    <p class="font-semibold text-gray-900 text-sm">{{ $addr['city'] ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <p class="text-gray-600 text-xs">Kecamatan</p>
                                    <p class="font-semibold text-gray-900 text-sm">{{ $addr['district'] ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 text-xs">Kelurahan</p>
                                    <p class="font-semibold text-gray-900 text-sm">{{ $addr['subdistrict'] ?? '-' }}</p>
                                </div>
                            </div>
                            <div>
                                <p class="text-gray-600 text-xs">Kode Pos</p>
                                <p class="font-semibold text-gray-900">{{ $addr['postal_code'] ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Method Card -->
                <div class="bg-white rounded-lg shadow-md border border-gray-100">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            <svg class="w-5 h-5 inline-block mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6m0 0L7 12m6-6l6 6"></path></svg>
                            Metode Pengiriman
                        </h3>
                        <div class="space-y-4 text-sm">
                            <div>
                                <p class="text-gray-600">Kurir</p>
                                <p class="font-semibold text-gray-900">{{ $order->shipping_service ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Tipe Pengiriman</p>
                                <p class="font-semibold text-gray-900">{{ $order->shipping_service_type ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Biaya</p>
                                <p class="text-lg font-bold text-amber-600">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Info Card -->
                <div class="bg-white rounded-lg shadow-md border border-gray-100">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            <svg class="w-5 h-5 inline-block mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h10m4 0a6 6 0 11-12 0 6 6 0 0112 0z"></path></svg>
                            Informasi Pembayaran
                        </h3>
                        <div class="space-y-4 text-sm">
                            <div>
                                <p class="text-gray-600">Metode Pembayaran</p>
                                <p class="font-semibold text-gray-900">{{ ucfirst($order->payment_method) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 mb-2">Status Pembayaran</p>
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold" style="
                                    @switch($order->payment_status)
                                        @case('unpaid')
                                        @case('pending')
                                            background-color: #fef3c7; color: #92400e;
                                        @break
                                        @case('paid')
                                        @case('confirmed')
                                            background-color: #dcfce7; color: #15803d;
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
                                    {{ match($order->payment_status) {
                                        'unpaid', 'pending' => 'Menunggu Pembayaran',
                                        'paid', 'confirmed' => 'Lunas',
                                        'expired' => 'Kadaluarsa',
                                        'failed' => 'Gagal',
                                        'refunded' => 'Dikembalikan',
                                        default => ucfirst($order->payment_status),
                                    } }}
                                </span>
                            </div>
                            @if($order->paid_at)
                                <div>
                                    <p class="text-gray-600">Waktu Pembayaran</p>
                                    <p class="font-semibold text-gray-900">{{ $order->paid_at->format('d M Y H:i') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Order Summary Card -->
                <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-lg shadow-md border border-amber-200">
                    <div class="p-6" style="color: black;">
                        <h3 class="text-lg font-bold mb-4">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            Ringkasan Pesanan
                        </h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span>Subtotal</span>
                                <span class="font-semibold">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Biaya Pengiriman</span>
                                <span class="font-semibold">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                            </div>
                            @if($order->discount > 0)
                                <div class="flex justify-between">
                                    <span>Diskon</span>
                                    <span class="font-semibold">-Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="border-t border-amber-300 pt-3 flex justify-between text-base">
                                <span class="font-bold">Total</span>
                                <span class="font-bold text-lg text-amber-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Validasi Form Script -->
<script>
    document.getElementById('statusForm').addEventListener('submit', function(e) {
        const statusSelect = document.getElementById('statusSelect');
        
        if (!statusSelect.value) {
            e.preventDefault();
            Swal.fire({
                title: 'Status Belum Dipilih',
                text: 'Silakan pilih status baru terlebih dahulu!',
                icon: 'warning', iconColor: '#f59e0b',
                confirmButtonText: 'OK', confirmButtonColor: '#b45309',
                customClass: { popup: 'rounded-2xl shadow-2xl' },
            }).then(() => { statusSelect.focus(); });
            statusSelect.style.borderColor = '#ef4444';
            statusSelect.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.1)';
            return false;
        }
        
        // Reset border style jika ada
        statusSelect.style.borderColor = '';
        statusSelect.style.boxShadow = '';
        return true;
    });

    // Hapus error style ketika user memilih status
    document.getElementById('statusSelect').addEventListener('change', function() {
        this.style.borderColor = '';
        this.style.boxShadow = '';
    });
</script>
</x-layouts.app>
