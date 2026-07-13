<x-layouts.app>
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Tambah Workshop Baru</h1>
            <p class="text-gray-600 mt-2">Input semua informasi workshop dalam satu langkah (nama, deskripsi, harga, tanggal & slot)</p>
        </div>

        <!-- Main Form -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <form action="{{ route('admin.workshop.store') }}" method="POST" id="workshopForm">
                @csrf

                <!-- Section 1: Basic Info -->
                <div class="mb-8 pb-8 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Informasi Workshop</h2>
                    
                    <!-- Title -->
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Workshop *
                        </label>
                        <input 
                            type="text" 
                            id="title"
                            name="title"
                            value="{{ old('title') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror"
                            placeholder="Contoh: Batik Workshop Dasar"
                            required
                        >
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi Workshop *
                        </label>
                        <textarea 
                            id="description"
                            name="description"
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror"
                            placeholder="Jelaskan apa yang akan dipelajari, siapa target peserta, apa yang dibawa, dll"
                            required
                        >{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                                Harga per Peserta (Rp) *
                            </label>
                            <input 
                                type="number" 
                                id="amount"
                                name="amount"
                                value="{{ old('amount') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('amount') border-red-500 @enderror"
                                placeholder="350000"
                                step="1000"
                                min="0"
                                required
                            >
                            @error('amount')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Harga ini akan dikalikan dengan jumlah peserta saat user booking</p>
                        </div>

                        <!-- Location -->
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                                Lokasi Workshop *
                            </label>
                            <input 
                                type="text" 
                                id="location"
                                name="location"
                                value="{{ old('location') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('location') border-red-500 @enderror"
                                placeholder="Contoh: Giri Alam Studio, Cilacap"
                                required
                            >
                            @error('location')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Time Slots -->
                <div class="mb-8 pb-8 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Slot Waktu Workshop</h2>
                    <p class="text-gray-600 text-sm mb-4">Buat minimal 1 slot waktu (contoh: Pagi 08:30-10:30, Sore 14:00-17:00)</p>
                    
                    <div id="slotsContainer" class="space-y-4">
                        <!-- Slot 1 (Default) -->
                        <div class="slot-item bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="font-bold text-gray-900">Slot 1</h3>
                                <button type="button" onclick="removeSlot(this)" class="text-red-600 hover:text-red-800 text-sm font-bold">
                                    Hapus
                                </button>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Slot Name -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Nama Slot
                                    </label>
                                    <input 
                                        type="text" 
                                        name="slots[0][name]"
                                        placeholder="Pagi / Sore"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        required
                                    >
                                </div>

                                <!-- Start Time -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Jam Mulai
                                    </label>
                                    <input 
                                        type="time" 
                                        name="slots[0][start_time]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        required
                                    >
                                </div>

                                <!-- End Time -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Jam Selesai
                                    </label>
                                    <input 
                                        type="time" 
                                        name="slots[0][end_time]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        required
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add Slot Button -->
                    <button 
                        type="button" 
                        onclick="addSlot()"
                        class="mt-4 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium"
                    >
                        Tambah Slot Lain
                    </button>
                </div>

                <!-- Section 3: Available Dates -->
                <div class="mb-8 pb-8 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Tanggal Tersedia</h2>
                    <p class="text-gray-600 text-sm mb-4">Pilih tanggal-tanggal kapan workshop akan diadakan (minimal 1 tanggal)</p>
                    
                    <div id="datesContainer" class="space-y-3">
                        <!-- Date 1 (Default) -->
                        <div class="date-item flex gap-3 items-end">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Tanggal
                                </label>
                                <input 
                                    type="date" 
                                    name="dates[]"
                                    min="{{ now()->format('Y-m-d') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    required
                                >
                            </div>
                            <button 
                                type="button" 
                                onclick="removeDate(this)"
                                class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-bold"
                            >
                                🗑️
                            </button>
                        </div>
                    </div>

                    <!-- Add Date Button -->
                        <button 
                        type="button" 
                        onclick="addDate()"
                        class="mt-4 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium"
                    >
                        Tambah Tanggal Lain
                    </button>
                </div>

                <!-- Info Box -->
                <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-600 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <strong>Cara Kerja:</strong> Sistem akan otomatis membuat jadwal booking dengan kombinasi:
                    </p>
                    <p class="text-sm text-blue-800 mt-2">
                        <strong>Contoh:</strong> Jika Anda membuat 2 slot & 2 tanggal → akan ada 4 jadwal yang bisa di-booking (2 slot × 2 tanggal)
                    </p>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-3">
                    <button 
                        type="submit"
                        class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold transition-all"
                    >
                        Buat Workshop Sekarang
                    </button>
                    <a 
                        href="{{ route('admin.workshop.index') }}"
                        class="flex-1 px-6 py-3 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 font-bold text-center transition-all"
                    >
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        let slotIndex = 1;
        let dateIndex = 1;

        function addSlot() {
            const container = document.getElementById('slotsContainer');
            slotIndex++;
            const slotNum = slotIndex;
            
            const html = `
                <div class="slot-item bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="font-bold text-gray-900">Slot ${slotNum}</h3>
                        <button type="button" onclick="removeSlot(this)" class="text-red-600 hover:text-red-800 text-sm font-bold">
                            Hapus
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Slot</label>
                            <input type="text" name="slots[${slotIndex}][name]" placeholder="Pagi / Sore / Malam" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai</label>
                            <input type="time" name="slots[${slotIndex}][start_time]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai</label>
                            <input type="time" name="slots[${slotIndex}][end_time]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        </div>
                    </div>
                </div>
            `;
                        </div>

                        <!-- Location -->
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                                Lokasi Workshop *
                            </label>
                            <input 
                                type="text" 
                                id="location"
                                name="location"
                                value="{{ old('location') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('location') border-red-500 @enderror"
                                placeholder="Contoh: Giri Alam Studio, Cilacap"
                                required
                            >
                            @error('location')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Time Slots -->
                <div class="mb-8 pb-8 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Slot Waktu Workshop</h2>
                    <p class="text-gray-600 text-sm mb-4">Buat minimal 1 slot waktu (contoh: Pagi 08:30-10:30, Sore 14:00-17:00)</p>
                    
                    <div id="slotsContainer" class="space-y-4">
                        <!-- Slot 1 (Default) -->
                        <div class="slot-item bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="font-bold text-gray-900">Slot 1</h3>
                                <button type="button" onclick="removeSlot(this)" class="text-red-600 hover:text-red-800 text-sm font-bold">
                                    Hapus
                                </button>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Slot Name -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Nama Slot
                                    </label>
                                    <input 
                                        type="text" 
                                        name="slots[0][name]"
                                        placeholder="Pagi / Sore"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        required
                                    >
                                </div>

                                <!-- Start Time -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Jam Mulai
                                    </label>
                                    <input 
                                        type="time" 
                                        name="slots[0][start_time]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        required
                                    >
                                </div>

                                <!-- End Time -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Jam Selesai
                                    </label>
                                    <input 
                                        type="time" 
                                        name="slots[0][end_time]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        required
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add Slot Button -->
                    <button 
                        type="button" 
                        onclick="addSlot()"
                        class="mt-4 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium"
                    >
                        Tambah Slot Lain
                    </button>
                </div>

                <!-- Section 3: Available Dates -->
                <div class="mb-8 pb-8 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Tanggal Tersedia</h2>
                    <p class="text-gray-600 text-sm mb-4">Pilih tanggal-tanggal kapan workshop akan diadakan (minimal 1 tanggal)</p>
                    
                    <div id="datesContainer" class="space-y-3">
                        <!-- Date 1 (Default) -->
                        <div class="date-item flex gap-3 items-end">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Tanggal
                                </label>
                                <input 
                                    type="date" 
                                    name="dates[]"
                                    min="{{ now()->format('Y-m-d') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    required
                                >
                            </div>
                            <button 
                                type="button" 
                                onclick="removeDate(this)"
                                class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-bold"
                            >
                                🗑️
                            </button>
                        </div>
                    </div>

                    <!-- Add Date Button -->
                        <button 
                        type="button" 
                        onclick="addDate()"
                        class="mt-4 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium"
                    >
                        Tambah Tanggal Lain
                    </button>
                </div>

                <!-- Info Box -->
                <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-600 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <strong>Cara Kerja:</strong> Sistem akan otomatis membuat jadwal booking dengan kombinasi:
                    </p>
                    <p class="text-sm text-blue-800 mt-2">
                        <strong>Contoh:</strong> Jika Anda membuat 2 slot & 2 tanggal → akan ada 4 jadwal yang bisa di-booking (2 slot × 2 tanggal)
                    </p>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-3">
                    <button 
                        type="submit"
                        class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold transition-all"
                    >
                        Buat Workshop Sekarang
                    </button>
                    <a 
                        href="{{ route('admin.workshop.index') }}"
                        class="flex-1 px-6 py-3 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 font-bold text-center transition-all"
                    >
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        let slotIndex = 1;
        let dateIndex = 1;

        function addSlot() {
            const container = document.getElementById('slotsContainer');
            slotIndex++;
            const slotNum = slotIndex;
            
            const html = `
                <div class="slot-item bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="font-bold text-gray-900">Slot ${slotNum}</h3>
                        <button type="button" onclick="removeSlot(this)" class="text-red-600 hover:text-red-800 text-sm font-bold">
                            Hapus
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Slot</label>
                            <input type="text" name="slots[${slotIndex}][name]" placeholder="Pagi / Sore / Malam" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai</label>
                            <input type="time" name="slots[${slotIndex}][start_time]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai</label>
                            <input type="time" name="slots[${slotIndex}][end_time]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        </div>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', html);
        }

        function removeSlot(button) {
            if (document.querySelectorAll('.slot-item').length > 1) {
                button.closest('.slot-item').remove();
            } else {
                Swal.fire({ title: 'Tidak Dapat Menghapus', text: 'Minimal harus ada 1 slot waktu!', icon: 'warning', iconColor: '#f59e0b', confirmButtonText: 'OK', confirmButtonColor: '#b45309', customClass: { popup: 'rounded-2xl shadow-2xl' } });
            }
        }

        function addDate() {
            const container = document.getElementById('datesContainer');
            dateIndex++;
            
            const html = `
                <div class="date-item flex gap-3 items-end">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                        <input type="date" name="dates[]" min="{{ now()->format('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <button type="button" onclick="removeDate(this)" class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-bold">🗑️</button>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', html);
        }

        function removeDate(button) {
            if (document.querySelectorAll('.date-item').length > 1) {
                button.closest('.date-item').remove();
            } else {
                Swal.fire({ title: 'Tidak Dapat Menghapus', text: 'Minimal harus ada 1 tanggal!', icon: 'warning', iconColor: '#f59e0b', confirmButtonText: 'OK', confirmButtonColor: '#b45309', customClass: { popup: 'rounded-2xl shadow-2xl' } });
            }
        }

        // Form validation
        document.getElementById('workshopForm').addEventListener('submit', function(e) {
            const slots = document.querySelectorAll('.slot-item').length;
            const dates = document.querySelectorAll('.date-item').length;
            
            if (slots === 0) {
                e.preventDefault();
                Swal.fire({ title: 'Validasi Gagal', text: 'Tambahkan minimal 1 slot waktu!', icon: 'error', iconColor: '#ef4444', confirmButtonText: 'OK', confirmButtonColor: '#b45309', customClass: { popup: 'rounded-2xl shadow-2xl' } });
                return false;
            }
            
            if (dates === 0) {
                e.preventDefault();
                Swal.fire({ title: 'Validasi Gagal', text: 'Tambahkan minimal 1 tanggal!', icon: 'error', iconColor: '#ef4444', confirmButtonText: 'OK', confirmButtonColor: '#b45309', customClass: { popup: 'rounded-2xl shadow-2xl' } });
                return false;
            }
        });
    </script>
</x-layouts.app>
