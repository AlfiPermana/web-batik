<x-layouts.app :title="__('Detail Workshop')">
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <flux:heading size="xl">{{ $workshop->title }}</flux:heading>
                <flux:subheading>{{ Str::limit($workshop->description, 100) }}</flux:subheading>
            </div>
            <flux:button :href="route('admin.workshop.edit', $workshop)" variant="primary">Edit Workshop</flux:button>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6">
                <div class="text-sm text-gray-600 dark:text-gray-400">Harga</div>
                <div class="mt-2 text-2xl font-bold text-indigo-600 dark:text-indigo-400">Rp {{ number_format($workshop->amount, 0, ',', '.') }}</div>
            </div>
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6">
                <div class="text-sm text-gray-600 dark:text-gray-400">Time Slots</div>
                <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $workshop->timeSlots()->count() }}</div>
            </div>
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6">
                <div class="text-sm text-gray-600 dark:text-gray-400">Available Dates</div>
                <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $workshop->availableDates()->count() }}</div>
            </div>
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6">
                <div class="text-sm text-gray-600 dark:text-gray-400">Status</div>
                <div class="mt-2">
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $workshop->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $workshop->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900">
                <flux:heading size="lg">Jadwal Ketersediaan</flux:heading>
                <flux:subheading class="mt-1">Status booking untuk setiap kombinasi slot × tanggal</flux:subheading>
            </div>

            <div class="bg-white dark:bg-zinc-900 p-6">
                @php $schedules = $workshop->schedules; @endphp

                @if($schedules->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-zinc-50 dark:bg-zinc-800 border-b border-neutral-200 dark:border-neutral-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Slot Waktu</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Total Peserta</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Jumlah Booking</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Kapasitas</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                                @foreach($schedules as $schedule)
                                    @if($schedule->timeSlot)
                                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $schedule->date->format('d F Y') }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ substr($schedule->timeSlot->start_time, 0, 5) }} - {{ substr($schedule->timeSlot->end_time, 0, 5) }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $schedule->bookings?->where('status', '!=', 'cancelled')->sum('num_participants') ?? 0 }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $schedule->bookings?->where('status', '!=', 'cancelled')->count() ?? 0 }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100"><span class="text-gray-500 dark:text-gray-400">Unlimited</span></td>
                                            <td class="px-6 py-4 text-sm">
                                                @if($schedule->status === 'PAID')
                                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">🔒 CLOSED (Sudah Dibayar)</span>
                                                @elseif($schedule->status === 'fully_booked')
                                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">Penuh</span>
                                                @elseif($schedule->status === 'on_book')
                                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">⏳ Dalam Proses</span>
                                                @elseif($schedule->is_cancelled)
                                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">❌ Dibatalkan</span>
                                                @else
                                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">✅ Tersedia</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 text-center">
                        <p class="text-blue-800">Belum ada jadwal. Buat time slots dan pilih tanggal di halaman Edit.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900">
                <flux:heading size="lg">Peserta Booking</flux:heading>
            </div>

            <div class="bg-white dark:bg-zinc-900 p-6">
                @if($workshop->bookings->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-zinc-50 dark:bg-zinc-800 border-b border-neutral-200 dark:border-neutral-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Nama</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Email</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Telepon</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Jadwal</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Peserta</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Status Booking</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Status Pembayaran</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                                @foreach($workshop->bookings as $booking)
                                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $booking->customer_name ?? '-' }}</td>
                                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $booking->customer_email ?? '-' }}</td>
                                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $booking->customer_phone ?? '-' }}</td>
                                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                            @if($booking->slotSchedule && $booking->slotSchedule->timeSlot)
                                                {{ is_string($booking->slotSchedule->date) ? $booking->slotSchedule->date : $booking->slotSchedule->date->format('d M Y') }}
                                                ({{ substr($booking->slotSchedule->timeSlot->start_time, 0, 5) }} - {{ substr($booking->slotSchedule->timeSlot->end_time, 0, 5) }})
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $booking->num_participants ?? '-' }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs rounded-full {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : ($booking->status === 'cancelled' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            @php
                                                $pay = $booking->payment_status;
                                                $payLabel = match ($pay) {
                                                    'fully_paid' => 'Lunas',
                                                    'deposit_paid' => 'DP Dibayar',
                                                    'failed' => 'Gagal',
                                                    'expired' => 'Kadaluarsa',
                                                    'refunded' => 'Refund',
                                                    'pending' => 'Menunggu',
                                                    default => $pay ?? '-',
                                                };
                                                $payClass = match ($pay) {
                                                    'fully_paid' => 'bg-green-100 text-green-800',
                                                    'deposit_paid' => 'bg-blue-100 text-blue-800',
                                                    'failed' => 'bg-red-100 text-red-800',
                                                    'expired' => 'bg-orange-100 text-orange-800',
                                                    'refunded' => 'bg-purple-100 text-purple-800',
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    default => 'bg-gray-100 text-gray-800',
                                                };
                                            @endphp
                                            <span class="px-2 py-1 text-xs rounded-full {{ $payClass }}">{{ $payLabel }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="py-10 text-center text-gray-600 dark:text-gray-400">Belum ada booking untuk workshop ini</div>
                @endif
            </div>
        </div>

        <div>
            <a href="{{ route('admin.workshop.index') }}" class="text-primary-600 dark:text-primary-400 hover:underline font-semibold">← Kembali ke daftar workshop</a>
        </div>
    </div>
</x-layouts.app>
