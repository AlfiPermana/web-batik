<x-layouts.landing title="Pesan Workshop Batik">
    <div class="min-h-screen bg-white py-12">
        <div class="max-w-2xl mx-auto px-4">
            <!-- Header -->
            <div class="mb-12">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">🎨 Booking Workshop Batik</h1>
                <p class="text-gray-600">Pilih workshop, slot waktu, dan lanjutkan ke pembayaran</p>
            </div>

            <!-- Workshop Card Header -->
            <div class="bg-gradient-to-r from-orange-50 to-yellow-50 border-2 border-orange-200 rounded-lg p-6 mb-8">
                <h2 class="text-lg font-bold text-gray-900 mb-2">{{ $workshop->title ?? 'Workshop' }}</h2>
                <p class="text-gray-600 mb-4">{{ $workshop->description ?? '' }}</p>
                <p class="text-3xl font-bold text-orange-600">Rp{{ number_format($workshop->amount ?? 0, 0, ',', '.') }}</p>
                <p class="text-sm text-gray-600 mt-2">harga fix (tidak bergantung jumlah peserta)</p>

            </div>

            <form id="bookingForm" method="POST" action="{{ route('workshop.booking.store') }}">
                @csrf
                <input type="hidden" id="workshopId" name="workshop_id" value="{{ $workshop->id ?? '0' }}">
                <input type="hidden" id="scheduleId" name="workshop_slot_schedule_id">
                <input type="hidden" id="unitPrice" name="unit_price" value="{{ $workshop->amount ?? 0 }}">
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
                    <!-- Section 1: Pilih Jadwal -->
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">🗓️ Pilih Jadwal Workshop</h3>
                        <p class="text-sm text-gray-600 mb-4">Pilih salah satu jadwal yang tersedia</p>

                        <div id="schedulesList" class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-8">
                            <div class="text-center py-8 text-gray-500 col-span-full">⏳ Memuat jadwal...</div>
                        </div>
                    </div>

                    <!-- Section 2: Input Data & Pembayaran -->
                    <div id="dataSection" style="display: none;" class="bg-gray-50 border-2 border-gray-300 rounded-lg p-6">
                        <!-- Jumlah Peserta -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-900 mb-3">👥 Jumlah Peserta</label>
                            <div class="flex items-center gap-3">
                                <button type="button" onclick="decrementParticipants()" class="w-10 h-10 bg-gray-300 text-gray-900 font-bold rounded hover:bg-gray-400">−</button>
                                <input 
                                    type="number" 
                                    id="numParticipants" 
                                    name="number_of_participants" 
                                    value="1" 
                                    min="1" 
                                    max="100" 
                                    class="w-20 text-center text-xl font-bold border-2 border-gray-300 rounded py-2"
                                    onchange="updateTotalPrice()"
                                >
                                <button type="button" onclick="incrementParticipants()" class="w-10 h-10 bg-blue-600 text-white font-bold rounded hover:bg-blue-700">+</button>
                            </div>
                        </div>

                        <!-- Nama Lengkap -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-900 mb-2">👤 Nama Lengkap</label>
                            <input 
                                type="text" 
                                id="customerName" 
                                name="customer_name" 
                                value="{{ auth()->user()->name ?? '' }}"
                                class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg bg-gray-100"
                                disabled
                            >
                        </div>

                        <!-- Email -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-900 mb-2">📧 Email</label>
                            <input 
                                type="email" 
                                id="customerEmail" 
                                name="customer_email" 
                                value="{{ auth()->user()->email ?? '' }}"
                                class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg bg-gray-100"
                                disabled
                            >
                        </div>

                        <!-- Nomor WhatsApp -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-900 mb-2">📱 Nomor WhatsApp</label>
                            <input 
                                type="tel" 
                                id="customerPhone" 
                                name="customer_phone" 
                                value="{{ auth()->user()->phone ?? '' }}"
                                placeholder="08xxxxxxxxxx"
                                class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg"
                            >
                        </div>

                        <!-- Alamat -->
                        <div class="mb-8">
                            <label class="block text-sm font-bold text-gray-900 mb-2">🏠 Alamat Lengkap</label>
                            <select 
                                id="addressSelect" 
                                name="address_id" 
                                class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none"
                                onchange="updateAddressDisplay()"
                            >
                                <option value="">-- Pilih alamat yang sudah tersimpan --</option>
                            </select>
                        </div>

                        <!-- Summary: Total Harga -->
                        <div class="bg-yellow-50 border-2 border-yellow-300 rounded-lg p-6 mb-6">
                            <p class="text-sm text-gray-600 mb-2">Total harga:</p>
                            <p class="text-3xl font-bold text-yellow-600 mb-4">Rp <span id="totalPrice">{{ number_format($workshop->amount ?? 0, 0, ',', '.') }}</span></p>
                            
                            <div class="space-y-2 text-sm text-gray-700 border-t border-yellow-300 pt-4">
                                <p class="flex justify-between">
                                    <span>Jumlah peserta:</span>
                                    <span class="font-bold"><span id="displayCount">1</span> orang</span>
                                </p>
                                <p class="flex justify-between">
                                    <span>Harga workshop (fix):</span>
                                    <span class="font-bold">Rp{{ number_format($workshop->amount ?? 0, 0, ',', '.') }}</span>
                                </p>
                                <p class="text-xs text-gray-500 mt-2">Total harga tidak berubah walaupun jumlah peserta berbeda.</p>

                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-900 mb-3">Metode Pembayaran:</label>
                            <div class="space-y-2">
                                <label class="flex items-center p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-blue-600 hover:bg-blue-50 transition-all">
                                    <input type="radio" name="payment_method" value="direct_transfer" class="w-4 h-4" required>
                                    <span class="ml-3 font-bold text-gray-900">💳 Transfer Bank / E-Wallet</span>
                                </label>
                                <label class="flex items-center p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-blue-600 hover:bg-blue-50 transition-all">
                                    <input type="radio" name="payment_method" value="tripay" class="w-4 h-4">
                                    <span class="ml-3 font-bold text-gray-900">🏦 Tripay</span>
                                </label>
                            </div>
                        </div>

                        <!-- T&C -->
                        <label class="flex items-start mb-8 cursor-pointer">
                            <input type="checkbox" name="agree_terms" class="w-4 h-4 mt-1" required>
                            <span class="ml-2 text-sm text-gray-700">Saya setuju dengan <strong>Syarat & Ketentuan</strong> dan <strong>Kebijakan Privasi</strong></span>
                        </label>

                        <!-- Buttons -->
                        <div class="flex gap-3">
                            <button type="button" onclick="backToSchedules()" class="flex-1 px-6 py-3 bg-gray-400 text-gray-900 font-bold rounded-lg hover:bg-gray-500">
                                ← Kembali
                            </button>
                            <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 text-lg">
                                💳 Bayar Sekarang
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
            const workshopId = document.getElementById('workshopId').value;
            fetch(`/workshop/${workshopId}/available-schedules`)
                .then(r => r.json())
                .then(schedules => {
                    const list = document.getElementById('schedulesList');
                    list.innerHTML = '';

                    if (!schedules.length) {
                        list.innerHTML = '<p class="text-center text-gray-500 py-8 col-span-full">Tidak ada jadwal tersedia</p>';
                        return;
                    }

                    schedules.forEach(schedule => {
                        const card = document.createElement('div');
                        card.className = 'p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-blue-600 hover:bg-blue-50 transition-all schedule-card text-center';
                        card.onclick = () => selectSchedule(schedule, card);
                        
                        card.innerHTML = `
                            <p class="text-sm text-gray-600 mb-2">${schedule.date}</p>
                            <p class="font-bold text-gray-900 text-lg">${schedule.time_slot_name} ${schedule.start_time}-${schedule.end_time}</p>
                        `;
                        
                        list.appendChild(card);
                    });
                })
                .catch(err => {
                    document.getElementById('schedulesList').innerHTML = '<p class="text-center text-red-600 col-span-full">Gagal memuat jadwal</p>';
                });
        }

        function selectSchedule(schedule, cardElement) {
            selectedSchedule = schedule;
            
            document.getElementById('scheduleId').value = schedule.id;

            // Visual feedback
            document.querySelectorAll('.schedule-card').forEach(el => el.classList.remove('border-blue-600', 'bg-blue-50'));
            cardElement.classList.add('border-blue-600', 'bg-blue-50');

            // Show data section
            document.getElementById('dataSection').style.display = 'block';
            
            // Scroll ke data section
            window.scrollTo({top: document.getElementById('dataSection').offsetTop - 100, behavior: 'smooth'});
        }

        function incrementParticipants() {
            const input = document.getElementById('numParticipants');
            input.value = parseInt(input.value) + 1;
            updateTotalPrice();
        }

        function decrementParticipants() {
            const input = document.getElementById('numParticipants');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
            }
            updateTotalPrice();
        }

        function updateTotalPrice() {
            const count = parseInt(document.getElementById('numParticipants').value);
            const unitPrice = parseInt(document.getElementById('unitPrice').value);
            const total = unitPrice;


            document.getElementById('totalPrice').textContent = total.toLocaleString('id-ID');
            document.getElementById('displayCount').textContent = count;
        }

        function updateAddressDisplay() {
            const select = document.getElementById('addressSelect');
            document.getElementById('addressId').value = select.value;
        }

        function backToSchedules() {
            selectedSchedule = null;
            document.getElementById('dataSection').style.display = 'none';
            document.querySelectorAll('.schedule-card').forEach(el => el.classList.remove('border-blue-600', 'bg-blue-50'));
            window.scrollTo({top: 0, behavior: 'smooth'});
        }

        @auth
        function loadAddresses() {
            fetch('/api/user/addresses')
                .then(r => r.json())
                .then(addresses => {
                    const select = document.getElementById('addressSelect');
                    select.innerHTML = '<option value="">-- Pilih alamat yang sudah tersimpan --</option>';

                    if (!addresses.length) {
                        select.innerHTML += '<option disabled>Anda belum memiliki alamat tersimpan</option>';
                        return;
                    }

                    addresses.forEach(addr => {
                        const fullAddress = `${addr.street}, ${addr.city}, ${addr.province}${addr.zip ? ' ' + addr.zip : ''}`;
                        const option = document.createElement('option');
                        option.value = addr.id;
                        option.textContent = fullAddress;
                        select.appendChild(option);
                    });
                });
        }
        @endauth
    </script>
</x-layouts.landing>
