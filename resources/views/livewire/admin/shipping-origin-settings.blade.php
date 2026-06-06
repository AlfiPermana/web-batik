<div class="space-y-6">
    <div>
        <flux:heading size="xl">Alamat Asal Pengiriman</flux:heading>
        <flux:subheading>Atur lokasi admin/asal barang untuk perhitungan ongkir</flux:subheading>
    </div>

    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-4">
        <flux:heading size="lg">Konfigurasi Saat Ini</flux:heading>

        @if($currentOrigin)
            <div class="text-sm text-gray-700 dark:text-gray-300 space-y-1">
                <div><span class="font-semibold">Kota/Kab:</span> {{ $currentOrigin->origin_city_name ?? '-' }} <span class="text-gray-500">({{ $currentOrigin->origin_city_id ?? '-' }})</span></div>
                <div><span class="font-semibold">Kecamatan:</span> {{ $currentOrigin->origin_district_name ?? '-' }} <span class="text-gray-500">({{ $currentOrigin->origin_district_id ?? '-' }})</span></div>
                <div><span class="font-semibold">Alamat:</span> {{ $currentOrigin->address ?? '-' }}</div>
                <div><span class="font-semibold">Jasa Kirim:</span>
                    @php
                        $enabled = is_array($currentOrigin->enabled_couriers ?? null) ? $currentOrigin->enabled_couriers : [];
                        $enabled = array_values(array_filter(array_map(fn($c) => strtoupper(trim((string)$c)), $enabled)));
                    @endphp
                    {{ $enabled ? implode(', ', $enabled) : 'AUTO (dari API)' }}
                </div>
            </div>
        @else
            <div class="text-sm text-gray-700 dark:text-gray-300">Belum ada alamat asal diset. Silakan isi form di bawah.</div>
        @endif
    </div>

    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-5">
        <flux:heading size="lg">Update Alamat Asal</flux:heading>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-2">Provinsi</label>
                <select wire:model.live="provinceId" class="w-full px-4 py-2 rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-zinc-900">
                    <option value="">-- Pilih Provinsi --</option>
                    @foreach($provinces as $prov)
                        <option value="{{ $prov['id'] }}">{{ $prov['name'] }}</option>
                    @endforeach
                </select>
                @error('provinceId') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Kota / Kab</label>
                <select wire:model.live="cityId" class="w-full px-4 py-2 rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-zinc-900" @disabled(!$provinceId)>
                    <option value="">-- Pilih Kota/Kab --</option>
                    @foreach($cities as $c)
                        <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                    @endforeach
                </select>
                @error('cityId') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Kecamatan</label>
                <select wire:model.live="districtId" class="w-full px-4 py-2 rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-zinc-900" @disabled(!$cityId)>
                    <option value="">-- Pilih Kecamatan --</option>
                    @foreach($districts as $d)
                        <option value="{{ $d['id'] }}">{{ $d['name'] }}</option>
                    @endforeach
                </select>
                @error('districtId') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-2">Alamat Lengkap (opsional)</label>
            <textarea wire:model="address" rows="3" placeholder="Contoh: Jl. ..., RT/RW, Kelurahan ..." class="w-full px-4 py-2 rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-zinc-900"></textarea>
            @error('address') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-2">
            <div class="flex items-center justify-between gap-3">
                <label class="block text-sm font-medium">Jasa Kirim yang Ditampilkan ke Customer</label>
                <div class="flex gap-2">
                    <flux:button size="sm" variant="ghost" wire:click="selectAllCouriers">Pilih Semua</flux:button>
                    <flux:button size="sm" variant="ghost" wire:click="clearCouriers">Kosongkan</flux:button>
                </div>
            </div>

            <p class="text-xs text-gray-600 dark:text-gray-400">
                Kosongkan pilihan jasa kirim untuk menampilkan semua kurir yang tersedia dari API.
            </p>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                @foreach($courierCatalog as $c)
                    <label class="flex items-center gap-2 px-3 py-2 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900">
                        <input type="checkbox" wire:model.live="enabledCouriers" value="{{ $c['code'] }}">
                        <span class="text-sm">{{ $c['name'] }}</span>
                    </label>
                @endforeach
            </div>
            @error('enabledCouriers') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end">
            <flux:button variant="primary" wire:click="save" wire:loading.attr="disabled">Simpan</flux:button>
        </div>
    </div>
</div>
