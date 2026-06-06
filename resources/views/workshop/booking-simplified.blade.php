<x-layouts.landing title="Booking Workshop Batik">
    <div class="min-h-screen bg-white py-8">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">🎨 Pesan Workshop Batik</h1>
                <p class="text-gray-600">5 Langkah Sederhana</p>
            </div>

            <!-- Progress Bar -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-bold text-blue-600">Langkah <span id="currentStepNum">1</span> dari 5</span>
                    <span class="text-sm text-gray-600" id="stepTitle">Pilih Slot Waktu</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="progressBar" class="bg-blue-600 h-2 rounded-full" style="width: 20%"></div>
                </div>
            </div>

            <!-- Main Form -->
            <form id="bookingForm" method="POST" action="{{ route('workshop.booking.store') }}" class="bg-white rounded-2xl shadow-xl p-8">
                @csrf

                <!-- Hidden Fields -->
                <input type="hidden" id="workshopId" name="workshop_id" value="">
                <input type="hidden" id="scheduleId" name="workshop_slot_schedule_id" value="">
                <input type="hidden" id="workshopName" name="workshop_name" value="">
                <input type="hidden" id="scheduleTime" name="schedule_time" value="">
                <input type="hidden" id="workshopPrice" name="unit_price" value="">
                <input type="hidden" id="selectedAddressId" name="address_id" value="">

                <!-- STEP 1: PILIH SLOT -->
                <div class="step-content" data-step="1">
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">📅 Langkah 1: Pilih Slot Tersedia</h2>
                        <p class="text-gray-600">Pilih waktu dan tanggal yang sesuai untuk Anda</p>
                    </div>

                    <!-- Workshop & Schedule Selection -->
                    <div id="scheduleList" class="space-y-3">
                        <div class="text-center py-8 text-gray-500">⏳ Loading jadwal...</div>
                    </div>
                    <div id="scheduleError" class="text-red-600 text-sm font-bold hidden"></div>

                    <!-- Help Text -->
                    <div class="mt-6 p-4 bg-blue-50 border-l-4 border-blue-600 rounded">
                        <p class="text-sm text-blue-900">💡 <strong>Tips:</strong> Pilih slot yang paling sesuai dengan jadwal Anda</p>
                    </div>

                    <button type="button" onclick="nextStep(1)" class="w-full mt-6 px-6 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition-all">
                        Lanjutkan → Step 2
                    </button>
                </div>

                <!-- STEP 2: INPUT JUMLAH PESERTA -->
                <div class="step-content hidden" data-step="2">
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">👥 Langkah 2: Berapa Peserta?</h2>
                        <p class="text-gray-600">Input jumlah peserta yang akan mengikuti workshop</p>
                    </div>

                    <!-- Selected Schedule Display -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-lg p-4 mb-6">
                        <p class="text-sm text-gray-600 mb-1">Slot yang dipilih:</p>
                        <p id="selectedScheduleDisplay" class="text-lg font-bold text-gray-900"></p>
                    </div>

                    <!-- Participant Count -->
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-3">Jumlah Peserta *</label>
                        <div class="flex items-center gap-4">
                            <button type="button" onclick="decrementParticipants()" class="w-12 h-12 bg-gray-200 text-gray-900 font-bold text-xl rounded-lg hover:bg-gray-300">
                                −
                            </button>
                            <input 
                                type="number" 
                                id="numParticipants"
                                name="number_of_participants"
                                min="1"
                                max="100"
                                value="1"
                                class="flex-1 px-4 py-3 border-2 border-blue-300 rounded-lg text-center text-2xl font-bold focus:ring-2 focus:ring-blue-500"
                                onchange="updatePrice(); validateStep2();"
                                required
                            >
                            <button type="button" onclick="incrementParticipants()" class="w-12 h-12 bg-blue-600 text-white font-bold text-xl rounded-lg hover:bg-blue-700">
                                +
                            </button>
                        </div>
                    </div>

                    <!-- Price Display -->
                    <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-4 mb-6">
                        <p class="text-sm text-gray-600 mb-1">Total Harga:</p>
                        <p class="text-3xl font-bold text-yellow-700">
                            Rp <span id="totalPrice">0</span>
                        </p>
                        <p class="text-xs text-gray-500 mt-2">Harga workshop (fix): Rp <span id="unitPrice">0</span> • Jumlah peserta: <span id="displayParticipants">1</span></p>

                    </div>

                    <div class="flex gap-3">
                        <button type="button" onclick="prevStep(2)" class="flex-1 px-6 py-3 bg-gray-300 text-gray-800 font-bold rounded-lg hover:bg-gray-400 transition-all">
                            ← Kembali
                        </button>
                        <button type="button" onclick="nextStep(2)" class="flex-1 px-6 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition-all">
                            Lanjutkan → Step 3
                        </button>
                    </div>
                </div>

                <!-- STEP 3: PROFIL (Auto-filled for logged in) -->
                <div class="step-content hidden" data-step="3">
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">👤 Langkah 3: Data Profil</h2>
                        <p class="text-gray-600">Informasi peserta utama (sudah terisi dari profil Anda)</p>
                    </div>

                    @auth
                        <!-- For Logged In Users -->
                        <div class="space-y-4 mb-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap *</label>
                                <input 
                                    type="text" 
                                    id="customerName"
                                    name="customer_name"
                                    value="{{ auth()->user()->name ?? '' }}"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500"
                                    required
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Email *</label>
                                <input 
                                    type="email" 
                                    id="customerEmail"
                                    name="customer_email"
                                    value="{{ auth()->user()->email ?? '' }}"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500"
                                    required
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Nomor HP *</label>
                                <input 
                                    type="tel" 
                                    id="customerPhone"
                                    name="customer_phone"
                                    value="{{ auth()->user()->phone ?? '' }}"
                                    placeholder="08xx-xxxx-xxxx"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    required
                                >
                            </div>
                        </div>

                        <div class="bg-green-50 border-l-4 border-green-600 rounded p-4 mb-6">
                            <p class="text-sm text-green-900">✅ Data sudah terisi dari profil Anda. Ubah jika diperlukan.</p>
                        </div>
                    @else
                        <!-- For Guest Users -->
                        <div class="space-y-4 mb-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap *</label>
                                <input 
                                    type="text" 
                                    id="customerName"
                                    name="customer_name"
                                    placeholder="Masukkan nama lengkap Anda"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    required
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Email *</label>
                                <input 
                                    type="email" 
                                    id="customerEmail"
                                    name="customer_email"
                                    placeholder="email@example.com"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    required
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Nomor HP *</label>
                                <input 
                                    type="tel" 
                                    id="customerPhone"
                                    name="customer_phone"
                                    placeholder="08xx-xxxx-xxxx"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    required
                                >
                            </div>
                        </div>

                        <div class="bg-blue-50 border-l-4 border-blue-600 rounded p-4 mb-6">
                            <p class="text-sm text-blue-900">ℹ️ Isi data diri yang valid untuk konfirmasi booking</p>
                        </div>
                    @endauth

                    <div class="flex gap-3">
                        <button type="button" onclick="prevStep(3)" class="flex-1 px-6 py-3 bg-gray-300 text-gray-800 font-bold rounded-lg hover:bg-gray-400 transition-all">
                            ← Kembali
                        </button>
                        <button type="button" onclick="nextStep(3)" class="flex-1 px-6 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition-all">
                            Lanjutkan → Step 4
                        </button>
                    </div>
                </div>

                <!-- STEP 4: PILIH ALAMAT -->
                <div class="step-content hidden" data-step="4">
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">📍 Langkah 4: Alamat Pengiriman</h2>
                        <p class="text-gray-600">Pilih alamat untuk pengiriman hasil workshop (jika ada)</p>
                    </div>

                    @auth
                        <!-- Saved Addresses -->
                        <div id="addressList" class="space-y-3 mb-6">
                            <div class="text-center py-8 text-gray-500">⏳ Loading alamat...</div>
                        </div>

                        <!-- Add New Address -->
                        <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-4 mb-6">
                            <button type="button" onclick="showAddAddressForm()" class="w-full text-blue-600 font-bold hover:text-blue-800">
                                ➕ Tambah Alamat Baru
                            </button>
                        </div>

                        <!-- New Address Form (hidden) -->
                        <div id="newAddressForm" class="hidden bg-blue-50 border-2 border-blue-200 rounded-lg p-4 mb-6">
                            <h3 class="font-bold text-gray-900 mb-4">Alamat Baru:</h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Alamat Lengkap *</label>
                                    <input type="text" id="newAddressStreet" placeholder="Jl. Contoh No. 123" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" required>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Kota *</label>
                                        <input type="text" id="newAddressCity" placeholder="Cilacap" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Provinsi *</label>
                                        <input type="text" id="newAddressProvince" placeholder="Jawa Tengah" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" required>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Kode Pos</label>
                                    <input type="text" id="newAddressZip" placeholder="53200" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                </div>
                                <div class="flex gap-2">
                                    <button type="button" onclick="cancelAddAddress()" class="flex-1 px-3 py-2 bg-gray-300 text-gray-800 font-bold text-sm rounded-lg hover:bg-gray-400">Batal</button>
                                    <button type="button" onclick="saveNewAddress()" class="flex-1 px-3 py-2 bg-blue-600 text-white font-bold text-sm rounded-lg hover:bg-blue-700">Simpan</button>
                                </div>
                            </div>
                        </div>

                        <div class="bg-blue-50 border-l-4 border-blue-600 rounded p-4 mb-6">
                            <p class="text-sm text-blue-900">💡 Pilih salah satu alamat simpanan atau tambah baru</p>
                        </div>
                    @else
                        <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-4 mb-6">
                            <p class="text-sm text-yellow-900">
                                <strong>⚠️ Anda belum login.</strong> Harap login terlebih dahulu untuk memilih atau menyimpan alamat pengiriman.
                            </p>
                            <a href="{{ route('login') }}" class="inline-block mt-3 px-4 py-2 bg-yellow-600 text-white font-bold rounded-lg hover:bg-yellow-700">
                                🔐 Login Sekarang
                            </a>
                        </div>
                    @endauth

                    <div class="flex gap-3">
                        <button type="button" onclick="prevStep(4)" class="flex-1 px-6 py-3 bg-gray-300 text-gray-800 font-bold rounded-lg hover:bg-gray-400 transition-all">
                            ← Kembali
                        </button>
                        <button type="button" onclick="nextStep(4)" class="flex-1 px-6 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition-all">
                            Lanjutkan → Step 5
                        </button>
                    </div>
                </div>

                <!-- STEP 5: REVIEW & BAYAR -->
                <div class="step-content hidden" data-step="5">
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">💳 Langkah 5: Review & Bayar</h2>
                        <p class="text-gray-600">Periksa kembali semua data, kemudian lanjutkan ke pembayaran</p>
                    </div>

                    <!-- Summary -->
                    <div class="space-y-4 mb-8">
                        <!-- Workshop Info -->
                        <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border-2 border-indigo-200 rounded-lg p-4">
                            <p class="text-xs text-gray-600 mb-1">Workshop:</p>
                            <p id="summaryWorkshop" class="text-lg font-bold text-gray-900"></p>
                            <p id="summarySchedule" class="text-sm text-gray-700 mt-1"></p>
                        </div>

                        <!-- Participants & Price -->
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-lg p-4">
                            <p class="text-xs text-gray-600 mb-2">Detail Peserta:</p>
                            <p class="text-sm font-bold text-gray-900 mb-1">👥 <span id="summaryParticipants">1</span> Peserta</p>
                            <p class="text-sm font-bold text-gray-900">💰 Rp <span id="summaryPrice">0</span></p>
                        </div>

                        <!-- Personal Info -->
                        <div class="bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-purple-200 rounded-lg p-4">
                            <p class="text-xs text-gray-600 mb-2">Atas Nama:</p>
                            <p id="summaryName" class="text-sm font-bold text-gray-900 mb-1"></p>
                            <p id="summaryEmail" class="text-sm text-gray-700"></p>
                            <p id="summaryPhone" class="text-sm text-gray-700"></p>
                        </div>

                        <!-- Address -->
                        <div class="bg-gradient-to-r from-orange-50 to-red-50 border-2 border-orange-200 rounded-lg p-4">
                            <p class="text-xs text-gray-600 mb-2">Alamat Pengiriman:</p>
                            <p id="summaryAddress" class="text-sm text-gray-900"></p>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <div class="mb-8">
                        <h3 class="font-bold text-gray-900 mb-4">Pilih Metode Pembayaran:</h3>
                        <div class="space-y-2">
                            <label class="flex items-center p-3 border-2 border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 cursor-pointer">
                                <input type="radio" name="payment_method" value="direct_transfer" class="w-4 h-4 text-blue-600" required>
                                <span class="ml-3 font-bold text-gray-900">💳 Transfer Bank / E-Wallet</span>
                            </label>
                            <label class="flex items-center p-3 border-2 border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 cursor-pointer">
                                <input type="radio" name="payment_method" value="tripay" class="w-4 h-4 text-blue-600" required>
                                <span class="ml-3 font-bold text-gray-900">🏦 Tripay (Virtual Account, E-Wallet)</span>
                            </label>
                        </div>
                    </div>

                    <!-- T&C -->
                    <div class="bg-gray-50 border border-gray-300 rounded-lg p-4 mb-8">
                        <label class="flex items-start cursor-pointer">
                            <input type="checkbox" id="agreeTC" name="agree_terms" class="w-4 h-4 text-blue-600 mt-1" required>
                            <span class="ml-2 text-sm text-gray-700">
                                Saya menyetujui <strong>Syarat & Ketentuan</strong> serta <strong>Kebijakan Privasi</strong> untuk booking workshop ini
                            </span>
                        </label>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3">
                        <button type="button" onclick="prevStep(5)" class="flex-1 px-6 py-3 bg-gray-300 text-gray-800 font-bold rounded-lg hover:bg-gray-400 transition-all">
                            ← Kembali
                        </button>
                        <button type="submit" id="submitBtn" class="flex-1 px-6 py-4 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-bold text-lg rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all">
                            ✅ Lanjutkan ke Pembayaran
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentStep = 1;
        let selectedSchedule = null;

        // Load schedules on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadSchedules();
        });

        function loadSchedules() {
            fetch('/api/workshops/active')
                .then(r => r.json())
                .then(workshops => {
                    if (!workshops.length) {
                        document.getElementById('scheduleError').textContent = 'Tidak ada workshop tersedia saat ini.';
                        document.getElementById('scheduleError').classList.remove('hidden');
                        return;
                    }

                    const scheduleList = document.getElementById('scheduleList');
                    scheduleList.innerHTML = '';

                    workshops.forEach(workshop => {
                        fetch(`/workshop/${workshop.id}/available-schedules`)
                            .then(r => r.json())
                            .then(schedules => {
                                schedules.forEach(schedule => {
                                    const card = document.createElement('div');
                                    card.className = 'border-2 border-gray-300 rounded-lg p-4 cursor-pointer hover:border-blue-600 hover:bg-blue-50 transition-all schedule-card';
                                    card.onclick = () => selectSchedule(schedule, workshop);
                                    
                                    card.innerHTML = `
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <h3 class="font-bold text-gray-900">${workshop.title}</h3>
                                                <p class="text-sm text-gray-600 mt-1">📅 ${schedule.date}</p>
                                                <p class="text-sm text-gray-600">⏰ ${schedule.time_slot_name} (${schedule.start_time} - ${schedule.end_time})</p>
                                                <p class="text-sm text-gray-600 mt-2">📍 ${workshop.location}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-lg font-bold text-yellow-600">Rp ${parseInt(workshop.amount).toLocaleString('id-ID')}</p>
                                                <p class="text-xs text-gray-600 mt-1">Sudah booking: ${schedule.booked_count ?? 0} peserta</p>
                                            </div>
                                        </div>
                                    `;
                                    scheduleList.appendChild(card);
                                });
                            });
                    });
                })
                .catch(err => {
                    document.getElementById('scheduleError').textContent = 'Gagal memuat jadwal: ' + err.message;
                    document.getElementById('scheduleError').classList.remove('hidden');
                });
        }

        function selectSchedule(schedule, workshop) {
            selectedSchedule = {schedule, workshop};
            document.getElementById('workshopId').value = workshop.id;
            document.getElementById('scheduleId').value = schedule.id;
            document.getElementById('workshopName').value = workshop.title;
            document.getElementById('workshopPrice').value = workshop.amount;
            document.getElementById('scheduleTime').value = `${schedule.date} ${schedule.time_slot_name}`;

            // Update display for step 2
            document.getElementById('selectedScheduleDisplay').innerHTML = `
                <strong>${workshop.title}</strong><br>
                📅 ${schedule.date} • ⏰ ${schedule.time_slot_name} (${schedule.start_time}-${schedule.end_time})<br>
                📍 ${workshop.location}<br>
                💰 Rp ${parseInt(workshop.amount).toLocaleString('id-ID')} (harga fix)

            `;

            // Visual feedback
            document.querySelectorAll('.schedule-card').forEach(card => card.classList.remove('border-blue-600', 'bg-blue-50'));
            event.currentTarget.classList.add('border-blue-600', 'bg-blue-50');

            updatePrice();
        }

        function incrementParticipants() {
            const input = document.getElementById('numParticipants');
            input.value = parseInt(input.value) + 1;
            updatePrice();
            validateStep2();
        }

        function decrementParticipants() {
            const input = document.getElementById('numParticipants');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
                updatePrice();
                validateStep2();
            }
        }

        function updatePrice() {
            if (!selectedSchedule) return;
            const num = parseInt(document.getElementById('numParticipants').value) || 1;
            const price = parseInt(selectedSchedule.workshop.amount);

            
            document.getElementById('unitPrice').textContent = parseInt(selectedSchedule.workshop.amount).toLocaleString('id-ID');
            document.getElementById('displayParticipants').textContent = num;
            document.getElementById('totalPrice').textContent = price.toLocaleString('id-ID');
            document.getElementById('numParticipants').value = num;
        }

        function validateStep2() {
            return selectedSchedule && parseInt(document.getElementById('numParticipants').value) >= 1;
        }

        function nextStep(current) {
            // Validate before moving
            if (current === 1 && !selectedSchedule) {
                alert('Pilih slot terlebih dahulu!');
                return;
            }
            if (current === 2 && !validateStep2()) {
                alert('Input jumlah peserta yang valid!');
                return;
            }

            // Update summary if moving to step 5
            if (current === 4) {
                updateSummary();
            }

            showStep(current + 1);
        }

        function prevStep(current) {
            showStep(current - 1);
        }

        function showStep(step) {
            currentStep = step;
            document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));
            document.querySelector(`[data-step="${step}"]`).classList.remove('hidden');

            // Update step indicators
            document.querySelectorAll('.step-indicator').forEach(el => {
                const s = parseInt(el.getAttribute('data-step'));
                el.classList.toggle('bg-blue-600', s <= step);
                el.classList.toggle('bg-gray-300', s > step);
            });

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function updateSummary() {
            if (!selectedSchedule) return;

            const num = parseInt(document.getElementById('numParticipants').value);
            const price = parseInt(selectedSchedule.workshop.amount);


            document.getElementById('summaryWorkshop').textContent = selectedSchedule.workshop.title;
            document.getElementById('summarySchedule').textContent = 
                `${selectedSchedule.schedule.date} • ${selectedSchedule.schedule.time_slot_name} (${selectedSchedule.schedule.start_time}-${selectedSchedule.schedule.end_time})`;
            document.getElementById('summaryParticipants').textContent = num;
            document.getElementById('summaryPrice').textContent = price.toLocaleString('id-ID');
            document.getElementById('summaryName').textContent = document.getElementById('customerName').value || '-';
            document.getElementById('summaryEmail').textContent = document.getElementById('customerEmail').value || '-';
            document.getElementById('summaryPhone').textContent = document.getElementById('customerPhone').value || '-';
        }

        // Address Management
        @auth
        function loadAddresses() {
            fetch('/api/user/addresses')
                .then(r => r.json())
                .then(addresses => {
                    const list = document.getElementById('addressList');
                    list.innerHTML = '';
                    if (!addresses.length) {
                        list.innerHTML = '<p class="text-gray-500 text-center py-4">Belum ada alamat tersimpan. Tambah alamat baru.</p>';
                        return;
                    }
                    addresses.forEach(addr => {
                        const label = document.createElement('label');
                        label.className = 'flex items-start p-3 border-2 border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 cursor-pointer';
                        label.innerHTML = `
                            <input type="radio" name="address_id" value="${addr.id}" class="w-4 h-4 text-blue-600 mt-1" onchange="selectAddress(${addr.id})">
                            <div class="ml-3 flex-1">
                                <p class="font-bold text-gray-900">${addr.label || 'Alamat'}</p>
                                <p class="text-sm text-gray-700">${addr.address}</p>
                                <p class="text-xs text-gray-600">${addr.city}, ${addr.province} ${addr.zip}</p>
                            </div>
                        `;
                        list.appendChild(label);
                    });
                });
        }

        function selectAddress(id) {
            document.getElementById('selectedAddressId').value = id;
            const selected = document.querySelector(`input[value="${id}"]`).closest('label');
            document.querySelectorAll('#addressList label').forEach(l => l.classList.remove('border-blue-500', 'bg-blue-50'));
            selected.classList.add('border-blue-500', 'bg-blue-50');
            
            // Update summary
            const addr = document.querySelector(`input[value="${id}"]`).closest('label').textContent;
            document.getElementById('summaryAddress').textContent = addr.trim();
        }

        function showAddAddressForm() {
            document.getElementById('newAddressForm').classList.remove('hidden');
        }

        function cancelAddAddress() {
            document.getElementById('newAddressForm').classList.add('hidden');
        }

        function saveNewAddress() {
            const data = {
                street: document.getElementById('newAddressStreet').value,
                city: document.getElementById('newAddressCity').value,
                province: document.getElementById('newAddressProvince').value,
                zip: document.getElementById('newAddressZip').value,
            };

            if (!data.street || !data.city || !data.province) {
                alert('Isi semua field yang diperlukan!');
                return;
            }

            fetch('/api/user/addresses', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('[name=_token]').value},
                body: JSON.stringify(data)
            })
            .then(r => r.json())
            .then(data => {
                loadAddresses();
                document.getElementById('newAddressForm').classList.add('hidden');
                alert('Alamat berhasil ditambahkan!');
            });
        }

        // Load addresses on page load
        document.addEventListener('DOMContentLoaded', loadAddresses);
        @endauth
    </script>
</x-layouts.landing>
