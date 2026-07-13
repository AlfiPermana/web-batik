<div class="container mx-auto p-6">
    <div class="bg-white rounded-lg shadow-lg">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800">Kelola Slot Waktu Workshop</h2>
            <p class="text-gray-600 mt-2">Buat dan kelola slot waktu untuk workshop (contoh: 10:00-13:00, 14:00-17:00)</p>
        </div>

        <div class="p-6">
            <!-- Workshop Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Workshop</label>
                <select wire:model.live="workshopId" class="w-full border border-gray-300 rounded-lg p-2">
                    <option value="">-- Pilih Workshop --</option>
                    @foreach($workshops as $ws)
                        <option value="{{ $ws->id }}">{{ $ws->title }} - Rp {{ number_format($ws->amount, 0, ',', '.') }}</option>
                    @endforeach
                </select>
            </div>

            @if($workshop)
                <!-- Current Time Slots Table -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Active Time Slots</h3>
                        @unless($showCreateForm || $showEditForm)
                            <button 
                                wire:click="openCreateForm" 
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition">
                                + Add Time Slot
                            </button>
                        @endunless
                    </div>

                    @if(count($timeSlots) > 0)
                            <table class="w-full">
                                <thead class="bg-gray-100 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Nama Slot</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Jam</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($timeSlots as $slot)
                                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $slot['name'] }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                {{ substr($slot['start_time'], 0, 5) }} - {{ substr($slot['end_time'], 0, 5) }}
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $slot['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $slot['is_active'] ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <button 
                                                    wire:click="openEditForm({{ $slot['id'] }})"
                                                    class="text-blue-500 hover:text-blue-700 mr-3">
                                                    Edit
                                                </button>
                                                <button 
                                                    wire:click="toggleSlotStatus({{ $slot['id'] }})"
                                                    class="text-amber-500 hover:text-amber-700 mr-3">
                                                    {{ $slot['is_active'] ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                                <button 
                                                    type="button"
                                                    class="text-red-500 hover:text-red-700"
                                                    onclick="swConfirmDeleteSlot({{ $slot['id'] }}, '{{ addslashes($slot['name']) }}', $wire)">
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                    @else
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                            <p class="text-blue-800">No time slots created yet. Add your first slot below!</p>
                        </div>
                    @endif
                </div>

                <!-- Create/Edit Form -->
                @if($showCreateForm || $showEditForm)
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            {{ $showCreateForm ? 'Create New Time Slot' : 'Edit Time Slot' }}
                        </h3>

                        <form wire:submit="{{ $showCreateForm ? 'createSlot' : 'updateSlot' }}" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Slot Name -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Slot</label>
                                    <input 
                                        type="text"
                                        wire:model="name"
                                        placeholder="Contoh: Pagi"
                                        class="w-full border border-gray-300 rounded-lg p-2 @error('name') border-red-500 @enderror"
                                    />
                                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Start Time -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Jam Mulai</label>
                                    <input 
                                        type="text"
                                        wire:model="start_time"
                                        placeholder="10:00"
                                        class="w-full border border-gray-300 rounded-lg p-2 @error('start_time') border-red-500 @enderror"
                                    />
                                    @error('start_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- End Time -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Jam Selesai</label>
                                    <input 
                                        type="text"
                                        wire:model="end_time"
                                        placeholder="13:00"
                                        class="w-full border border-gray-300 rounded-lg p-2 @error('end_time') border-red-500 @enderror"
                                    />
                                    @error('end_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="flex gap-3 pt-4">
                                <button 
                                    type="submit"
                                    class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg transition">
                                    {{ $showCreateForm ? 'Create Slot' : 'Update Slot' }}
                                </button>
                                <button 
                                    type="button"
                                    wire:click="resetForm"
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                    <p class="text-yellow-800">Please select a workshop to manage time slots</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Info Box -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h4 class="font-semibold text-blue-900 mb-2">💡 How It Works</h4>
        <ul class="text-blue-800 text-sm space-y-1">
            <li>✓ Create multiple time slots (e.g., 10:00-13:00, 14:00-17:00)</li>
            <li>✓ Buat beberapa slot waktu (mis. 10:00-13:00, 14:00-17:00)</li>
            <li>✓ Customer mengisi jumlah peserta saat booking</li>
            <li>✓ Sistem menandai slot sebagai "On Book" ketika ada booking</li>
        </ul>
    </div>
</div>

<script>
function swConfirmDeleteSlot(slotId, slotName, wire) {
    Swal.fire({
        title: 'Hapus Slot Waktu?',
        html: 'Slot <strong>' + slotName + '</strong> akan dihapus permanen.',
        icon: 'warning', iconColor: '#ef4444',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!', confirmButtonColor: '#ef4444',
        cancelButtonText: 'Batal', cancelButtonColor: '#6b7280',
        reverseButtons: true, focusCancel: true,
        customClass: { popup: 'rounded-2xl shadow-2xl' },
    }).then(r => { if (r.isConfirmed) wire.deleteSlot(slotId); });
}
</script>
