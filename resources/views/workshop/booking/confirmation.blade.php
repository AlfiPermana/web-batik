<x-layouts.app>
<div class="workshop-confirmation-page bg-gray-50 min-h-screen py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        <!-- Success Header -->
        <div class="text-center mb-8">
            <div class="inline-block mb-4">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                    <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Terima Kasih!</h1>
            <p class="text-xl text-gray-600 mb-4">Booking Anda telah dikonfirmasi</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Booking Details Card -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 pb-4 border-b">Detail Booking</h2>

                    <div class="space-y-4">
                        <!-- Booking Number -->
                        <div class="flex justify-between items-center p-4 bg-blue-50 rounded-lg">
                            <span class="text-gray-600 font-semibold">Nomor Booking:</span>
                            <span class="text-lg font-bold text-blue-600">{{ $booking->booking_number }}</span>
                        </div>

                        <!-- Workshop Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Paket Workshop</p>
                                <p class="text-lg font-semibold text-gray-800">{{ $booking->workshop_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Tanggal</p>
                                <p class="text-lg font-semibold text-gray-800">{{ \Carbon\Carbon::parse($booking->workshop_date)->format('d M Y') }}</p>
                            </div>
                        </div>

                        <!-- Time & Duration -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Jam</p>
                                <p class="text-lg font-semibold text-gray-800">{{ $booking->start_time }} - {{ $booking->end_time }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Jumlah Peserta</p>
                                <p class="text-lg font-semibold text-gray-800">{{ $booking->num_participants }} orang</p>
                            </div>
                        </div>

                        <!-- Location -->
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Lokasi</p>
                            <p class="text-lg font-semibold text-gray-800">{{ $booking->address }}</p>
                        </div>

                        <!-- Customer Info -->
                        <div class="border-t pt-4">
                            <p class="text-sm text-gray-500 mb-3">Informasi Pemesan</p>
                            <div class="space-y-2">
                                <p class="flex justify-between">
                                    <span class="text-gray-600">Nama:</span>
                                    <span class="font-semibold text-gray-800">{{ $booking->customer_name }}</span>
                                </p>
                                <p class="flex justify-between">
                                    <span class="text-gray-600">Email:</span>
                                    <span class="font-semibold text-gray-800">{{ $booking->customer_email }}</span>
                                </p>
                                <p class="flex justify-between">
                                    <span class="text-gray-600">Telepon:</span>
                                    <span class="font-semibold text-gray-800">{{ $booking->customer_phone }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 pb-4 border-b">Ringkasan Pembayaran</h2>

                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Harga:</span>
                            <span class="font-semibold">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between p-3 bg-green-50 rounded">
                            <span class="text-gray-700 font-semibold">Deposit (50%):</span>
                            <span class="font-bold text-green-600">Rp {{ number_format($booking->deposit_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between p-3 bg-orange-50 rounded">
                            <span class="text-gray-700 font-semibold">Sisa Pembayaran (50%):</span>
                            <span class="font-bold text-orange-600">Rp {{ number_format($booking->remaining_amount, 0, ',', '.') }}</span>
                        </div>

                        <!-- Status Badge -->
                        <div class="mt-4 pt-4 border-t">
                            <p class="text-sm text-gray-600 mb-2">Status Pembayaran:</p>
                            <div class="inline-block px-4 py-2 rounded-full bg-yellow-100 text-yellow-800 font-semibold text-sm">
                                ⏳ Menunggu Verifikasi
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h3 class="font-bold text-blue-900 mb-4 text-lg">📋 Langkah Selanjutnya</h3>
                    <ol class="space-y-3 text-blue-900">
                        <li class="flex items-start">
                            <span class="font-bold mr-3">1.</span>
                            <span>Tim kami akan memverifikasi pembayaran dalam waktu 1-2 jam</span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-bold mr-3">2.</span>
                            <span>Anda akan menerima email konfirmasi setelah pembayaran terverifikasi</span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-bold mr-3">3.</span>
                            <span>Bayar sisa 50% pada hari workshop atau dapat dicicil lebih dulu</span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-bold mr-3">4.</span>
                            <span>Datang 15 menit lebih awal sebelum workshop dimulai</span>
                        </li>
                    </ol>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Status Info -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-lg p-4 mb-6">
                    <h3 class="font-bold text-green-900 mb-3">✓ Booking Dikonfirmasi</h3>
                    <p class="text-sm text-green-800 mb-3">Booking Anda telah berhasil dibuat dan menunggu verifikasi pembayaran.</p>
                    <p class="text-xs text-green-700">Waktu pemrosesan: dalam 1-2 jam</p>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-md p-4 mb-6">
                    <h3 class="font-bold text-gray-800 mb-3">Tindakan Cepat</h3>
                    <div class="space-y-2">
                        <a href="{{ route('workshop.my-bookings') }}" class="block px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-center text-sm font-semibold transition-colors">
                            Lihat Booking Saya
                        </a>
                        <a href="{{ route('workshop.index') }}" class="block px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-center text-sm font-semibold transition-colors">
                            Booking Lagi
                        </a>
                    </div>
                </div>

                <!-- Contact Support -->
                <div class="bg-white rounded-lg shadow-md p-4 mb-6">
                    <h3 class="font-bold text-gray-800 mb-3">📞 Hubungi Kami</h3>
                    <div class="space-y-2 text-sm">
                        <p class="flex items-center text-gray-700">
                            <span class="mr-2">📞</span>
                            <a href="tel:+628123456789" class="text-blue-600 hover:underline">+62 812-3456-789</a>
                        </p>
                        <p class="flex items-center text-gray-700">
                            <span class="mr-2">📧</span>
                            <a href="mailto:info@batikal.com" class="text-blue-600 hover:underline">info@batikal.com</a>
                        </p>
                        <p class="flex items-center text-gray-700">
                            <span class="mr-2">💬</span>
                            <a href="#" class="text-blue-600 hover:underline">WhatsApp Chat</a>
                        </p>
                    </div>
                </div>

                <!-- Payment Reminder -->
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <h3 class="font-bold text-amber-900 mb-3">⚠️ Ingat!</h3>
                    <ul class="text-sm text-amber-800 space-y-2">
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Simpan no. booking: {{ $booking->booking_number }}</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Cek email untuk konfirmasi verifikasi</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Bayar sisa 50% sebelum workshop</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Download Button -->
        <div class="mt-8 text-center">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-semibold">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M5 4a2 2 0 012-2h6a1 1 0 10 2h-6a4 4 0 00-4 4v12a1 1 0 102 0V4zm3 5a1 1 0 10-2 0v3H4a1 1 0 100 2h2v1a1 1 0 102 0v-1h2a1 1 0 100-2H8V9z"/>
                </svg>
                Cetak Konfirmasi
            </button>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        .no-print {
            display: none;
        }
        
        .workshop-confirmation-page {
            background-color: white;
        }
    }
</style>
@endpush
</div>
</x-layouts.app>
