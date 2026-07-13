@extends('layouts.checkout', ['title' => 'Konfirmasi Pembayaran'])

@section('content')
<div class="bg-gray-50 py-12" style="font-family: 'Poppins', sans-serif;">
    <div class="max-w-6xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold mb-2" style="font-family: 'Playfair Display', serif; color: #8B4513;">
                ✅ Pesanan Berhasil Dibuat
            </h1>
            <p class="text-gray-600 text-lg">Order #<strong>{{ $order->order_number }}</strong></p>
        </div>

        <!-- Success Alert -->
        <div class="mb-8 p-6 bg-green-50 border-2 border-green-200 rounded-lg">
            <p class="text-green-900 font-semibold mb-2">
                Terima kasih telah berbelanja! Pesanan Anda telah diterima.
            </p>
            <p class="text-green-800 text-sm">
                Silakan lanjutkan pembayaran dengan metode yang telah Anda pilih. Instruksi pembayaran telah dikirim ke email Anda.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Details -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h2 class="text-2xl font-bold mb-6" style="color: #8B4513;">Detail Pesanan</h2>

                    <!-- Shipping Address -->
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-900 mb-3">Alamat Pengiriman</h3>
                        @php
                            $address = $order->shipping_address;
                        @endphp
                        <p class="text-sm text-gray-600">
                            <strong>{{ $address['full_name'] ?? $address['name'] ?? 'N/A' }}</strong><br>
                            {{ $address['address'] ?? 'N/A' }}<br>
                            {{ $address['city'] ?? 'N/A' }}, {{ $address['postal_code'] ?? 'N/A' }}<br>
                            <span class="text-xs">📞 {{ $address['phone_number'] ?? $address['phone'] ?? 'N/A' }}</span>
                        </p>
                    </div>

                    <!-- Shipping Method -->
                    @if($order->shipping_service)
                        <div class="mb-6 pb-6 border-b border-gray-200">
                            <h3 class="font-semibold text-gray-900 mb-3">Metode Pengiriman</h3>
                            <p class="text-sm text-gray-600">
                                <strong>{{ $order->shipping_service }} - {{ $order->shipping_service_type }}</strong>
                            </p>
                        </div>
                    @endif

                    <!-- Payment Method -->
                    @if($order->payment_method_name)
                        <div class="pb-6 border-b border-gray-200">
                            <h3 class="font-semibold text-gray-900 mb-3">Metode Pembayaran</h3>
                            <p class="text-sm text-gray-600">
                                <strong>{{ $order->payment_method_name }}</strong>
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Payment Instructions -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h2 class="text-2xl font-bold mb-6" style="color: #8B4513;">Instruksi Pembayaran</h2>

                    @if($order->payment_method === 'bank_transfer')
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <h3 class="font-semibold text-blue-900 mb-3">Transfer Bank</h3>
                            <p class="text-sm text-blue-800 mb-3">
                                Silakan transfer sesuai jumlah yang tertera ke rekening di bawah ini:
                            </p>
                            <div class="bg-white p-4 rounded border border-blue-200 space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-700">Bank:</span>
                                    <strong>Bank Mandiri</strong>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-700">Nomor Rekening:</span>
                                    <strong>1234567890</strong>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-700">Atas Nama:</span>
                                    <strong>PT. Batik Giri Alam</strong>
                                </div>
                                <div class="flex justify-between border-t border-gray-200 pt-2">
                                    <span class="text-gray-700">Jumlah Transfer:</span>
                                    <strong style="color: #8B4513;">Rp {{ number_format($order->total, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <p class="text-sm text-yellow-800">
                                <strong>⚠️ Penting:</strong> Pastikan jumlah transfer sesuai dengan total di atas untuk memudahkan verifikasi pembayaran.
                            </p>
                        </div>
                    @elseif($order->payment_method === 'ewallet')
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-4">
                            <h3 class="font-semibold text-purple-900 mb-3">Pembayaran E-Wallet via Tripay</h3>
                            <p class="text-sm text-purple-800 mb-4">
                                Klik tombol di bawah untuk membuka halaman pembayaran Tripay. Anda dapat memilih dari berbagai metode pembayaran (GCash, Dana, OVO, Virtual Account Bank, dll).
                            </p>
                            <button onclick="openTripayPayment({{ $order->id }}, {{ $order->total }})"
                                    class="w-full px-6 py-3 rounded-lg font-semibold text-white transition-colors"
                                    style="background-color: #8B4513;">
                                Buka Halaman Pembayaran Tripay →
                            </button>
                            <div class="bg-white p-4 rounded border border-purple-200 mt-4">
                                <div class="flex justify-between">
                                    <span class="text-gray-700">Jumlah Pembayaran:</span>
                                    <strong style="color: #8B4513;">Rp {{ number_format($order->total, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                        </div>
                    @elseif($order->payment_method === 'credit_card')
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <h3 class="font-semibold text-blue-900 mb-3">Pembayaran Kartu Kredit/Debit</h3>
                            <p class="text-sm text-blue-800 mb-3">
                                Silakan lakukan pembayaran menggunakan kartu kredit atau debit Anda dengan jumlah:
                            </p>
                            <div class="bg-white p-4 rounded border border-blue-200">
                                <div class="flex justify-between">
                                    <span class="text-gray-700">Jumlah Pembayaran:</span>
                                    <strong style="color: #8B4513;">Rp {{ number_format($order->total, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Items -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h2 class="text-2xl font-bold mb-6" style="color: #8B4513;">Rincian Produk</h2>
                    <div class="space-y-4">
                        @forelse($order->items as $item)
                            <div class="flex justify-between items-start pb-4 border-b border-gray-200 last:border-0">
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">{{ $item->product->title }}</p>
                                    <p class="text-xs text-gray-600">Ukuran: {{ $item->productSize->size }} | Qty: {{ $item->quantity }}</p>
                                </div>
                                <p class="text-sm font-semibold text-gray-900">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
                            </div>
                        @empty
                            <p class="text-gray-600">Tidak ada produk</p>
                        @endforelse
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col gap-3">
                    <a href="{{ route('customer.dashboard') }}"
                       class="w-full px-6 py-3 rounded-lg font-semibold text-white transition-colors text-center"
                       style="background-color: #8B4513;">
                        Lihat Status Order →
                    </a>
                    <a href="{{ route('landing.shop') }}"
                       class="w-full px-6 py-3 rounded-lg border border-gray-300 text-gray-900 font-semibold hover:bg-gray-50 transition-colors text-center">
                        ← Kembali ke Shop
                    </a>
                </div>
            </div>

            <!-- Sidebar: Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg border border-gray-200 p-6 sticky top-24 h-fit">
                    <h3 class="text-lg font-bold mb-4" style="color: #8B4513;">Ringkasan Pembayaran</h3>

                    <div class="space-y-3 pb-4 border-b border-gray-200 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-semibold">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Pengiriman</span>
                            <span class="font-semibold">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        @if ($order->discount > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Diskon</span>
                                <span class="font-semibold text-green-600">-Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <div class="flex justify-between items-baseline">
                            <span class="text-gray-900 font-semibold">Total Pembayaran</span>
                            <span class="text-2xl font-bold" style="color: #8B4513;">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- Status -->
                    @php
                        $payLabel = match($order->payment_status) {
                            'paid', 'confirmed' => 'LUNAS',
                            'expired' => 'KADALUARSA',
                            'failed' => 'GAGAL',
                            'refunded' => 'DIKEMBALIKAN',
                            'unpaid', 'pending' => 'MENUNGGU PEMBAYARAN',
                            default => strtoupper($order->payment_status ?? 'MENUNGGU PEMBAYARAN'),
                        };
                    @endphp
                    <div id="paymentStatusBox" class="p-4 bg-yellow-50 rounded-lg border border-yellow-200 text-center mb-4">
                        <p id="paymentStatusText" class="text-xs font-semibold text-yellow-800">⏳ Status: {{ $payLabel }}</p>
                        <p id="paymentDeadlineText" class="text-xs text-gray-700 mt-2">Batas Pembayaran: -</p>
                    </div>

                    <!-- Order Number -->
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-600 mb-1">Nomor Pesanan</p>
                        <p class="text-lg font-bold text-gray-900 break-all">{{ $order->order_number }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tripay Iframe Modal -->
<div id="tripayModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center p-4 overflow-y-auto hidden" style="display: none;">
    <div class="bg-white rounded-lg w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
        <!-- Modal Header -->
        <div class="bg-gray-100 px-6 py-4 flex justify-between items-center border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">Pembayaran Tripay</h3>
            <button onclick="closeTripayModal()" class="text-gray-600 hover:text-gray-900 text-2xl">&times;</button>
        </div>
        <!-- Modal Body - Iframe -->
        <div class="flex-1 overflow-hidden">
            <iframe id="tripayIframe" style="width: 100%; height: 100%; border: none;"></iframe>
        </div>
    </div>
</div>

<script>
    function formatPaymentStatusLabel(status) {
        switch (status) {
            case 'paid':
            case 'confirmed':
                return 'LUNAS';
            case 'expired':
                return 'KADALUARSA';
            case 'failed':
                return 'GAGAL';
            case 'refunded':
                return 'DIKEMBALIKAN';
            case 'unpaid':
            case 'pending':
                return 'MENUNGGU PEMBAYARAN';
            default:
                return (status || 'MENUNGGU PEMBAYARAN').toString().toUpperCase();
        }
    }

    function setDeadlineLabel(expiredAt) {
        const el = document.getElementById('paymentDeadlineText');
        if (!el) return;
        el.textContent = expiredAt ? `Batas Pembayaran: ${expiredAt}` : 'Batas Pembayaran: -';
    }

    function setStatusLabel(status) {
        const el = document.getElementById('paymentStatusText');
        if (!el) return;
        el.textContent = `⏳ Status: ${formatPaymentStatusLabel(status)}`;
    }

    function openTripayPayment(orderId, amount) {
        const checkoutUrl = `/api/payment/tripay/checkout?order_id=${orderId}&amount=${amount}`;

        fetch(checkoutUrl)
            .then(response => response.json())
            .then(data => {
                if (data.checkout_url) {
                    document.getElementById('tripayIframe').src = data.checkout_url;
                    document.getElementById('tripayModal').style.display = 'flex';

                    if (data.expired_at) {
                        setDeadlineLabel(data.expired_at);
                    }
                } else {
                    Swal.fire({ title: 'Gagal', text: data.error || 'Gagal membuka halaman pembayaran', icon: 'error', iconColor: '#ef4444', confirmButtonText: 'OK', confirmButtonColor: '#b45309', customClass: { popup: 'rounded-2xl shadow-2xl' } });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({ title: 'Terjadi Kesalahan', text: 'Gagal terhubung ke server pembayaran.', icon: 'error', iconColor: '#ef4444', confirmButtonText: 'OK', confirmButtonColor: '#b45309', customClass: { popup: 'rounded-2xl shadow-2xl' } });
            });
    }

    function closeTripayModal() {
        document.getElementById('tripayModal').style.display = 'none';
        document.getElementById('tripayIframe').src = '';
    }

    // Auto-refresh payment status setiap 3 detik
    setInterval(function() {
        fetch(`/api/payment/{{ $order->id }}/status`)
            .then(response => response.json())
            .then(data => {
                if (!data || data.error) return;

                if (data.expired_at) {
                    setDeadlineLabel(data.expired_at);
                }

                if (data.payment_status) {
                    setStatusLabel(data.payment_status);
                }

                if (data.payment_status === 'paid') {
                    Swal.fire({
                        title: 'Pembayaran Berhasil! 🎉',
                        text: 'Terima kasih, pembayaran Anda telah dikonfirmasi.',
                        icon: 'success', iconColor: '#10b981',
                        confirmButtonText: 'Lihat Detail', confirmButtonColor: '#b45309',
                        allowOutsideClick: false,
                        customClass: { popup: 'rounded-2xl shadow-2xl' },
                    }).then(() => {
                        closeTripayModal();
                        window.location.href = '{{ route("payment.success", $order->id) }}';
                    });
                    return;
                }

                if (['failed', 'expired', 'refunded'].includes(data.payment_status)) {
                    Swal.fire({
                        title: 'Status Pembayaran',
                        text: `Pembayaran ${formatPaymentStatusLabel(data.payment_status)}.`,
                        icon: 'warning', iconColor: '#f59e0b',
                        confirmButtonText: 'OK', confirmButtonColor: '#b45309',
                        customClass: { popup: 'rounded-2xl shadow-2xl' },
                    }).then(() => closeTripayModal());
                }
            })
            .catch(() => {});
    }, 3000);
</script>

@endsection


