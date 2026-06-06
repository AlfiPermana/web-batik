<x-layouts.app>
    <div class="space-y-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold" style="font-family: 'Playfair Display', serif; color: #8B4513;">Workshop Saya</h1>
                <p class="mt-1 text-sm sm:text-base text-gray-600" style="font-family: 'Poppins', sans-serif;">Daftar booking workshop yang pernah Anda ikuti / booking.</p>
            </div>
            <a href="{{ route('landing.workshop') }}" class="inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm sm:text-base font-semibold text-white transition-all hover:shadow" style="background-color:#8B4513; font-family: 'Poppins', sans-serif;">
                Lihat Workshop
            </a>
        </div>

        @if($bookings->count() === 0)
            <div class="rounded-lg border border-dashed border-gray-300 bg-white p-8 text-center dark:border-gray-700 dark:bg-gray-900">
                <p class="text-gray-700 dark:text-gray-300">Belum ada booking workshop.</p>
            </div>
        @else
            <!-- Mobile cards -->
            <div class="md:hidden space-y-3">
                @foreach($bookings as $booking)
                    @php
                        $workshopTitle = $booking->workshopAvailableDate?->workshop?->title ?? $booking->workshop_name ?? 'Workshop';

                        $dateVal = $booking->slotSchedule?->date ?? $booking->workshopAvailableDate?->date ?? $booking->workshop_date ?? null;
                        $dateStr = is_string($dateVal) ? $dateVal : ($dateVal?->format('Y-m-d') ?? '-');

                        $startVal = $booking->slotSchedule?->timeSlot?->start_time ?? $booking->start_time ?? null;
                        $endVal = $booking->slotSchedule?->timeSlot?->end_time ?? $booking->end_time ?? null;

                        $startStr = is_string($startVal) ? substr($startVal, 0, 5) : ($startVal?->format('H:i') ?? '-');
                        $endStr = is_string($endVal) ? substr($endVal, 0, 5) : ($endVal?->format('H:i') ?? '-');

                        $pay = $booking->payment_status;
                        $payLabel = match ($pay) {
                            'fully_paid' => 'Lunas',
                            'deposit_paid' => 'DP Dibayar',
                            'pending' => 'Menunggu Pembayaran',
                            'failed' => 'Gagal',
                            'expired' => 'Kadaluarsa',
                            'refunded' => 'Dikembalikan',
                            default => $pay ?? 'Unknown',
                        };
                        $payClass = match ($pay) {
                            'fully_paid' => 'bg-green-100 text-green-800',
                            'deposit_paid' => 'bg-blue-100 text-blue-800',
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'failed' => 'bg-red-100 text-red-800',
                            'expired' => 'bg-orange-100 text-orange-800',
                            'refunded' => 'bg-purple-100 text-purple-800',
                            default => 'bg-gray-100 text-gray-800',
                        };
                    @endphp

                    <div class="rounded-xl border border-amber-200/70 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-xs text-gray-500 font-mono">{{ $booking->booking_number }}</div>
                                <div class="mt-1 truncate font-semibold text-gray-900" style="font-family: 'Poppins', sans-serif;">{{ $workshopTitle }}</div>
                                <div class="mt-1 text-sm text-gray-600" style="font-family: 'Poppins', sans-serif;">{{ $dateStr }} • {{ $startStr }}-{{ $endStr }}</div>
                                <div class="mt-1 text-sm text-gray-600" style="font-family: 'Poppins', sans-serif;">Peserta: {{ $booking->num_participants }}</div>
                            </div>
                            <span class="shrink-0 inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $payClass }}">
                                {{ $payLabel }}
                            </span>
                        </div>

                        <div class="mt-3 flex items-center justify-between">
                            <a href="{{ route('workshop.booking.detail', $booking->id) }}" wire:navigate class="text-sm font-semibold hover:underline" style="color:#8B4513; font-family: 'Poppins', sans-serif;">Detail</a>
                            @if(in_array($booking->payment_status, ['pending', 'deposit_paid']))
                                <button
                                    type="button"
                                    class="text-sm font-semibold hover:underline"
                                    style="color:#8B4513; font-family: 'Poppins', sans-serif;"
                                    data-booking-id="{{ $booking->id }}"
                                    data-total-price="{{ $booking->total_price }}"
                                    data-workshop-title="{{ e($workshopTitle) }}"
                                    data-participants="{{ $booking->num_participants }}"
                                    onclick="openWorkshopPaymentModalFromBtn(this)"
                                >
                                    Bayar
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Desktop table -->
            <div class="hidden md:block rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px] table-auto divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Booking</th>
                                <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Workshop</th>
                                <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Jadwal</th>
                                <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Peserta</th>
                                <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Pembayaran</th>
                                <th class="whitespace-nowrap px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($bookings as $booking)
                                @php
                                    $workshopTitle = $booking->workshopAvailableDate?->workshop?->title ?? $booking->workshop_name ?? 'Workshop';

                                    $dateVal = $booking->slotSchedule?->date ?? $booking->workshopAvailableDate?->date ?? $booking->workshop_date ?? null;
                                    $dateStr = is_string($dateVal) ? $dateVal : ($dateVal?->format('Y-m-d') ?? '-');

                                    $startVal = $booking->slotSchedule?->timeSlot?->start_time ?? $booking->start_time ?? null;
                                    $endVal = $booking->slotSchedule?->timeSlot?->end_time ?? $booking->end_time ?? null;

                                    $startStr = is_string($startVal) ? substr($startVal, 0, 5) : ($startVal?->format('H:i') ?? '-');
                                    $endStr = is_string($endVal) ? substr($endVal, 0, 5) : ($endVal?->format('H:i') ?? '-');

                                    $pay = $booking->payment_status;
                                    $payLabel = match ($pay) {
                                        'fully_paid' => 'Lunas',
                                        'deposit_paid' => 'DP Dibayar',
                                        'pending' => 'Menunggu Pembayaran',
                                        'failed' => 'Gagal',
                                        'expired' => 'Kadaluarsa',
                                        'refunded' => 'Dikembalikan',
                                        default => $pay ?? 'Unknown',
                                    };
                                    $payClass = match ($pay) {
                                        'fully_paid' => 'bg-green-100 text-green-800',
                                        'deposit_paid' => 'bg-blue-100 text-blue-800',
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'failed' => 'bg-red-100 text-red-800',
                                        'expired' => 'bg-orange-100 text-orange-800',
                                        'refunded' => 'bg-purple-100 text-purple-800',
                                        default => 'bg-gray-100 text-gray-800',
                                    };
                                @endphp

                                <tr class="bg-white dark:bg-gray-900">
                                    <td class="whitespace-nowrap px-4 py-4">
                                        <div class="font-mono text-sm font-semibold text-gray-900 dark:text-white">{{ $booking->booking_number }}</div>
                                        <div class="text-xs text-gray-600 dark:text-gray-400">{{ $booking->created_at?->format('Y-m-d H:i') }}</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="max-w-[260px] truncate font-semibold text-gray-900 dark:text-white md:max-w-[420px]" title="{{ $workshopTitle }}">{{ $workshopTitle }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $dateStr }}
                                        <div class="text-xs text-gray-600 dark:text-gray-400">{{ $startStr }} - {{ $endStr }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-center text-sm text-gray-700 dark:text-gray-300">{{ $booking->num_participants }}</td>
                                    <td class="whitespace-nowrap px-4 py-4">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $payClass }}">
                                            {{ $payLabel }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-right text-sm">
                                        <div class="flex flex-col items-end gap-2 sm:flex-row sm:justify-end">
                                            <a href="{{ route('workshop.booking.detail', $booking->id) }}" class="font-semibold text-blue-700 hover:underline dark:text-blue-300">Detail</a>
                                            @if(in_array($booking->payment_status, ['pending', 'deposit_paid']))
                                                <button
                                                    type="button"
                                                    class="font-semibold text-amber-700 hover:underline dark:text-amber-300"
                                                    data-booking-id="{{ $booking->id }}"
                                                    data-total-price="{{ $booking->total_price }}"
                                                    data-workshop-title="{{ e($workshopTitle) }}"
                                                    data-participants="{{ $booking->num_participants }}"
                                                    onclick="openWorkshopPaymentModalFromBtn(this)"
                                                >
                                                    Bayar
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-200 p-4 dark:border-gray-700">
                    {{ $bookings->links() }}
                </div>
            </div>
        @endif

        @include('workshop.booking.payment-popup')
    </div>
</x-layouts.app>
