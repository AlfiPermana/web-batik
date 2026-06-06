<button type="button"
        wire:click="toggle"
        class="inline-flex items-center justify-center rounded-full border transition-colors
               {{ $compact ? 'w-9 h-9' : 'w-10 h-10' }}
               {{ $isWishlisted ? 'bg-rose-600 border-rose-600 text-white' : 'bg-white border-gray-300 text-gray-600 hover:bg-rose-50 hover:border-rose-200 hover:text-rose-600' }}">
    <svg class="{{ $compact ? 'w-4 h-4' : 'w-5 h-5' }}" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
    </svg>
</button>
