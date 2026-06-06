<x-layouts.app :title="__('Workshop')">
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="xl">Workshop</flux:heading>
                <flux:subheading>Manage your workshop programs</flux:subheading>
            </div>
            <flux:button :href="route('workshop.create')" icon="plus" variant="primary">
                Add New
            </flux:button>
        </div>

        @if(session('success'))
            <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-zinc-50 dark:bg-zinc-800 border-b border-neutral-200 dark:border-neutral-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse($workshops as $workshop)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ ($workshops->currentPage() - 1) * $workshops->perPage() + $loop->iteration }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $workshop->title }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-indigo-600 dark:text-indigo-400">
                                        Rp {{ number_format($workshop->amount, 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex justify-center gap-2">
                                        <flux:button size="sm" :href="route('workshop.edit', $workshop)" icon="pencil" variant="ghost">
                                            Edit
                                        </flux:button>
                                        <flux:button size="sm" onclick="openDeleteModal({{ $workshop->id }}, '{{ addslashes($workshop->title) }}')" icon="trash" variant="danger">
                                            Delete
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <flux:heading size="lg" class="text-zinc-400">No workshops found</flux:heading>
                                    <flux:subheading class="mt-2">Click "Add New" to create your first workshop</flux:subheading>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $workshops->links() }}
        </div>
    </div>

    <!-- Delete Modal -->
    <flux:modal name="delete-modal" class="space-y-6">
        <div>
            <flux:heading size="lg">Delete Workshop</flux:heading>
            <flux:subheading>Are you sure you want to delete this workshop?</flux:subheading>
        </div>

        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm text-red-800">This action cannot be undone. The workshop "<span id="delete-title" class="font-semibold"></span>" will be permanently deleted.</p>
        </div>

        <form id="delete-form" method="POST">
            @csrf
            @method('DELETE')

            <div class="flex gap-3">
                <flux:button type="submit" variant="danger">Delete</flux:button>
                <flux:button type="button" onclick="closeDeleteModal()" variant="ghost">Cancel</flux:button>
            </div>
        </form>
    </flux:modal>

    <script>
        function openDeleteModal(id, title) {
            document.getElementById('delete-form').action = `/workshop/${id}`;
            document.getElementById('delete-title').textContent = title;
            Flux.modal('delete-modal').show();
        }

        function closeDeleteModal() {
            Flux.modal('delete-modal').close();
        }
    </script>
</x-layouts.app>
