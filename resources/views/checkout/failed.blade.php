@extends('layouts.checkout')

@section('content')
@php
    $tripayResponse = $order->tripay_response;
    $totalAmount = $order->total;
    if (is_array($tripayResponse) && isset($tripayResponse['amount'])) {
        $totalAmount = (int) $tripayResponse['amount'];
    }
    
    $statusLabel = 'Gagal';
    $statusBg = 'bg-red-50';
    $statusBorder = 'border-red-200';
    $statusText = 'text-red-800';
    $message = 'Pembayaran Anda gagal diproses atau dibatalkan.';
    $iconColor = 'text-red-600';
    $iconBg = 'bg-red-50';
    
    if ($order->payment_status === 'expired') {
        $statusLabel = 'Kadaluarsa';
        $statusBg = 'bg-amber-50';
        $statusBorder = 'border-amber-200';
        $statusText = 'text-amber-800';
        $message = 'Waktu batas pembayaran untuk transaksi ini telah habis.';
        $iconColor = 'text-amber-600';
        $iconBg = 'bg-amber-50';
    } elseif ($order->payment_status === 'refunded') {
        $statusLabel = 'Dikembalikan';
        $statusBg = 'bg-blue-50';
        $statusBorder = 'border-blue-200';
        $statusText = 'text-blue-800';
        $message = 'Pembayaran Anda telah dikembalikan (refunded) oleh sistem.';
        $iconColor = 'text-blue-600';
        $iconBg = 'bg-blue-50';
    }
    
    $address = $order->shipping_address;
@endphp

<div class="bg-gray-50 py-12" style="font-family: 'Poppins', sans-serif;">
    <div class="max-w-6xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold mb-2" style="font-family: 'Playfair Display', serif; color: #8B4513;">
                ⚠️ Pembayaran {{ $statusLabel }}
            </h1>
            <p class="text-gray-600 text-lg">Order #<strong>{{ $order->order_number }}</strong></p>
        </div>

        <!-- Alert Box -->
        <div class="mb-8 p-6 {{ $statusBg }} border {{ $statusBorder }} rounded-lg">
            <p class="font-semibold mb-2 {{ $statusText }}">
                Mohon maaf! Transaksi ini berstatus {{ $statusLabel }}.
            </p>
            <p class="text-gray-750 text-sm">
                {{ $message }} Jika saldo Anda sudah terpotong namun status pembayaran belum berubah, hubungi layanan pelanggan kami agar kami dapat memverifikasi transaksi secara manual.
            </p>
        </div>

        <!-- 3-Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Column 1 & 2: Detailed Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Shipping Address Card -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h2 class="text-xl font-bold mb-4" style="color: #8B4513; font-family: 'Playfair Display', serif;">Alamat Pengiriman</h2>
                    @if($address)
                        <p class="text-sm text-gray-600 leading-relaxed">
                            <strong>{{ $address['full_name'] }}</strong><br>
                            {{ $address['phone_number'] }}<br>
                            {{ $address['address'] }}<br>
                            {{ $address['subdistrict'] }}, {{ $address['district'] }}, {{ $address['city'] }}, {{ $address['province'] }} {{ $address['postal_code'] }}
                        </p>
                    @else
                        <p class="text-sm text-gray-500 italic">Data alamat tidak tersedia.</p>
                    @endif
                </div>

                <!-- Product Details Card -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h2 class="text-xl font-bold mb-6" style="color: #8B4513; font-family: 'Playfair Display', serif;">Rincian Produk</h2>
                    <div class="space-y-4">
                        @foreach($order->display_items as $item)
                            <div class="flex justify-between items-center pb-4 border-b border-gray-200 last:border-0 last:pb-0">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded overflow-hidden border border-gray-200 bg-gray-50 flex-shrink-0">
                                        @if($item->product && $item->product->photo)
                                            <img src="{{ asset('storage/' . $item->product->photo) }}" alt="{{ $item->product->title }}" class="w-full h-full object-cover">
                                        @elseif($item->product && $item->product->images && $item->product->images->first())
                                            <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" alt="{{ $item->product->title }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-[#f5f1e8] text-[#8B4513] font-bold text-xs">BATIK</div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm">{{ $item->product->title ?? 'Product' }}</p>
                                        <p class="text-xs text-gray-600">Ukuran: {{ $item->productSize->size ?? 'N/A' }} | Qty: {{ $item->quantity }}</p>
                                    </div>
                                </div>
                                <p class="text-sm font-semibold text-gray-900">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Column 3: Sidebar Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg border border-gray-200 p-6 sticky top-24 h-fit">
                    <h3 class="text-lg font-bold mb-4" style="color: #8B4513; font-family: 'Playfair Display', serif;">Ringkasan Pembayaran</h3>

                    <div class="space-y-3 pb-4 border-b border-gray-200 mb-4 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-semibold">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Pengiriman</span>
                            <span class="font-semibold">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        @if ($order->discount > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Diskon</span>
                                <span class="font-semibold">-Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <div class="flex justify-between items-baseline">
                            <span class="text-gray-900 font-semibold text-sm">Total Pembayaran</span>
                            <span class="text-2xl font-bold" style="color: #8B4513;">
                                Rp {{ number_format($totalAmount, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- Status Box matching checkout/payment pages -->
                    <div class="p-4 {{ $statusBg }} rounded-lg border {{ $statusBorder }} text-center mb-6">
                        <p class="text-xs font-semibold {{ $statusText }}">⏳ STATUS: {{ strtoupper($statusLabel) }}</p>
                        @if($order->tripay_reference)
                            <p class="text-[10px] text-gray-500 mt-1 break-all">Ref: {{ $order->tripay_reference }}</p>
                        @endif
                    </div>

                    <!-- Action Buttons inside sidebar -->
                    <div class="space-y-2.5 no-print">
                        <a href="{{ route('cart.index') }}"
                           class="w-full block text-center py-2.5 rounded-lg font-semibold text-white transition-colors text-sm bg-red-650 hover:bg-red-750"
                           style="background-color: #8B4513;">
                            Coba Pembayaran Lagi
                        </a>
                        <a href="https://wa.me/6285111230011" target="_blank"
                           class="w-full block text-center py-2.5 rounded-lg border border-gray-300 text-gray-900 hover:bg-gray-50 font-semibold transition-colors text-sm">
                            💬 WhatsApp Support
                        </a>
                        <a href="{{ route('landing.shop') }}"
                           class="w-full block text-center py-2.5 rounded-lg border border-gray-300 text-gray-900 hover:bg-gray-50 font-semibold transition-colors text-sm">
                            ← Kembali ke Shop
                        </a>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection
