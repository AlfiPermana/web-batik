<x-layouts.landing title="Pilih Workshop">
    <div class="min-h-screen bg-gradient-to-b from-white to-amber-50 py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold mb-4" style="font-family: 'Playfair Display', serif; color: #8B4513;">
                    Workshop Batik
                </h1>
                <p class="text-gray-600 text-lg" style="font-family: 'Poppins', sans-serif;">
                    Pilih workshop yang ingin Anda ikuti
                </p>
            </div>

            <!-- Workshop List -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($workshops as $workshop)
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <!-- Workshop Card -->
                        <div class="bg-gradient-to-r from-amber-100 to-orange-100 p-6 border-b-4 border-orange-300">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $workshop->title }}</h3>
                            <p class="text-gray-700 text-sm mb-4">{{ $workshop->description }}</p>
                        </div>

                        <!-- Workshop Details -->
                        <div class="p-6">
                            <!-- Price -->
                            <div class="mb-4">
                                <span class="text-gray-600 text-sm">Harga Paket</span>
                                <p class="text-3xl font-bold text-orange-600">
                                    Rp {{ number_format($workshop->amount, 0, ',', '.') }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">Harga workshop bersifat fix (tidak bergantung jumlah peserta)</p>
                            </div>


                            <!-- Location -->
                            @if($workshop->location)
                                <div class="mb-4 flex items-center gap-2 text-gray-600">
                                    <span>📍</span>
                                    <span>{{ $workshop->location }}</span>
                                </div>
                            @endif

                            <!-- Availability Info -->
                            <div class="mb-6 p-3 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                                @php
                                    $scheduleCount = $workshop->schedules()
                                        ->where('date', '>=', now()->toDateString())
                                        ->where('status', 'available')
                                        ->where('is_cancelled', false)
                                        ->count();

                                @endphp
                                @if($scheduleCount > 0)
                                    <p class="text-sm text-blue-900 font-semibold">
                                        ✓ {{ $scheduleCount }} jadwal tersedia
                                    </p>
                                @else
                                    <p class="text-sm text-gray-600">
                                        ⚠️ Jadwal sedang penuh
                                    </p>
                                @endif
                            </div>

                            <!-- Book Button -->
                            @if($scheduleCount > 0)
                                <a href="{{ route('workshop.book', $workshop) }}" 
                                   class="block w-full text-center px-6 py-3 bg-orange-600 text-white font-bold rounded-lg hover:bg-orange-700 transition-colors duration-200">
                                    Booking Sekarang
                                </a>
                            @else
                                <button disabled 
                                        class="w-full px-6 py-3 bg-gray-400 text-gray-600 font-bold rounded-lg cursor-not-allowed">
                                    Jadwal Penuh
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <!-- Empty State -->
                    <div class="col-span-full text-center py-16">
                        <div class="text-gray-400 text-6xl mb-4">📭</div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Tidak Ada Workshop</h3>
                        <p class="text-gray-600">Maaf, saat ini belum ada workshop yang tersedia.</p>
                    </div>
                @endforelse
            </div>

            <!-- Info Box -->
            <div class="mt-12 bg-amber-50 border-l-4 border-amber-600 p-6 rounded-lg">
                <h3 class="text-lg font-bold text-amber-900 mb-2">💡 Informasi Penting</h3>
                <ul class="text-amber-800 space-y-2">
                    <li>✓ Pilih workshop sesuai jadwal Anda</li>
                    <li>✓ Setelah pilih jadwal, lanjut ke data peserta</li>
                    <li>✓ Pembayaran bisa dilakukan via transfer atau metode lain</li>
                    <li>✓ Konfirmasi booking akan dikirim ke email Anda</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // Nothing needed - everything is handled by Laravel/Blade
    </script>
</x-layouts.landing>
