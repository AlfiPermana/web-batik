<x-layouts.app>
    <div class="container mx-auto px-4 py-8">
        <!-- Messages -->
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Edit Workshop</h1>
            <p class="text-gray-600 mt-2">Ubah informasi workshop dan kelola jadwal</p>
        </div>

        <!-- Tabs -->
        <div class="mb-8 border-b border-gray-200">
            <div class="flex gap-8">
                <button 
                    class="pb-4 px-2 border-b-2 border-blue-600 text-blue-600 font-medium tab-button"
                    data-tab="info"
                >
                    Informasi Workshop
                </button>
                <button 
                    class="pb-4 px-2 border-b-2 border-gray-200 text-gray-600 font-medium tab-button"
                    data-tab="slots"
                >
                    Kelola Slot Waktu
                </button>
                <button 
                    class="pb-4 px-2 border-b-2 border-gray-200 text-gray-600 font-medium tab-button"
                    data-tab="dates"
                >
                    Tanggal Tersedia
                </button>
            </div>
        </div>

        <!-- Info Tab -->
        <div id="tab-info" class="tab-content">
            <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
                <form action="{{ route('admin.workshop.update', $workshop) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Title -->
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Judul Workshop *
                        </label>
                        <input 
                            type="text" 
                            id="title"
                            name="title"
                            value="{{ old('title', $workshop->title) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror"
                            placeholder="Masukkan judul workshop"
                            required
                        >
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi *
                        </label>
                        <textarea 
                            id="description"
                            name="description"
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror"
                            placeholder="Masukkan deskripsi workshop"
                            required
                        >{{ old('description', $workshop->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price -->
                    <div class="mb-6">
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                            Harga (Rp) *
                        </label>
                        <input 
                            type="number" 
                            id="amount"
                            name="amount"
                            value="{{ old('amount', $workshop->amount) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('amount') border-red-500 @enderror"
                            placeholder="0"
                            step="0.01"
                            min="0"
                            required
                        >
                        @error('amount')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>



                    <!-- Location -->
                    <div class="mb-6">
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                            Lokasi *
                        </label>
                        <input 
                            type="text" 
                            id="location"
                            name="location"
                            value="{{ old('location', $workshop->location) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('location') border-red-500 @enderror"
                            placeholder="Masukkan lokasi workshop"
                            required
                        >
                        @error('location')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Active Status -->
                    <div class="mb-6">
                        <label class="flex items-center gap-3">
                            <input 
                                type="checkbox"
                                name="is_active"
                                value="1"
                                {{ old('is_active', $workshop->is_active) ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            >
                            <span class="text-sm font-medium text-gray-700">Workshop Aktif</span>
                        </label>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-3 pt-4 border-t">
                        <button 
                            type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
                        >
                            Simpan Perubahan
                        </button>
                        <a 
                            href="{{ route('admin.workshop.index') }}"
                            class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition font-medium"
                        >
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Slots Tab (NEW SYSTEM) -->
        <div id="tab-slots" class="tab-content hidden">
            @livewire('manage-workshop-time-slots', ['workshopId' => $workshop->id])
        </div>

        <!-- Dates Tab (NEW SYSTEM) -->
        <div id="tab-dates" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-lg">
                <!-- Header -->
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-800">Kelola Tanggal Tersedia</h2>
                    <p class="text-gray-600 mt-2">Pilih tanggal workshop untuk membuat jadwal booking</p>
                </div>

                <div class="p-6">
                    <!-- Add Date Form - Input dan Button -->
                    <form method="POST" action="{{ route('admin.workshop.addDate', $workshop->id) }}" class="mb-6">
                        @csrf
                        <!-- DEBUG: Show action URL -->
                        <!-- Action: {{ route('admin.workshop.addDate', $workshop->id) }} -->
                        <div>
                            <label for="available_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Pilih Tanggal *
                            </label>
                            <div class="flex gap-4 items-end">
                                <input 
                                    type="date" 
                                    id="available_date"
                                    name="date"
                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    min="{{ now()->format('Y-m-d') }}"
                                    required
                                >
                                <button 
                                    type="submit"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition"
                                >
                                    + Add Date
                                </button>
                            </div>
                            @error('date')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </form>

                    <!-- Available Dates List -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Tanggal Tersedia</h3>
                        </div>
                        
                        @php
                            $availableDates = $workshop->availableDates()->orderBy('date')->get();
                        @endphp
                        
                        @if($availableDates->count() > 0)
                            <table class="w-full">
                                <thead class="bg-gray-100 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Tanggal</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Slot Waktu</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Jadwal</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($availableDates as $date)
                                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                {{ $date->date->format('d F Y') }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                {{ $workshop->timeSlots()->count() }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                {{ $date->schedules()->count() }}
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <form action="{{ route('admin.workshop.removeDate', $date->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus tanggal ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                                <p class="text-blue-800">Belum ada tanggal yang dipilih. Tambahkan tanggal terlebih dahulu!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-semibold text-blue-900 mb-2">💡 Cara Kerja</h4>
                <ul class="text-blue-800 text-sm space-y-1">
                    <li>✓ Pilih tanggal yang akan dijadwalkan</li>
                    <li>✓ Sistem otomatis membuat jadwal untuk setiap slot waktu pada tanggal tersebut</li>
                    <li>✓ Contoh: 2 slot waktu × 1 tanggal = 2 jadwal tersedia untuk booking</li>
                    <li>✓ Users dapat memilih slot mana saja untuk booking</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // Tab switching
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.addEventListener('click', function() {
                const tabName = this.dataset.tab;
                
                // Hide all tabs
                document.querySelectorAll('.tab-content').forEach(tab => {
                    tab.classList.add('hidden');
                });
                
                // Remove active style from all buttons
                document.querySelectorAll('.tab-button').forEach(b => {
                    b.classList.remove('border-blue-600', 'text-blue-600');
                    b.classList.add('border-gray-200', 'text-gray-600');
                });
                
                // Show selected tab
                document.getElementById('tab-' + tabName).classList.remove('hidden');
                
                // Add active style to clicked button
                this.classList.remove('border-gray-200', 'text-gray-600');
                this.classList.add('border-blue-600', 'text-blue-600');
            });
        });
    </script>
</x-layouts.app>
