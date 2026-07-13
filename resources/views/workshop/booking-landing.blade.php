<x-layouts.landing title="Booking Workshop Batik">
    <div class="min-h-screen bg-white py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-12">
                <h1 class="text-4xl font-bold mb-4" style="font-family: 'Playfair Display', serif; color: #8B4513;">
                    Booking Workshop Batik
                </h1>
                <p class="text-gray-600" style="font-family: 'Poppins', sans-serif;">
                    Pilih workshop, slot waktu, dan lanjutkan ke pembayaran
                </p>
            </div>

            <!-- Info Box -->
            <div class="bg-amber-50 border-l-4 border-amber-600 p-6 mb-8 rounded" style="font-family: 'Poppins', sans-serif;">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-lg font-bold text-amber-900 mb-2" id="paketName">{{ $workshop->title }}</h3>
                        <p class="text-gray-700 mb-2" id="paketTitle">{{ $workshop->description }}</p>
                        <p class="text-2xl font-bold text-amber-700" id="paketPrice">Rp {{ number_format($workshop->amount, 0, ',', '.') }}/orang</p>
                    </div>
                </div>
            </div>
            <form id="bookingForm" method="POST" action="{{ route('workshop.booking.store') }}" class="bg-white rounded-lg shadow-lg p-8 space-y-6">
                @csrf
                <!-- Hidden Fields -->
                <input type="hidden" id="workshopId" name="workshop_id" value="{{ $workshop->id }}">
                <input type="hidden" id="workshopName" name="workshop_name" value="{{ $workshop->title }}">
                <input type="hidden" id="packagePrice" name="package_price" value="{{ $workshop->amount }}">
                <input type="hidden" id="scheduleId" name="workshop_slot_schedule_id">
                
                <!-- Pilih Jadwal Workshop (Button Grid) -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3" style="font-family: 'Poppins', sans-serif;">
                        🗓️ Pilih Jadwal Workshop
                    </label>
                    <div id="scheduleContainer" class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                        <!-- Schedules will be loaded here -->
                        <div class="col-span-full text-center py-8 text-gray-500">
                            ⏳ Loading jadwal tersedia...
                        </div>
                    </div>
                    <small class="text-gray-500 block mt-2">Pilih salah satu jadwal yang tersedia</small>
                    <div id="scheduleError" class="text-red-600 text-sm mt-2 font-bold" style="display: none;"></div>
                </div>
                
                <!-- Jumlah Peserta -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2" style="font-family: 'Poppins', sans-serif;">
                        👥 Jumlah Peserta
                    </label>
                    <input 
                        type="number" 
                        name="number_of_participants"
                        min="1"
                        value="1"
                        id="participants"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                        style="font-family: 'Poppins', sans-serif;">
                </div>
                <!-- Nama Lengkap -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2" style="font-family: 'Poppins', sans-serif;">
                        👤 Nama Lengkap
                    </label>
                    <input 
                        type="text" 
                        name="full_name"
                        placeholder="Masukkan nama Anda"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                        style="font-family: 'Poppins', sans-serif;">
                </div>
                <!-- Email -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2" style="font-family: 'Poppins', sans-serif;">
                        📧 Email
                    </label>
                    <input 
                        type="email" 
                        name="email"
                        placeholder="email@example.com"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                        style="font-family: 'Poppins', sans-serif;">
                </div>
                <!-- Nomor WhatsApp -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2" style="font-family: 'Poppins', sans-serif;">
                        📱 Nomor WhatsApp
                    </label>
                    <input 
                        type="tel" 
                        name="phone"
                        placeholder="08xxxxxxxxxx"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                        style="font-family: 'Poppins', sans-serif;">
                </div>
                <!-- Alamat -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2" style="font-family: 'Poppins', sans-serif;">
                        🏠 Alamat Lengkap
                    </label>
                    <textarea 
                        name="address"
                        rows="3"
                        placeholder="Masukkan alamat lengkap Anda"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                        style="font-family: 'Poppins', sans-serif;"></textarea>
                </div>
                <!-- Price Summary -->
                <div class="bg-amber-50 p-6 rounded-lg border-2 border-amber-300">
                    <div class="space-y-3" style="font-family: 'Poppins', sans-serif;">
                        <div class="border-b-2 border-amber-300 pb-3 flex justify-between items-center">
                            <span class="font-bold text-gray-900">Total harga:</span>
                            <span class="font-bold text-xl text-amber-700" id="totalPrice">Rp0</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">Jumlah peserta:</span>
                            <span class="font-bold text-gray-900" id="countParticipants">1</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">Harga per peserta:</span>
                            <span class="font-bold text-gray-900" id="pricePerPerson">Rp0</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Total = harga per peserta × jumlah peserta.</p>

                    </div>
                </div>
                <!-- Form Actions -->
                <div class="flex gap-3 pt-6">
                    <a href="{{ route('landing.workshop') }}" class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-100 transition-colors text-center" style="font-family: 'Poppins', sans-serif;">
                        ← Kembali
                    </a>
                    <button 
                        type="button" 
                        onclick="openPaymentModal()"
                        class="flex-1 px-6 py-3 bg-[#8B4513] text-white font-bold rounded-lg hover:bg-[#6B3410] transition-all duration-300 shadow-lg hover:shadow-xl"
                        style="font-family: 'Poppins', sans-serif;">
                        💳 Bayar Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Unified Payment & Confirmation Modal -->
    <div id="paymentModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm max-h-[85vh] overflow-hidden flex flex-col">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200" id="modalHeader">
                <h2 class="text-lg font-bold text-amber-700" id="modalTitle">💳 Pembayaran</h2>
                <button onclick="closePaymentModal()" class="p-1 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body - Scrollable -->
            <div class="overflow-y-auto flex-1 p-4 space-y-4">
                <!-- STEP 1: Payment Method Selection -->
                <div id="stepPaymentMethod">
                    <!-- Payment Amount - Compact -->
                    <div class="bg-gradient-to-br from-orange-50 to-amber-50 border-2 border-amber-300 p-4 rounded-lg text-center mb-4">
                        <p class="text-xs font-semibold text-amber-900 tracking-wide mb-1">Jumlah Pembayaran</p>
                        <p class="text-2xl font-bold text-amber-700" id="modalTotalPrice">Rp 0</p>
                    </div>

                    <!-- Payment Method Selection Form -->
                    <form id="paymentForm" method="POST" action="{{ route('workshop.booking.store') }}" enctype="multipart/form-data">
                        @csrf
                        <!-- Copy booking form data -->
                        <div id="formDataContainer"></div>

                        <!-- Payment Method Section -->
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden mb-3">
                            <!-- Header -->
                            <div class="bg-gradient-to-r from-amber-600 to-amber-700 px-4 py-3">
                                <h3 class="text-sm font-bold text-white">💳 Pilih Metode Pembayaran</h3>
                            </div>

                            <!-- Content -->
                            <div class="p-4 space-y-3">
                                <!-- Payment Method Selection -->
                                <div class="space-y-2">
                                    <select name="payment_method" id="paymentMethodSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:outline-none font-medium text-sm transition-all" style="font-family: 'Poppins', sans-serif;" onchange="updatePaymentDisplay()" required>
                                        <option value="">-- Pilih Metode --</option>
                                        <optgroup label="💳 VIRTUAL ACCOUNT">
                                            <option value="BCAVA">🏦 BCA VA</option>
                                            <option value="BRIVA">🏦 BRI VA</option>
                                            <option value="BNIVA">🏦 BNI VA</option>
                                            <option value="MANDIRIVA">🏦 Mandiri VA</option>
                                        </optgroup>
                                        <optgroup label="📱 DOMPET DIGITAL">
                                            <option value="QRIS">📲 QRIS</option>
                                        </optgroup>
                                        <optgroup label="🏪 TOKO">
                                            <option value="ALFAMART">🏪 Alfamart</option>
                                            <option value="INDOMARET">🏪 Indomaret</option>
                                        </optgroup>
                                        <optgroup label="🏧 MANUAL">
                                            <option value="bank_transfer">🏦 Transfer Manual</option>
                                        </optgroup>
                                    </select>
                                </div>

                                <!-- Manual Transfer Details -->
                                <div id="manualTransferDetails" class="hidden space-y-2 pt-3 border-t border-gray-200 text-xs">
                                    <div class="bg-blue-50 border border-blue-200 rounded p-2">
                                        <p class="font-bold text-gray-700 mb-1">BCA: 1234567890</p>
                                        <p class="text-gray-600">PT. Batik Giri Alam</p>
                                    </div>
                                    <div class="bg-red-50 border border-red-200 rounded p-2">
                                        <p class="font-bold text-gray-700 mb-1">Mandiri: 1234567890</p>
                                        <p class="text-gray-600">PT. Batik Giri Alam</p>
                                    </div>
                                </div>

                                <!-- Online Payment Info -->
                                <div id="paymentMethodInfo" class="hidden bg-green-50 border border-green-200 rounded-lg p-3 text-xs">
                                    <p class="font-bold text-green-900 mb-1">✅ Pembayaran Online</p>
                                    <p class="text-green-800">Proses otomatis & cepat</p>
                                </div>
                            </div>
                        </div>

                        <!-- Terms & Conditions -->
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-3">
                            <label class="flex items-start gap-2 cursor-pointer">
                                <input type="checkbox" name="agree_terms" class="mt-0.5 w-4 h-4 cursor-pointer accent-amber-600" required>
                                <span class="text-xs text-gray-700">Saya setuju dengan syarat & ketentuan. Pembayaran harus dalam 24 jam.</span>
                            </label>
                        </div>
                    </form>
                </div>

                <!-- STEP 2: Processing -->
                <div id="stepProcessing" class="hidden space-y-3">
                    <!-- Amount Section -->
                    <div class="bg-gradient-to-br from-orange-50 to-amber-50 border-2 border-amber-300 p-4 rounded-lg text-center">
                        <p class="text-xs font-semibold text-amber-900 tracking-wide mb-1">Jumlah Pembayaran</p>
                        <p class="text-2xl font-bold text-amber-700" id="processingAmount">Rp 0</p>
                    </div>

                    <!-- QRIS Display -->
                    <div id="paymentQRIS" class="hidden space-y-3">
                        <div class="bg-white border border-gray-300 rounded-lg p-4 text-center">
                            <h4 class="text-sm font-bold text-gray-800 mb-3">📲 Scan QR Code</h4>
                            <div class="bg-gray-100 p-3 rounded inline-block">
                                <img id="qrisImage" src="" alt="QRIS" class="w-40 h-40 object-contain">
                            </div>
                            <p class="text-xs text-gray-600 mt-2">Arahkan kamera smartphone</p>
                        </div>
                    </div>

                    <!-- Virtual Account Display -->
                    <div id="paymentVA" class="hidden space-y-3">
                        <div class="bg-white border border-gray-300 rounded-lg p-4">
                            <h4 class="text-sm font-bold text-gray-800 mb-3">🏦 Transfer VA</h4>
                            <div class="bg-blue-50 border border-blue-300 rounded p-3 text-center mb-3">
                                <p class="text-xs text-gray-600 mb-1">Nomor Rekening</p>
                                <p class="text-lg font-mono font-bold text-blue-700 break-words mb-1" id="vaNumber">-</p>
                                <p class="text-xs text-gray-700" id="vaBank">-</p>
                                <p class="text-xs text-gray-600 mt-1">PT. Batik Giri Alam</p>
                            </div>
                            <button type="button" onclick="copyToClipboard(document.getElementById('vaNumber').textContent)" class="w-full px-3 py-2 bg-blue-600 text-white text-xs rounded font-semibold hover:bg-blue-700 transition-all">
                                📋 Salin
                            </button>
                        </div>
                    </div>

                    <!-- Store Code Display -->
                    <div id="paymentStore" class="hidden space-y-3">
                        <div class="bg-white border border-gray-300 rounded-lg p-4">
                            <h4 class="text-sm font-bold text-gray-800 mb-3">🏪 Bayar di Toko</h4>
                            <div class="bg-orange-50 border border-orange-300 rounded p-3 text-center mb-3">
                                <p class="text-xs text-gray-600 mb-1">Kode Referensi</p>
                                <p class="text-lg font-mono font-bold text-orange-700 break-words" id="storeReference">-</p>
                                <p class="text-xs text-gray-600 mt-2">Gunakan kode di kasir</p>
                            </div>
                            <button type="button" onclick="copyToClipboard(document.getElementById('storeReference').textContent)" class="w-full px-3 py-2 bg-orange-600 text-white text-xs rounded font-semibold hover:bg-orange-700 transition-all">
                                📋 Salin
                            </button>
                        </div>
                    </div>

                    <!-- Loading Status -->
                    <div class="bg-amber-50 border border-amber-300 rounded-lg p-3">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="relative w-4 h-4">
                                <div class="absolute inset-0 rounded-full border-2 border-transparent border-t-amber-600 border-r-amber-600 animate-spin"></div>
                            </div>
                            <p class="text-xs font-bold text-amber-900">Menunggu Konfirmasi...</p>
                        </div>
                        <p class="text-xs text-amber-800 ml-6">Halaman akan auto-update</p>
                    </div>
                </div>

                <!-- STEP 3: Confirmation -->
                <div id="stepConfirmation" class="hidden text-left space-y-3">
                    <!-- Success Icon -->
                    <div class="flex justify-center mb-2">
                        <div class="bg-green-100 rounded-full p-3">
                            <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Success Message -->
                    <div class="text-center mb-2">
                        <h3 class="text-lg font-bold text-green-600 mb-1">Pembayaran Berhasil!</h3>
                        <p class="text-xs text-gray-600">Terima kasih telah memesan workshop kami.</p>
                    </div>

                    <!-- Order Details -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 space-y-2 text-xs">
                        <h4 class="font-bold text-gray-900 mb-2">📋 Detail Pesanan</h4>
                        
                        <div class="pb-2 border-b border-gray-300">
                            <p class="text-gray-600">Workshop</p>
                            <p class="font-bold text-gray-900" id="confirmWorkshopName">-</p>
                        </div>

                        <div class="pb-2 border-b border-gray-300">
                            <p class="text-gray-600">Tanggal</p>
                            <p class="font-bold text-gray-900" id="confirmWorkshopDate">-</p>
                        </div>

                        <div class="pb-2 border-b border-gray-300">
                            <p class="text-gray-600">Jam</p>
                            <p class="font-bold text-gray-900" id="confirmWorkshopTime">-</p>
                        </div>

                        <div class="pb-2 border-b border-gray-300">
                            <p class="text-gray-600">Nama</p>
                            <p class="font-bold text-gray-900" id="confirmParticipantName">-</p>
                        </div>

                        <div class="pb-2 border-b border-gray-300">
                            <p class="text-gray-600">Email</p>
                            <p class="font-bold text-gray-900 break-words" id="confirmParticipantEmail">-</p>
                        </div>

                        <div class="pb-2 border-b border-gray-300">
                            <p class="text-gray-600">Telepon</p>
                            <p class="font-bold text-gray-900" id="confirmParticipantPhone">-</p>
                        </div>

                        <div class="bg-green-50 rounded p-2 mt-2">
                            <p class="text-gray-600">Tanggal Pembayaran</p>
                            <p class="font-bold text-gray-900" id="confirmPaymentDate">-</p>
                            <p class="text-gray-600 mt-1">Total Pembayaran</p>
                            <p class="font-bold text-green-600 text-base" id="confirmPaymentAmount">Rp 0</p>
                        </div>
                    </div>

                    <!-- Email Notification -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <p class="text-xs text-blue-900">
                            <span class="font-bold block mb-1">📧 Email Dikirim</span>
                            <span>Konfirmasi email telah dikirim. Cek inbox untuk link akses video.</span>
                        </p>
                    </div>

                    <!-- Download Receipt -->
                    <button type="button" onclick="downloadReceiptPDF()" class="w-full px-4 py-3 bg-gradient-to-r from-amber-500 to-amber-600 text-white rounded-lg font-bold text-sm transition-all shadow-md hover:shadow-lg active:scale-95">
                        📥 Download Bukti Pembayaran
                    </button>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="border-t border-gray-200 px-4 py-3 bg-white flex gap-2 flex-shrink-0">
                <!-- Step 1 & 2 Actions -->
                <div id="stepActionsButtons" class="flex gap-2 w-full">
                    <button type="button" onclick="closePaymentModal()" class="flex-1 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-bold transition-all text-sm">
                        ← Kembali
                    </button>
                    <button type="button" onclick="submitPaymentForm()" class="flex-1 px-3 py-2 bg-gradient-to-r from-amber-600 to-amber-700 text-white rounded-lg hover:from-amber-700 hover:to-amber-800 font-bold transition-all shadow-md text-sm" id="submitPaymentBtn">
                        Lanjut →
                    </button>
                </div>
                <!-- Step 3 Actions -->
                <div id="confirmationActionsButtons" class="hidden w-full gap-2" style="display: none;">
                    <button type="button" onclick="closePaymentModal()" class="flex-1 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-bold transition-all text-sm">
                        ← Tutup
                    </button>
                    <button type="button" onclick="closePaymentModal(); location.reload();" class="flex-1 px-3 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 font-bold transition-all shadow-md text-sm">
                        ✓ Selesai
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- CDNs -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

    <script>
        let pollingInterval = null;
        let currentBookingId = null;
        let pollAttempt = 0;
        const MAX_POLL_ATTEMPTS = 120;

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                Swal.fire({ title: 'Berhasil', text: 'Berhasil disalin ke clipboard!', icon: 'success', iconColor: '#10b981', confirmButtonText: 'OK', confirmButtonColor: '#b45309', customClass: { popup: 'rounded-2xl shadow-2xl' } });
            }).catch(() => {
                Swal.fire({ title: 'Gagal', text: 'Gagal menyalin ke clipboard', icon: 'error', iconColor: '#ef4444', confirmButtonText: 'OK', confirmButtonColor: '#b45309', customClass: { popup: 'rounded-2xl shadow-2xl' } });
            });
        }

        function showStep(stepName) {
            document.getElementById('stepPaymentMethod').classList.add('hidden');
            document.getElementById('stepProcessing').classList.add('hidden');
            document.getElementById('stepConfirmation').classList.add('hidden');

            document.getElementById(stepName).classList.remove('hidden');

            const stepActionsBtn = document.getElementById('stepActionsButtons');
            const confirmActionsBtn = document.getElementById('confirmationActionsButtons');

            if (stepName === 'stepConfirmation') {
                stepActionsBtn.classList.add('hidden');
                confirmActionsBtn.style.display = 'flex';
                document.getElementById('modalTitle').textContent = '✅ Pembayaran Berhasil';
                if (pollingInterval) clearInterval(pollingInterval);
            } else {
                stepActionsBtn.classList.remove('hidden');
                confirmActionsBtn.style.display = 'none';
                if (stepName === 'stepProcessing') {
                    document.getElementById('modalTitle').textContent = '📱 Lakukan Pembayaran';
                } else {
                    document.getElementById('modalTitle').textContent = '💳 Pilih Metode Pembayaran';
                }
            }
        }

        function openPaymentModal() {
            try {
                console.log('openPaymentModal called');
                const form = document.getElementById('bookingForm');
                console.log('Form found:', form);
                
                if (!form.checkValidity()) {
                    console.log('Form validation failed');
                    form.reportValidity();
                    return;
                }

                // Verify schedule is selected
                const scheduleId = document.getElementById('scheduleId').value;
                console.log('Schedule ID:', scheduleId);
                if (!scheduleId) {
                    const scheduleError = document.getElementById('scheduleError');
                    scheduleError.textContent = '⚠️ Silakan pilih jadwal workshop terlebih dahulu!';
                    scheduleError.style.display = 'block';
                    console.log('Schedule not selected');
                    return;
                }

                // Verify number of participants
                const participants = parseInt(document.getElementById('participants').value, 10);
                console.log('Participants:', participants);
                if (isNaN(participants) || participants < 1) {
                    Swal.fire({ title: 'Jumlah Peserta', text: 'Jumlah peserta harus minimal 1!', icon: 'warning', confirmButtonText: 'OK', confirmButtonColor: '#b45309', customClass: { popup: 'rounded-2xl shadow-2xl' } });
                    return;
                }

                console.log('All validation passed, opening modal');
                const bookingFormData = new FormData(form);
                const formDataContainer = document.getElementById('formDataContainer');
                console.log('Form data container:', formDataContainer);
                formDataContainer.innerHTML = '';
                for (let [key, value] of bookingFormData.entries()) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = value;
                    formDataContainer.appendChild(input);
                }

                const totalPrice = document.getElementById('totalPrice').textContent;
                console.log('Total price:', totalPrice);
                document.getElementById('modalTotalPrice').textContent = totalPrice;

                const modal = document.getElementById('paymentModal');
                console.log('Modal element:', modal);
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                // Prevent interaction with form behind modal
                document.getElementById('bookingForm').style.pointerEvents = 'none';

                console.log('Calling showStep');
                showStep('stepPaymentMethod');
                console.log('Calling updatePaymentDisplay');
                updatePaymentDisplay();
                console.log('Modal opened successfully');
            } catch (error) {
                console.error('Error in openPaymentModal:', error);
                Swal.fire({ title: 'Terjadi Kesalahan', text: error.message, icon: 'error', iconColor: '#ef4444', confirmButtonText: 'OK', confirmButtonColor: '#b45309', customClass: { popup: 'rounded-2xl shadow-2xl' } });
            }
        }

        function closePaymentModal() {
            const modal = document.getElementById('paymentModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            // Restore form interactivity
            document.getElementById('bookingForm').style.pointerEvents = 'auto';
            if (pollingInterval) clearInterval(pollingInterval);
        }

        function updatePaymentDisplay() {
            const paymentMethod = document.getElementById('paymentMethodSelect').value;
            const manualTransferDetails = document.getElementById('manualTransferDetails');
            const paymentMethodInfo = document.getElementById('paymentMethodInfo');
            if (paymentMethod === 'bank_transfer') {
                manualTransferDetails.classList.remove('hidden');
                paymentMethodInfo.classList.add('hidden');
            } else if (paymentMethod) {
                manualTransferDetails.classList.add('hidden');
                paymentMethodInfo.classList.remove('hidden');
            } else {
                manualTransferDetails.classList.add('hidden');
                paymentMethodInfo.classList.add('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('paymentModal');
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closePaymentModal();
                }
            });
        });

        let globalWorkshopData = {};
        document.addEventListener('DOMContentLoaded', function() {
            const params = new URLSearchParams(window.location.search);
            let workshopData = {};
            const urlParamsObj = {};
            params.forEach((value, key) => {
                urlParamsObj[key] = value;
            });

            const storedData = sessionStorage.getItem('workshopData');

            if (storedData) {
                try {
                    workshopData = JSON.parse(storedData);
                } catch (e) {
                    console.error('Error parsing sessionStorage:', e);
                }
            }

            if (params.has('workshop_name') && params.has('package_price')) {
                workshopData.paketName = params.get('workshop_name');
                workshopData.price = parseInt(params.get('package_price')) || 0;
            }

            if (!workshopData.paketName || !workshopData.price) {
                workshopData = {
                    paketName: 'PAKET 1',
                    title: 'Workshop Batik Premium',
                    price: 250000
                };
            }

            globalWorkshopData = workshopData;

            try {
                document.getElementById('paketName').textContent = workshopData.paketName;
                document.getElementById('paketTitle').textContent = workshopData.title || '';
                document.getElementById('paketPrice').textContent = 'Rp' + new Intl.NumberFormat('id-ID').format(workshopData.price);
                document.getElementById('workshopName').value = workshopData.paketName;
                document.getElementById('packagePrice').value = workshopData.price;
                
                // Get workshopId from hidden field (set from route parameter)
                // Don't fallback to 4, use what's in the form
                // If not set, it means something is wrong
                const workshopId = document.getElementById('workshopId').value;
                if (!workshopId) {
                    console.error('❌ Workshop ID tidak tersedia! Check route parameter.');
                }
            } catch (e) {
                console.error('Error updating display:', e);
            }

            updatePriceDisplay(workshopData.price);

            // Remove old listeners to prevent duplicates
            const participantsInput = document.getElementById('participants');
            const changeHandler = function() {
                updatePriceDisplay(globalWorkshopData.price);
            };
            const inputHandler = function() {
                updatePriceDisplay(globalWorkshopData.price);
            };
            
            // Clone and replace to remove all listeners
            const newParticipantsInput = participantsInput.cloneNode(true);
            participantsInput.parentNode.replaceChild(newParticipantsInput, participantsInput);
            
            // Add fresh listeners
            document.getElementById('participants').addEventListener('change', changeHandler);
            document.getElementById('participants').addEventListener('input', inputHandler);
            
            // Load available schedules
            const workshopId = document.getElementById('workshopId').value;
            loadAvailableSchedules(workshopId);
            
            // Setup booking form submission
            const bookingForm = document.getElementById('bookingForm');
            if (bookingForm) {
                bookingForm.addEventListener('submit', handleBookingFormSubmit);
            }
        });

        function loadAvailableSchedules(workshopId) {
            const scheduleContainer = document.getElementById('scheduleContainer');
            const scheduleError = document.getElementById('scheduleError');
            
            // Clear and show loading
            scheduleContainer.innerHTML = '<div class="col-span-full text-center py-8 text-gray-500">⏳ Loading jadwal tersedia...</div>';
            scheduleError.style.display = 'none';
            
            fetch(`/workshop/${workshopId}/available-schedules`)
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);
                    return response.json();
                })
                .then(schedules => {
                    scheduleContainer.innerHTML = '';
                    
                    if (schedules.length === 0) {
                        scheduleError.textContent = '❌ Tidak ada jadwal tersedia untuk workshop ini.';
                        scheduleError.style.display = 'block';
                        return;
                    }
                    
                    schedules.forEach(schedule => {
                        const button = document.createElement('button');
                        button.type = 'button';
                        button.className = 'schedule-btn p-4 border-2 border-gray-300 rounded-lg text-left hover:border-amber-500 hover:bg-amber-50 transition-all duration-200 cursor-pointer';
                        button.dataset.scheduleId = schedule.id;
                        button.dataset.scheduleData = JSON.stringify(schedule);
                        
                        button.innerHTML = `
                            <div class="font-bold text-gray-800">${schedule.date}</div>
                            <div class="text-sm text-amber-700">${schedule.slot_name} ${schedule.start_time}-${schedule.end_time}</div>
                        `;
                        
                        button.addEventListener('click', function(e) {
                            e.preventDefault();
                            selectSchedule(this);
                        });
                        
                        scheduleContainer.appendChild(button);
                    });
                })
                .catch(error => {
                    console.error('❌ Error loading schedules:', error);
                    scheduleError.textContent = '❌ Gagal memuat jadwal tersedia.';
                    scheduleError.style.display = 'block';
                });
        }

        // Store selected schedule data globally
        let selectedScheduleData = null;

        function selectSchedule(button) {
            // Remove active state from all buttons
            document.querySelectorAll('.schedule-btn').forEach(btn => {
                btn.classList.remove('border-amber-600', 'bg-amber-50', 'border-2');
                btn.classList.add('border-gray-300');
            });
            
            // Add active state to selected button
            button.classList.remove('border-gray-300');
            button.classList.add('border-amber-600', 'bg-amber-50', 'border-2');
            
            // Set hidden field and store data globally
            selectedScheduleData = JSON.parse(button.dataset.scheduleData);
            document.getElementById('scheduleId').value = selectedScheduleData.id;
        }

        function handleBookingFormSubmit(e) {
            e.preventDefault();
            
            const scheduleId = document.getElementById('scheduleId').value;
            
            if (!scheduleId) {
                Swal.fire({ title: 'Jadwal Diperlukan', text: 'Silahkan pilih jadwal workshop terlebih dahulu!', icon: 'warning', confirmButtonText: 'OK', confirmButtonColor: '#b45309', customClass: { popup: 'rounded-2xl shadow-2xl' } });
                return;
            }
            
            // Now submit the form normally
            this.submit();
        }

        function updatePriceDisplay(pricePerPerson) {
            const participants = parseInt(document.getElementById('participants').value) || 1;
            const totalPrice = pricePerPerson * participants;

            document.getElementById('pricePerPerson').textContent = 'Rp' + new Intl.NumberFormat('id-ID').format(pricePerPerson);
            document.getElementById('countParticipants').textContent = participants;
            document.getElementById('totalPrice').textContent = 'Rp' + new Intl.NumberFormat('id-ID').format(totalPrice);
        }

        function testPostEndpoint() {
            console.log('🧪 Testing POST endpoint /test-json...');
            fetch('/test-json', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({test: 'data'})
            })
            .then(r => r.text())
            .then(t => {
                console.log('🧪 TEST RESPONSE:', t.substring(0, 200));
            })
            .catch(e => console.error('🧪 TEST ERROR:', e));
        }

        function submitPaymentForm() {
            const paymentForm = document.getElementById('paymentForm');
            const paymentMethodSelect = document.getElementById('paymentMethodSelect');
            const paymentMethod = paymentMethodSelect.value;
            const agreeTerms = document.querySelector('input[name="agree_terms"]:checked');

            if (!paymentMethod) {
                Swal.fire({ title: 'Metode Pembayaran', text: 'Silahkan pilih metode pembayaran!', icon: 'warning', confirmButtonText: 'OK', confirmButtonColor: '#b45309', customClass: { popup: 'rounded-2xl shadow-2xl' } });
                return;
            }
            if (!agreeTerms) {
                Swal.fire({ title: 'Syarat & Ketentuan', text: 'Silahkan setujui syarat & ketentuan pembayaran!', icon: 'warning', confirmButtonText: 'OK', confirmButtonColor: '#b45309', customClass: { popup: 'rounded-2xl shadow-2xl' } });
                return;
            }

            showStep('stepProcessing');
            document.getElementById('processingAmount').textContent = document.getElementById('modalTotalPrice').textContent;

            const formData = new FormData(paymentForm);
            console.log('📤 Form Data being submitted:');
            for (let [key, value] of formData.entries()) {
                console.log(`  ${key}: ${String(value).substring(0, 50)}`);
            }
            console.log('📤 Posting to:', '{{ route("workshop.booking.store") }}');
            fetch('{{ route("workshop.booking.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('📥 Booking store response status:', response.status, response.statusText);
                console.log('📥 Content-Type:', response.headers.get('content-type'));
                // Check if response is ok first
                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('❌ Server error text (first 500 chars):', text.substring(0, 500));
                        throw new Error(`HTTP ${response.status}: ${text.substring(0, 150)}`);
                    });
                }
                return response.text().then(text => {
                    console.log('📥 Response text length:', text.length);
                    console.log('📥 Response first 300 chars:', text.substring(0, 300));
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('❌ Failed to parse JSON. Raw response:', text);
                        throw new Error('Invalid JSON response: ' + e.message);
                    }
                });
            })
            .then(data => {
                console.log('✅ Booking created successfully:', data);
                if (data.success) {
                    const bookingId = data.booking_id;
                    currentBookingId = bookingId;
                    console.log('🔄 Processing payment for booking:', bookingId);
                    processPayment(bookingId, paymentMethod);
                } else {
                    Swal.fire({ title: 'Gagal', text: data.message || 'Terjadi kesalahan saat membuat booking', icon: 'error', iconColor: '#ef4444', confirmButtonText: 'OK', confirmButtonColor: '#b45309', customClass: { popup: 'rounded-2xl shadow-2xl' } });
                    showStep('stepPaymentMethod');
                }
            })
            .catch(error => {
                console.error('❌ submitPaymentForm Error:', error);
                Swal.fire({ title: 'Terjadi Kesalahan', text: error.message, icon: 'error', iconColor: '#ef4444', confirmButtonText: 'OK', confirmButtonColor: '#b45309', customClass: { popup: 'rounded-2xl shadow-2xl' } });
                showStep('stepPaymentMethod');
            });
        }

        function processPayment(bookingId, paymentMethod) {
            console.log('📤 Sending payment request for booking:', bookingId, 'method:', paymentMethod);
            fetch(`/workshop/booking/${bookingId}/process-payment`, {
                method: 'POST',
                body: JSON.stringify({
                    payment_method: paymentMethod,
                    type: 'full'
                }),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            })
            .then(response => {
                console.log('📥 Process payment response status:', response.status);
                // Check if response is OK first - if not, get text and throw error
                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('❌ Server error response:', text.substring(0, 300));
                        throw new Error(`HTTP ${response.status}: ${text.substring(0, 150)}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('✅ Payment processed successfully:', data);
                if (data.success) {
                    displayPaymentDetails(data.payment_details, data.amount);
                    startPollingPaymentStatus(bookingId);
                } else {
                    Swal.fire({ title: 'Gagal', text: data.message || 'Terjadi kesalahan saat memproses pembayaran', icon: 'error', iconColor: '#ef4444', confirmButtonText: 'OK', confirmButtonColor: '#b45309', customClass: { popup: 'rounded-2xl shadow-2xl' } });
                    showStep('stepPaymentMethod');
                }
            })
            .catch(error => {
                console.error('❌ processPayment Error:', error);
                Swal.fire({ title: 'Terjadi Kesalahan', text: error.message, icon: 'error', iconColor: '#ef4444', confirmButtonText: 'OK', confirmButtonColor: '#b45309', customClass: { popup: 'rounded-2xl shadow-2xl' } });
                showStep('stepPaymentMethod');
            });
        }

        function displayPaymentDetails(paymentDetails, amount) {
            document.getElementById('paymentQRIS').classList.add('hidden');
            document.getElementById('paymentVA').classList.add('hidden');
            document.getElementById('paymentStore').classList.add('hidden');

            if (!paymentDetails) {
                return;
            }

            switch (paymentDetails.type) {
                case 'qris':
                    document.getElementById('paymentQRIS').classList.remove('hidden');
                    const qrImageUrl = paymentDetails.qr_url || paymentDetails.qr_image_url || paymentDetails.qr_image || null;
                    if (qrImageUrl) {
                        document.getElementById('qrisImage').src = qrImageUrl;
                    }
                    break;
                case 'virtual_account':
                    document.getElementById('paymentVA').classList.remove('hidden');
                    const vaNum = paymentDetails.pay_code || paymentDetails.va_number || paymentDetails.account_number || paymentDetails.customer_id || '-';
                    document.getElementById('vaNumber').textContent = vaNum;
                    const bankName = paymentDetails.payment_name || paymentDetails.bank_name || paymentDetails.method_name || paymentDetails.method || 'Bank';
                    document.getElementById('vaBank').textContent = bankName;
                    break;
                case 'convenience_store':
                    document.getElementById('paymentStore').classList.remove('hidden');
                    const storeRef = paymentDetails.reference || paymentDetails.reference_code || '-';
                    document.getElementById('storeReference').textContent = storeRef;
                    break;
                default:
                    console.warn('Unknown payment type:', paymentDetails.type);
                    document.getElementById('paymentVA').classList.remove('hidden');
                    const fallbackVA = paymentDetails.pay_code || paymentDetails.account_number || paymentDetails.customer_id || '(No VA data)';
                    document.getElementById('vaNumber').textContent = fallbackVA;
                    document.getElementById('vaBank').textContent = paymentDetails.payment_name || paymentDetails.method_name || paymentDetails.method || 'Unknown';
            }
        }

        function startPollingPaymentStatus(bookingId) {
            pollAttempt = 0;
            pollingInterval = setInterval(() => {
                pollAttempt++;
                checkPaymentStatus(bookingId);
                if (pollAttempt >= MAX_POLL_ATTEMPTS) {
                    clearInterval(pollingInterval);
                    pollingInterval = null;
                }
            }, 3000);
        }

        function checkPaymentStatus(bookingId) {
            fetch(`/workshop/booking/${bookingId}/payment-status`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('Payment status check error:', text.substring(0, 200));
                        throw new Error(`HTTP ${response.status}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.payment_status === 'confirmed') {
                    if (pollingInterval) {
                        clearInterval(pollingInterval);
                        pollingInterval = null;
                    }
                    if ('Notification' in window && Notification.permission === 'granted') {
                        new Notification('✅ Pembayaran Berhasil!', {
                            body: 'Pembayaran Anda telah dikonfirmasi.',
                            icon: '🎉'
                        });
                    }
                    showConfirmation(bookingId);
                }
            })
            .catch(error => {
                console.error('❌ Polling error:', error);
            });
        }

        function showConfirmation(bookingId) {
            const participantName = document.querySelector('input[name="full_name"]').value;
            const participantEmail = document.querySelector('input[name="email"]').value;
            const participantPhone = document.querySelector('input[name="phone"]').value;
            const workshopName = document.querySelector('input[name="workshop_name"]').value;
            const totalPrice = document.getElementById('modalTotalPrice').textContent;

            document.getElementById('confirmWorkshopName').textContent = workshopName || 'Workshop Batik';

            // Use data from selected schedule
            if (selectedScheduleData) {
                const workshopDateObj = new Date(selectedScheduleData.date);
                document.getElementById('confirmWorkshopDate').textContent = workshopDateObj.toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                document.getElementById('confirmWorkshopTime').textContent = `${selectedScheduleData.start_time} - ${selectedScheduleData.end_time} WIB`;
            } else {
                document.getElementById('confirmWorkshopDate').textContent = '-';
                document.getElementById('confirmWorkshopTime').textContent = '-';
            }

            document.getElementById('confirmParticipantName').textContent = participantName || '-';
            document.getElementById('confirmParticipantEmail').textContent = participantEmail || '-';
            document.getElementById('confirmParticipantPhone').textContent = participantPhone || '-';

            document.getElementById('confirmPaymentAmount').textContent = totalPrice;
            document.getElementById('confirmPaymentDate').textContent = new Date().toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            window.confirmationData = {
                bookingId: bookingId,
                participantName: participantName,
                participantEmail: participantEmail,
                participantPhone: participantPhone,
                workshopName: workshopName,
                workshopDate: selectedScheduleData?.date || new Date(),
                startTime: selectedScheduleData?.start_time || '-',
                endTime: selectedScheduleData?.end_time || '-',
                totalPrice: totalPrice,
                paymentDate: new Date().toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                })
            };

            showStep('stepConfirmation');
        }

        // PDF Download Function
        function downloadReceiptPDF() {
            if (!window.confirmationData) {
                Swal.fire({ title: 'Data Tidak Tersedia', text: 'Data konfirmasi tidak tersedia.', icon: 'error', iconColor: '#ef4444', confirmButtonText: 'OK', confirmButtonColor: '#b45309', customClass: { popup: 'rounded-2xl shadow-2xl' } });
                return;
            }
            
            const data = window.confirmationData;
            const htmlContent = `
            <!DOCTYPE html>
            <html lang="id">
            <head>
                <meta charset="UTF-8">
                <title>Bukti Pembayaran - Batik Giri Alam</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; color: #333; }
                    .header { text-align: center; border-bottom: 3px solid #8B4513; padding-bottom: 15px; margin-bottom: 20px; }
                    .title { font-size: 24px; font-weight: bold; color: #8B4513; }
                    .section { margin-bottom: 20px; }
                    .label { font-weight: bold; color: #666; }
                    .value { color: #333; }
                    .price-box { background: #FFF8E6; border: 2px solid #FFD54F; border-radius: 10px; padding: 15px; text-align: center; margin: 15px 0; }
                    .price { font-size: 24px; font-weight: bold; color: #8B4513; }
                </style>
            </head>
            <body>
                <div class="header">
                    <div class="title">Batik Giri Alam</div>
                    <div>Bukti Pembayaran Workshop Batik</div>
                </div>
                <div class="section">
                    <div class="label">Workshop:</div>
                    <div class="value">${data.workshopName || '-'}</div>
                </div>
                <div class="section">
                    <div class="label">Peserta:</div>
                    <div class="value">${data.participantName || '-'}</div>
                </div>
                <div class="section">
                    <div class="label">Email:</div>
                    <div class="value">${data.participantEmail || '-'}</div>
                </div>
                <div class="price-box">
                    <div>Jumlah Pembayaran</div>
                    <div class="price">${data.totalPrice}</div>
                </div>
                <div class="section">
                    <div class="label">Status:</div>
                    <div class="value" style="color: #2E7D32; font-weight: bold;">✔ BERHASIL</div>
                </div>
            </body>
            </html>`;

            const printWindow = window.open('', '', 'width=800,height=600');
            printWindow.document.write(htmlContent);
            printWindow.document.close();
            setTimeout(() => printWindow.print(), 250);
        }
    </script>
</x-layouts.landing>
