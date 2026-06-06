@use('App\Models\User')

<x-layouts.app>
    <div class="space-y-6 sm:space-y-8">
        <!-- Page Header -->
        <div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold leading-tight" style="font-family: 'Playfair Display', serif; color: #8B4513;">
                {{ __('Welcome, ') . auth()->user()->name }}
            </h1>
            <p class="mt-1.5 text-sm sm:text-base text-gray-600" style="font-family: 'Poppins', sans-serif;">
                {{ __('Manage your orders and wishlist') }}
            </p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <!-- Total Orders Card -->
            <div class="rounded-lg border border-gray-200 bg-gradient-to-br from-blue-50 to-blue-100 p-6 dark:border-gray-700 dark:from-blue-900 dark:to-blue-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ __('Total Orders') }}
                        </p>
                        <p class="mt-2 text-4xl font-bold text-gray-900 dark:text-white">
                            {{ auth()->user()->orders()->count() ?? 0 }}
                        </p>
                    </div>
                    <svg class="h-12 w-12 text-blue-400 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 6H6.28l-.31-1.243A1 1 0 005 4H3a1 1 0 000 2h1.23l.447 1.789h2.111l.16.803H5a1 1 0 000 2h1.696l.464 1.858H5a1 1 0 000 2h2.18l.316 1.264A1 1 0 0010 17h7a1 1 0 000-2h-1.695L15 12.5a1 1 0 000-2z"/>
                    </svg>
                </div>
                <div class="mt-4">
                    <a href="{{ route('orders.index') }}" class="text-sm font-semibold hover:underline dark:text-blue-200" style="color: #8B4513; font-family: 'Poppins', sans-serif;">
                        {{ __('View My Orders') }}
                    </a>
                </div>
            </div>

            <!-- Wishlist Items Card -->
            <div class="rounded-lg border border-gray-200 bg-gradient-to-br from-rose-50 to-rose-100 p-6 dark:border-gray-700 dark:from-rose-900 dark:to-rose-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ __('Wishlist Items') }}
                        </p>
                        <p class="mt-2 text-4xl font-bold text-gray-900 dark:text-white">
                            {{ auth()->user()->wishlistItems()->count() ?? 0 }}
                        </p>
                    </div>
                    <svg class="h-12 w-12 text-rose-400 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="mt-4">
                    <a href="{{ route('wishlist') }}" class="text-sm font-semibold hover:underline dark:text-rose-200" style="color: #8B4513; font-family: 'Poppins', sans-serif;">
                        {{ __('View My Wishlist') }}
                    </a>
                </div>
            </div>

            <!-- Workshops Joined Card -->
            <div class="rounded-lg border border-gray-200 bg-gradient-to-br from-amber-50 to-amber-100 p-6 dark:border-gray-700 dark:from-amber-900 dark:to-amber-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ __('Workshops Joined') }}
                        </p>
                        <p class="mt-2 text-4xl font-bold text-gray-900 dark:text-white">
                            {{ auth()->user()->workshopBookings()->count() ?? 0 }}
                        </p>
                    </div>
                    <svg class="h-12 w-12 text-amber-400 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.5 1.5H5.75A2.25 2.25 0 003.5 3.75v12.5A2.25 2.25 0 005.75 18.5h8.5a2.25 2.25 0 002.25-2.25V6.5m-12-5v5m9-5v5M3.5 11.5h13"/>
                    </svg>
                </div>
                <div class="mt-4">
                    <a href="{{ route('workshop.my-bookings') }}" class="text-sm font-semibold text-amber-800 hover:underline dark:text-amber-200">
                        {{ __('View My Workshops') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Aktivitas Terbaru (Orders + Workshop) -->
        <div class="rounded-2xl border border-amber-200/70 bg-white p-4 sm:p-6 shadow-sm">
            <div class="mb-3 sm:mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-base sm:text-lg font-bold text-gray-900" style="font-family: 'Poppins', sans-serif;">
                    {{ __('Aktivitas Terbaru') }}
                </h2>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('orders.index') }}" class="text-sm font-semibold hover:underline" style="color: #8B4513; font-family: 'Poppins', sans-serif;">
                        {{ __('Pesanan') }}
                    </a>
                    <a href="{{ route('workshop.my-bookings') }}" class="text-sm font-semibold hover:underline" style="color: #8B4513; font-family: 'Poppins', sans-serif;">
                        {{ __('Workshop') }}
                    </a>
                </div>
            </div>

            @php
                $recent = $recentActivities ?? collect();
            @endphp

            @if($recent->count() === 0)
                <div class="rounded-xl border border-dashed border-amber-200/70 bg-amber-50/40 p-5 sm:p-6 text-center">
                    <p class="text-sm text-gray-700" style="font-family: 'Poppins', sans-serif;">{{ __('Belum ada aktivitas terbaru.') }}</p>
                    <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:justify-center">
                        <a href="{{ route('landing.shop') }}" class="inline-flex items-center justify-center rounded-xl px-5 py-2.5 text-sm font-semibold text-white transition-all hover:shadow" style="background-color: #8B4513; font-family: 'Poppins', sans-serif;">
                            {{ __('Belanja Produk') }}
                        </a>
                        <a href="{{ route('landing.workshop') }}" class="inline-flex items-center justify-center rounded-xl border border-amber-200 bg-white px-5 py-2.5 text-sm font-semibold text-gray-900 transition-all hover:bg-amber-50" style="font-family: 'Poppins', sans-serif;">
                            {{ __('Lihat Workshop') }}
                        </a>
                    </div>
                </div>
            @else
                <div class="divide-y divide-amber-100 overflow-hidden rounded-xl border border-amber-200/70">
                    @foreach($recent as $activity)
                        @php
                            $type = $activity['type'] ?? '-';
                            $typeLabel = $type === 'order' ? 'Order' : 'Workshop';
                            $typeClass = $type === 'order' ? 'bg-amber-100 text-amber-900' : 'bg-orange-100 text-orange-900';
                        @endphp
                        <div class="px-3 py-3 sm:px-4 sm:py-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $typeClass }}">
                                    {{ $typeLabel }}
                                </span>
                                <div class="min-w-0 flex-1 truncate font-semibold text-gray-900" style="font-family: 'Poppins', sans-serif;">
                                    {{ $activity['title'] ?? '-' }}
                                </div>
                            </div>

                            <div class="mt-1 text-sm text-gray-600" style="font-family: 'Poppins', sans-serif;">
                                {{ $activity['subtitle'] ?? '-' }}
                            </div>

                            <div class="mt-2 flex flex-wrap items-center justify-between gap-2">
                                <div class="text-xs text-gray-600" style="font-family: 'Poppins', sans-serif;">
                                    {{ ($activity['at'] ?? null)?->format('d M Y H:i') ?? '-' }}
                                </div>

                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $activity['badge_class'] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $activity['badge_label'] ?? '-' }}
                                    </span>

                                    @if(!empty($activity['url']))
                                        <a href="{{ $activity['url'] }}" class="inline-flex items-center text-xs font-semibold hover:underline" style="color: #8B4513; font-family: 'Poppins', sans-serif;">Lihat →</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
