<x-layouts.app>
    @php
        $workshopTitle = $booking->workshopAvailableDate?->workshop?->title ?? $booking->workshop_name ?? 'Workshop';

        $dateVal = $booking->slotSchedule?->date ?? $booking->workshopAvailableDate?->date ?? $booking->workshop_date ?? null;
        $dateStr = is_string($dateVal) ? $dateVal : ($dateVal?->format('Y-m-d') ?? '-');

        $startVal = $booking->slotSchedule?->timeSlot?->start_time ?? $booking->start_time ?? null;
        $endVal = $booking->slotSchedule?->timeSlot?->end_time ?? $booking->end_time ?? null;

        $startStr = is_string($startVal) ? substr($startVal, 0, 5) : ($startVal?->format('H:i') ?? '-');
        $endStr = is_string($endVal) ? substr($endVal, 0, 5) : ($endVal?->format('H:i') ?? '-');
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Detail Booking Workshop</h1>
                <p class="mt-1 text-gray-600 dark:text-gray-400">No. Booking: <span class="font-mono font-semibold">{{ $booking->booking_number }}</span></p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('workshop.my-bookings') }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                    Kembali
                </a>
                @if(in_array($booking->payment_status, ['pending', 'deposit_paid']))
                    <button
                        type="button"
                        class="inline-flex items-center rounded-lg bg-amber-700 px-4 py-2 font-semibold text-white hover:bg-amber-800"
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

        @php
            $latestPayment = ($booking->payments ?? collect())->sortByDesc('created_at')->first();
            $paymentMethodCode = $latestPayment?->payment_method;
            $paymentMethodName = $paymentMethodCode ? (config("payment.methods.{$paymentMethodCode}.name") ?? $paymentMethodCode) : '-';
        @endphp

        <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Ringkasan</h2>
            <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-sm text-gray-600 dark:text-gray-400">Tanggal</dt>
                    <dd class="font-semibold text-gray-900 dark:text-white">{{ $dateStr }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-600 dark:text-gray-400">Workshop</dt>
                    <dd class="font-semibold text-gray-900 dark:text-white">{{ $workshopTitle }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-600 dark:text-gray-400">Jadwal</dt>
                    <dd class="font-semibold text-gray-900 dark:text-white">{{ $startStr }} - {{ $endStr }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-600 dark:text-gray-400">Jumlah Peserta</dt>
                    <dd class="font-semibold text-gray-900 dark:text-white">{{ $booking->num_participants }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-600 dark:text-gray-400">Harga Total</dt>
                    <dd class="font-semibold text-gray-900 dark:text-white">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-600 dark:text-gray-400">Status Booking</dt>
                    <dd class="font-semibold text-gray-900 dark:text-white">{{ $booking->status }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-600 dark:text-gray-400">Status Pembayaran</dt>
                    <dd class="font-semibold text-gray-900 dark:text-white">{{ $booking->payment_status }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-600 dark:text-gray-400">Metode Pembayaran</dt>
                    <dd class="font-semibold text-gray-900 dark:text-white">{{ $paymentMethodName }}</dd>
                </div>
            </dl>
        </div>

        @include('workshop.booking.payment-popup')
    </div>
</x-layouts.app>
