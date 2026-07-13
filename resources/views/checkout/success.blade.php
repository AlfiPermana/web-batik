@extends('layouts.checkout')

@section('content')
@php
    $tripayResponse = $order->tripay_response;
    $totalPaid = $order->total;
    $tripayFee = 0;
    
    if (is_array($tripayResponse) && isset($tripayResponse['amount'])) {
        $totalPaid = (int) $tripayResponse['amount'];
        $feeCustomer = $tripayResponse['fee_customer'] ?? null;
        $totalFee = $tripayResponse['total_fee'] ?? null;
        $feeMerchant = $tripayResponse['fee_merchant'] ?? null;
        
        if (is_numeric($feeCustomer) && $feeCustomer > 0) {
            $tripayFee = (int) $feeCustomer;
        } elseif (is_numeric($totalFee) && $totalFee > 0) {
            $tripayFee = (int) $totalFee;
        } elseif (is_numeric($feeMerchant) && $feeMerchant > 0) {
            $tripayFee = (int) $feeMerchant;
        } else {
            $baseTotal = (int) $order->subtotal + (int) $order->shipping_cost - (int) $order->discount;
            $tripayFee = max(0, $totalPaid - $baseTotal);
        }
    }
    
    $address = $order->shipping_address;
@endphp

<div class="bg-gray-50 py-12" style="font-family: 'Poppins', sans-serif;">
    <div class="max-w-6xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-start gap-4">
            <div>
                <h1 class="text-4xl font-bold mb-2" style="font-family: 'Playfair Display', serif; color: #8B4513;">
                    ✅ Pembayaran Berhasil
                </h1>
                <p class="text-gray-600 text-lg">Order #<strong>{{ $order->order_number }}</strong></p>
            </div>
            <button onclick="window.print()" class="px-4 py-2 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-lg text-sm transition-colors flex items-center gap-2 no-print">
                🖨️ Cetak Struk
            </button>
        </div>

        <!-- Main Alert Box -->
        <div class="mb-8 p-6 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-900 font-semibold mb-2">
                Terima kasih! Pembayaran Anda telah kami terima.
            </p>
            <p class="text-green-800 text-sm">
                Transaksi Anda telah berhasil dibayar. Kami akan memproses pengiriman pesanan Anda secepatnya. Status pelacakan paket dapat Anda lihat di menu pesanan saya pada dashboard Anda.
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

                <!-- Shipping Method Card -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h2 class="text-xl font-bold mb-4" style="color: #8B4513; font-family: 'Playfair Display', serif;">Metode Pengiriman</h2>
                    <p class="text-sm text-gray-600">
                        Layanan kurir: <strong class="text-gray-800 uppercase">{{ $order->shipping_service }}</strong> ({{ $order->shipping_service_type }})
                    </p>
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
                        @if($tripayFee > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Admin Fee</span>
                                <span class="font-semibold">Rp {{ number_format($tripayFee, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <div class="flex justify-between items-baseline">
                            <span class="text-gray-900 font-semibold text-sm">Total Pembayaran</span>
                            <span class="text-2xl font-bold" style="color: #8B4513;">
                                Rp {{ number_format($totalPaid, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- Paid Badge status matching payment status block styling -->
                    <div class="p-4 bg-green-50 rounded-lg border border-green-200 text-center mb-6">
                        <p class="text-xs font-semibold text-green-800">✅ PEMBAYARAN LUNAS</p>
                        @if($order->paid_at)
                            <p class="text-[10px] text-gray-500 mt-1">Dibayar pada {{ $order->paid_at->setTimezone('Asia/Jakarta')->format('d M Y') }}</p>
                        @endif
                    </div>

                    <!-- Action Buttons inside sidebar -->
                    <div class="space-y-2.5 no-print">
                        <a href="{{ route('customer.dashboard') }}"
                           class="w-full block text-center py-2.5 rounded-lg font-semibold text-white transition-colors text-sm"
                           style="background-color: #8B4513;">
                            Lihat Status Order →
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

<style>
    @media print {
        .no-print {
            display: none !important;
        }
        body {
            background-color: white;
            color: black;
        }
        .bg-gray-50 {
            background-color: white !important;
            padding: 0 !important;
        }
        .max-w-6xl {
            padding: 0 !important;
            max-width: 100% !important;
        }
        .bg-white {
            border: none !important;
        }
    }
</style>
@endsection
