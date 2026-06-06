<x-layouts.landing title="Pesan Workshop">
    <div class="min-h-screen bg-white py-8">
        <div class="max-w-lg mx-auto px-4">
            <!-- Progress -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-3">
                    <h1 class="text-2xl font-bold text-gray-900">🎨 Pesan Workshop</h1>
                    <span class="text-sm font-bold text-blue-600">Langkah <span id="stepNum">1</span>/5</span>
                </div>
                <div class="w-full bg-gray-200 h-2 rounded-full overflow-hidden">
                    <div id="progressBar" class="bg-blue-600 h-full transition-all" style="width: 20%"></div>
                </div>
            </div>

            <form id="bookingForm" method="POST" action="{{ route('workshop.booking.store') }}">
                @csrf
                <input type="hidden" id="workshopId" name="workshop_id">
                <input type="hidden" id="scheduleId" name="workshop_slot_schedule_id">
                <input type="hidden" id="addressId" name="address_id">

                <!-- STEP 1: PILIH SLOT -->
                <div class="step-container" data-step="1">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">📅 Langkah 1: Pilih Slot Waktu</h2>
                    <p class="text-gray-600 mb-6">Pilih jadwal workshop yang sesuai dengan Anda</p>

                    <div id="slotsList" class="space-y-3 mb-6">
                        <div class="text-center py-8 text-gray-500">⏳ Memuat jadwal...</div>
                    </div>

                    <button type="button" onclick="goToStep(2)" class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700">
                        Lanjutkan ➜
                    </button>
                </div>

                <!-- STEP 2: INPUT PESERTA -->
                <div class="step-container hidden" data-step="2">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">👥 Langkah 2: Berapa Peserta?</h2>

                    <div class="bg-blue-50 border-l-4 border-blue-600 p-4 rounded mb-6">
                        <p class="text-sm text-gray-700" id="selectedSlotDisplay">-</p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-3">Jumlah Peserta</label>
                        <div class="flex items-center gap-4">
                            <button type="button" onclick="decrementCount()" class="w-12 h-12 bg-gray-300 text-gray-900 font-bold text-xl rounded hover:bg-gray-400">−</button>
                            <input type="number" id="participantCount" name="number_of_participants" value="1" min="1" max="100" class="flex-1 text-center text-2xl font-bold border-2 border-blue-300 rounded py-2" onchange="updatePrice()">
                            <button type="button" onclick="incrementCount()" class="w-12 h-12 bg-blue-600 text-white font-bold text-xl rounded hover:bg-blue-700">+</button>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded mb-6">
                        <p class="text-sm text-gray-700 mb-1">Total Harga:</p>
                        <p class="text-3xl font-bold text-yellow-600">Rp <span id="totalPrice">0</span></p>
                        <p class="text-xs text-gray-600 mt-2">= Rp <span id="unitPrice">0</span> × <span id="countDisplay">1</span> peserta</p>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" onclick="goToStep(1)" class="flex-1 bg-gray-300 text-gray-900 font-bold py-3 rounded hover:bg-gray-400">← Kembali</button>
                        <button type="button" onclick="goToStep(3)" class="flex-1 bg-blue-600 text-white font-bold py-3 rounded hover:bg-blue-700">Lanjutkan ➜</button>
                    </div>
                </div>

                <!-- STEP 3: PROFIL -->
                <div class="step-container hidden" data-step="3">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">👤 Langkah 3: Data Diri</h2>

                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap *</label>
                            <input type="text" id="customerName" name="customer_name" value="{{ auth()->user()->name ?? '' }}" class="w-full px-4 py-2 border-2 border-gray-300 rounded focus:border-blue-500 focus:outline-none" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Email *</label>
                            <input type="email" id="customerEmail" name="customer_email" value="{{ auth()->user()->email ?? '' }}" class="w-full px-4 py-2 border-2 border-gray-300 rounded focus:border-blue-500 focus:outline-none" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nomor HP *</label>
                            <input type="tel" id="customerPhone" name="customer_phone" value="{{ auth()->user()->phone ?? '' }}" class="w-full px-4 py-2 border-2 border-gray-300 rounded focus:border-blue-500 focus:outline-none" required>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" onclick="goToStep(2)" class="flex-1 bg-gray-300 text-gray-900 font-bold py-3 rounded hover:bg-gray-400">← Kembali</button>
                        <button type="button" onclick="goToStep(4)" class="flex-1 bg-blue-600 text-white font-bold py-3 rounded hover:bg-blue-700">Lanjutkan ➜</button>
                    </div>
                </div>

                <!-- STEP 4: ALAMAT -->
                <div class="step-container hidden" data-step="4">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">📍 Langkah 4: Alamat Pengiriman</h2>

                    @auth
                        <div id="addressList" class="space-y-3 mb-6">
                            <div class="text-center py-6 text-gray-500">⏳ Memuat alamat...</div>
                        </div>

                        <button type="button" onclick="showAddAddressModal()" class="w-full mb-6 p-3 border-2 border-dashed border-gray-400 text-blue-600 font-bold rounded hover:border-blue-600 hover:bg-blue-50">
                            + Tambah Alamat Baru
                        </button>
                    @else
                        <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded mb-6">
                            <p class="text-sm text-orange-900 font-bold mb-3">⚠️ Anda belum login</p>
                            <p class="text-sm text-orange-800 mb-4">Silakan login untuk menyimpan dan memilih alamat.</p>
                            <a href="{{ route('login') }}" class="inline-block px-6 py-2 bg-orange-600 text-white font-bold rounded hover:bg-orange-700">🔐 Login Sekarang</a>
                        </div>
                    @endauth

                    <div class="flex gap-3">
                        <button type="button" onclick="goToStep(3)" class="flex-1 bg-gray-300 text-gray-900 font-bold py-3 rounded hover:bg-gray-400">← Kembali</button>
                        <button type="button" onclick="goToStep(5)" class="flex-1 bg-blue-600 text-white font-bold py-3 rounded hover:bg-blue-700">Lanjutkan ➜</button>
                    </div>
                </div>

                <!-- STEP 5: BAYAR -->
                <div class="step-container hidden" data-step="5">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">💳 Langkah 5: Konfirmasi & Bayar</h2>

                    <!-- Summary Cards -->
                    <div class="space-y-3 mb-6">
                        <!-- Workshop Card -->
                        <div class="bg-blue-50 border-l-4 border-blue-600 p-4 rounded">
                            <p class="text-xs text-gray-600 mb-1">Workshop & Jadwal:</p>
                            <p id="summaryWorkshop" class="font-bold text-gray-900">-</p>
                            <p id="summarySchedule" class="text-sm text-gray-700 mt-1">-</p>
                        </div>

                        <!-- Peserta & Harga Card -->
                        <div class="bg-green-50 border-l-4 border-green-600 p-4 rounded">
                            <p class="text-sm font-bold text-gray-900 mb-1">👥 <span id="summaryCount">1</span> Peserta</p>
                            <p class="text-2xl font-bold text-green-600">Rp <span id="summaryPrice">0</span></p>
                        </div>

                        <!-- Data Card -->
                        <div class="bg-purple-50 border-l-4 border-purple-600 p-4 rounded">
                            <p class="text-xs text-gray-600 mb-2">Atas Nama:</p>
                            <p id="summaryName" class="font-bold text-gray-900 mb-2">-</p>
                            <p id="summaryEmail" class="text-sm text-gray-700 mb-1">-</p>
                            <p id="summaryPhone" class="text-sm text-gray-700">-</p>
                        </div>

                        <!-- Alamat Card -->
                        <div class="bg-orange-50 border-l-4 border-orange-600 p-4 rounded">
                            <p class="text-xs text-gray-600 mb-1">Alamat Pengiriman:</p>
                            <p id="summaryAddress" class="text-sm text-gray-900">-</p>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-3">Metode Pembayaran:</label>
                        <div class="space-y-2">
                            <label class="flex items-center p-3 border-2 border-gray-300 rounded cursor-pointer hover:border-blue-600 hover:bg-blue-50">
                                <input type="radio" name="payment_method" value="direct_transfer" class="w-4 h-4" required>
                                <span class="ml-3 font-bold text-gray-900">💳 Transfer Bank / E-Wallet</span>
                            </label>
                            <label class="flex items-center p-3 border-2 border-gray-300 rounded cursor-pointer hover:border-blue-600 hover:bg-blue-50">
                                <input type="radio" name="payment_method" value="tripay" class="w-4 h-4">
                                <span class="ml-3 font-bold text-gray-900">🏦 Tripay (VA, E-Wallet)</span>
                            </label>
                        </div>
                    </div>

                    <!-- T&C -->
                    <label class="flex items-start mb-6 cursor-pointer">
                        <input type="checkbox" name="agree_terms" class="w-4 h-4 mt-1" required>
                        <span class="ml-2 text-sm text-gray-700">Saya setuju dengan Syarat & Ketentuan</span>
                    </label>

                    <!-- Buttons -->
                    <div class="flex gap-3">
                        <button type="button" onclick="goToStep(4)" class="flex-1 bg-gray-300 text-gray-900 font-bold py-3 rounded hover:bg-gray-400">← Kembali</button>
                        <button type="submit" class="flex-1 bg-green-600 text-white font-bold py-3 rounded hover:bg-green-700 text-lg">✅ Pesan Sekarang</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah Alamat -->
    <div id="addressModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg p-6 max-w-sm w-full">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Tambah Alamat Baru</h3>
            <div class="space-y-3 mb-4">
                <input type="text" id="newAddressStreet" placeholder="Jalan dan Nomor" class="w-full px-3 py-2 border-2 border-gray-300 rounded text-sm">
                <input type="text" id="newAddressCity" placeholder="Kota" class="w-full px-3 py-2 border-2 border-gray-300 rounded text-sm">
                <input type="text" id="newAddressProvince" placeholder="Provinsi" class="w-full px-3 py-2 border-2 border-gray-300 rounded text-sm">
                <input type="text" id="newAddressZip" placeholder="Kode Pos" class="w-full px-3 py-2 border-2 border-gray-300 rounded text-sm">
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeAddAddressModal()" class="flex-1 bg-gray-300 text-gray-900 font-bold py-2 rounded">Batal</button>
                <button type="button" onclick="saveNewAddress()" class="flex-1 bg-blue-600 text-white font-bold py-2 rounded hover:bg-blue-700">Simpan</button>
            </div>
        </div>
    </div>

    <script>
        let currentStep = 1;
        let selectedSchedule = null;
        let selectedAddress = null;

        // Load schedules on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadSchedules();
            @auth
            loadAddresses();
            @endauth
        });

        function updateProgress() {
            const percent = (currentStep / 5) * 100;
            document.getElementById('progressBar').style.width = percent + '%';
            document.getElementById('stepNum').textContent = currentStep;
        }

        function goToStep(step) {
            // Validate current step before moving
            if (currentStep === 1 && !selectedSchedule) {
                alert('Pilih slot terlebih dahulu!');
                return;
            }
            if (currentStep === 2) {
                const count = parseInt(document.getElementById('participantCount').value);
                if (!count || count < 1) {
                    alert('Input jumlah peserta yang valid!');
                    return;
                }
            }
            if (currentStep === 3) {
                if (!document.getElementById('customerName').value || !document.getElementById('customerEmail').value || !document.getElementById('customerPhone').value) {
                    alert('Isi semua data diri!');
                    return;
                }
            }
            if (currentStep === 4 && @auth true @else false @endauth) {
                if (!selectedAddress) {
                    alert('Pilih alamat pengiriman!');
                    return;
                }
            }

            // Update summary before showing step 5
            if (step === 5) {
                updateSummary();
            }

            currentStep = step;
            showCurrentStep();
            updateProgress();
            window.scrollTo(0, 0);
        }

        function showCurrentStep() {
            document.querySelectorAll('.step-container').forEach(el => el.classList.add('hidden'));
            document.querySelector(`[data-step="${currentStep}"]`).classList.remove('hidden');
        }

        function loadSchedules() {
            fetch('/api/workshops/active')
                .then(r => r.json())
                .then(workshops => {
                    const list = document.getElementById('slotsList');
                    list.innerHTML = '';

                    workshops.forEach(workshop => {
                        fetch(`/workshop/${workshop.id}/available-schedules`)
                            .then(r => r.json())
                            .then(schedules => {
                                schedules.forEach(schedule => {
                                    const card = document.createElement('div');
                                    card.className = 'p-4 border-2 border-gray-300 rounded cursor-pointer hover:border-blue-600 hover:bg-blue-50 transition-all schedule-option';
                                    card.onclick = () => selectSlot(schedule, workshop);
                                    card.innerHTML = `
                                        <p class="font-bold text-gray-900">${workshop.title}</p>
                                        <p class="text-sm text-gray-700">📅 ${schedule.date}</p>
                                        <p class="text-sm text-gray-700">⏰ ${schedule.time_slot_name} (${schedule.start_time}-${schedule.end_time})</p>
                                        <p class="text-sm text-gray-700">📍 ${workshop.location}</p>
                                        <p class="font-bold text-yellow-600 mt-2">Rp ${parseInt(workshop.amount).toLocaleString('id-ID')}</p>
                                    `;
                                    list.appendChild(card);
                                });
                            });
                    });
                });
        }

        function selectSlot(schedule, workshop) {
            selectedSchedule = {schedule, workshop};
            document.getElementById('workshopId').value = workshop.id;
            document.getElementById('scheduleId').value = schedule.id;

            document.querySelectorAll('.schedule-option').forEach(el => el.classList.remove('border-blue-600', 'bg-blue-50'));
            event.currentTarget.classList.add('border-blue-600', 'bg-blue-50');

            document.getElementById('selectedSlotDisplay').innerHTML = `
                <strong>${workshop.title}</strong><br>
                ${schedule.date} • ${schedule.time_slot_name} (${schedule.start_time}-${schedule.end_time})<br>
                📍 ${workshop.location}
            `;

            updatePrice();
        }

        function incrementCount() {
            const input = document.getElementById('participantCount');
            input.value = parseInt(input.value) + 1;
            updatePrice();
        }

        function decrementCount() {
            const input = document.getElementById('participantCount');
            if (parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;
            updatePrice();
        }

        function updatePrice() {
            if (!selectedSchedule) return;
            const count = parseInt(document.getElementById('participantCount').value);
            const unitPrice = parseInt(selectedSchedule.workshop.amount);
            const total = unitPrice * count;

            document.getElementById('unitPrice').textContent = unitPrice.toLocaleString('id-ID');
            document.getElementById('countDisplay').textContent = count;
            document.getElementById('totalPrice').textContent = total.toLocaleString('id-ID');
            document.getElementById('participantCount').value = count;
        }

        function updateSummary() {
            if (!selectedSchedule) return;

            const count = parseInt(document.getElementById('participantCount').value);
            const total = parseInt(selectedSchedule.workshop.amount) * count;

            document.getElementById('summaryWorkshop').textContent = selectedSchedule.workshop.title;
            document.getElementById('summarySchedule').textContent = `${selectedSchedule.schedule.date} • ${selectedSchedule.schedule.time_slot_name} (${selectedSchedule.schedule.start_time}-${selectedSchedule.schedule.end_time})`;
            document.getElementById('summaryCount').textContent = count;
            document.getElementById('summaryPrice').textContent = total.toLocaleString('id-ID');
            document.getElementById('summaryName').textContent = document.getElementById('customerName').value;
            document.getElementById('summaryEmail').textContent = document.getElementById('customerEmail').value;
            document.getElementById('summaryPhone').textContent = document.getElementById('customerPhone').value;
            document.getElementById('summaryAddress').textContent = selectedAddress ? selectedAddress.address : '(Tidak dipilih)';
        }

        @auth
        function loadAddresses() {
            fetch('/api/user/addresses')
                .then(r => r.json())
                .then(addresses => {
                    const list = document.getElementById('addressList');
                    list.innerHTML = '';

                    if (!addresses.length) {
                        list.innerHTML = '<p class="text-center text-gray-500 py-4">Belum ada alamat tersimpan</p>';
                        return;
                    }

                    addresses.forEach(addr => {
                        const label = document.createElement('label');
                        label.className = 'flex items-start p-3 border-2 border-gray-300 rounded cursor-pointer hover:border-blue-600 hover:bg-blue-50';
                        label.innerHTML = `
                            <input type="radio" name="address_id" value="${addr.id}" class="w-4 h-4 mt-1" onchange="selectAddress('${addr.id}', '${addr.street}, ${addr.city}, ${addr.province}')">
                            <div class="ml-3 flex-1">
                                <p class="font-bold text-gray-900">${addr.label || 'Alamat'}</p>
                                <p class="text-sm text-gray-700">${addr.street}</p>
                                <p class="text-xs text-gray-600">${addr.city}, ${addr.province}</p>
                            </div>
                        `;
                        list.appendChild(label);
                    });
                });
        }

        function selectAddress(id, address) {
            selectedAddress = {id, address};
            document.getElementById('addressId').value = id;
        }

        function showAddAddressModal() {
            document.getElementById('addressModal').classList.remove('hidden');
        }

        function closeAddAddressModal() {
            document.getElementById('addressModal').classList.add('hidden');
        }

        function saveNewAddress() {
            const data = {
                street: document.getElementById('newAddressStreet').value,
                city: document.getElementById('newAddressCity').value,
                province: document.getElementById('newAddressProvince').value,
                zip: document.getElementById('newAddressZip').value,
            };

            if (!data.street || !data.city || !data.province) {
                alert('Isi semua field!');
                return;
            }

            fetch('/api/user/addresses', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('[name=_token]').value
                },
                body: JSON.stringify(data)
            })
            .then(r => r.json())
            .then(addr => {
                closeAddAddressModal();
                loadAddresses();
                alert('Alamat berhasil ditambahkan!');
            });
        }
        @endauth
    </script>
</x-layouts.landing>
