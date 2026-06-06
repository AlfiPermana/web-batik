<x-layouts.app :title="__('Detail Booking Workshop')">
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <flux:heading size="xl">Detail Booking</flux:heading>
                <flux:subheading>{{ $workshop->title }}</flux:subheading>
            </div>
            <flux:button :href="route('admin.workshop.bookings', $workshop)" variant="ghost">Kembali</flux:button>
        </div>

        @php
            $schedule = $booking->slotSchedule;
            $timeSlot = $schedule?->timeSlot;

            $pay = $booking->payment_status;
            $payLabel = match ($pay) {
                'fully_paid' => 'Lunas',
                'deposit_paid' => 'Dibayar Sebagian',
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

            $statusClass = match ($booking->status) {
                'confirmed' => 'bg-green-100 text-green-800',
                'cancelled' => 'bg-gray-100 text-gray-800',
                'completed' => 'bg-purple-100 text-purple-800',
                default => 'bg-yellow-100 text-yellow-800',
            };

            $confirmedPaidAmount = (float) ($booking->payments?->where('payment_status', 'confirmed')->sum('amount') ?? 0);
        @endphp

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="font-mono text-sm text-gray-600 dark:text-gray-400">Booking</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $booking->booking_number }}</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $statusClass }}">{{ ucfirst($booking->status) }}</span>
                            <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $payClass }}">{{ $payLabel }}</span>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Nama</div>
                            <div class="text-gray-900 dark:text-gray-100">{{ $booking->customer_name ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Email</div>
                            <div class="text-gray-900 dark:text-gray-100">{{ $booking->customer_email ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Telepon</div>
                            <div class="text-gray-900 dark:text-gray-100">{{ $booking->customer_phone ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Peserta</div>
                            <div class="text-gray-900 dark:text-gray-100">{{ $booking->num_participants }}</div>
                        </div>
                        <div class="md:col-span-2">
                            <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Jadwal</div>
                            <div class="text-gray-900 dark:text-gray-100">
                                @if($schedule && $timeSlot)
                                    <div>{{ is_string($schedule->date) ? $schedule->date : $schedule->date->format('Y-m-d') }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ substr($timeSlot->start_time, 0, 5) }} - {{ substr($timeSlot->end_time, 0, 5) }} ({{ $timeSlot->name }})</div>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Alamat</div>
                            <div class="text-gray-900 dark:text-gray-100">{{ $booking->address ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900">
                        <flux:heading size="lg">Riwayat Pembayaran</flux:heading>
                    </div>

                    <div class="bg-white dark:bg-zinc-900">
                        @if($booking->payments && $booking->payments->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-zinc-50 dark:bg-zinc-800 border-b border-neutral-200 dark:border-neutral-700">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Waktu</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Tipe</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Metode</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Ref</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Jumlah</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                                        @foreach($booking->payments->sortByDesc('id') as $p)
                                            @php
                                                $ps = $p->payment_status;
                                                $psLabel = match ($ps) {
                                                    'confirmed' => 'Confirmed',
                                                    'pending' => 'Pending',
                                                    'failed' => 'Failed',
                                                    'expired' => 'Expired',
                                                    'refunded' => 'Refunded',
                                                    default => $ps ?? '-',
                                                };
                                                $psClass = match ($ps) {
                                                    'confirmed' => 'bg-green-100 text-green-800',
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'failed' => 'bg-red-100 text-red-800',
                                                    'expired' => 'bg-orange-100 text-orange-800',
                                                    'refunded' => 'bg-purple-100 text-purple-800',
                                                    default => 'bg-gray-100 text-gray-800',
                                                };
                                            @endphp
                                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $p->created_at?->format('Y-m-d H:i') }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $p->type }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $p->payment_method }}</td>
                                                <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100">{{ $p->reference_number ?? '-' }}</td>
                                                <td class="px-4 py-3 text-sm font-bold text-indigo-600 dark:text-indigo-400">Rp {{ number_format((float) $p->amount, 0, ',', '.') }}</td>
                                                <td class="px-4 py-3 text-sm">
                                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $psClass }}">{{ $psLabel }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="px-6 py-6 text-sm text-gray-600 dark:text-gray-400">Belum ada data pembayaran.</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6">
                    <flux:heading size="lg">Ringkasan</flux:heading>

                    <div class="mt-4 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-700 dark:text-gray-300">Total</span>
                            <span class="font-semibold text-gray-900 dark:text-gray-100">Rp {{ number_format((float) $booking->total_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-700 dark:text-gray-300">Sudah Dibayar</span>
                            <span class="font-semibold text-gray-900 dark:text-gray-100">Rp {{ number_format($confirmedPaidAmount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-700 dark:text-gray-300">Sisa Tagihan</span>
                            <span class="font-semibold text-gray-900 dark:text-gray-100">Rp {{ number_format((float) $booking->remaining_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-neutral-200 dark:border-neutral-700">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Payment Date</div>
                        <div class="text-gray-900 dark:text-gray-100">{{ $booking->payment_date?->format('Y-m-d H:i') ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
