{{-- Workshop Payment Popup (match checkout payment modal) --}}

<div id="wsPaymentModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" style="display: none;">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm max-h-[85vh] overflow-hidden flex flex-col">
        <!-- Header -->
        <div id="wsModalHeader" class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
            <h2 id="wsModalTitle" class="text-lg font-bold" style="color: #8B4513;">💳 Pembayaran</h2>
            <button type="button" onclick="closeWorkshopPaymentModal()" class="p-1 transition-colors text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-4">
            <!-- STEP: Select Method -->
            <div id="wsStepSelect">
                <div class="mb-4 p-3 bg-orange-50 rounded-lg border border-orange-200">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-700">Total</span>
                        <span class="font-bold" style="color: #8B4513;" id="wsAmountLabel">Rp 0</span>
                    </div>
                    <div class="mt-1 text-xs text-gray-600" id="wsWorkshopTitleLabel">-</div>
                </div>

                <form id="wsModalPaymentForm" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="full">

                    <label class="block text-sm font-semibold text-gray-800 mb-2">Metode Pembayaran</label>
                    <select name="payment_method" id="wsTripayMethods" class="w-full px-3 py-3 border border-gray-300 rounded-lg bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-600 focus:border-amber-600">
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
                    </select>

                    <label class="mt-4 flex items-start gap-2 text-xs text-gray-700">
                        <input type="checkbox" id="wsAgree" class="mt-1" required>
                        <span>Saya setuju untuk melanjutkan pembayaran dan memahami pembayaran akan diverifikasi otomatis oleh sistem.</span>
                    </label>

                    <button type="button" onclick="wsSubmitPayment()" class="mt-4 w-full px-3 py-3 text-sm rounded-lg text-white transition-colors font-medium" style="background-color: #8B4513;" id="wsPayBtn">
                        Bayar
                    </button>
                </form>

                <div class="mt-4 text-xs text-gray-600">
                    Setelah pembayaran dibuat, kode VA / QRIS akan tampil di popup ini (seperti checkout) dan status akan ter-update otomatis.
                </div>
            </div>

            <!-- STEP: Pending Payment -->
            <div id="wsStepPending" class="hidden">
                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-xs font-semibold text-blue-900 mb-1">Ref:</p>
                    <p class="text-sm font-mono font-bold text-blue-700 break-all" id="wsRefLabel">-</p>
                </div>

                <div class="mb-4 p-3 bg-orange-50 rounded-lg border border-orange-200">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-700">Total</span>
                        <span class="font-bold" style="color: #8B4513;" id="wsPendingAmountLabel">Rp 0</span>
                    </div>

                    <div id="wsFeeRow" class="hidden flex justify-between text-sm mb-2">
                        <span class="text-gray-700">Admin Fee Tripay</span>
                        <span class="text-orange-600" id="wsAdminFeeLabel">Rp 0</span>
                    </div>

                    <div id="wsGrandTotalRow" class="hidden flex justify-between font-bold pt-2 border-t border-orange-300">
                        <span style="color: #8B4513;">Total Bayar</span>
                        <span style="color: #8B4513;" id="wsGrandTotalLabel">Rp 0</span>
                    </div>

                    <div class="mt-2 text-xs text-gray-600" id="wsPendingMethodName">-</div>
                    <div class="mt-1 text-xs text-gray-700">
                        <span class="font-semibold">Batas Pembayaran:</span>
                        <span id="wsExpiredAtLabel">-</span>
                    </div>
                </div>

                <!-- VA -->
                <div id="wsVaBox" class="hidden mb-4 bg-blue-50 border-2 border-blue-300 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-600 mb-1">Transfer ke nomor:</p>
                    <p class="text-xl font-mono font-bold text-blue-700 break-all" id="wsPayCode">-</p>
                    <p class="text-xs text-gray-600 mt-1" id="wsPayBankName">-</p>
                </div>

                <!-- QRIS -->
                <div id="wsQrisBox" class="hidden mb-4 text-center">
                    <img id="wsQrImg" src="" alt="QRIS" class="w-40 h-40 mx-auto rounded-lg border border-gray-300" />
                    <p class="text-xs text-gray-600 mt-2">Scan dengan e-wallet favorit</p>
                </div>

                <!-- Convenience Store / Generic -->
                <div id="wsGenericBox" class="hidden mb-4 bg-gray-100 p-3 rounded-lg text-center">
                    <p class="text-xs text-gray-600 mb-1">Kode Pembayaran:</p>
                    <p class="font-mono text-sm font-bold text-gray-900 break-all" id="wsGenericCode">-</p>
                </div>

                <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-xs text-yellow-900 text-center" id="wsStatusHint">⏳ Menunggu pembayaran... status akan otomatis berubah setelah Tripay mengirim konfirmasi.</p>
                </div>

            </div>

            <!-- STEP: Paid -->
            <div id="wsStepPaid" class="hidden py-6">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full" style="background-color: #d1fae5;">
                        <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>

                <div class="text-center mb-6">
                    <h3 class="font-bold text-green-900 mb-2">Pembayaran Berhasil!</h3>
                    <p class="text-sm text-gray-600">Status pembayaran workshop sudah terkonfirmasi.</p>
                </div>

                <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-xs text-blue-900 text-center">Anda bisa menutup popup ini. Halaman akan diperbarui otomatis.</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t border-gray-200 px-4 py-3 bg-gray-50" id="wsModalFooter">
            <button type="button" onclick="closeWorkshopPaymentModal()" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors" id="wsFooterCloseBtn">
                Tutup
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    if (window.__workshopPaymentPopupInitialized) return;
    window.__workshopPaymentPopupInitialized = true;

    const rupiah = (n) => 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(n || 0));

    let wsBookingId = null;
    let wsPollTimer = null;
    let wsLastRef = null;

    function el(id) { return document.getElementById(id); }

    function showStep(name) {
        el('wsStepSelect')?.classList.toggle('hidden', name !== 'select');
        el('wsStepPending')?.classList.toggle('hidden', name !== 'pending');
        el('wsStepPaid')?.classList.toggle('hidden', name !== 'paid');

        const closeBtn = el('wsFooterCloseBtn');
        if (closeBtn) closeBtn.textContent = (name === 'paid') ? 'Selesai' : 'Tutup';
    }

    function setHeaderPaid(isPaid) {
        const header = el('wsModalHeader');
        const title = el('wsModalTitle');
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
        if (wsPollTimer) {
            clearInterval(wsPollTimer);
            wsPollTimer = null;
        }
    }

    async function pollStatus() {
        if (!wsBookingId) return;
        try {
            const resp = await fetch(`/workshop/booking/${wsBookingId}/payment-status`, {
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

            // Webhook mapping: PAID -> 'confirmed'
            if (st === 'confirmed') {
                clearPoll();
                setHeaderPaid(true);
                showStep('paid');
                setTimeout(() => window.location.reload(), 1200);
            } else if (st === 'failed' || st === 'expired' || st === 'refunded') {
                clearPoll();
                el('wsStatusHint').textContent = (st === 'expired')
                    ? '⚠️ Pembayaran kedaluwarsa. Silakan buat pembayaran baru.'
                    : (st === 'refunded')
                        ? '↩️ Pembayaran dikembalikan (refund).' 
                        : '⚠️ Pembayaran gagal. Silakan coba metode lain.';
            }
        } catch (_) {
            // silent
        }
    }

    window.openWorkshopPaymentModal = function(opts) {
        const modal = el('wsPaymentModal');
        if (!modal) return;

        wsBookingId = opts?.bookingId ?? null;
        wsLastRef = null;

        el('wsAmountLabel').textContent = rupiah(opts?.totalPrice);
        el('wsWorkshopTitleLabel').textContent = opts?.workshopTitle ?? '-';

        el('wsPendingAmountLabel').textContent = rupiah(opts?.totalPrice);
        el('wsPendingMethodName').textContent = '-';
        el('wsExpiredAtLabel').textContent = '-';
        el('wsFeeRow')?.classList.add('hidden');
        el('wsGrandTotalRow')?.classList.add('hidden');
        el('wsAdminFeeLabel').textContent = 'Rp 0';
        el('wsGrandTotalLabel').textContent = 'Rp 0';

        el('wsTripayMethods').value = '';
        el('wsAgree').checked = false;

        // reset pending UI
        el('wsRefLabel').textContent = '-';
        el('wsPayCode').textContent = '-';
        el('wsPayBankName').textContent = '-';
        el('wsGenericCode').textContent = '-';
        el('wsQrImg').src = '';
        el('wsStatusHint').textContent = '⏳ Menunggu pembayaran... status akan otomatis berubah setelah Tripay mengirim konfirmasi.';

        el('wsVaBox').classList.add('hidden');
        el('wsQrisBox').classList.add('hidden');
        el('wsGenericBox').classList.add('hidden');


        clearPoll();
        setHeaderPaid(false);
        showStep('select');

        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    };

    window.openWorkshopPaymentModalFromBtn = function(btn) {
        const d = btn?.dataset || {};
        window.openWorkshopPaymentModal({
            bookingId: d.bookingId,
            totalPrice: d.totalPrice,
            workshopTitle: d.workshopTitle,
            participants: d.participants,
        });
    };

    window.closeWorkshopPaymentModal = function() {
        const modal = el('wsPaymentModal');
        if (!modal) return;
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        clearPoll();
    };

    window.wsSubmitPayment = async function() {
        if (!wsBookingId) return;

        const method = el('wsTripayMethods')?.value;
        const agree = el('wsAgree')?.checked;

        if (!method) {
            Swal.fire({ title: 'Metode Pembayaran', text: 'Pilih metode pembayaran terlebih dahulu', icon: 'warning', confirmButtonText: 'OK', confirmButtonColor: '#b45309', customClass: { popup: 'rounded-2xl shadow-2xl' } });
            return;
        }
        if (!agree) {
            Swal.fire({ title: 'Persetujuan Diperlukan', text: 'Centang persetujuan untuk melanjutkan', icon: 'warning', confirmButtonText: 'OK', confirmButtonColor: '#b45309', customClass: { popup: 'rounded-2xl shadow-2xl' } });
            return;
        }

        const payBtn = el('wsPayBtn');
        const closeBtn = el('wsFooterCloseBtn');
        if (payBtn) {
            payBtn.disabled = true;
            payBtn.textContent = 'Memproses...';
        }
        if (closeBtn) closeBtn.disabled = true;

        let token = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!token) token = document.querySelector('input[name="_token"]')?.value;

        const formData = new FormData();
        if (token) formData.append('_token', token);
        formData.append('type', 'full');
        formData.append('payment_method', method);

        try {
            const resp = await fetch(`/workshop/booking/${wsBookingId}/process-payment`, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    ...(token ? { 'X-CSRF-TOKEN': token } : {})
                },
                credentials: 'same-origin'
            });

            const data = await resp.json().catch(() => null);
            if (!resp.ok) throw new Error(data?.message || 'Gagal memproses pembayaran');

            wsLastRef = data?.reference || null;
            el('wsRefLabel').textContent = wsLastRef || '-';

            const baseAmount = Number(data?.amount || 0);
            el('wsPendingAmountLabel').textContent = rupiah(baseAmount);

            const details = data?.payment_details || {};
            const methodName = details?.method_name || data?.method || method;
            el('wsPendingMethodName').textContent = methodName;

            // Batas pembayaran dari Tripay
            const expTs = Number(data?.expired_time || 0);
            const expStr = data?.expired_at || '';
            let expLabel = '-';
            if (Number.isFinite(expTs) && expTs > 0) {
                const dt = new Date(expTs * 1000);
                const pad = (n) => String(n).padStart(2, '0');
                expLabel = `${pad(dt.getDate())}-${pad(dt.getMonth() + 1)}-${dt.getFullYear()} ${pad(dt.getHours())}:${pad(dt.getMinutes())}:${pad(dt.getSeconds())}`;
            } else if (expStr) {
                expLabel = expStr;
            }
            el('wsExpiredAtLabel').textContent = expLabel;

            const fee = Number(data?.fee || details?.fee || 0);
            const feeRow = el('wsFeeRow');
            const grandRow = el('wsGrandTotalRow');

            if (fee > 0) {
                el('wsAdminFeeLabel').textContent = rupiah(fee);
                el('wsGrandTotalLabel').textContent = rupiah(baseAmount + fee);
                feeRow?.classList.remove('hidden');
                grandRow?.classList.remove('hidden');
            } else {
                feeRow?.classList.add('hidden');
                grandRow?.classList.add('hidden');
            }

            // Reset sections
            el('wsVaBox').classList.add('hidden');
            el('wsQrisBox').classList.add('hidden');
            el('wsGenericBox').classList.add('hidden');

            const isVa = String(method).includes('VA') || ['BCAVA','BRIVA','BNIVA','MANDIRIVA','PERMATAVA','MYBVA','CIMBVA'].includes(method);
            if (isVa) {
                const payCode = details?.pay_code || details?.va_code || details?.va_number || details?.account_number || data?.pay_code || data?.pay_code;
                el('wsPayCode').textContent = payCode || '-';
                el('wsPayBankName').textContent = details?.bank_name || methodName || 'Bank';
                el('wsVaBox').classList.remove('hidden');
            } else if (method === 'QRIS') {
                const qrUrl = details?.qr_url || details?.qr_image_url || data?.qr_url;
                if (qrUrl) {
                    el('wsQrImg').src = qrUrl;
                }
                el('wsQrisBox').classList.remove('hidden');
            } else {
                const code = details?.reference || details?.reference_code || data?.reference || wsLastRef;
                el('wsGenericCode').textContent = code || '-';
                el('wsGenericBox').classList.remove('hidden');
            }

            showStep('pending');

            // Start polling status (match checkout behavior)
            clearPoll();
            await pollStatus();
            wsPollTimer = setInterval(pollStatus, 3000);

        } catch (e) {
            Swal.fire({ title: 'Pembayaran Gagal', text: e?.message || 'Terjadi kesalahan saat memproses pembayaran', icon: 'error', iconColor: '#ef4444', confirmButtonText: 'OK', confirmButtonColor: '#b45309', customClass: { popup: 'rounded-2xl shadow-2xl' } });
        } finally {
            if (payBtn) {
                payBtn.disabled = false;
                payBtn.textContent = 'Bayar';
            }
            if (closeBtn) closeBtn.disabled = false;
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        const modal = el('wsPaymentModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) closeWorkshopPaymentModal();
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const m = el('wsPaymentModal');
                if (m && m.style.display === 'flex') closeWorkshopPaymentModal();
            }
        });
    });
})();
</script>
@endpush
