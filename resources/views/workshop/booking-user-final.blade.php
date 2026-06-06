<x-layouts.landing title="Pesan Workshop">
    <div class="min-h-screen bg-white py-12">
        <div class="max-w-xl mx-auto px-4">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">🎨 Pesan Workshop</h1>
                <p class="text-gray-600 mt-2">Pilih jadwal → Bayar. Selesai!</p>
            </div>

            <form id="bookingForm" method="POST" action="{{ route('workshop.booking.store') }}">
                @csrf
                <input type="hidden" id="workshopId" name="workshop_id">
                <input type="hidden" id="scheduleId" name="workshop_slot_schedule_id">
                <input type="hidden" id="numParticipants" name="number_of_participants" value="1">
                <input type="hidden" id="unitPrice" name="unit_price">
                <input type="hidden" id="addressId" name="address_id">

                @guest
                    <!-- Warning untuk Guest -->
                    <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded-lg mb-8">
                        <p class="text-sm text-orange-900 font-bold mb-2">⚠️ Anda Belum Login</p>
                        <p class="text-sm text-orange-800 mb-4">Silakan login terlebih dahulu untuk melakukan booking.</p>
                        <a href="{{ route('login') }}" class="inline-block px-6 py-2 bg-orange-600 text-white font-bold rounded-lg hover:bg-orange-700">
                            🔐 Login Sekarang
                        </a>
                    </div>
                @else
                    <!-- Step 1: PILIH SLOT -->
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">📅 Pilih Jadwal Workshop</h2>
                        <p class="text-gray-600 mb-6">Pilih salah satu jadwal yang tersedia</p>

                        <div id="schedulesList" class="space-y-3 mb-8">
                            <div class="text-center py-8 text-gray-500">⏳ Memuat jadwal...</div>
                        </div>
                    </div>

                    <!-- Step 2: REVIEW & BAYAR -->
                    <div id="paymentSection" style="display: none;" class="mb-8 pb-8 border-t pt-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">💳 Konfirmasi Pemesanan</h2>

                        <!-- Summary Card: Workshop -->
                        <div class="bg-blue-50 border-l-4 border-blue-600 p-4 rounded-lg mb-4">
                            <p class="text-xs text-gray-600 mb-1">Workshop & Jadwal:</p>
                            <p id="summaryWorkshop" class="text-lg font-bold text-gray-900">-</p>
                            <p id="summarySchedule" class="text-sm text-gray-700 mt-2">-</p>
                        </div>

                        <!-- Summary Card: Harga -->
                        <div class="bg-green-50 border-l-4 border-green-600 p-4 rounded-lg mb-4">
                            <p class="text-xs text-gray-600 mb-1">Total Harga:</p>
                            <p class="text-3xl font-bold text-green-600">Rp <span id="summaryPrice">0</span></p>
                            <p class="text-xs text-gray-600 mt-2" id="priceDetail">-</p>
                        </div>

                        <!-- Summary Card: Data Diri -->
                        <div class="bg-purple-50 border-l-4 border-purple-600 p-4 rounded-lg mb-4">
                            <p class="text-xs text-gray-600 mb-2">Atas Nama:</p>
                            <p class="text-sm font-bold text-gray-900 mb-3">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-600">Email: <span class="text-gray-900 font-bold">{{ auth()->user()->email }}</span></p>
                            <p class="text-xs text-gray-600">HP: <span class="text-gray-900 font-bold">{{ auth()->user()->phone ?? '-' }}</span></p>
                        </div>

                        <!-- Summary Card: Alamat -->
                        <div class="bg-orange-50 border-l-4 border-orange-600 p-4 rounded-lg mb-6">
                            <p class="text-xs text-gray-600 mb-2">Alamat Pengiriman:</p>
                            <div id="addressSection">
                                <select id="addressSelect" name="address_id" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none mb-3" onchange="updateAddressDisplay()">
                                    <option value="">-- Pilih Alamat --</option>
                                </select>
                                <p id="selectedAddressDisplay" class="text-sm text-gray-700 p-2 bg-white rounded border border-gray-300">Belum dipilih</p>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-3">Metode Pembayaran:</label>
                            <div class="space-y-2">
                                <label class="flex items-center p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-blue-600 hover:bg-blue-50 transition-all">
                                    <input type="radio" name="payment_method" value="direct_transfer" class="w-4 h-4" required>
                                    <span class="ml-3 font-bold text-gray-900">💳 Transfer Bank / E-Wallet</span>
                                </label>
                                <label class="flex items-center p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-blue-600 hover:bg-blue-50 transition-all">
                                    <input type="radio" name="payment_method" value="tripay" class="w-4 h-4">
                                    <span class="ml-3 font-bold text-gray-900">🏦 Tripay (Virtual Account, E-Wallet)</span>
                                </label>
                            </div>
                        </div>

                        <!-- T&C -->
                        <label class="flex items-start mb-8 cursor-pointer">
                            <input type="checkbox" name="agree_terms" class="w-4 h-4 mt-1" required>
                            <span class="ml-2 text-sm text-gray-700">Saya menyetujui <strong>Syarat & Ketentuan</strong> serta <strong>Kebijakan Privasi</strong></span>
                        </label>

                        <!-- Buttons -->
                        <div class="flex gap-3">
                            <button type="button" onclick="backToSchedules()" class="flex-1 px-6 py-3 bg-gray-300 text-gray-900 font-bold rounded-lg hover:bg-gray-400">
                                ← Kembali
                            </button>
                            <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-bold rounded-lg hover:from-green-700 hover:to-emerald-700 text-lg">
                                ✅ Pesan Sekarang
                            </button>
                        </div>
                    </div>
                @endguest
            </form>
        </div>
    </div>

    <script>
        let selectedSchedule = null;

        document.addEventListener('DOMContentLoaded', function() {
            loadSchedules();
            @auth
            loadAddresses();
            @endauth
        });

        function loadSchedules() {
            fetch('/api/workshops/active')
                .then(r => r.json())
                .then(workshops => {
                    const list = document.getElementById('schedulesList');
                    list.innerHTML = '';

                    if (!workshops.length) {
                        list.innerHTML = '<p class="text-center text-gray-500 py-8">Tidak ada jadwal tersedia</p>';
                        return;
                    }

                    workshops.forEach(workshop => {
                        fetch(`/workshop/${workshop.id}/available-schedules`)
                            .then(r => r.json())
                            .then(schedules => {
                                schedules.forEach(schedule => {
                                    const card = document.createElement('div');
                                    card.className = 'p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-blue-600 hover:bg-blue-50 transition-all schedule-card';
                                    card.onclick = () => selectSchedule(schedule, workshop, card);
                                    
                                    card.innerHTML = `
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <h3 class="font-bold text-gray-900 text-lg">${workshop.title}</h3>
                                                <p class="text-sm text-gray-700 mt-2">📅 ${schedule.date}</p>
                                                <p class="text-sm text-gray-700">⏰ ${schedule.time_slot_name} (${schedule.start_time} - ${schedule.end_time})</p>
                                                <p class="text-sm text-gray-700">📍 ${workshop.location}</p>
                                            </div>
                                            <div class="text-right ml-4">
                                                <p class="text-xl font-bold text-yellow-600">Rp ${parseInt(workshop.amount).toLocaleString('id-ID')}</p>
                                                <p class="text-xs text-gray-600 mt-2">Sudah booking: ${schedule.booked_count ?? 0} peserta</p>
                                            </div>
                                        </div>
                                    `;
                                    
                                    list.appendChild(card);
                                });
                            });
                    });
                })
                .catch(err => {
                    document.getElementById('schedulesList').innerHTML = '<p class="text-center text-red-600">Gagal memuat jadwal</p>';
                });
        }

        function selectSchedule(schedule, workshop, cardElement) {
            selectedSchedule = {schedule, workshop};
            
            document.getElementById('workshopId').value = workshop.id;
            document.getElementById('scheduleId').value = schedule.id;
            document.getElementById('unitPrice').value = workshop.amount;
            document.getElementById('numParticipants').value = 1;

            // Visual feedback
            document.querySelectorAll('.schedule-card').forEach(el => el.classList.remove('border-blue-600', 'bg-blue-50'));
            cardElement.classList.add('border-blue-600', 'bg-blue-50');

            // Show payment section
            updateSummary();
            document.getElementById('paymentSection').style.display = 'block';
            
            // Scroll ke payment section
            window.scrollTo({top: document.getElementById('paymentSection').offsetTop - 100, behavior: 'smooth'});
        }

        function updateSummary() {
            if (!selectedSchedule) return;

            const workshop = selectedSchedule.workshop;
            const schedule = selectedSchedule.schedule;

            document.getElementById('summaryWorkshop').textContent = workshop.title;
            document.getElementById('summarySchedule').innerHTML = `
                ${schedule.date}<br>
                ${schedule.time_slot_name} (${schedule.start_time} - ${schedule.end_time})<br>
                📍 ${workshop.location}
            `;
            
            const price = parseInt(workshop.amount);
            document.getElementById('summaryPrice').textContent = price.toLocaleString('id-ID');
            document.getElementById('priceDetail').textContent = `Harga workshop (fix): Rp ${price.toLocaleString('id-ID')} (jumlah peserta tidak mempengaruhi harga)`;

        }

        function updateAddressDisplay() {
            const select = document.getElementById('addressSelect');
            const selectedOption = select.options[select.selectedIndex];
            
            if (select.value) {
                const address = selectedOption.dataset.address;
                document.getElementById('selectedAddressDisplay').textContent = address;
                document.getElementById('addressId').value = select.value;
            } else {
                document.getElementById('selectedAddressDisplay').textContent = 'Belum dipilih';
                document.getElementById('addressId').value = '';
            }
        }

        function backToSchedules() {
            selectedSchedule = null;
            document.getElementById('paymentSection').style.display = 'none';
            document.querySelectorAll('.schedule-card').forEach(el => el.classList.remove('border-blue-600', 'bg-blue-50'));
            window.scrollTo({top: 0, behavior: 'smooth'});
        }

        @auth
        function loadAddresses() {
            fetch('/api/user/addresses')
                .then(r => r.json())
                .then(addresses => {
                    const select = document.getElementById('addressSelect');
                    select.innerHTML = '<option value="">-- Pilih Alamat --</option>';

                    if (!addresses.length) {
                        select.innerHTML += '<option disabled>Anda belum memiliki alamat tersimpan</option>';
                        return;
                    }

                    addresses.forEach(addr => {
                        const fullAddress = `${addr.street}, ${addr.city}, ${addr.province}${addr.zip ? ' ' + addr.zip : ''}`;
                        const option = document.createElement('option');
                        option.value = addr.id;
                        option.textContent = addr.label || fullAddress;
                        option.dataset.address = fullAddress;
                        select.appendChild(option);
                    });
                });
        }
        @endauth
    </script>
</x-layouts.landing>
