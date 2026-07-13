<div class="space-y-6 sm:space-y-8">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div class="min-w-0">
            <h1 class="text-2xl sm:text-3xl font-bold" style="font-family: 'Playfair Display', serif; color: #8B4513;">
                {{ __('My Orders') }}
            </h1>
            <p class="mt-1 text-sm sm:text-base text-gray-600" style="font-family: 'Poppins', sans-serif;">
                {{ __('Track and manage all your purchases') }}
            </p>
        </div>
    </div>

    <!-- Filter Status -->
    <div class="rounded-xl border border-amber-200/70 bg-white p-4 shadow-sm sm:p-5">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-4">
            <label for="status-filter" class="text-sm font-medium text-gray-700" style="font-family: 'Poppins', sans-serif;">{{ __('Filter by Status:') }}</label>
            <select id="status-filter" wire:model.live="filterStatus" class="w-full sm:w-[260px] rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-transparent" style="font-family: 'Poppins', sans-serif;">
                <option value="all">{{ __('All Status') }}</option>
                <option value="pending">{{ __('Pending') }}</option>
                <option value="processing">{{ __('Processing') }}</option>
                <option value="shipped">{{ __('Shipped') }}</option>
                <option value="delivered">{{ __('Delivered') }}</option>
                <option value="cancelled">{{ __('Cancelled') }}</option>
            </select>
        </div>
    </div>

    <!-- Orders List -->
    @if ($orders->count() > 0)
        <div class="space-y-4">
            @foreach ($orders as $order)
                <div class="rounded-xl border border-amber-200/70 bg-white p-4 shadow-sm transition-shadow hover:shadow-md sm:p-6">
                    <!-- Order Header -->
                    <div class="mb-4 flex flex-col gap-3 border-b border-amber-100 pb-4 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0">
                            <h3 class="text-base sm:text-lg font-semibold truncate" style="font-family: 'Poppins', sans-serif; color: #8B4513;">
                                {{ __('Order') }} #{{ $order->id }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500" style="font-family: 'Poppins', sans-serif;">{{ $order->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <div class="sm:text-right">
                            <p class="text-base sm:text-lg font-bold text-gray-900" style="font-family: 'Poppins', sans-serif;">{{ $order->formatted_total }}</p>
                            <!-- Status Badge -->
                            <div class="mt-2">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                        'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                        'shipped' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                                        'delivered' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                        'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                    ];
                                    $colorClass = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
                                @endphp
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $colorClass }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Order Details -->
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-4 lg:grid-cols-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide" style="font-family: 'Poppins', sans-serif;">{{ __('Shipping Service') }}</p>
                            <p class="mt-1 text-sm font-medium text-gray-900" style="font-family: 'Poppins', sans-serif;">{{ $order->shipping_service ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide" style="font-family: 'Poppins', sans-serif;">{{ __('Payment Method') }}</p>
                            <p class="mt-1 text-sm font-medium text-gray-900" style="font-family: 'Poppins', sans-serif;">{{ $order->payment_method_name ?? $order->payment_method ?? '-' }}</p>
                            @php
                                $p = $order->payment_status;
                                $pBadge = match($p) {
                                    'unpaid', 'pending' => ['class' => 'bg-yellow-100 text-yellow-800', 'label' => 'Menunggu Pembayaran'],
                                    'paid', 'confirmed' => ['class' => 'bg-green-100 text-green-800', 'label' => 'Lunas'],
                                    'expired' => ['class' => 'bg-orange-100 text-orange-800', 'label' => 'Kadaluarsa'],
                                    'failed' => ['class' => 'bg-red-100 text-red-800', 'label' => 'Gagal'],
                                    'refunded' => ['class' => 'bg-blue-100 text-blue-800', 'label' => 'Dikembalikan'],
                                    default => ['class' => 'bg-gray-100 text-gray-800', 'label' => ucfirst($p ?? '-')],
                                };
                            @endphp
                            <div class="mt-2">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $pBadge['class'] }}" style="font-family: 'Poppins', sans-serif;">
                                    {{ __('Payment Status') }}: {{ $pBadge['label'] }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide" style="font-family: 'Poppins', sans-serif;">{{ __('Items') }}</p>
                            <div class="mt-2 flex items-center">
                                @php
                                    $items = $order->items ?? collect();
                                    $maxThumbs = 3;
                                    $thumbItems = $items->take($maxThumbs);
                                @endphp

                                <div class="flex -space-x-2">
                                    @foreach($thumbItems as $it)
                                        @php
                                            // ProductImage table uses column `photo` (bukan `image_path`)
                                            $imgPath = $it->product?->images?->first()?->photo ?? $it->product?->photo;
                                        @endphp

                                        @if($imgPath)
                                            <img src="{{ asset('storage/' . $imgPath) }}"
                                                 alt="{{ $it->product?->title ?? 'Produk' }}"
                                                 class="h-8 w-8 rounded-md border border-gray-200 bg-white object-cover dark:border-gray-700 sm:h-9 sm:w-9">
                                        @else
                                            <div class="flex h-8 w-8 items-center justify-center rounded-md border border-gray-200 bg-gray-100 dark:border-gray-700 sm:h-9 sm:w-9">
                                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                                @if($items->count() > $maxThumbs)
                                    <span class="ml-2 text-xs text-gray-500" style="font-family: 'Poppins', sans-serif;">+{{ $items->count() - $maxThumbs }}</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide" style="font-family: 'Poppins', sans-serif;">{{ __('Shipping Address') }}</p>
                            @php
                                $addr = $order->shipping_address;
                                if (is_array($addr)) {
                                    $addressStr = trim(($addr['address'] ?? '') . ', ' . 
                                                      ($addr['city'] ?? '') . ', ' . 
                                                      ($addr['province'] ?? ''));
                                } else {
                                    $addressStr = $addr ?? '-';
                                }
                            @endphp
                            <p class="mt-1 text-sm font-medium text-gray-900" style="font-family: 'Poppins', sans-serif;">{{ Str::limit($addressStr ?: '-', 30) }}</p>
                        </div>
                    </div>

                    <!-- Notes if any -->
                    @if ($order->notes)
                        <div class="mt-4 border-t border-amber-100 pt-4">
                            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-gray-500" style="font-family: 'Poppins', sans-serif;">{{ __('Notes') }}</p>
                            <p class="text-sm text-gray-700" style="font-family: 'Poppins', sans-serif;">{{ $order->notes }}</p>
                        </div>
                    @endif

                    <!-- Action Button -->
                    @php
                        $tripay = $order->tripay_response;
                        $expiresAt = null;
                        try {
                            if (is_array($tripay) && !empty($tripay['expired_time']) && is_numeric($tripay['expired_time'])) {
                                $expiresAt = \Illuminate\Support\Carbon::createFromTimestamp((int) $tripay['expired_time']);
                            } elseif (is_array($tripay) && !empty($tripay['expired_at'])) {
                                $expiresAt = \Illuminate\Support\Carbon::parse($tripay['expired_at']);
                            }
                        } catch (\Throwable $e) {
                            $expiresAt = null;
                        }

                        $isWaitingPayment = in_array($order->payment_status, ['unpaid', 'pending'], true);
                        $canContinuePayment = $isWaitingPayment && (!$expiresAt || $expiresAt->isFuture());
                    @endphp
                    <div class="mt-4 flex flex-col gap-2 border-t border-amber-100 pt-4 sm:flex-row sm:justify-end">
                        @if($canContinuePayment)
                            <button
                                type="button"
                                class="inline-flex w-full items-center justify-center rounded-lg px-4 py-2 text-sm font-semibold text-white transition-all hover:shadow sm:w-auto"
                                style="background-color:#8B4513; font-family: 'Poppins', sans-serif;"
                                data-order-id="{{ $order->id }}"
                                data-order-number="{{ $order->order_number ?? ('Order #' . $order->id) }}"
                                data-total="{{ (int) ($order->total ?? 0) }}"
                                data-payment-method="{{ $order->payment_method }}"
                                onclick="openOrderPaymentModalFromBtn(this)"
                            >
                                {{ __('Lanjutkan Pembayaran') }}
                            </button>
                        @endif
                        <a href="{{ route('orders.show', $order) }}"
                           class="inline-flex w-full items-center justify-center rounded-lg border border-amber-200 bg-white px-4 py-2 text-sm font-semibold transition-colors hover:bg-amber-50 sm:w-auto"
                           style="color:#8B4513; font-family: 'Poppins', sans-serif;">
                            {{ __('View Details') }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $orders->links(data: ['scrollTo' => false]) }}
        </div>
    @else
        <!-- Empty State -->
        <div class="rounded-xl border-2 border-dashed border-amber-200/70 bg-amber-50/40 px-6 py-10 text-center sm:py-12">
            <svg class="mx-auto h-12 w-12 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-3 text-base sm:text-lg font-semibold text-gray-900" style="font-family: 'Poppins', sans-serif;">{{ __('No Orders Yet') }}</h3>
            <p class="mt-1 text-sm text-gray-600" style="font-family: 'Poppins', sans-serif;">{{ __('You haven\'t placed any orders yet. Start shopping now!') }}</p>
            <a href="{{ route('landing.shop') }}" class="mt-5 inline-flex items-center justify-center rounded-lg px-5 py-2.5 text-sm font-semibold text-white transition-all hover:shadow" style="background-color:#8B4513; font-family: 'Poppins', sans-serif;">
                {{ __('Browse Products') }}
            </a>
        </div>
    @endif

    <!-- Order Payment Popup (My Orders) -->
    <div id="orderPaymentModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" style="display:none;">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm max-h-[85vh] overflow-hidden flex flex-col">
            <div id="orderPayHeader" class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
                <h2 id="orderPayTitle" class="text-lg font-bold" style="color:#8B4513;">💳 Pembayaran</h2>
                <button type="button" onclick="closeOrderPaymentModal()" class="p-1 transition-colors text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-4">
                <div class="mb-4 p-3 bg-orange-50 rounded-lg border border-orange-200">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-700">Total</span>
                        <span class="font-bold" style="color:#8B4513;" id="orderPayAmount">Rp 0</span>
                    </div>
                    <div class="mt-1 text-xs text-gray-600" id="orderPayOrderNo">-</div>
                </div>

                <!-- STEP: Pending Payment -->
                <div id="orderPayStepPending">
                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-xs font-semibold text-blue-900 mb-1">Ref:</p>
                        <p class="text-sm font-mono font-bold text-blue-700 break-all" id="orderPayRef">-</p>
                        <p class="text-xs text-gray-700 mt-2" id="orderPayDeadline">Batas Pembayaran: -</p>
                    </div>

                    <div class="mb-4 p-3 bg-orange-50 rounded-lg border border-orange-200">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-700">Subtotal (Tripay)</span>
                            <span id="orderPayBaseAmount">Rp 0</span>
                        </div>
                        <div id="orderPayFeeRow" class="hidden flex justify-between text-sm mb-2">
                            <span class="text-gray-700">Admin Fee Tripay</span>
                            <span class="text-orange-600" id="orderPayFee">Rp 0</span>
                        </div>
                        <div id="orderPayGrandRow" class="hidden flex justify-between font-bold pt-2 border-t border-orange-300">
                            <span style="color:#8B4513;">Total Bayar</span>
                            <span style="color:#8B4513;" id="orderPayGrand">Rp 0</span>
                        </div>
                        <div class="mt-2 text-xs text-gray-600" id="orderPayMethodName">-</div>
                    </div>

                    <div id="orderPayVaBox" class="hidden mb-4 bg-blue-50 border-2 border-blue-300 rounded-lg p-3 text-center">
                        <p class="text-xs text-gray-600 mb-1">Transfer ke nomor:</p>
                        <p class="text-xl font-mono font-bold text-blue-700 break-all" id="orderPayCode">-</p>
                        <p class="text-xs text-gray-600 mt-1" id="orderPayBank">-</p>
                    </div>

                    <div id="orderPayQrisBox" class="hidden mb-4 text-center">
                        <img id="orderPayQrImg" src="" alt="QRIS" class="w-40 h-40 mx-auto rounded-lg border border-gray-300" />
                        <p class="text-xs text-gray-600 mt-2">Scan dengan e-wallet favorit</p>
                    </div>

                    <div id="orderPayGenericBox" class="hidden mb-4 bg-gray-100 p-3 rounded-lg text-center">
                        <p class="text-xs text-gray-600 mb-1">Kode Referensi:</p>
                        <p class="font-mono text-sm font-bold text-gray-900 break-all" id="orderPayGeneric">-</p>
                    </div>

                    <div id="orderPayStatusHint" class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-xs text-yellow-900 text-center">⏳ Menunggu pembayaran... status akan otomatis berubah setelah Tripay mengirim konfirmasi.</p>
                    </div>
                </div>

                <!-- STEP: Need method (fallback) -->
                <div id="orderPayStepSelect" class="hidden">
                    <label class="block text-sm font-semibold text-gray-800 mb-2">Metode Pembayaran</label>
                    <select id="orderPayMethodSelect" class="w-full px-3 py-3 border border-gray-300 rounded-lg bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-600 focus:border-amber-600">
                        <option value="">-- Pilih Metode Pembayaran --</option>
                        <optgroup label="Transfer Bank (VA)">
                            <option value="BCAVA">BCA Virtual Account</option>
                            <option value="BRIVA">BRI Virtual Account</option>
                            <option value="BNIVA">BNI Virtual Account</option>
                            <option value="MANDIRIVA">Mandiri Virtual Account</option>
                            <option value="PERMATAVA">Permata Virtual Account</option>
                            <option value="MYBVA">Maybank Virtual Account</option>
                            <option value="CIMBVA">CIMB Virtual Account</option>
                        </optgroup>
                        <optgroup label="Convenience Store">
                            <option value="ALFAMART">Alfamart / Alfacart</option>
                            <option value="INDOMARET">Indomaret</option>
                        </optgroup>
                        <optgroup label="QRIS">
                            <option value="QRIS">QRIS (Scan QR)</option>
                        </optgroup>
                        <optgroup label="E-Wallet">
                            <option value="DANA">DANA</option>
                            <option value="OVO">OVO</option>
                        </optgroup>
                    </select>

                    <label class="mt-4 flex items-start gap-2 text-xs text-gray-700">
                        <input type="checkbox" id="orderPayAgree" class="mt-1" required>
                        <span>Saya setuju untuk melanjutkan pembayaran dan memahami pembayaran akan diverifikasi otomatis oleh sistem.</span>
                    </label>

                    <button type="button" onclick="orderPaySubmitSelectedMethod()" class="mt-4 w-full px-3 py-3 text-sm rounded-lg text-white transition-colors font-medium" style="background-color:#8B4513;" id="orderPayBtn">Bayar</button>
                </div>

                <!-- STEP: Paid -->
                <div id="orderPayStepPaid" class="hidden py-6">
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full" style="background-color:#d1fae5;">
                            <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-center mb-6">
                        <h3 class="font-bold text-green-900 mb-2">Pembayaran Berhasil!</h3>
                        <p class="text-sm text-gray-600">Status pembayaran sudah terkonfirmasi.</p>
                    </div>
                    <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-xs text-blue-900 text-center">Anda bisa menutup popup ini. Halaman akan diperbarui otomatis.</p>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 px-4 py-3 bg-gray-50">
                <button type="button" onclick="closeOrderPaymentModal()" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors" id="orderPayCloseBtn">Tutup</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    (function(){
        if (window.__orderPaymentPopupInitialized) return;
        window.__orderPaymentPopupInitialized = true;

        const rupiah = (n) => 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(n || 0));
        const el = (id) => document.getElementById(id);

        let currentOrderId = null;
        let pollTimer = null;

        function showStep(name) {
            el('orderPayStepPending')?.classList.toggle('hidden', name !== 'pending');
            el('orderPayStepSelect')?.classList.toggle('hidden', name !== 'select');
            el('orderPayStepPaid')?.classList.toggle('hidden', name !== 'paid');

            const closeBtn = el('orderPayCloseBtn');
            if (closeBtn) closeBtn.textContent = (name === 'paid') ? 'Selesai' : 'Tutup';
        }

        function setHeaderPaid(isPaid) {
            const header = el('orderPayHeader');
            const title = el('orderPayTitle');
            const closeIconBtn = header?.querySelector('button');

            if (!header || !title) return;

            if (isPaid) {
                header.style.background = 'linear-gradient(to right, #10b981, #059669)';
                title.style.color = 'white';
                title.textContent = '✅ Pembayaran Berhasil';
                if (closeIconBtn) closeIconBtn.style.color = 'white';
            } else {
                header.style.background = '';
                title.style.color = '#8B4513';
                title.textContent = '💳 Pembayaran';
                if (closeIconBtn) closeIconBtn.style.color = '';
            }
        }

        function clearPoll() {
            if (pollTimer) {
                clearInterval(pollTimer);
                pollTimer = null;
            }
        }

        function resetBoxes() {
            el('orderPayVaBox')?.classList.add('hidden');
            el('orderPayQrisBox')?.classList.add('hidden');
            el('orderPayGenericBox')?.classList.add('hidden');
            el('orderPayQrImg').src = '';
        }

        function applyPaymentDetails(data) {
            const tripay = data?.tripay || data?.payment_details || {};

            const ref = data?.reference || tripay?.reference || '-';
            el('orderPayRef').textContent = ref || '-';
            el('orderPayGeneric').textContent = ref || '-';

            const expiredAt = data?.expired_at || '-';
            el('orderPayDeadline').textContent = expiredAt ? `Batas Pembayaran: ${expiredAt}` : 'Batas Pembayaran: -';

            const amount = Number(data?.amount || tripay?.amount || 0);
            const fee = Number(data?.fee || tripay?.fee_customer || tripay?.total_fee || tripay?.fee_merchant || 0);

            el('orderPayBaseAmount').textContent = rupiah(amount);

            const feeRow = el('orderPayFeeRow');
            const grandRow = el('orderPayGrandRow');
            if (fee > 0) {
                el('orderPayFee').textContent = rupiah(fee);
                el('orderPayGrand').textContent = rupiah(amount + fee);
                feeRow?.classList.remove('hidden');
                grandRow?.classList.remove('hidden');
            } else {
                feeRow?.classList.add('hidden');
                grandRow?.classList.add('hidden');
            }

            const methodName = data?.payment_name || tripay?.payment_name || tripay?.method_name || '-';
            el('orderPayMethodName').textContent = methodName;

            resetBoxes();

            const payCode = tripay?.pay_code;
            const qrUrl = tripay?.qr_url;

            if (qrUrl) {
                el('orderPayQrImg').src = qrUrl;
                el('orderPayQrisBox')?.classList.remove('hidden');
            } else if (payCode) {
                el('orderPayCode').textContent = payCode;
                el('orderPayBank').textContent = methodName || 'Bank';
                el('orderPayVaBox')?.classList.remove('hidden');
            } else {
                el('orderPayGenericBox')?.classList.remove('hidden');
            }
        }

        async function pollStatusOnce() {
            if (!currentOrderId) return;
            try {
                const resp = await fetch(`/api/payment/${currentOrderId}/status`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin'
                });

                if (!resp.ok) return;
                const data = await resp.json();

                const st = data?.payment_status;
                if (st === 'paid' || st === 'confirmed') {
                    clearPoll();
                    setHeaderPaid(true);
                    showStep('paid');
                    setTimeout(() => window.location.reload(), 1200);
                    return;
                }

                if (st === 'failed' || st === 'expired' || st === 'refunded') {
                    clearPoll();
                    el('orderPayStatusHint').innerHTML = `<p class="text-xs text-yellow-900 text-center">⚠️ Pembayaran ${String(st).toUpperCase()}.</p>`;
                    return;
                }

                // keep refreshing details & deadline
                applyPaymentDetails(data);

            } catch (_) {
                // silent
            }
        }

        async function ensurePaymentExists(methodFromOrder) {
            if (!currentOrderId) return;

            // Try to use existing transaction first
            const stResp = await fetch(`/api/payment/${currentOrderId}/status`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin'
            });
            const stData = await stResp.json().catch(() => null);

            if (stData?.tripay?.reference || stData?.tripay?.checkout_url || stData?.tripay?.pay_code || stData?.tripay?.qr_url) {
                applyPaymentDetails(stData);
                showStep('pending');
                return;
            }

            // No stored tripay data: auto-create using order's payment_method when available
            if (methodFromOrder) {
                await processPayment(methodFromOrder);
                return;
            }

            // Fallback: show select
            showStep('select');
        }

        async function processPayment(method) {
            const token = document.querySelector('meta[name="csrf-token"]')?.content;

            const btn = el('orderPayBtn');
            const closeBtn = el('orderPayCloseBtn');
            if (btn) { btn.disabled = true; btn.textContent = 'Memproses...'; }
            if (closeBtn) closeBtn.disabled = true;

            try {
                const resp = await fetch(`/api/payment/${currentOrderId}/process`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        ...(token ? { 'X-CSRF-TOKEN': token } : {}),
                        'Content-Type': 'application/json',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ payment_method: method }),
                });

                const data = await resp.json().catch(() => null);
                if (!resp.ok) throw new Error(data?.error || data?.message || 'Gagal memproses pembayaran');

                applyPaymentDetails(data);
                showStep('pending');

            } catch (e) {
                Swal.fire({
                    title: 'Pembayaran Gagal',
                    text: e?.message || 'Terjadi kesalahan saat memproses pembayaran',
                    icon: 'error', iconColor: '#ef4444',
                    confirmButtonText: 'OK', confirmButtonColor: '#b45309',
                    customClass: { popup: 'rounded-2xl shadow-2xl' },
                });
                showStep('select');
            } finally {
                if (btn) { btn.disabled = false; btn.textContent = 'Bayar'; }
                if (closeBtn) closeBtn.disabled = false;
            }
        }

        window.openOrderPaymentModalFromBtn = function(btn){
            const d = btn?.dataset || {};
            currentOrderId = d.orderId || null;

            el('orderPayAmount').textContent = rupiah(d.total);
            el('orderPayOrderNo').textContent = d.orderNumber || '-';

            // reset UI
            setHeaderPaid(false);
            resetBoxes();
            el('orderPayRef').textContent = '-';
            el('orderPayDeadline').textContent = 'Batas Pembayaran: -';
            el('orderPayMethodName').textContent = '-';
            el('orderPayBaseAmount').textContent = rupiah(0);
            el('orderPayFeeRow')?.classList.add('hidden');
            el('orderPayGrandRow')?.classList.add('hidden');
            el('orderPayFee').textContent = rupiah(0);
            el('orderPayGrand').textContent = rupiah(0);
            el('orderPayStatusHint').innerHTML = '<p class="text-xs text-yellow-900 text-center">⏳ Menunggu pembayaran... status akan otomatis berubah setelah Tripay mengirim konfirmasi.</p>';

            const modal = el('orderPaymentModal');
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';

            clearPoll();
            showStep('pending');

            ensurePaymentExists(d.paymentMethod);

            pollStatusOnce();
            pollTimer = setInterval(pollStatusOnce, 3000);
        };

        window.orderPaySubmitSelectedMethod = function(){
            if (!currentOrderId) return;
            const method = el('orderPayMethodSelect')?.value;
            const agree = el('orderPayAgree')?.checked;
            if (!method) {
                Swal.fire({ title: 'Pilih Metode', text: 'Pilih metode pembayaran terlebih dahulu.', icon: 'warning', iconColor: '#f59e0b', confirmButtonText: 'OK', confirmButtonColor: '#b45309', customClass: { popup: 'rounded-2xl shadow-2xl' } });
                return;
            }
            if (!agree) {
                Swal.fire({ title: 'Persetujuan Diperlukan', text: 'Centang persetujuan untuk melanjutkan.', icon: 'info', confirmButtonText: 'OK', confirmButtonColor: '#b45309', customClass: { popup: 'rounded-2xl shadow-2xl' } });
                return;
            }
            processPayment(method);
        };

        window.closeOrderPaymentModal = function(){
            const modal = el('orderPaymentModal');
            if (!modal) return;
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            clearPoll();
        };

        document.addEventListener('DOMContentLoaded', function(){
            const modal = el('orderPaymentModal');
            if (modal) {
                modal.addEventListener('click', function(e){
                    if (e.target === modal) closeOrderPaymentModal();
                });
            }

            document.addEventListener('keydown', function(e){
                if (e.key === 'Escape') {
                    const m = el('orderPaymentModal');
                    if (m && m.style.display === 'flex') closeOrderPaymentModal();
                }
            });
        });
    })();
    </script>
    @endpush
</div>
