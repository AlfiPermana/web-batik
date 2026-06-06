<x-layouts.app :title="__('Pesanan')">
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <flux:heading size="xl">Pesanan</flux:heading>
                <flux:subheading>Kelola dan pantau semua pesanan customer</flux:subheading>
            </div>
        </div>

        @if ($message = Session::get('success'))
            <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl">
                {{ $message }}
            </div>
        @endif

        @if ($message = Session::get('error'))
            <div class="p-4 bg-red-100 border border-red-300 text-red-700 rounded-xl">
                {{ $message }}
            </div>
        @endif

        <!-- Ringkasan Status Pesanan -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="rounded-xl border border-yellow-300 dark:border-yellow-800 bg-yellow-100 hover:bg-yellow-200 dark:bg-yellow-900/40 dark:hover:bg-yellow-900/60 p-5 hover:shadow transition-colors">
                <div class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Pending</div>
                <div class="mt-1 text-2xl font-bold text-yellow-950 dark:text-yellow-100">{{ $pendingOrdersCount ?? 0 }}</div>
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'processing']) }}" class="rounded-xl border border-blue-200 dark:border-blue-800 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-900/50 p-5 hover:shadow transition">
                <div class="text-sm font-medium text-blue-700 dark:text-blue-300">Processing</div>
                <div class="mt-1 text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $processingOrdersCount ?? 0 }}</div>
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'shipped']) }}" class="rounded-xl border border-purple-200 dark:border-purple-800 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/30 dark:to-purple-900/50 p-5 hover:shadow transition">
                <div class="text-sm font-medium text-purple-700 dark:text-purple-300">Shipped</div>
                <div class="mt-1 text-2xl font-bold text-purple-900 dark:text-purple-100">{{ $shippedOrdersCount ?? 0 }}</div>
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'delivered']) }}" class="rounded-xl border border-green-200 dark:border-green-800 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-900/50 p-5 hover:shadow transition">
                <div class="text-sm font-medium text-green-700 dark:text-green-300">Delivered</div>
                <div class="mt-1 text-2xl font-bold text-green-900 dark:text-green-100">{{ $deliveredOrdersCount ?? 0 }}</div>
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}" class="rounded-xl border border-red-300 dark:border-red-800 bg-red-100 hover:bg-red-200 dark:bg-red-900/40 dark:hover:bg-red-900/60 p-5 hover:shadow transition-colors">
                <div class="text-sm font-medium text-red-800 dark:text-red-200">Cancelled</div>
                <div class="mt-1 text-2xl font-bold text-red-950 dark:text-red-100">{{ $cancelledOrdersCount ?? 0 }}</div>
            </a>
        </div>

        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 bg-white dark:bg-zinc-900">
            <div class="mb-3">
                <flux:heading size="lg">Ringkasan Booking Workshop</flux:heading>
                <flux:subheading class="mt-1">Booking workshop sekarang juga tampil di halaman pesanan admin</flux:subheading>
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                <div class="rounded-xl border border-yellow-300 dark:border-yellow-800 bg-yellow-100 dark:bg-yellow-900/40 p-5">
                    <div class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Pending Booking</div>
                    <div class="mt-1 text-2xl font-bold text-yellow-950 dark:text-yellow-100">{{ $pendingWorkshopOrdersCount ?? 0 }}</div>
                </div>
                <div class="rounded-xl border border-blue-200 dark:border-blue-800 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-900/50 p-5">
                    <div class="text-sm font-medium text-blue-700 dark:text-blue-300">Pending Payment</div>
                    <div class="mt-1 text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $pendingWorkshopPaymentsCount ?? 0 }}</div>
                </div>
                <div class="rounded-xl border border-green-200 dark:border-green-800 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-900/50 p-5">
                    <div class="text-sm font-medium text-green-700 dark:text-green-300">Confirmed</div>
                    <div class="mt-1 text-2xl font-bold text-green-900 dark:text-green-100">{{ $confirmedWorkshopOrdersCount ?? 0 }}</div>
                </div>
                <div class="rounded-xl border border-purple-200 dark:border-purple-800 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/30 dark:to-purple-900/50 p-5">
                    <div class="text-sm font-medium text-purple-700 dark:text-purple-300">Completed</div>
                    <div class="mt-1 text-2xl font-bold text-purple-900 dark:text-purple-100">{{ $completedWorkshopOrdersCount ?? 0 }}</div>
                </div>
                <div class="rounded-xl border border-red-300 dark:border-red-800 bg-red-100 dark:bg-red-900/40 p-5">
                    <div class="text-sm font-medium text-red-800 dark:text-red-200">Cancelled</div>
                    <div class="mt-1 text-2xl font-bold text-red-950 dark:text-red-100">{{ $cancelledWorkshopOrdersCount ?? 0 }}</div>
                </div>
            </div>
        </div>

        <!-- Search & Filter -->
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="p-6 bg-white dark:bg-zinc-900">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cari Pesanan</label>
                        <input
                            type="text"
                            name="search"
                            placeholder="No pesanan atau email..."
                            value="{{ request('search') }}"
                            class="w-full px-4 py-2 rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status Pesanan</label>
                        <select name="status" class="w-full px-4 py-2 rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                            <option value="">-- Semua Status --</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status Pembayaran</label>
                        <select name="payment_status" class="w-full px-4 py-2 rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                            <option value="">-- Semua Status --</option>
                            @foreach($paymentStatuses as $paymentStatus)
                                <option value="{{ $paymentStatus }}" {{ request('payment_status') === $paymentStatus ? 'selected' : '' }}>
                                    {{ ucfirst($paymentStatus) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end">
                        <flux:button type="submit" variant="primary" class="w-full">Cari</flux:button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Orders Table -->
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900">
                <flux:heading size="lg">Order Produk</flux:heading>
                <flux:subheading class="mt-1">Pesanan produk fisik</flux:subheading>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-zinc-50 dark:bg-zinc-800 border-b border-neutral-200 dark:border-neutral-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">No Pesanan</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Status Pesanan</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Status Pembayaran</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse($orders as $order)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $order->order_number }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $order->user->name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $order->user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-indigo-600 dark:text-indigo-400">Rp {{ number_format($order->total, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php $badge = $order->status_badge @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badge['bg'] }} {{ $badge['text'] }}">
                                        {{ $badge['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $paymentBadge = match($order->payment_status) {
                                            'unpaid', 'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'label' => 'Menunggu Pembayaran'],
                                            'paid', 'confirmed' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Lunas'],
                                            'expired' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'label' => 'Kadaluarsa'],
                                            'refunded' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'Dikembalikan'],
                                            'failed' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Gagal'],
                                            default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => ucfirst($order->payment_status)],
                                        };
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $paymentBadge['bg'] }} {{ $paymentBadge['text'] }}">
                                        {{ $paymentBadge['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    {{ $order->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <flux:button size="sm" :href="route('admin.orders.show', $order)" variant="ghost">
                                        Detail
                                    </flux:button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <flux:heading size="lg" class="text-zinc-400">Tidak ada pesanan ditemukan</flux:heading>
                                    <flux:subheading class="mt-2">Coba ubah filter atau kata kunci pencarian</flux:subheading>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($orders->hasPages())
            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        @endif

        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900">
                <flux:heading size="lg">Order Workshop</flux:heading>
                <flux:subheading class="mt-1">Booking workshop dan status pembayarannya</flux:subheading>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-zinc-50 dark:bg-zinc-800 border-b border-neutral-200 dark:border-neutral-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">No Booking</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Workshop</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Peserta</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Status Booking</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Status Pembayaran</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse($workshopBookings as $booking)
                            @php
                                $workshop = $booking->workshopAvailableDate?->workshop;
                                $customerStats = $customerWorkshopStats[$booking->id] ?? [
                                    'total_bookings' => 1,
                                    'total_participants' => (int) $booking->num_participants,
                                ];
                                $paymentBadge = match($booking->payment_status) {
                                    'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'label' => 'Menunggu Pembayaran'],
                                    'deposit_paid' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'DP Dibayar'],
                                    'fully_paid' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Lunas'],
                                    'expired' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'label' => 'Kadaluarsa'],
                                    'failed' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Gagal'],
                                    'refunded' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'label' => 'Refund'],
                                    default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => ucfirst($booking->payment_status ?? '-')],
                                };
                                $statusBadge = match($booking->status) {
                                    'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'label' => 'Pending'],
                                    'confirmed' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Confirmed'],
                                    'completed' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'label' => 'Completed'],
                                    'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Cancelled'],
                                    default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => ucfirst($booking->status ?? '-')],
                                };
                            @endphp
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $booking->booking_number }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $workshop?->title ?? 'Workshop' }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        @if($booking->slotSchedule && $booking->slotSchedule->timeSlot)
                                            {{ is_string($booking->slotSchedule->date) ? $booking->slotSchedule->date : $booking->slotSchedule->date->format('d M Y') }}
                                            • {{ substr($booking->slotSchedule->timeSlot->start_time, 0, 5) }} - {{ substr($booking->slotSchedule->timeSlot->end_time, 0, 5) }}
                                        @else
                                            Jadwal belum tersedia
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $booking->customer_name ?? $booking->user?->name ?? '-' }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $booking->customer_email ?? $booking->user?->email ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ (int) $booking->num_participants }} orang</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        Paket ini: {{ $customerStats['total_bookings'] }} booking / {{ $customerStats['total_participants'] }} peserta
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-indigo-600 dark:text-indigo-400">Rp {{ number_format((float) $booking->total_price, 0, ',', '.') }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        @if((float) $booking->num_participants > 0)
                                            Rp {{ number_format((float) $booking->total_price / (float) $booking->num_participants, 0, ',', '.') }}/orang
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusBadge['bg'] }} {{ $statusBadge['text'] }}">
                                        {{ $statusBadge['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $paymentBadge['bg'] }} {{ $paymentBadge['text'] }}">
                                        {{ $paymentBadge['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    {{ $booking->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($workshop)
                                        <flux:button size="sm" :href="route('admin.workshop.bookings.show', [$workshop, $booking])" variant="ghost">
                                            Detail
                                        </flux:button>
                                    @else
                                        <span class="text-xs text-gray-400">Workshop tidak ditemukan</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center">
                                    <flux:heading size="lg" class="text-zinc-400">Tidak ada booking workshop ditemukan</flux:heading>
                                    <flux:subheading class="mt-2">Booking workshop akan tampil di sini bersama order produk</flux:subheading>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($workshopBookings->hasPages())
            <div class="mt-4">
                {{ $workshopBookings->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
