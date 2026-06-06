<x-layouts.landing title="Booking Workshop Batik">
    <div class="min-h-screen bg-white py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-12">
                <h1 class="text-4xl font-bold mb-4" style="font-family: 'Playfair Display', serif; color: #8B4513;">
                    Booking Workshop Batik
                </h1>
                <p class="text-gray-600">Pilih workshop dan slot waktu, kemudian lanjutkan ke pembayaran</p>
            </div>

            <!-- Step Indicator -->
            <div class="mb-8 grid grid-cols-3 gap-4">
                <div class="flex items-center gap-3 pb-4 border-b-2 border-blue-600">
                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm">1</div>
                    <div>
                        <p class="font-bold text-gray-900">Pilih Workshop</p>
                        <p class="text-xs text-gray-600">& Slot Waktu</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 pb-4 border-b-2 border-gray-300">
                    <div class="w-8 h-8 rounded-full bg-gray-300 text-white flex items-center justify-center font-bold text-sm">2</div>
                    <div>
                        <p class="font-bold text-gray-700">Data Peserta</p>
                        <p class="text-xs text-gray-500">Menggunakan profil</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 pb-4 border-b-2 border-gray-300">
                    <div class="w-8 h-8 rounded-full bg-gray-300 text-white flex items-center justify-center font-bold text-sm">3</div>
                    <div>
                        <p class="font-bold text-gray-700">Pembayaran</p>
                        <p class="text-xs text-gray-500">Pilih metode</p>
                    </div>
                </div>
            </div>

            <form id="bookingForm" method="POST" action="{{ route('workshop.booking.store') }}" class="bg-white rounded-lg shadow-lg p-8">
                @csrf

                <!-- Hidden Fields -->
                <input type="hidden" id="workshopId" name="workshop_id" value="">
                <input type="hidden" id="scheduleId" name="workshop_slot_schedule_id" value="">
                <input type="hidden" id="workshopName" name="workshop_name" value="">
                <input type="hidden" id="packagePrice" name="package_price" value="">
                <input type="hidden" id="numParticipants" name="number_of_participants" value="1">

                <!-- STEP 1: Workshop Selection -->
                <div class="space-y-6 mb-8 pb-8 border-b border-gray-200">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">📋 Pilih Workshop</h2>

                        <!-- Workshop List -->
                        <div id="workshopList" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="col-span-full text-center py-8 text-gray-500">
                                ⏳ Loading workshop...
                            </div>
                        </div>
                        <div id="workshopError" class="text-red-600 text-sm font-bold" style="display: none;"></div>
                    </div>
                </div>

                <!-- STEP 2: Schedule Selection (shown when workshop selected) -->
                <div id="scheduleSection" style="display: none;" class="space-y-6 mb-8 pb-8 border-b border-gray-200">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">🗓️ Pilih Slot Waktu</h2>
                        
                        <!-- Schedule List -->
                        <div id="scheduleContainer" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="col-span-full text-center py-8 text-gray-500">
                                ⏳ Loading jadwal...
                            </div>
                        </div>
                        <div id="scheduleError" class="text-red-600 text-sm font-bold" style="display: none;"></div>
                    </div>

                    <!-- Participant Count -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            👥 Jumlah Peserta
                        </label>
                        <input 
                            type="number" 
                            id="participantCount"
                            name="number_of_participants"
                            min="1"
                            value="1"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            onchange="updatePrice()">
                    </div>
                </div>

                <!-- STEP 3: User Data (when logged in) -->
                @auth
                <div id="userDataSection" style="display: none;" class="space-y-6 mb-8 pb-8 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">👤 Data Peserta</h2>
                    
                    <!-- Name (from profile) -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap</label>
                        <input 
                            type="text" 
                            name="full_name"
                            value="{{ auth()->user()->name ?? '' }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white"
                            readonly>
                        <p class="text-xs text-gray-500 mt-1">Dari profil Anda</p>
                    </div>

                    <!-- Email (from profile) -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Email</label>
                        <input 
                            type="email" 
                            name="email"
                            value="{{ auth()->user()->email ?? '' }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white"
                            readonly>
                        <p class="text-xs text-gray-500 mt-1">Dari profil Anda</p>
                    </div>

                    <!-- Phone (from profile) -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nomor WhatsApp</label>
                        <input 
                            type="tel" 
                            name="phone"
                            value="{{ auth()->user()->phone ?? '' }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white"
                            readonly>
                        <p class="text-xs text-gray-500 mt-1">Dari profil Anda</p>
                    </div>

                    <!-- Address Selection -->
                    @php
                        $addresses = auth()->user()->addresses ?? [];
                    @endphp
                    
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <label class="block text-sm font-bold text-gray-700 mb-2">🏠 Pilih Alamat</label>
                        
                        @if($addresses->count() > 0)
                            <select 
                                name="address"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                required>
                                <option value="">-- Pilih Alamat --</option>
                                @foreach($addresses as $addr)
                                    <option value="{{ $addr->full_address }}">
                                        {{ $addr->label ?? 'Alamat' }} - {{ substr($addr->full_address, 0, 40) }}...
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-blue-600 mt-2">
                                ✓ Gunakan salah satu alamat yang sudah tersimpan
                            </p>
                        @else
                            <textarea 
                                name="address"
                                rows="3"
                                placeholder="Masukkan alamat lengkap Anda"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                required></textarea>
                            <p class="text-xs text-gray-500 mt-2">Alamat yang Anda tambahkan disini</p>
                        @endif
                    </div>
                </div>
                @endauth

                <!-- Guest User Data -->
                @guest
                <div id="guestDataSection" style="display: none;" class="space-y-6 mb-8 pb-8 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">👤 Data Peserta</h2>
                    
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <p class="text-yellow-800 text-sm">
                            ℹ️ <a href="{{ route('login') }}" class="underline font-bold">Login terlebih dahulu</a> untuk menggunakan profil Anda
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap</label>
                        <input 
                            type="text" 
                            name="full_name"
                            placeholder="Masukkan nama Anda"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                            required>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Email</label>
                        <input 
                            type="email" 
                            name="email"
                            placeholder="email@example.com"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                            required>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nomor WhatsApp</label>
                        <input 
                            type="tel" 
                            name="phone"
                            placeholder="08xxxxxxxxxx"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                            required>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Alamat Lengkap</label>
                        <textarea 
                            name="address"
                            rows="3"
                            placeholder="Masukkan alamat lengkap"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                            required></textarea>
                    </div>
                </div>
                @endguest

                <!-- PRICE SUMMARY -->
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 border-2 border-amber-300 p-6 rounded-lg mb-8">
                    <h3 class="font-bold text-gray-900 mb-3">💰 Ringkasan Pembayaran</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-700">Harga workshop (fix):</span>
                            <span class="font-bold" id="pricePerPerson">-</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-700">Jumlah peserta:</span>
                            <span class="font-bold" id="numParticipantsDisplay">-</span>
                        </div>
                        <div class="border-t border-amber-300 pt-2 mt-2 flex justify-between text-lg">
                            <span class="font-bold text-gray-900">Total:</span>
                            <span class="font-bold text-amber-700" id="totalPrice">Rp 0</span>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-3">
                    <a href="{{ route('landing.workshop') }}" class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-100 text-center">
                        ← Kembali
                    </a>
                    <button 
                        type="submit" 
                        id="submitBtn"
                        disabled
                        class="flex-1 px-6 py-3 bg-blue-600 text-white font-bold rounded-lg disabled:opacity-50 disabled:cursor-not-allowed enabled:hover:bg-blue-700">
                        💳 Lanjut ke Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Load workshops on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadWorkshops();
        });

        // Load all available workshops
        async function loadWorkshops() {
            try {
                const response = await fetch('/api/workshops/active');
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                
                const workshops = await response.json();
                const container = document.getElementById('workshopList');
                
                if (workshops.length === 0) {
                    container.innerHTML = '<div class="col-span-full text-center py-8 text-gray-500">Belum ada workshop tersedia</div>';
                    return;
                }

                container.innerHTML = workshops.map(ws => `
                    <div class="border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-all" 
                         onclick="selectWorkshop(${ws.id}, '${ws.title}', ${ws.amount})">
                        <h3 class="font-bold text-gray-900">${ws.title}</h3>
                        <p class="text-sm text-gray-600 mt-1 line-clamp-2">${ws.description}</p>
                        <p class="text-lg font-bold text-blue-600 mt-2">Rp ${new Intl.NumberFormat('id-ID').format(ws.amount)}</p>
                    </div>
                `).join('');
            } catch (error) {
                console.error('Error loading workshops:', error);
                document.getElementById('workshopError').textContent = '❌ Gagal memuat workshop';
                document.getElementById('workshopError').style.display = 'block';
            }
        }

        // Select workshop and load schedules
        async function selectWorkshop(workshopId, name, price) {
            document.getElementById('workshopId').value = workshopId;
            document.getElementById('workshopName').value = name;
            document.getElementById('packagePrice').value = price;
            
            // Show schedule section
            document.getElementById('scheduleSection').style.display = 'block';
            @auth
                document.getElementById('userDataSection').style.display = 'block';
            @else
                document.getElementById('guestDataSection').style.display = 'block';
            @endauth
            
            // Load schedules
            loadSchedules(workshopId);
            
            // Scroll to schedules
            document.getElementById('scheduleSection').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // Load available schedules for workshop
        async function loadSchedules(workshopId) {
            try {
                const response = await fetch(`/workshop/${workshopId}/available-schedules`);
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                
                const schedules = await response.json();
                const container = document.getElementById('scheduleContainer');
                
                if (schedules.length === 0) {
                    container.innerHTML = '<div class="col-span-full text-center py-8 text-gray-500">Tidak ada jadwal tersedia untuk workshop ini</div>';
                    return;
                }

                container.innerHTML = schedules.map(sch => `
                    <div class="border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-all" 
                         onclick="selectSchedule(${sch.id})">
                        <h3 class="font-bold text-gray-900">${sch.date_formatted}</h3>
                        <p class="text-sm text-gray-600 mt-1">${sch.slot_name}</p>
                        <p class="text-xs text-gray-500">⏰ ${sch.start_time} - ${sch.end_time}</p>
                        <p class="text-sm font-bold text-blue-600 mt-2">✓ Tersedia</p>
                    </div>
                `).join('');
            } catch (error) {
                console.error('Error loading schedules:', error);
                document.getElementById('scheduleError').textContent = '❌ Gagal memuat jadwal';
                document.getElementById('scheduleError').style.display = 'block';
            }
        }

        // Select schedule
        function selectSchedule(scheduleId) {
            document.getElementById('scheduleId').value = scheduleId;
            document.getElementById('numParticipants').value = document.getElementById('participantCount').value;
            updatePrice();
            enableSubmitButton();
        }

        // Update price calculation
        function updatePrice() {
            const price = parseFloat(document.getElementById('packagePrice').value) || 0;
            const participants = parseInt(document.getElementById('participantCount').value) || 1;
            const total = price;

            
            document.getElementById('pricePerPerson').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(price);
            document.getElementById('numParticipantsDisplay').textContent = participants;
            document.getElementById('totalPrice').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
            document.getElementById('numParticipants').value = participants;
        }

        // Enable submit button when all fields are filled
        function enableSubmitButton() {
            const workshopId = document.getElementById('workshopId').value;
            const scheduleId = document.getElementById('scheduleId').value;
            const submitBtn = document.getElementById('submitBtn');
            
            if (workshopId && scheduleId) {
                submitBtn.disabled = false;
            }
        }

        // Enable button on input change
        document.getElementById('participantCount')?.addEventListener('change', function() {
            updatePrice();
            if (document.getElementById('scheduleId').value) {
                document.getElementById('numParticipants').value = this.value;
            }
        });
    </script>
</x-layouts.landing>
