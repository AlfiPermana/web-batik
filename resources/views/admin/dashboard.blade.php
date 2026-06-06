<x-layouts.app :title="__('Admin Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <flux:heading size="xl">Admin Dashboard</flux:heading>
                <flux:subheading>Ringkasan performa toko</flux:subheading>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3 xl:grid-cols-6">
            <div class="rounded-xl border border-orange-200 bg-gradient-to-br from-orange-50 to-orange-100 p-6 dark:border-orange-800 dark:from-orange-900/30 dark:to-orange-900/50">
                <div class="text-sm font-medium text-orange-700 dark:text-orange-300">{{ __('Total Products') }}</div>
                <div class="mt-2 text-2xl font-bold text-orange-900 dark:text-orange-100">{{ $totalProducts ?? 0 }}</div>
            </div>
            <div class="rounded-xl border border-orange-200 bg-gradient-to-br from-orange-50 to-orange-100 p-6 dark:border-orange-800 dark:from-orange-900/30 dark:to-orange-900/50">
                <div class="text-sm font-medium text-orange-700 dark:text-orange-300">{{ __('Total Workshops') }}</div>
                <div class="mt-2 text-2xl font-bold text-orange-900 dark:text-orange-100">{{ $totalWorkshops ?? 0 }}</div>
            </div>
            <div class="rounded-xl border border-orange-200 bg-gradient-to-br from-orange-50 to-orange-100 p-6 dark:border-orange-800 dark:from-orange-900/30 dark:to-orange-900/50">
                <div class="text-sm font-medium text-orange-700 dark:text-orange-300">{{ __('Total Customers') }}</div>
                <div class="mt-2 text-2xl font-bold text-orange-900 dark:text-orange-100">{{ $totalUsers ?? 0 }}</div>
            </div>
            <div class="rounded-xl border border-orange-200 bg-gradient-to-br from-orange-50 to-orange-100 p-6 dark:border-orange-800 dark:from-orange-900/30 dark:to-orange-900/50">
                <div class="text-sm font-medium text-orange-700 dark:text-orange-300">{{ __('Total Orders') }}</div>
                <div class="mt-2 text-2xl font-bold text-orange-900 dark:text-orange-100">{{ $totalOrders ?? 0 }}</div>
            </div>
            <div class="rounded-xl border border-amber-200 bg-gradient-to-br from-amber-50 to-yellow-100 p-6 dark:border-amber-800 dark:from-amber-900/30 dark:to-amber-900/50">
                <div class="text-sm font-medium text-amber-700 dark:text-amber-300">Booking Workshop</div>
                <div class="mt-2 text-2xl font-bold text-amber-900 dark:text-amber-100">{{ $totalWorkshopBookings ?? 0 }}</div>
            </div>
            <div class="rounded-xl border border-yellow-200 bg-gradient-to-br from-yellow-50 to-yellow-100 p-6 dark:border-yellow-800 dark:from-yellow-900/30 dark:to-yellow-900/50">
                <div class="text-sm font-medium text-yellow-700 dark:text-yellow-300">Pending Workshop Payment</div>
                <div class="mt-2 text-2xl font-bold text-yellow-900 dark:text-yellow-100">{{ $pendingWorkshopPayments ?? 0 }}</div>
            </div>
            <div class="rounded-xl border border-orange-200 bg-gradient-to-br from-orange-50 to-orange-100 p-6 dark:border-orange-800 dark:from-orange-900/30 dark:to-orange-900/50">
                <div class="text-sm font-medium text-orange-700 dark:text-orange-300">{{ __('Total Revenue') }}</div>
                <div class="mt-2 text-2xl font-bold text-orange-900 dark:text-orange-100">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
            <a href="{{ route('admin.product.create') }}" class="flex items-center justify-between gap-3 rounded-xl border border-neutral-200 bg-white p-4 text-gray-900 hover:bg-zinc-50 dark:border-neutral-700 dark:bg-zinc-900 dark:text-gray-100 dark:hover:bg-zinc-800">
                <span class="font-semibold">{{ __('Add Product') }}</span>
                <span class="text-sm text-gray-500 dark:text-gray-400">+</span>
            </a>

            <a href="{{ route('admin.workshop.create') }}" class="flex items-center justify-between gap-3 rounded-xl border border-neutral-200 bg-white p-4 text-gray-900 hover:bg-zinc-50 dark:border-neutral-700 dark:bg-zinc-900 dark:text-gray-100 dark:hover:bg-zinc-800">
                <span class="font-semibold">{{ __('Add Workshop') }}</span>
                <span class="text-sm text-gray-500 dark:text-gray-400">+</span>
            </a>
            <a href="{{ route('admin.orders.index') }}" class="flex items-center justify-between gap-3 rounded-xl border border-neutral-200 bg-white p-4 text-gray-900 hover:bg-zinc-50 dark:border-neutral-700 dark:bg-zinc-900 dark:text-gray-100 dark:hover:bg-zinc-800">
                <span class="font-semibold">{{ __('View Orders') }}</span>
                <span class="text-sm text-gray-500 dark:text-gray-400">→</span>
            </a>
            <a href="#" class="flex items-center justify-between gap-3 rounded-xl border border-neutral-200 bg-white p-4 text-gray-900 hover:bg-zinc-50 dark:border-neutral-700 dark:bg-zinc-900 dark:text-gray-100 dark:hover:bg-zinc-800">
                <span class="font-semibold">{{ __('View Reports') }}</span>
                <span class="text-sm text-gray-500 dark:text-gray-400">→</span>
            </a>
        </div>

        <!-- Recent Orders -->
        @if(isset($recentOrders) && $recentOrders->count() > 0)
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900">
                    <flux:heading size="lg">Pesanan Terbaru</flux:heading>
                    <flux:subheading class="mt-1">10 pesanan terakhir</flux:subheading>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-zinc-50 dark:bg-zinc-800 border-b border-neutral-200 dark:border-neutral-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">No Pesanan</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-zinc-900 divide-y divide-neutral-200 dark:divide-neutral-700">
                            @foreach($recentOrders as $order)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $order->order_number }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $order->user->name }}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-indigo-600 dark:text-indigo-400">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        @php $badge = $order->status_badge @endphp
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badge['bg'] }} {{ $badge['text'] }}">
                                            {{ $badge['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $order->created_at->format('d M Y') }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <flux:button size="sm" :href="route('admin.orders.show', $order)" variant="ghost">Lihat</flux:button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900">
                    <a href="{{ route('admin.orders.index') }}" class="text-primary-600 dark:text-primary-400 hover:underline font-semibold">Lihat semua pesanan →</a>
                </div>
            </div>
        @endif

        @if(isset($recentWorkshopBookings) && $recentWorkshopBookings->count() > 0)
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900">
                    <flux:heading size="lg">Booking Workshop Terbaru</flux:heading>
                    <flux:subheading class="mt-1">10 booking workshop terakhir</flux:subheading>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-zinc-50 dark:bg-zinc-800 border-b border-neutral-200 dark:border-neutral-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Booking</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Workshop</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Peserta</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Pembayaran</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-zinc-900 divide-y divide-neutral-200 dark:divide-neutral-700">
                            @foreach($recentWorkshopBookings as $booking)
                                @php
                                    $pay = $booking->payment_status;
                                    $payLabel = match ($pay) {
                                        'fully_paid' => 'Lunas',
                                        'deposit_paid' => 'DP Dibayar',
                                        'failed' => 'Gagal',
                                        'expired' => 'Kadaluarsa',
                                        'refunded' => 'Refund',
                                        default => 'Menunggu',
                                    };
                                    $payClass = match ($pay) {
                                        'fully_paid' => 'bg-green-100 text-green-800',
                                        'deposit_paid' => 'bg-blue-100 text-blue-800',
                                        'failed' => 'bg-red-100 text-red-800',
                                        'expired' => 'bg-orange-100 text-orange-800',
                                        'refunded' => 'bg-purple-100 text-purple-800',
                                        default => 'bg-yellow-100 text-yellow-800',
                                    };
                                @endphp
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $booking->booking_number }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $booking->workshopAvailableDate?->workshop?->title ?? 'Workshop' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            @if($booking->slotSchedule && $booking->slotSchedule->timeSlot)
                                                {{ is_string($booking->slotSchedule->date) ? $booking->slotSchedule->date : $booking->slotSchedule->date->format('d M Y') }}
                                                • {{ substr($booking->slotSchedule->timeSlot->start_time, 0, 5) }} - {{ substr($booking->slotSchedule->timeSlot->end_time, 0, 5) }}
                                            @else
                                                Jadwal belum tersedia
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $booking->customer_name ?? $booking->user?->name ?? '-' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $booking->customer_email ?? $booking->user?->email ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ (int) $booking->num_participants }} orang</td>
                                    <td class="px-6 py-4 text-sm font-bold text-indigo-600 dark:text-indigo-400">Rp {{ number_format((float) $booking->total_price, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $payClass }}">{{ $payLabel }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ ucfirst($booking->status) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $booking->created_at->format('d M Y') }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @if($booking->workshopAvailableDate?->workshop)
                                            <flux:button size="sm" :href="route('admin.workshop.bookings.show', [$booking->workshopAvailableDate->workshop, $booking])" variant="ghost">Detail</flux:button>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
