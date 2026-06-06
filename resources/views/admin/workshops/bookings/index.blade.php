<x-layouts.app :title="__('Booking Workshop')">
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <flux:heading size="xl">Booking Workshop</flux:heading>
                <flux:subheading>{{ $workshop->title }}</flux:subheading>
            </div>
            <flux:button :href="route('admin.workshop.show', $workshop)" variant="ghost">Kembali</flux:button>
        </div>

        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
            <form method="GET" action="{{ route('admin.workshop.bookings', $workshop) }}" class="p-6 bg-white dark:bg-zinc-900">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cari</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Booking / nama / email" class="w-full px-4 py-2 rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status Booking</label>
                        <select name="status" class="w-full px-4 py-2 rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                            <option value="">Semua</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status Pembayaran</label>
                        <select name="payment_status" class="w-full px-4 py-2 rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                            <option value="">Semua</option>
                            <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="deposit_paid" {{ request('payment_status') === 'deposit_paid' ? 'selected' : '' }}>DP Dibayar</option>
                            <option value="fully_paid" {{ request('payment_status') === 'fully_paid' ? 'selected' : '' }}>Lunas</option>
                            <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Gagal</option>
                            <option value="expired" {{ request('payment_status') === 'expired' ? 'selected' : '' }}>Kadaluarsa</option>
                            <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>Refund</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <flux:button type="submit" variant="primary" class="w-full">Filter</flux:button>
                        <flux:button :href="route('admin.workshop.bookings', $workshop)" variant="ghost" class="w-full">Reset</flux:button>
                    </div>
                </div>
            </form>
        </div>

        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
            @if($bookings->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-zinc-50 dark:bg-zinc-800 border-b border-neutral-200 dark:border-neutral-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Booking</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Jadwal</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Peserta</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Pembayaran</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-zinc-900 divide-y divide-neutral-200 dark:divide-neutral-700">
                            @foreach($bookings as $booking)
                                @php
                                    $schedule = $booking->slotSchedule;
                                    $timeSlot = $schedule?->timeSlot;

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

                                    $statusClass = match ($booking->status) {
                                        'confirmed' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-gray-100 text-gray-800',
                                        'completed' => 'bg-purple-100 text-purple-800',
                                        default => 'bg-yellow-100 text-yellow-800',
                                    };
                                @endphp
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-mono text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $booking->booking_number }}</div>
                                        <div class="text-xs text-gray-600 dark:text-gray-400">{{ $booking->created_at?->format('Y-m-d H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $booking->customer_name ?? '-' }}</div>
                                        <div class="text-xs text-gray-600 dark:text-gray-400">{{ $booking->customer_email ?? '-' }}</div>
                                        <div class="text-xs text-gray-600 dark:text-gray-400">{{ $booking->customer_phone ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                        @if($schedule && $timeSlot)
                                            <div>{{ is_string($schedule->date) ? $schedule->date : $schedule->date->format('Y-m-d') }}</div>
                                            <div class="text-xs text-gray-600 dark:text-gray-400">{{ substr($timeSlot->start_time, 0, 5) }} - {{ substr($timeSlot->end_time, 0, 5) }}</div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $booking->num_participants }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $payClass }}">{{ $payLabel }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">{{ ucfirst($booking->status) }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <flux:button size="sm" :href="route('admin.workshop.bookings.show', [$workshop, $booking])" variant="ghost">Detail</flux:button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900">
                    {{ $bookings->links() }}
                </div>
            @else
                <div class="px-6 py-12 text-center bg-white dark:bg-zinc-900">
                    <flux:heading size="lg" class="text-zinc-400">Belum ada booking</flux:heading>
                    <flux:subheading class="mt-2">Belum ada booking untuk workshop ini.</flux:subheading>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
