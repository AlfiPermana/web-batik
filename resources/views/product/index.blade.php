<x-layouts.app :title="__('Product')">
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="xl">Product</flux:heading>
                <flux:subheading>Manage your product catalog</flux:subheading>
            </div>
            <flux:button :href="route('admin.product.create')" icon="plus" variant="primary">
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
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Photo</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Base Price</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Sizes</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Images</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse($products as $product)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <img src="{{ asset('storage/' . $product->photo) }}" alt="{{ $product->title }}" class="w-16 h-16 object-cover rounded-lg">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $product->title }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-indigo-600 dark:text-indigo-400">
                                        Rp {{ number_format($product->amount, 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($product->sizes->count() > 0)
                                        <div class="flex flex-col gap-1">
                                            @foreach($product->sizes->take(2) as $size)
                                                <div class="text-xs">
                                                    <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $size->size }}:</span>
                                                    <span class="text-blue-600 dark:text-blue-400">Rp {{ number_format($size->price, 0, ',', '.') }}</span>
                                                </div>
                                            @endforeach
                                            @if($product->sizes->count() > 2)
                                                <span class="text-xs text-gray-500 dark:text-gray-400 italic">+{{ $product->sizes->count() - 2 }} more</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 dark:text-gray-500">No sizes</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($product->images->count() > 0)
                                        <div class="flex items-center gap-2">
                                            <div class="flex -space-x-2">
                                                @foreach($product->images->take(3) as $image)
                                                    <img src="{{ asset('storage/' . $image->photo) }}"
                                                         alt="Product Image"
                                                         class="w-8 h-8 rounded-full border-2 border-white dark:border-zinc-900 object-cover">
                                                @endforeach
                                            </div>
                                            @if($product->images->count() > 3)
                                                <span class="text-xs text-gray-500 dark:text-gray-400">+{{ $product->images->count() - 3 }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 dark:text-gray-500">No images</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex justify-center gap-2">
                                        <flux:button size="sm" :href="route('admin.product.edit', $product)" icon="pencil" variant="ghost">
                                            Edit
                                        </flux:button>
                                        <flux:button size="sm" onclick="openDeleteModal({{ $product->id }}, '{{ addslashes($product->title) }}')" icon="trash" variant="danger">
                                            Delete
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <flux:heading size="lg" class="text-zinc-400">No products found</flux:heading>
                                    <flux:subheading class="mt-2">Click "Add New" to create your first product</flux:subheading>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </div>

    <!-- Delete Modal -->
    <flux:modal name="delete-modal" class="space-y-6">
        <div>
            <flux:heading size="lg">Delete Product</flux:heading>
            <flux:subheading>Are you sure you want to delete this product?</flux:subheading>
        </div>

        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm text-red-800">This action cannot be undone. The product "<span id="delete-title" class="font-semibold"></span>" will be permanently deleted.</p>
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
            document.getElementById('delete-form').action = `/admin/product/${id}`;
            document.getElementById('delete-title').textContent = title;
            Flux.modal('delete-modal').show();
        }

        function closeDeleteModal() {
            Flux.modal('delete-modal').close();
        }
    </script>
</x-layouts.app>
