<x-layouts.landing title="Pesan Workshop Batik">
    <div class="min-h-screen bg-gradient-to-b from-white to-amber-50 py-6 sm:py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6">
                <a href="{{ route('landing.workshop') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-700 hover:text-gray-900" style="font-family: 'Poppins', sans-serif;">
                    <span aria-hidden="true">←</span>
                    <span>Kembali ke halaman workshop</span>
                </a>

                <h1 class="mt-3 text-2xl sm:text-3xl font-bold leading-tight" style="font-family: 'Playfair Display', serif; color: #8B4513;">
                    Booking Workshop
                </h1>
                <p class="mt-1.5 text-sm text-gray-600" style="font-family: 'Poppins', sans-serif;">
                    Pilih jadwal, jumlah peserta, lalu lanjutkan pembayaran.
                </p>
            </div>

            <!-- Workshop Card -->
            <div class="bg-white border border-amber-200/70 rounded-2xl shadow-sm p-3 sm:p-4 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-gray-900" style="font-family: 'Poppins', sans-serif;">{{ $workshop->title }}</h2>
                        <p class="mt-1.5 text-gray-600 text-sm" style="font-family: 'Poppins', sans-serif;">{{ $workshop->description }}</p>
                    </div>
                    <div class="shrink-0 text-left sm:text-right">
                        <p class="text-xs text-gray-500" style="font-family: 'Poppins', sans-serif;">Harga</p>
                        <p class="text-xl sm:text-2xl font-bold text-[#8B4513]" style="font-family: 'Poppins', sans-serif;">Rp{{ number_format($workshop->amount, 0, ',', '.') }}<span class="text-sm font-semibold text-gray-600">/orang</span></p>
                    </div>
                </div>
            </div>

            <!-- Pilih Jadwal Section -->
            <div class="bg-white border border-gray-200/70 rounded-2xl shadow-sm p-3 sm:p-4 mb-6">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900" style="font-family: 'Poppins', sans-serif;">Pilih Jadwal Workshop</h3>
                        <p class="mt-1 text-gray-600 text-sm" style="font-family: 'Poppins', sans-serif;">Pilih salah satu jadwal yang tersedia.</p>
                    </div>
                    <div class="text-xs text-gray-500" style="font-family: 'Poppins', sans-serif;">Langkah 1 dari 2</div>
                </div>

                <div id="schedulesList" class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3">
                    <div class="col-span-full text-center py-4 sm:py-6 text-gray-500 text-sm">Memuat jadwal...</div>
                </div>
            </div>

            <!-- Payment Section (Hidden until schedule selected) -->
            <div id="paymentSection" style="display: none;" class="bg-white border border-amber-200/70 rounded-2xl shadow-lg p-3 sm:p-6">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900" style="font-family: 'Poppins', sans-serif;">Data & Pembayaran</h3>
                        <p class="mt-1 text-gray-600 text-sm" style="font-family: 'Poppins', sans-serif;">Lengkapi jumlah peserta, lalu pilih metode pembayaran.</p>
                    </div>
                    <div class="text-xs text-gray-500" style="font-family: 'Poppins', sans-serif;">Langkah 2 dari 2</div>
                </div>
                <form id="bookingForm" method="POST" action="{{ route('workshop.booking.store') }}" onsubmit="handleBookingSubmit(event)">
                    @csrf
                    <input type="hidden" id="workshopId" name="workshop_id" value="{{ $workshop->id }}">
                    <input type="hidden" name="workshop_name" value="{{ $workshop->title }}">
                    <input type="hidden" id="scheduleId" name="workshop_slot_schedule_id">
                    <input type="hidden" id="numParticipants" name="number_of_participants" value="1">
                    <input type="hidden" id="packagePrice" name="package_price" value="{{ $workshop->amount }}">
                    <input type="hidden" name="full_name" value="{{ auth()->user()->name ?? '' }}">
                    <input type="hidden" name="email" value="{{ auth()->user()->email ?? '' }}">
                    <input type="hidden" name="phone" value="{{ auth()->check() ? (auth()->user()->customerProfile?->phone_number ?? '') : '' }}">

                    <!-- Jumlah Peserta -->
                    <div class="bg-white rounded-lg p-3 sm:p-4 mb-4">
                        <label class="block text-sm font-bold text-gray-900 mb-2" style="font-family: 'Poppins', sans-serif;">Jumlah Peserta</label>
                        <div class="flex items-center gap-3">
                            <button type="button" onclick="decrementParticipants()" class="w-9 h-9 sm:w-10 sm:h-10 bg-gray-200 text-gray-900 font-bold text-base sm:text-lg rounded hover:bg-gray-300">−</button>
                            <input
                                type="number"
                                id="displayParticipants"
                                value="1"
                                min="1"
                                class="w-16 text-center text-lg sm:text-xl font-bold border-2 border-gray-300 rounded py-1.5"
                                onchange="updateTotalPrice()"
                            >
                            <button type="button" onclick="incrementParticipants()" class="w-9 h-9 sm:w-10 sm:h-10 bg-[#8B4513] text-white font-bold text-base sm:text-lg rounded hover:bg-[#6B3410]">+</button>
                        </div>
                    </div>

                    <!-- Data Peserta -->
                    <div class="bg-white rounded-lg p-3 sm:p-4 mb-4">
                        <p class="text-sm font-bold text-gray-900 mb-3" style="font-family: 'Poppins', sans-serif;">Data Peserta</p>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm" style="font-family: 'Poppins', sans-serif;">
                            <div class="rounded-lg border border-gray-200 p-3">
                                <p class="text-[11px] text-gray-500 mb-1">Nama</p>
                                <p class="font-semibold text-gray-900">{{ auth()->user()->name ?? '-' }}</p>
                            </div>
                            <div class="rounded-lg border border-gray-200 p-3">
                                <p class="text-[11px] text-gray-500 mb-1">Email</p>
                                <p class="font-semibold text-gray-900">{{ auth()->user()->email ?? '-' }}</p>
                            </div>
                            <div class="rounded-lg border border-gray-200 p-3">
                                <p class="text-[11px] text-gray-500 mb-1">WhatsApp</p>
                                <p class="font-semibold text-gray-900">
                                    @if(auth()->check() && auth()->user()->customerProfile?->phone_number)
                                        {{ auth()->user()->customerProfile->phone_number }}
                                    @else
                                        <span class="text-gray-500">08xxxxxxxxxx</span>
                                    @endif
                                </p>
                                <p class="mt-1 text-[11px] text-gray-500">Lengkapi di profil jika belum ada.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <!-- Total Harga Summary -->
                        <div class="bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 rounded-2xl p-4 sm:p-5 shadow-sm">
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-1" style="font-family: 'Poppins', sans-serif;">Total harga</p>
                                <p class="text-3xl font-bold text-[#8B4513]" style="font-family: 'Poppins', sans-serif;">Rp <span id="totalPrice">{{ number_format($workshop->amount, 0, ',', '.') }}</span></p>
                            </div>

                            <div class="space-y-2 border-t border-amber-200 pt-4 text-sm text-gray-700" style="font-family: 'Poppins', sans-serif;">
                                <div class="flex justify-between">
                                    <span>Jumlah peserta</span>
                                    <span class="font-bold"><span id="summaryCount">1</span> orang</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Harga per peserta</span>
                                    <span class="font-bold">Rp{{ number_format($workshop->amount, 0, ',', '.') }}</span>
                                </div>
                                <p class="text-[11px] text-gray-500 mt-2">Total = harga per peserta × jumlah peserta.</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
                        <!-- Metode Pembayaran -->
                        <div class="bg-white rounded-lg p-4">
                            <label class="block text-sm font-bold text-gray-900 mb-2" style="font-family: 'Poppins', sans-serif;">Metode Pembayaran</label>
                            <select
                                id="paymentMethodSelect"
                                class="w-full px-3 py-2 text-sm border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:outline-none text-gray-900 font-semibold"
                            >
                                <option value="">-- Pilih Metode Pembayaran --</option>
                                <optgroup label="TRANSFER BANK">
                                    <option value="BCAVA">🏦 BCA Transfer</option>
                                    <option value="BRIVA">🏦 BRI Transfer</option>
                                    <option value="MANDIRIVA">🏦 Mandiri Transfer</option>
                                    <option value="CIMBVA">🏦 CIMB Transfer</option>
                                </optgroup>
                                <optgroup label="E-WALLET & QRIS">
                                    <option value="OVO">💳 OVO</option>
                                    <option value="DANA">💳 DANA</option>
                                    <option value="QRIS">📱 QRIS</option>
                                </optgroup>
                                <optgroup label="TOKO">
                                    <option value="ALFAMART">🏪 Alfamart</option>
                                    <option value="INDOMARET">🏪 Indomaret</option>
                                </optgroup>
                            </select>
                        </div>

                        <!-- Syarat & Ketentuan -->
                        <div class="bg-white rounded-lg p-4">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="agree_terms" id="agreeTerms" class="w-4 h-4 mt-1">
                                <div style="font-family: 'Poppins', sans-serif;">
                                    <span class="text-sm text-gray-900">Saya setuju dengan <a href="#" class="text-[#8B4513] hover:underline">Syarat & Ketentuan</a> dan <a href="#" class="text-[#8B4513] hover:underline">Kebijakan Privasi</a></span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                        <button type="button" onclick="backToSchedules()" class="flex-1 px-4 py-2.5 bg-gray-200 text-gray-900 font-bold rounded-lg hover:bg-gray-300 text-sm sm:text-base">
                            ← Kembali
                        </button>
                        <button type="button" id="payNowBtn" class="flex-1 px-4 py-2.5 bg-[#8B4513] text-white font-bold rounded-lg hover:bg-[#6B3410] text-sm sm:text-base" onclick="handleFinalPayment()">
                            Bayar Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ===== TRIPAY PAYMENT MODAL (SAME STYLE AS CHECKOUT) ===== -->
    <div id="tripayModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" style="display: none;">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm max-h-[85vh] overflow-hidden flex flex-col">
            <!-- Modal Header -->
            <div id="tripayModalHeader" class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
                <h2 id="tripayModalTitle" class="text-lg font-bold" style="color: #8B4513;">💳 Pembayaran</h2>
                <button type="button" onclick="closeTripayModal()" class="p-1 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="flex-1 overflow-y-auto p-4">
                <div id="tripayModalBody"></div>
            </div>

            <!-- Modal Footer -->
            <div class="border-t border-gray-200 px-4 py-3 bg-gray-50">
                <button id="tripayModalCloseBtn" type="button" onclick="closeTripayModal()" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadSchedules();

        });

        function loadSchedules() {
            fetch('/workshop/{{ $workshop->id }}/available-schedules')
                .then(r => r.json())
                .then(schedules => {
                    const list = document.getElementById('schedulesList');
                    list.innerHTML = '';

                    if (!schedules.length) {
                        list.innerHTML = '<p class="text-center text-gray-500 py-6 col-span-full text-sm">Tidak ada jadwal tersedia</p>';
                        return;
                    }

                    schedules.forEach(schedule => {
                        const card = document.createElement('div');
                        card.className = 'p-3 sm:p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-amber-700 hover:bg-amber-50 transition-all shadow-sm hover:shadow schedule-card text-center';
                        card.onclick = () => selectSchedule(schedule, card);
                        
                        card.innerHTML = `
                            <p class="text-[11px] sm:text-xs text-gray-600 mb-1.5 font-semibold">${schedule.date}</p>
                            <p class="text-sm sm:text-base font-bold text-gray-900 leading-tight">${schedule.slot_name}</p>
                            <p class="text-[11px] sm:text-xs text-gray-600 mt-1">${schedule.start_time}-${schedule.end_time}</p>
                        `;
                        
                        list.appendChild(card);
                    });
                })
                .catch(err => {
                    document.getElementById('schedulesList').innerHTML = '<p class="text-center text-red-600 col-span-full">Gagal memuat jadwal</p>';
                });
        }

        function selectSchedule(schedule, cardElement) {
            document.getElementById('scheduleId').value = schedule.id;

            // Visual feedback
            document.querySelectorAll('.schedule-card').forEach(el => el.classList.remove('border-amber-700', 'bg-amber-50', 'ring-2', 'ring-amber-600/20'));
            cardElement.classList.add('border-amber-700', 'bg-amber-50', 'ring-2', 'ring-amber-600/20');

            // Show payment section
            document.getElementById('paymentSection').style.display = 'block';
            
            // Scroll to payment section
            window.scrollTo({top: document.getElementById('paymentSection').offsetTop - 100, behavior: 'smooth'});
        }

        function incrementParticipants() {
            const input = document.getElementById('displayParticipants');
            input.value = parseInt(input.value) + 1;
            updateTotalPrice();
        }

        function decrementParticipants() {
            const input = document.getElementById('displayParticipants');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
            }
            updateTotalPrice();
        }

        function updateTotalPrice() {
            const count = parseInt(document.getElementById('displayParticipants').value);
            const unitPrice = {{ $workshop->amount }};
            const total = unitPrice * count;

            document.getElementById('totalPrice').textContent = total.toLocaleString('id-ID');
            document.getElementById('summaryCount').textContent = count;
            document.getElementById('numParticipants').value = count;
        }

        function backToSchedules() {
            document.getElementById('paymentSection').style.display = 'none';
            document.querySelectorAll('.schedule-card').forEach(el => el.classList.remove('border-amber-700', 'bg-amber-50', 'ring-2', 'ring-amber-600/20'));
            window.scrollTo({top: 0, behavior: 'smooth'});
        }

        function validateForm() {
            console.log('🔍 Validating form...');
            
            // Check schedule selected
            const scheduleId = document.getElementById('scheduleId').value;
            if (!scheduleId) {
                alert('⚠️ Pilih jadwal terlebih dahulu!');
                return false;
            }
            
            // Check T&C
            const agreeTerms = document.getElementById('agreeTerms').checked;
            if (!agreeTerms) {
                alert('⚠️ Anda harus menyetujui Syarat & Ketentuan!');
                return false;
            }
            
            console.log('✅ Form validation passed');
            console.log('Schedule ID:', scheduleId);
            console.log('Participants:', document.getElementById('numParticipants').value);
            console.log('Total:', document.getElementById('totalPrice').textContent);
            
            return true;
        }


        // ===== PAYMENT FLOW =====
        function handleBookingSubmit(e) {
            if (e) e.preventDefault();
            handleFinalPayment();
        }

        function handleFinalPayment() {
            // Validate form
            if (!validateForm()) return;
            
            // Get payment method from dropdown
            const paymentMethod = document.getElementById('paymentMethodSelect').value;
            if (!paymentMethod) {
                alert('⚠️ Pilih metode pembayaran terlebih dahulu!');
                return;
            }
            
            console.log('📝 Creating booking with payment method:', paymentMethod);
            
            // Submit form via AJAX to create booking
            const form = document.getElementById('bookingForm');
            const formData = new FormData(form);
            formData.append('payment_method', paymentMethod);
            formData.append('type', 'full');
            
            const btn = document.getElementById('payNowBtn');
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Memproses...';
            }
            
            fetch('{{ route('workshop.booking.store') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                console.log('✅ Booking response:', data);
                
                if (!data.success) {
                    alert('❌ ' + (data.message || 'Gagal membuat booking'));
                    if (btn) {
                        btn.disabled = false;
                        btn.textContent = 'Bayar Sekarang';
                    }
                    return;
                }
                
                // Store booking ID globally
                window.currentBookingId = data.booking_id;
                window.currentBookingNumber = data.booking_number;
                window.currentTotalPrice = data.total_price;
                window.currentPaymentMethod = paymentMethod;
                
                console.log('🎉 Booking created - ID:', data.booking_id);
                
                // Process payment to get checkout URL
                processPaymentToTripay(data.booking_id, paymentMethod);
                
                if (btn) {
                    btn.disabled = false;
                    btn.textContent = 'Bayar Sekarang';
                }
            })
            .catch(err => {
                console.error('❌ Error:', err);
                alert('❌ ' + err.message);
                if (btn) {
                    btn.disabled = false;
                    btn.textContent = 'Bayar Sekarang';
                }
            });
        }

        function processPaymentToTripay(bookingId, paymentMethod) {
            console.log('🚀 Processing payment to Tripay...');
            
            const formData = new FormData();
            formData.append('payment_method', paymentMethod);
            formData.append('type', 'full');
            
            fetch(`/workshop/booking/${bookingId}/process-payment`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(r => r.json())
            .then(data => {
                console.log('✅ Payment response:', data);
                
                if (!data.success) {
                    alert('❌ ' + (data.message || 'Gagal memproses pembayaran'));
                    return;
                }
                
                // Tampilkan modal pembayaran (style baru seperti checkout) + polling status
                openTripayModal(data, bookingId);
            })
            .catch(err => {
                console.error('❌ Error:', err);
                alert('❌ ' + err.message);
            });
        }

        let tripayPollTimer = null;
        let tripayCurrentBookingId = null;
        let tripaySnapshot = null;

        function formatIDR(amount) {
            const n = Number(amount ?? 0);
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(Number.isFinite(n) ? n : 0);
        }

        function openTripayModal(paymentResp, bookingId) {
            tripayCurrentBookingId = bookingId;
            tripaySnapshot = paymentResp;

            renderTripayPending(paymentResp);

            const modal = document.getElementById('tripayModal');
            if (modal) modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';

            startTripayPolling();
        }

        function closeTripayModal() {
            stopTripayPolling();
            tripayCurrentBookingId = null;

            const modal = document.getElementById('tripayModal');
            if (modal) modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function startTripayPolling() {
            stopTripayPolling();
            tripayPollTimer = setInterval(checkTripayStatus, 3000);
        }

        function stopTripayPolling() {
            if (tripayPollTimer) {
                clearInterval(tripayPollTimer);
                tripayPollTimer = null;
            }
        }

        async function checkTripayStatus() {
            if (!tripayCurrentBookingId) return;

            try {
                const r = await fetch(`/workshop/booking/${tripayCurrentBookingId}/payment-status`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await r.json();
                if (!data || !data.success) return;

                if (data.payment_status === 'confirmed') {
                    renderTripayPaid();
                    stopTripayPolling();
                }
            } catch (e) {
                // silent polling errors
            }
        }

        function renderTripayPending(paymentResp) {
            const header = document.getElementById('tripayModalHeader');
            const title = document.getElementById('tripayModalTitle');
            const closeBtn = document.getElementById('tripayModalCloseBtn');
            const body = document.getElementById('tripayModalBody');

            if (header) header.removeAttribute('style');
            if (title) {
                title.textContent = '💳 Pembayaran';
                title.style.color = '#8B4513';
            }
            if (closeBtn) {
                closeBtn.textContent = 'Tutup';
                closeBtn.className = 'w-full px-3 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors';
                closeBtn.onclick = closeTripayModal;
            }

            const ref = paymentResp?.reference ?? '-';
            const fee = Number(paymentResp?.fee ?? 0);
            const baseAmount = Number(paymentResp?.amount ?? window.currentTotalPrice ?? 0);
            const totalToPay = (Number.isFinite(baseAmount) ? baseAmount : 0) + (Number.isFinite(fee) ? fee : 0);

            const methodName = paymentResp?.payment_details?.method_name ?? paymentResp?.method ?? '';

            let methodBox = '';
            const details = paymentResp?.payment_details;
            if (details?.type === 'virtual_account') {
                const payCode = details.pay_code || details.va_number || details.account_number || '';
                methodBox = payCode ? `
                    <div class="bg-blue-50 border-2 border-blue-300 rounded-lg p-3 text-center">
                        <p class="text-xs text-gray-600 mb-1">Transfer ke nomor:</p>
                        <p class="text-xl font-mono font-bold text-blue-700 break-all">${payCode}</p>
                        <p class="text-xs text-gray-600 mt-1">${details.bank_name || methodName || 'Virtual Account'}</p>
                    </div>
                ` : '';
            } else if (details?.type === 'qris') {
                const qrUrl = details.qr_url || details.qr_image_url || '';
                methodBox = qrUrl ? `
                    <div class="text-center">
                        <img src="${qrUrl}" alt="QRIS" class="w-40 h-40 mx-auto rounded-lg border border-gray-300" />
                        <p class="text-xs text-gray-600 mt-2">Scan dengan e-wallet favorit</p>
                    </div>
                ` : `
                    <div class="bg-gray-100 p-3 rounded-lg text-center">
                        <p class="text-xs text-gray-600 mb-1">Kode Referensi:</p>
                        <p class="font-mono text-sm font-bold text-gray-900">${ref}</p>
                    </div>
                `;
            } else {
                methodBox = `
                    <div class="bg-gray-100 p-3 rounded-lg text-center">
                        <p class="text-xs text-gray-600 mb-1">Kode Referensi:</p>
                        <p class="font-mono text-sm font-bold text-gray-900">${ref}</p>
                    </div>
                `;
            }

            const bookingNo = window.currentBookingNumber || (tripayCurrentBookingId ? `WS-${tripayCurrentBookingId}` : '-');

            const html = `
                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-xs font-semibold text-blue-900 mb-1">Ref:</p>
                    <p class="text-sm font-mono font-bold text-blue-700 break-all">${ref}</p>
                </div>

                <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="mb-3 pb-3 border-b border-gray-300">
                        <p class="text-xs text-gray-600">Nomor Booking:</p>
                        <p class="text-sm font-mono font-bold text-gray-900">${bookingNo}</p>
                    </div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-700">Total Booking</span>
                        <span>${formatIDR(baseAmount)}</span>
                    </div>
                    <div class="flex justify-between text-sm mb-2 pb-2 border-b border-gray-300">
                        <span class="text-gray-700">Admin Fee Tripay</span>
                        <span class="text-orange-600">${formatIDR(fee)}</span>
                    </div>
                    <div class="flex justify-between font-bold">
                        <span style="color: #8B4513;">Total Bayar</span>
                        <span style="color: #8B4513;">${formatIDR(totalToPay)}</span>
                    </div>
                </div>

                <div class="mb-4">
                    ${methodBox}
                </div>

                <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg">
                    <p class="text-xs text-amber-900 text-center">Setelah pembayaran berhasil, status akan terupdate otomatis.</p>
                </div>
            `;

            if (body) body.innerHTML = html;
        }

        function renderTripayPaid() {
            const header = document.getElementById('tripayModalHeader');
            const title = document.getElementById('tripayModalTitle');
            const closeBtn = document.getElementById('tripayModalCloseBtn');
            const body = document.getElementById('tripayModalBody');

            if (header) header.style.background = 'linear-gradient(to right, #10b981, #059669)';
            if (title) {
                title.textContent = '✅ Pembayaran Berhasil';
                title.style.color = 'white';
            }

            const bookingId = tripayCurrentBookingId;
            const bookingNo = window.currentBookingNumber || (bookingId ? `WS-${bookingId}` : '-');

            if (body) {
                body.innerHTML = `
                    <div class="py-6">
                        <div class="text-center mb-6">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full" style="background-color: #d1fae5;">
                                <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>

                        <div class="text-center mb-6">
                            <h3 class="font-bold text-green-900 mb-2">Pembayaran Berhasil!</h3>
                            <p class="text-sm text-gray-600">Terima kasih. Booking Anda sudah terkonfirmasi.</p>
                        </div>

                        <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-xs text-gray-600">Nomor Booking:</p>
                            <p class="text-sm font-mono font-bold text-gray-900">${bookingNo}</p>
                        </div>
                    </div>
                `;
            }

            if (closeBtn) {
                closeBtn.textContent = 'Selesai';
                closeBtn.className = 'w-full px-3 py-2 text-sm rounded-lg bg-green-600 text-white hover:bg-green-700 transition-colors font-medium';
                closeBtn.onclick = () => {
                    closeTripayModal();
                    if (bookingId) {
                        window.location.href = `{{ url('workshop/booking') }}/${bookingId}/detail`;
                    }
                };
            }
        }
    </script>

    <!-- ===== MODAL ANIMATIONS & STYLES ===== -->
    <style>
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes zoomIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        .animate-in {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        .zoom-in {
            animation: zoomIn 0.3s ease-in-out;
        }
        
        .duration-300 {
            animation-duration: 300ms;
        }
        
        /* Smooth transitions */
        button, input, select, textarea {
            transition: all 200ms ease-in-out;
        }
        
        /* Better focus states */
        input[type="radio"]:focus,
        input[type="checkbox"]:focus,
        select:focus {
            outline: none;
        }
    </style>
</x-layouts.landing>
