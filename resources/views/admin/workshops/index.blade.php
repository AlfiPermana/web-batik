<x-layouts.app :title="__('Workshop')">
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <flux:heading size="xl">Workshop</flux:heading>
                <flux:subheading>Kelola workshop dan jadwal</flux:subheading>
            </div>
            <flux:button :href="route('admin.workshop.create')" icon="plus" variant="primary">
                Tambah Workshop
            </flux:button>
        </div>

        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
            <form method="GET" action="{{ route('admin.workshop.index') }}" class="p-6 bg-white dark:bg-zinc-900">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cari Workshop</label>
                        <input
                            type="text"
                            name="search"
                            placeholder="Cari workshop..."
                            value="{{ request('search') }}"
                            class="w-full px-4 py-2 rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <select name="status" class="w-full px-4 py-2 rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-zinc-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <flux:button type="submit" variant="primary" class="w-full">Filter</flux:button>
                    </div>
                </div>
            </form>
        </div>

        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
            <div class="overflow-x-auto">
                @if($workshops->count() > 0)
                    <table class="w-full">
                        <thead class="bg-zinc-50 dark:bg-zinc-800 border-b border-neutral-200 dark:border-neutral-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Judul Workshop</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Slot</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Jadwal</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Harga</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-zinc-900 divide-y divide-neutral-200 dark:divide-neutral-700">
                            @foreach($workshops as $workshop)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ ($workshops->currentPage() - 1) * $workshops->perPage() + $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $workshop->title }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($workshop->description, 50) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        {{ $workshop->timeSlots()->count() }} slot
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        {{ $workshop->availableDates()->count() }} tanggal
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-indigo-600 dark:text-indigo-400">Rp {{ number_format($workshop->amount, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $workshop->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $workshop->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex justify-center gap-2">
                                            <flux:button size="sm" :href="route('admin.workshop.show', $workshop)" variant="ghost">Lihat</flux:button>
                                            <flux:button size="sm" :href="route('admin.workshop.edit', $workshop)" variant="ghost">Edit</flux:button>
                                            <flux:button size="sm" :href="route('admin.workshop.bookings', $workshop)" variant="ghost">Booking</flux:button>
                                            <form action="{{ route('admin.workshop.destroy', $workshop) }}" method="POST" onsubmit="return confirm('Hapus workshop ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <flux:button size="sm" type="submit" variant="danger">Hapus</flux:button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="px-6 py-12 text-center bg-white dark:bg-zinc-900">
                        <flux:heading size="lg" class="text-zinc-400">Belum ada workshop</flux:heading>
                        <flux:subheading class="mt-2">Klik "Tambah Workshop" untuk membuat workshop pertama</flux:subheading>
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-4">
            {{ $workshops->links() }}
        </div>
    </div>
</x-layouts.app>
