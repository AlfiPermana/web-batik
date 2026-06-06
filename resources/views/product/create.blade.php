<x-layouts.app :title="__('Create Product')">
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <div class="mb-2">
            <flux:heading size="xl">Create New Product</flux:heading>
            <flux:subheading>Add a new product to your catalog</flux:subheading>
        </div>

        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 max-w-4xl">
            <form action="{{ route('admin.product.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <flux:input
                            name="title"
                            label="Title"
                            type="text"
                            placeholder="Enter product title"
                            value="{{ old('title') }}"
                            required
                        />
                        @error('title')
                            <span class="text-sm text-red-600 mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <flux:input
                            name="amount"
                            label="Amount (Rp)"
                            type="number"
                            step="0.01"
                            min="0"
                            placeholder="Enter amount"
                            value="{{ old('amount') }}"
                            required
                        />
                        @error('amount')
                            <span class="text-sm text-red-600 mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>


                <div>
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
                        @error('photo')
                            <span class="text-sm text-red-600 mt-1">{{ $message }}</span>
                        @enderror
                    </flux:field>
                </div>

                <div>
                    <flux:field>
                        <flux:label>Description</flux:label>
                        <input id="description" type="hidden" name="description" value="{{ old('description') }}">
                        <trix-editor input="description" class="trix-content"></trix-editor>
                        @error('description')
                            <span class="text-sm text-red-600 mt-1">{{ $message }}</span>
                        @enderror
                    </flux:field>
                </div>

                <!-- Product Sizes -->
                <div>
                    <div class="flex justify-between items-center mb-3">
                        <flux:label>Product Sizes (Optional)</flux:label>
                        <flux:button type="button" size="sm" onclick="addSize()" icon="plus" variant="ghost">
                            Add Size
                        </flux:button>
                    </div>
                    <div id="sizes-container" class="space-y-3">
                        <!-- Size rows will be added here -->
                    </div>
                </div>

                <!-- Product Images -->
                <div>
                    <div class="flex justify-between items-center mb-3">
                        <flux:label>Additional Images (Optional)</flux:label>
                        <flux:button type="button" size="sm" onclick="addImage()" icon="plus" variant="ghost">
                            Add Image
                        </flux:button>
                    </div>
                    <div id="images-container" class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <!-- Image previews will be added here -->
                    </div>
                    @error('images')
                        <span class="text-sm text-red-600 mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex gap-3">
                    <flux:button type="submit" variant="primary">
                        Create Product
                    </flux:button>
                    <flux:button :href="route('admin.product.index')" variant="ghost">
                        Cancel
                    </flux:button>
                </div>
            </form>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/trix@2.0.0/dist/trix.css">
    <style>
        trix-toolbar .trix-button-group--file-tools { display: none; }

        trix-toolbar {
            background: #f4f4f5;
            border: 1px solid #d4d4d8;
            border-bottom: none;
            border-radius: 0.75rem 0.75rem 0 0;
            padding: 0.5rem;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        trix-editor {
            min-height: 400px;
            max-height: 600px;
            overflow-y: auto;
            border: 1px solid #d4d4d8;
            border-radius: 0 0 0.75rem 0.75rem;
            padding: 1rem;
            background: white;
            line-height: 1.6;
            font-size: 1rem;
        }

        trix-editor:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Styling untuk konten rich text */
        trix-editor h1 {
            font-size: 2em;
            font-weight: bold;
            margin: 1em 0 0.5em;
        }

        trix-editor ul, trix-editor ol {
            padding-left: 2em;
            margin: 1em 0;
        }

        trix-editor ul li {
            list-style-type: disc;
            color: #18181b;
        }

        trix-editor ol li {
            list-style-type: decimal;
            color: #18181b;
        }

        trix-editor ul li::marker,
        trix-editor ol li::marker {
            color: #18181b;
        }

        trix-editor blockquote {
            border-left: 4px solid #d4d4d8;
            padding-left: 1em;
            margin: 1em 0;
            color: #52525b;
        }

        trix-editor pre {
            background: #f4f4f5;
            padding: 1em;
            border-radius: 0.375rem;
            overflow-x: auto;
        }

        trix-editor a {
            color: #3b82f6;
            text-decoration: underline;
        }

        .dark trix-toolbar {
            background: #27272a;
            border-color: #52525b;
        }

        .dark trix-editor {
            border-color: #52525b;
            background-color: #18181b;
            color: #e4e4e7;
        }

        .dark trix-editor ul li,
        .dark trix-editor ol li {
            color: #e4e4e7;
        }

        .dark trix-editor ul li::marker,
        .dark trix-editor ol li::marker {
            color: #e4e4e7;
        }

        .dark trix-editor blockquote {
            border-left-color: #52525b;
            color: #a1a1aa;
        }

        .dark trix-editor pre {
            background: #27272a;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://unpkg.com/trix@2.0.0/dist/trix.umd.min.js"></script>
    <script>
        let sizeIndex = 0;
        let imageIndex = 0;

        function addSize() {
            const container = document.getElementById('sizes-container');
            const sizeRow = document.createElement('div');
            sizeRow.className = 'flex gap-3 items-start';
            sizeRow.id = `size-row-${sizeIndex}`;
            sizeRow.innerHTML = `
                <div class="flex-1">
                    <input
                        type="text"
                        name="sizes[${sizeIndex}][name]"
                        placeholder="Size name (e.g., S, M, L, XL)"
                        required
                        class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-zinc-800 dark:text-zinc-100"
                    />
                </div>
                <div class="flex-1">
                    <input
                        type="number"
                        name="sizes[${sizeIndex}][price]"
                        placeholder="Price (Rp)"
                        step="0.01"
                        min="0"
                        required
                        class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-zinc-800 dark:text-zinc-100"
                    />
                </div>
                <div class="w-40">
                    <input
                        type="number"
                        name="sizes[${sizeIndex}][stock]"
                        placeholder="Stock"
                        min="0"
                        required
                        class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-zinc-800 dark:text-zinc-100"
                    />
                </div>
                <button
                    type="button"
                    onclick="removeSize(${sizeIndex})"
                    class="px-3 py-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </button>
            `;
            container.appendChild(sizeRow);
            sizeIndex++;
        }

        function removeSize(index) {
            const row = document.getElementById(`size-row-${index}`);
            if (row) {
                row.remove();
            }
        }

        function addImage() {
            const container = document.getElementById('images-container');
            const imageBox = document.createElement('div');
            imageBox.className = 'relative group';
            imageBox.id = `image-box-${imageIndex}`;

            const currentIndex = imageIndex;

            imageBox.innerHTML = `
                <div class="aspect-square border-2 border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg flex items-center justify-center bg-zinc-50 dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors cursor-pointer overflow-hidden">
                    <input
                        type="file"
                        name="images[]"
                        accept="image/*"
                        class="hidden"
                        id="image-input-${currentIndex}"
                        onchange="previewImage(${currentIndex}, event)"
                    />
                    <label for="image-input-${currentIndex}" class="cursor-pointer flex flex-col items-center justify-center w-full h-full" id="image-placeholder-${currentIndex}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="text-xs text-zinc-500 mt-2">Click to upload</span>
                    </label>
                    <img id="image-preview-${currentIndex}" class="hidden w-full h-full object-cover" />
                </div>
                <button
                    type="button"
                    onclick="removeImage(${currentIndex})"
                    class="absolute top-2 right-2 bg-red-600 text-white p-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-700"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </button>
            `;

            container.appendChild(imageBox);
            imageIndex++;
        }

        function previewImage(index, event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById(`image-preview-${index}`);
                    const placeholder = document.getElementById(`image-placeholder-${index}`);

                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        }

        function removeImage(index) {
            const box = document.getElementById(`image-box-${index}`);
            if (box) {
                box.remove();
            }
        }
    </script>
    @endpush
</x-layouts.app>
