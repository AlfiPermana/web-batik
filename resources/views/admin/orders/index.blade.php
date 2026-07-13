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

        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
            <div class="mb-3 px-6 pt-4">
                <flux:heading size="lg">Ringkasan Booking Workshop</flux:heading>
                <flux:subheading class="mt-1">Booking workshop sekarang juga tampil di halaman pesanan admin</flux:subheading>
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5 px-6 pb-4 bg-white dark:bg-zinc-900">
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

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- TABEL ORDER PRODUK                                          -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <form id="form-delete-orders" method="POST" action="{{ route('admin.orders.bulkDelete') }}">
            @csrf
            @method('DELETE')

            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                <!-- Header + bulk actions -->
                <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <flux:heading size="lg">Order Produk</flux:heading>
                        <flux:subheading class="mt-1">Pesanan produk fisik</flux:subheading>
                    </div>
                    <div class="flex items-center gap-3" id="orders-bulk-actions" style="display:none!important">
                        <span id="orders-selected-count" class="text-sm text-gray-600 dark:text-gray-400 font-medium"></span>
                        <button
                            type="submit"
                            id="btn-delete-orders"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-semibold shadow transition-colors"
                            onclick="return confirmBulkDelete('orders')"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-7 0a1 1 0 01-1-1V5a1 1 0 011-1h8a1 1 0 011 1v1a1 1 0 01-1 1H9z" />
                            </svg>
                            Hapus Terpilih
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-zinc-50 dark:bg-zinc-800 border-b border-neutral-200 dark:border-neutral-700">
                            <tr>
                                <!-- Checkbox "pilih semua" -->
                                <th class="px-4 py-3 w-10">
                                    <input
                                        type="checkbox"
                                        id="check-all-orders"
                                        class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500 cursor-pointer"
                                        title="Pilih semua"
                                    >
                                </th>
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
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors order-row">
                                    <!-- Checkbox per baris -->
                                    <td class="px-4 py-4 w-10">
                                        <input
                                            type="checkbox"
                                            name="order_ids[]"
                                            value="{{ $order->id }}"
                                            class="order-checkbox h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500 cursor-pointer"
                                        >
                                    </td>
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
                                        <div class="flex items-center justify-center gap-2">
                                            <flux:button size="sm" :href="route('admin.orders.show', $order)" variant="ghost">
                                                Detail
                                            </flux:button>
                                            <button type="button" onclick="confirmSingleDelete('{{ route('admin.orders.destroy', $order) }}', 'pesanan produk')" class="p-1 text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded transition-colors" title="Hapus">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <flux:heading size="lg" class="text-zinc-400">Tidak ada pesanan ditemukan</flux:heading>
                                        <flux:subheading class="mt-2">Coba ubah filter atau kata kunci pencarian</flux:subheading>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </form>

        @if($orders->hasPages())
            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        @endif

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- TABEL ORDER WORKSHOP                                        -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <form id="form-delete-bookings" method="POST" action="{{ route('admin.orders.bulkDeleteBookings') }}">
            @csrf
            @method('DELETE')

            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                <!-- Header + bulk actions -->
                <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <flux:heading size="lg">Order Workshop</flux:heading>
                        <flux:subheading class="mt-1">Booking workshop dan status pembayarannya</flux:subheading>
                    </div>
                    <div class="flex items-center gap-3" id="bookings-bulk-actions" style="display:none!important">
                        <span id="bookings-selected-count" class="text-sm text-gray-600 dark:text-gray-400 font-medium"></span>
                        <button
                            type="submit"
                            id="btn-delete-bookings"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-semibold shadow transition-colors"
                            onclick="return confirmBulkDelete('bookings')"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-7 0a1 1 0 01-1-1V5a1 1 0 011-1h8a1 1 0 011 1v1a1 1 0 01-1 1H9z" />
                            </svg>
                            Hapus Terpilih
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-zinc-50 dark:bg-zinc-800 border-b border-neutral-200 dark:border-neutral-700">
                            <tr>
                                <!-- Checkbox "pilih semua" -->
                                <th class="px-4 py-3 w-10">
                                    <input
                                        type="checkbox"
                                        id="check-all-bookings"
                                        class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500 cursor-pointer"
                                        title="Pilih semua"
                                    >
                                </th>
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
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors booking-row">
                                    <!-- Checkbox per baris -->
                                    <td class="px-4 py-4 w-10">
                                        <input
                                            type="checkbox"
                                            name="booking_ids[]"
                                            value="{{ $booking->id }}"
                                            class="booking-checkbox h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500 cursor-pointer"
                                        >
                                    </td>
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
                                        <div class="flex items-center justify-center gap-2">
                                            @if($workshop)
                                                <flux:button size="sm" :href="route('admin.workshop.bookings.show', [$workshop, $booking])" variant="ghost">
                                                    Detail
                                                </flux:button>
                                            @else
                                                <span class="text-xs text-gray-400">Workshop tidak ditemukan</span>
                                            @endif
                                            <button type="button" onclick="confirmSingleDelete('{{ route('admin.orders.destroyBooking', $booking) }}', 'booking workshop')" class="p-1 text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded transition-colors" title="Hapus">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-6 py-12 text-center">
                                        <flux:heading size="lg" class="text-zinc-400">Tidak ada booking workshop ditemukan</flux:heading>
                                        <flux:subheading class="mt-2">Booking workshop akan tampil di sini bersama order produk</flux:subheading>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </form>

        @if($workshopBookings->hasPages())
            <div class="mt-4">
                {{ $workshopBookings->links() }}
            </div>
        @endif
    </div>

    <!-- Hidden Single Delete Form -->
    <form id="single-delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
    <script>
    (function () {
        // ─── Helper ─────────────────────────────────────────────────────────
        function initBulkSelect(options) {
            const checkAll   = document.getElementById(options.checkAllId);
            const actionsBar = document.getElementById(options.actionsBarId);
            const countLabel = document.getElementById(options.countLabelId);
            const rowClass   = options.rowClass;

            if (!checkAll) return;

            function getBoxes() {
                return document.querySelectorAll('.' + rowClass + ' input[type="checkbox"]');
            }

            function updateBar() {
                const boxes   = getBoxes();
                const checked = Array.from(boxes).filter(b => b.checked);
                const n       = checked.length;

                if (n > 0) {
                    actionsBar.style.removeProperty('display');
                    actionsBar.style.display = 'flex';
                    countLabel.textContent = n + ' data dipilih';
                } else {
                    actionsBar.style.display = 'none';
                }

                checkAll.indeterminate = n > 0 && n < boxes.length;
                checkAll.checked       = n > 0 && n === boxes.length;
            }

            checkAll.addEventListener('change', function () {
                getBoxes().forEach(b => { b.checked = checkAll.checked; });
                updateBar();
            });

            document.addEventListener('change', function (e) {
                if (e.target.closest('.' + rowClass)) {
                    updateBar();
                }
            });
        }

        initBulkSelect({
            checkAllId  : 'check-all-orders',
            actionsBarId: 'orders-bulk-actions',
            countLabelId: 'orders-selected-count',
            rowClass    : 'order-row',
        });

        initBulkSelect({
            checkAllId  : 'check-all-bookings',
            actionsBarId: 'bookings-bulk-actions',
            countLabelId: 'bookings-selected-count',
            rowClass    : 'booking-row',
        });
    })();

    // ─── SweetAlert2 Konfirmasi Bulk Delete ──────────────────────────────────
    function confirmBulkDelete(type) {
        const label  = type === 'orders' ? 'pesanan produk' : 'booking workshop';
        const formId = type === 'orders' ? 'form-delete-orders' : 'form-delete-bookings';
        const count  = document.querySelectorAll(
            type === 'orders' ? '.order-row input[type="checkbox"]:checked'
                              : '.booking-row input[type="checkbox"]:checked'
        ).length;

        Swal.fire({
            title: 'Hapus ' + count + ' Data?',
            html: 'Anda akan menghapus <strong>' + count + ' ' + label + '</strong>.<br>Tindakan ini <strong>tidak dapat dibatalkan</strong>.',
            icon: 'warning',
            iconColor: '#ef4444',
            showCancelButton: true,
            confirmButtonText: '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6"/></svg> Ya, Hapus!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            reverseButtons: true,
            focusCancel: true,
            customClass: {
                popup: 'rounded-2xl shadow-2xl',
                title: 'text-gray-800 font-bold',
                htmlContainer: 'text-gray-600',
                confirmButton: 'rounded-lg px-5 py-2.5 font-semibold text-sm',
                cancelButton: 'rounded-lg px-5 py-2.5 font-semibold text-sm',
            },
        }).then(function (result) {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });

        // Return false agar button type="submit" tidak langsung submit
        return false;
    }

    // ─── SweetAlert2 Konfirmasi Single Delete ────────────────────────────────
    function confirmSingleDelete(url, label) {
        Swal.fire({
            title: 'Hapus Data?',
            html: 'Anda akan menghapus 1 <strong>' + label + '</strong>.<br>Tindakan ini <strong>tidak dapat dibatalkan</strong>.',
            icon: 'warning',
            iconColor: '#ef4444',
            showCancelButton: true,
            confirmButtonText: '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6"/></svg> Ya, Hapus!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            reverseButtons: true,
            focusCancel: true,
            customClass: {
                popup: 'rounded-2xl shadow-2xl',
                title: 'text-gray-800 font-bold',
                htmlContainer: 'text-gray-600',
                confirmButton: 'rounded-lg px-5 py-2.5 font-semibold text-sm',
                cancelButton: 'rounded-lg px-5 py-2.5 font-semibold text-sm',
            },
        }).then(function (result) {
            if (result.isConfirmed) {
                const form = document.getElementById('single-delete-form');
                form.action = url;
                form.submit();
            }
        });
    }
    </script>
    @endpush
</x-layouts.app>
