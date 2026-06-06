<div class="bg-gray-50 py-12" style="font-family: 'Poppins', sans-serif;">
    <div class="max-w-6xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold mb-2" style="font-family: 'Playfair Display', serif; color: #8B4513;">
                Checkout
            </h1>
            <p class="text-gray-600">Langkah {{ $currentStep }} dari {{ $totalSteps }}</p>
        </div>

        <!-- Progress Bar -->
        <div class="mb-12">
            <div class="flex justify-between items-start mb-6 px-2">
                @for ($i = 1; $i <= $totalSteps; $i++)
                    <div class="flex flex-col items-center flex-1">
                        <!-- Step Circle -->
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white mb-2"
                             style="background-color: {{ $i <= $currentStep ? '#8B4513' : '#D1D5DB' }};">
                            {{ $i }}
                        </div>
                        <!-- Step Label -->
                        <p class="text-xs font-semibold text-center" style="color: {{ $i <= $currentStep ? '#8B4513' : '#999999' }};">
                            @if ($i == 1) Alamat
                            @elseif ($i == 2) Pengiriman
                            @elseif ($i == 3) Pembayaran
                            @else Konfirmasi @endif
                        </p>
                    </div>
                @endfor
            </div>
            <!-- Progress Line -->
            <div class="flex h-1 bg-gray-200 rounded-full overflow-hidden">
                <div class="bg-[#8B4513] transition-all duration-300" style="width: {{ ($currentStep / $totalSteps) * 100 }}%"></div>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Main Content -->
            <div class="w-full">
                @if ($currentStep == 1)
                    @include('livewire.checkout.step-1')
                @elseif ($currentStep == 2)
                    @include('livewire.checkout.step-2')
                @elseif ($currentStep == 3)
                    @include('livewire.checkout.step-3')
                @else
                    @include('livewire.checkout.step-4')
                @endif
            </div>

            <!-- Sidebar: Order Summary (Show on all steps, visible on desktop and mobile) -->
            <div class="w-full">
                @include('livewire.checkout.summary')
            </div>

            <!-- Navigation Buttons - Below Summary -->
            <div class="flex gap-3 justify-end relative z-10">
                @if ($currentStep > 1)
                    <button type="button" wire:click="previousStep"
                            class="px-6 py-3 rounded-lg border border-gray-300 text-gray-900 font-semibold hover:bg-gray-50 transition-colors">
                        ← Kembali
                    </button>
                @endif

                @if ($currentStep < $totalSteps)
                    <button type="button" wire:click="nextStep"
                            class="px-8 py-3 rounded-lg font-semibold text-white transition-colors"
                            style="background-color: #8B4513;"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            wire:target="nextStep">
                        <span wire:loading.remove wire:target="nextStep">Lanjutkan</span>
                        <span wire:loading wire:target="nextStep"><svg class="inline-block w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Memproses...</span>
                    </button>
                @else
                    <button type="button"
                            x-data="{ confirming: false }"
                            x-on:click="confirming = true; setTimeout(() => confirming = false, 1500)"
                            wire:click.prevent="confirmOrder"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            wire:target="confirmOrder"
                            class="px-8 py-3 rounded-lg font-semibold text-white bg-[#8B4513] hover:bg-[#6B3410] hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-200 cursor-pointer">
                        <span x-show="!confirming" wire:loading.remove wire:target="confirmOrder">Konfirmasi Order</span>
                    </button>
                @endif
            </div>

        </div>

        <!-- Tripay Payment Modal -->
        @include('livewire.checkout.payment-modal')
    </div>
</div>
