<div class="space-y-6 sm:space-y-8">
    <!-- Header -->
    <div class="mb-6 sm:mb-8">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold mb-1.5" style="color: #8B4513; font-family: 'Playfair Display', serif;">
                    Alamat Saya
                </h1>
                <p class="text-sm sm:text-base text-gray-600">Kelola alamat pengiriman untuk pesanan Anda</p>
            </div>
            <a href="{{ route('add-address') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg text-white font-semibold transition-all hover:shadow-lg text-sm sm:text-base" style="background-color: #8B4513;">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Alamat
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <!-- Address List -->
    @forelse($addresses as $addr)
        <div class="bg-white rounded-xl border-l-4 p-4 sm:p-6 mb-4 transition-all hover:shadow-lg" style="border-left-color: {{ $addr['is_default'] ? '#8B4513' : '#D1D5DB' }};">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <h3 class="text-lg font-bold" style="color: #8B4513;">{{ $addr['label'] }}</h3>
                        @if($addr['is_default'])
                            <span class="px-3 py-1 rounded-full text-xs font-semibold text-white" style="background-color: #8B4513;">
                                ✓ Alamat Default
                            </span>
                        @endif
                    </div>

                    <div class="text-gray-700 space-y-1.5 mb-4">
                        <p class="font-semibold">{{ $addr['full_name'] }}</p>
                        <p class="text-sm text-gray-700">Telp: {{ $addr['phone_number'] }}</p>
                        <p class="text-sm">{{ $addr['address'] }}</p>
                        <p class="text-sm">
                            <span class="text-gray-600">{{ $addr['subdistrict'] }}, {{ $addr['district'] }}, {{ $addr['city'] }}, {{ $addr['province'] }}</span>
                            @if($addr['postal_code'])
                                <span class="text-gray-600"> - {{ $addr['postal_code'] }}</span>
                            @endif
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2">
                        <a href="{{ route('edit-address', $addr['id']) }}" class="w-full sm:w-auto px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors font-semibold text-center">
                            Edit
                        </a>
                        @if(!$addr['is_default'] && count($addresses) > 1)
                            <button wire:click="setDefaultAddress({{ $addr['id'] }})" class="w-full sm:w-auto px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors font-semibold text-center">
                                Jadikan Default
                            </button>
                        @endif
                        @if(count($addresses) > 1)
                            <button wire:click="deleteAddress({{ $addr['id'] }})" class="w-full sm:w-auto px-4 py-2 text-sm rounded-lg border border-red-300 text-red-600 hover:bg-red-50 transition-colors font-semibold text-center">
                                Hapus
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-gray-50 rounded-lg p-12 text-center border-2 border-dashed border-gray-300">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <p class="text-gray-600 mb-6 text-lg">Belum ada alamat tersimpan</p>
            <p class="text-gray-500 mb-6">Tambahkan alamat pertama Anda untuk memudahkan proses checkout</p>
            <a href="{{ route('add-address') }}" class="inline-block px-8 py-3 rounded-lg text-white font-semibold transition-all hover:shadow-lg" style="background-color: #8B4513;">
                + Tambah Alamat Sekarang
            </a>
        </div>
    @endforelse
</div>