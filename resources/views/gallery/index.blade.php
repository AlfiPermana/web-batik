<x-layouts.app :title="__('Gallery')">
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="xl">Gallery</flux:heading>
                <flux:subheading>Manage your gallery images</flux:subheading>
            </div>
            <flux:button onclick="openCreateModal()" icon="plus" variant="primary">
                Add New
            </flux:button>
        </div>

        @if(session('success'))
            <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            @forelse($galleries as $gallery)
                <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 group">
                    <img src="{{ asset('storage/' . $gallery->photo) }}"
                         alt="{{ $gallery->title }}"
                         class="absolute inset-0 size-full object-cover">

                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <h3 class="text-white font-semibold mb-2 truncate">{{ $gallery->title }}</h3>
                            <div class="flex gap-2">
                                <flux:button size="sm" onclick="openEditModal({{ $gallery->id }}, '{{ $gallery->title }}', '{{ asset('storage/' . $gallery->photo) }}')" icon="pencil" variant="primary">
                                    Edit
                                </flux:button>
                                <flux:button size="sm" onclick="openDeleteModal({{ $gallery->id }}, '{{ $gallery->title }}')" icon="trash" variant="danger">
                                    Delete
                                </flux:button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    <flux:heading size="lg" class="text-zinc-400">No galleries found</flux:heading>
                    <flux:subheading class="mt-2">Click "Add New" to create your first gallery</flux:subheading>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $galleries->links() }}
        </div>
    </div>

    <!-- Create Modal -->
    <flux:modal name="create-modal" class="space-y-6">
        <div>
            <flux:heading size="lg">Create New Gallery</flux:heading>
            <flux:subheading>Add a new image to your gallery</flux:subheading>
        </div>

        <form action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <flux:input
                name="title"
                label="Title"
                type="text"
                placeholder="Enter gallery title"
                required
            />

            <flux:field>
                <flux:label>Photo</flux:label>
                <input
                    type="file"
                    name="photo"
                    accept="image/*"
                    required
                    class="block w-full text-sm text-zinc-500
                           file:mr-4 file:py-2 file:px-4
                           file:rounded-md file:border-0
                           file:text-sm file:font-semibold
                           file:bg-zinc-100 file:text-zinc-700
                           hover:file:bg-zinc-200"
                />
            </flux:field>

            <div class="flex gap-3">
                <flux:button type="submit" variant="primary">Create</flux:button>
                <flux:button type="button" onclick="closeCreateModal()" variant="ghost">Cancel</flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Edit Modal -->
    <flux:modal name="edit-modal" class="space-y-6">
        <div>
            <flux:heading size="lg">Edit Gallery</flux:heading>
            <flux:subheading>Update gallery information</flux:subheading>
        </div>

        <form id="edit-form" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <flux:input
                id="edit-title"
                name="title"
                label="Title"
                type="text"
                placeholder="Enter gallery title"
                required
            />

            <flux:field>
                <flux:label>Current Photo</flux:label>
                <img id="edit-current-photo" src="" alt="" class="w-48 h-48 object-cover rounded-xl border border-neutral-200 dark:border-neutral-700 mb-3">
            </flux:field>

            <flux:field>
                <flux:label>New Photo (optional)</flux:label>
                <input
                    type="file"
                    name="photo"
                    accept="image/*"
                    class="block w-full text-sm text-zinc-500
                           file:mr-4 file:py-2 file:px-4
                           file:rounded-md file:border-0
                           file:text-sm file:font-semibold
                           file:bg-zinc-100 file:text-zinc-700
                           hover:file:bg-zinc-200"
                />
                <flux:description>Leave empty to keep current photo</flux:description>
            </flux:field>

            <div class="flex gap-3">
                <flux:button type="submit" variant="primary">Update</flux:button>
                <flux:button type="button" onclick="closeEditModal()" variant="ghost">Cancel</flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Delete Modal -->
    <flux:modal name="delete-modal" class="space-y-6">
        <div>
            <flux:heading size="lg">Delete Gallery</flux:heading>
            <flux:subheading>Are you sure you want to delete this gallery?</flux:subheading>
        </div>

        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm text-red-800">This action cannot be undone. The gallery "<span id="delete-title" class="font-semibold"></span>" will be permanently deleted.</p>
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
        function openCreateModal() {
            Flux.modal('create-modal').show();
        }

        function closeCreateModal() {
            Flux.modal('create-modal').close();
        }

        function openEditModal(id, title, photo) {
            document.getElementById('edit-form').action = `/admin/gallery/${id}`;
            document.getElementById('edit-title').value = title;
            document.getElementById('edit-current-photo').src = photo;
            Flux.modal('edit-modal').show();
        }

        function closeEditModal() {
            Flux.modal('edit-modal').close();
        }

        function openDeleteModal(id, title) {
            document.getElementById('delete-form').action = `/admin/gallery/${id}`;
            document.getElementById('delete-title').textContent = title;
            Flux.modal('delete-modal').show();
        }

        function closeDeleteModal() {
            Flux.modal('delete-modal').close();
        }
    </script>
</x-layouts.app>