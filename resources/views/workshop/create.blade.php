<x-layouts.app :title="__('Create Workshop')">
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <div class="mb-2">
            <flux:heading size="xl">Create New Workshop</flux:heading>
            <flux:subheading>Add a new workshop program</flux:subheading>
        </div>

        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 max-w-2xl">
            <form action="{{ route('workshop.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <flux:input
                        name="title"
                        label="Title"
                        type="text"
                        placeholder="Enter workshop title"
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

                <div class="flex gap-3">
                    <flux:button type="submit" variant="primary">
                        Create Workshop
                    </flux:button>
                    <flux:button :href="route('workshop.index')" variant="ghost">
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
    @endpush
</x-layouts.app>
