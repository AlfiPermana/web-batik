<x-layouts.landing title="Pesan Workshop Batik">
    <div class="min-h-screen bg-white py-12">
        <div class="max-w-2xl mx-auto px-4">
            <!-- Header -->
            <div class="mb-12">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">🎨 Booking Workshop Batik</h1>
                <p class="text-gray-600">Pilih workshop, slot waktu, dan lanjutkan ke pembayaran</p>
            </div>

            <!-- Package Card -->
            <div class="bg-gradient-to-r from-orange-50 to-yellow-50 border-2 border-orange-200 rounded-lg p-6 mb-12">
                <h2 class="text-lg font-bold text-gray-900 mb-2">PAKET 1</h2>
                <p class="text-gray-600 mb-4">Test</p>
                <p class="text-3xl font-bold text-orange-600">Rp350.000</p>
            </div>

            <!-- Step 1: Pilih Jadwal -->
            <div id="step1" class="mb-12">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">🗓️ Pilih Jadwal Workshop</h3>
                
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div id="schedulesList" class="col-span-2 space-y-3">
                        <div class="text-center py-8 text-gray-500">⏳ Memuat jadwal...</div>
                    </div>
                </div>

                <p class="text-gray-600 text-sm">Pilih salah satu jadwal yang tersedia</p>
            </div>

            <!-- Step 2: Data & Bayar (Hidden) -->
            <div id="step2" style="display: none;" class="space-y-6">
                <!-- Jumlah Peserta -->
                <div class="bg-white border-2 border-gray-300 rounded-lg p-6">
                    <p class="text-lg font-bold text-gray-900 mb-4">👥 Jumlah Peserta</p>
                    <div class="flex items-center gap-4">
                        <button type="button" onclick="minus()" class="w-10 h-10 bg-gray-300 text-gray-900 font-bold text-xl rounded">−</button>
                        <input type="number" id="count" value="1" min="1" class="w-16 text-center text-2xl font-bold border-2 border-gray-300 rounded py-2" onchange="calc()">
                        <button type="button" onclick="plus()" class="w-10 h-10 bg-blue-600 text-white font-bold text-xl rounded">+</button>
                    </div>
                </div>

                <!-- Nama -->
                <div class="bg-white border-2 border-gray-300 rounded-lg p-6">
                    <p class="text-lg font-bold text-gray-900 mb-3">👤 Nama Lengkap</p>
                    <p class="text-gray-900 text-base">{{ auth()->user()->name ?? 'Masukkan nama Anda' }}</p>
                </div>

                <!-- Email -->
                <div class="bg-white border-2 border-gray-300 rounded-lg p-6">
                    <p class="text-lg font-bold text-gray-900 mb-3">📧 Email</p>
                    <p class="text-gray-900 text-base">{{ auth()->user()->email ?? 'email@example.com' }}</p>
                </div>

                <!-- WhatsApp -->
                <div class="bg-white border-2 border-gray-300 rounded-lg p-6">
                    <p class="text-lg font-bold text-gray-900 mb-3">📱 Nomor WhatsApp</p>
                    <p class="text-gray-900 text-base">{{ auth()->user()->phone ?? '08xxxxxxxxxx' }}</p>
                </div>

                <!-- Alamat -->
                <div class="bg-white border-2 border-gray-300 rounded-lg p-6">
                    <p class="text-lg font-bold text-gray-900 mb-3">🏠 Alamat Lengkap</p>
                    <select id="addressSelect" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg">
                        <option>Masukkan alamat lengkap Anda</option>
                    </select>
                </div>

                <!-- Summary -->
                <div class="bg-yellow-50 border-2 border-yellow-400 rounded-lg p-8">
                    <p class="text-sm text-gray-600 mb-2">Total harga:</p>
                    <p class="text-4xl font-bold text-yellow-600 mb-6">Rp <span id="total">350.000</span></p>

                    <div class="space-y-2 text-sm text-gray-700 border-t border-yellow-400 pt-6">
                        <p class="flex justify-between">
                            <span>Jumlah peserta:</span>
                            <span><span id="countDisplay">1</span></span>
                        </p>
                        <p class="flex justify-between">
                            <span>Harga per orang:</span>
                            <span>Rp350.000</span>
                        </p>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3">
                    <button type="button" onclick="backStep()" class="flex-1 px-6 py-3 bg-gray-400 text-gray-900 font-bold rounded-lg hover:bg-gray-500 text-lg">
                        ← Kembali
                    </button>
                    <form id="submitForm" method="POST" action="{{ route('workshop.booking.store') }}" class="flex-1">
                        @csrf
                        <input type="hidden" id="hScheduleId" name="workshop_slot_schedule_id">
                        <input type="hidden" id="hWorkshopId" name="workshop_id" value="{{ $workshopId ?? '0' }}">
                        <input type="hidden" id="hCount" name="number_of_participants" value="1">
                        <input type="hidden" id="hPrice" name="unit_price" value="350000">
                        <input type="hidden" id="hName" name="customer_name" value="{{ auth()->user()->name ?? '' }}">
                        <input type="hidden" id="hEmail" name="customer_email" value="{{ auth()->user()->email ?? '' }}">
                        <input type="hidden" id="hPhone" name="customer_phone" value="{{ auth()->user()->phone ?? '' }}">
                        <input type="hidden" id="hAddress" name="address_id" value="">
                        <button type="submit" class="w-full px-6 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 text-lg">
                            💳 Bayar Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', loadJadwal);

        function loadJadwal() {
            fetch('/workshop/4/available-schedules')
                .then(r => r.json())
                .then(data => {
                    const html = data.map(s => `
                        <div class="border-2 border-gray-300 rounded-lg p-6 cursor-pointer hover:border-blue-600 hover:bg-blue-50 text-center slot-card" onclick="pilih(${s.id}, this)">
                            <p class="text-gray-600 text-sm font-bold mb-2">${s.date}</p>
                            <p class="text-gray-900 font-bold text-lg">${s.time_slot_name}</p>
                            <p class="text-gray-600 text-sm mt-2">${s.start_time}-${s.end_time}</p>
                        </div>
                    `).join('');
                    document.getElementById('schedulesList').innerHTML = html;
                });
        }

        function pilih(id, el) {
            document.querySelectorAll('.slot-card').forEach(e => e.classList.remove('border-blue-600', 'bg-blue-50'));
            el.classList.add('border-blue-600', 'bg-blue-50');
            document.getElementById('hScheduleId').value = id;
            document.getElementById('step1').style.display = 'none';
            document.getElementById('step2').style.display = 'block';
            window.scrollTo(0, 0);
            @auth
            loadAddr();
            @endauth
        }

        function backStep() {
            document.getElementById('step1').style.display = 'block';
            document.getElementById('step2').style.display = 'none';
            document.querySelectorAll('.slot-card').forEach(e => e.classList.remove('border-blue-600', 'bg-blue-50'));
            window.scrollTo(0, 0);
        }

        function plus() {
            const c = parseInt(document.getElementById('count').value) + 1;
            document.getElementById('count').value = c;
            calc();
        }

        function minus() {
            const c = parseInt(document.getElementById('count').value);
            if (c > 1) document.getElementById('count').value = c - 1;
            calc();
        }

        function calc() {
            const c = parseInt(document.getElementById('count').value);
            const total = 350000 * c;
            document.getElementById('total').textContent = total.toLocaleString('id-ID');
            document.getElementById('countDisplay').textContent = c;
            document.getElementById('hCount').value = c;
        }

        @auth
        function loadAddr() {
            fetch('/api/user/addresses')
                .then(r => r.json())
                .then(addrs => {
                    let html = '<option>Masukkan alamat lengkap Anda</option>';
                    addrs.forEach(a => {
                        html += `<option value="${a.id}">${a.street}, ${a.city}</option>`;
                    });
                    document.getElementById('addressSelect').innerHTML = html;
                    document.getElementById('addressSelect').onchange = () => {
                        document.getElementById('hAddress').value = document.getElementById('addressSelect').value;
                    };
                });
        }
        @endauth
    </script>
</x-layouts.landing>
