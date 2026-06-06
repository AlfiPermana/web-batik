@if($showTripayModal)
    <!-- Payment Modal Overlay -->
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        
        <!-- Modal Container - Minimalist & Mobile Friendly -->
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm max-h-[85vh] overflow-hidden flex flex-col"
             wire:poll.keep-alive-300ms="checkPaymentStatus">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200"
                 @if($paymentStatus === 'paid') style="background: linear-gradient(to right, #10b981, #059669);" @endif>
                <h2 class="text-lg font-bold" 
                    @if($paymentStatus === 'paid') style="color: white;" @else style="color: #8B4513;" @endif>
                    @if($paymentStatus === 'paid')
                        ✅ Pembayaran Berhasil
                    @else
                        💳 Pembayaran
                    @endif
                </h2>
                <button type="button"
                        wire:click="closeTripayModal"
                        class="p-1 transition-colors"
                        @if($paymentStatus === 'paid') style="color: white;" @else class="text-gray-400 hover:text-gray-600" @endif>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="flex-1 overflow-y-auto p-4">
                
                @if($paymentStatus === 'paid' && $paymentStatusMessage)
                    <!-- SUCCESS STATE - DETAILED -->
                    <div class="py-6">
                        <!-- Success Icon -->
                        <div class="text-center mb-6">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full" style="background-color: #d1fae5;">
                                <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>

                        <!-- Main Message -->
                        <div class="text-center mb-6">
                            <h3 class="font-bold text-green-900 mb-2">Pembayaran Berhasil!</h3>
                            <p class="text-sm text-gray-600">Terima kasih telah melakukan pembelian.</p>
                        </div>

                        <!-- Order Details Card -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <h4 class="font-semibold text-gray-900 mb-3 text-sm">📋 Detail Pesanan</h4>
                            
                            <!-- Order Number -->
                            <div class="mb-3 pb-3 border-b border-gray-300">
                                <p class="text-xs text-gray-600">Nomor Pesanan:</p>
                                <p class="text-sm font-mono font-bold text-gray-900">{{ $orderNumber ?? 'N/A' }}</p>
                            </div>

                            <!-- Items -->
                            @if($orderItems && count($orderItems) > 0)
                            <div class="mb-3 pb-3 border-b border-gray-300">
                                <p class="text-xs text-gray-600 mb-2">Produk:</p>
                                @foreach($orderItems as $item)
                                <div class="text-xs text-gray-700 mb-1">
                                    <span class="font-medium">{{ $item['product_title'] }}</span>
                                    <span class="text-gray-500">({{ $item['quantity']}}x)</span>
                                    <span class="text-right float-right">Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            <!-- Payment Details -->
                            <div class="mb-3 pb-3 border-b border-gray-300">
                                <p class="text-xs text-gray-600">Tanggal Pembayaran:</p>
                                <p class="text-sm text-gray-900">{{ $paymentPaidAt ?? 'N/A' }}</p>
                            </div>

                            <!-- Total -->
                            <div>
                                <p class="text-xs text-gray-600">Total Pembayaran:</p>
                                <p class="text-lg font-bold" style="color: #8B4513;">Rp {{ number_format($totalAmount, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <!-- Confirmation Message -->
                        <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-xs text-blue-900 text-center">
                                📧 Konfirmasi email telah dikirim ke alamat Anda. Silakan cek inbox untuk informasi lengkap pesanan.
                            </p>
                        </div>
                    </div>

                @elseif($tripayPaymentUrl && $tripayPaymentData)
                    <!-- PENDING PAYMENT STATE -->
                    <!-- Referensi Pembayaran -->
                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-xs font-semibold text-blue-900 mb-1">Ref:</p>
                        <p class="text-sm font-mono font-bold text-blue-700 break-all">{{ $tripayReferenceCode }}</p>
                    </div>

                    <!-- Total Harga Ringkas -->
                    <div class="mb-4 p-3 bg-orange-50 rounded-lg border border-orange-200">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-700">Subtotal</span>
                            <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-700">Ongkir</span>
                            <span>Rp {{ number_format($shippingCost, 0, ',', '.') }}</span>
                        </div>
                        @if($tripayFee > 0)
                        <div class="flex justify-between text-sm mb-2 pb-2 border-b border-orange-300">
                            <span class="text-gray-700">Admin Fee</span>
                            <span class="text-orange-600">Rp {{ number_format($tripayFee, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between font-bold">
                            <span style="color: #8B4513;">Total</span>
                            <span style="color: #8B4513;">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                        </div>
                    </div>                    <!-- Metode Pembayaran -->
                    <div class="mb-4">
                        @if(str_contains($selectedPaymentMethod['id'] ?? '', 'VA'))
                            <!-- Virtual Account -->
                            @if($tripayPaymentData['pay_code'] ?? false)
                                <div class="bg-blue-50 border-2 border-blue-300 rounded-lg p-3 text-center">
                                    <p class="text-xs text-gray-600 mb-1">Transfer ke nomor:</p>
                                    <p class="text-xl font-mono font-bold text-blue-700 break-all">{{ $tripayPaymentData['pay_code'] }}</p>
                                    <p class="text-xs text-gray-600 mt-1">{{ $tripayPaymentData['payment_name'] ?? 'Bank' }}</p>
                                </div>
                            @endif
                        @elseif(str_contains($selectedPaymentMethod['id'] ?? '', 'QRIS'))
                            <!-- QRIS Code -->
                            @if($tripayPaymentData['qr_url'] ?? false)
                                <div class="text-center">
                                    <img src="{{ $tripayPaymentData['qr_url'] }}" 
                                         alt="QRIS Code" 
                                         class="w-40 h-40 mx-auto rounded-lg border border-gray-300">
                                    <p class="text-xs text-gray-600 mt-2">Scan dengan e-wallet favorit</p>
                                </div>
                            @endif
                        @else
                            <!-- Generic Code -->
                            <div class="bg-gray-100 p-3 rounded-lg text-center">
                                <p class="text-xs text-gray-600 mb-1">Kode Referensi:</p>
                                <p class="font-mono text-sm font-bold text-gray-900">{{ $tripayReferenceCode }}</p>
                            </div>
                        @endif
                    </div>

                @else
                    <!-- Loading State -->
                    <div class="flex flex-col items-center justify-center py-12">
                        <div class="mb-4">
                            <svg class="w-12 h-12 text-gray-400 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-gray-600 font-semibold text-sm">Mempersiapkan Pembayaran...</p>
                    </div>
                @endif
            </div>

            <!-- Modal Footer -->
            <div class="border-t border-gray-200 px-4 py-3 bg-gray-50 flex gap-2">
                @if($paymentStatus === 'paid')
                    <!-- Success Footer - Only Close Button -->
                    <button type="button"
                            wire:click="closeTripayModal"
                            class="w-full px-3 py-2 text-sm rounded-lg bg-green-600 text-white hover:bg-green-700 transition-colors font-medium">
                        Selesai
                    </button>
                @else
                    <!-- Pending Payment Footer - Waiting for callback -->
                    <button type="button"
                            wire:click="closeTripayModal"
                            class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors">
                        Tutup
                    </button>
                @endif
            </div>
        </div>
    </div>
@endif
