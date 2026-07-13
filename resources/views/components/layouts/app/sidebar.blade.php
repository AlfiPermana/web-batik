<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
    @stack('styles')
</head>

<body class="min-h-screen bg-gradient-to-b from-white to-amber-50 dark:bg-zinc-800">
    <flux:sidebar sticky stashable class="border-e border-amber-200/70 bg-white dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ auth()->user()->role === 'admin' ? route('landing.home') : route('customer.dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
            <img src="{{ asset('assets/logo.png') }}" alt="{{ config('app.name', 'Laravel') }}" class="h-10 sm:h-12 w-auto object-contain">
        </a>

        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('Platform')" class="grid">
                @if(auth()->user()->role === 'admin')
                    {{-- Admin Menu --}}
                    <flux:navlist.item icon="home" :href="route('admin.dashboard')" :current="request()->routeIs('admin.dashboard')"
                        wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                    <flux:navlist.item icon="shopping-bag" :href="route('admin.product.index')"
                        :current="request()->routeIs('admin.product.*')" wire:navigate>{{ __('Product') }}</flux:navlist.item>

                    <flux:navlist.item icon="academic-cap" :href="route('admin.workshop.index')"
                        :current="request()->routeIs('admin.workshop.*')" wire:navigate>{{ __('Workshop') }}</flux:navlist.item>
                    <flux:navlist.item icon="inbox" :href="route('admin.orders.index')"
                        :current="request()->routeIs('admin.orders.*')" wire:navigate>{{ __('Orders') }}</flux:navlist.item>
                    <flux:navlist.item icon="map-pin" :href="route('admin.settings.shipping-origin')"
                        :current="request()->routeIs('admin.settings.shipping-origin')" wire:navigate>{{ __('Alamat Asal Pengiriman') }}</flux:navlist.item>
                @else
                    {{-- Customer Menu --}}
                    <flux:navlist.item icon="home" :href="route('customer.dashboard')" :current="request()->routeIs('customer.dashboard')"
                        wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                    <flux:navlist.item icon="shopping-bag" :href="route('landing.shop')"
                        :current="request()->routeIs('landing.shop')" wire:navigate>{{ __('Shop') }}</flux:navlist.item>
                    <flux:navlist.item icon="shopping-cart" :href="route('cart.index')"
                        :current="request()->routeIs('cart.index')" wire:navigate>{{ __('Cart') }}</flux:navlist.item>
                    <flux:navlist.item icon="receipt-refund" :href="route('orders.index')"
                        :current="request()->routeIs('orders.index')" wire:navigate>{{ __('My Orders') }}</flux:navlist.item>
                    <flux:navlist.item icon="map-pin" :href="route('my-addresses')"
                        :current="request()->routeIs('my-addresses')" wire:navigate>{{ __('Alamat Saya') }}</flux:navlist.item>
                    <flux:navlist.item icon="heart" :href="route('wishlist')"
                        :current="request()->routeIs('wishlist')" wire:navigate>{{ __('Wishlist') }}</flux:navlist.item>
                    <flux:navlist.item icon="academic-cap" :href="route('workshop.my-bookings')"
                        :current="request()->routeIs('workshop.my-bookings', 'workshop.booking.*')" wire:navigate>{{ __('Workshop Saya') }}</flux:navlist.item>
                @endif
            </flux:navlist.group>

            @if(auth()->user()->role !== 'admin')
                <flux:navlist.group :heading="__('Account')" class="grid">
                    <flux:navlist.item icon="cog" :href="route('profile.edit')"
                        :current="request()->routeIs('profile.edit', 'user-password.edit')" wire:navigate>{{ __('Settings') }}</flux:navlist.item>
                </flux:navlist.group>
            @endif
        </flux:navlist>

        <flux:spacer />

        <!-- Desktop User Menu -->
        <flux:dropdown class="hidden lg:block" position="bottom" align="start">
            <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()"
                icon:trailing="chevrons-up-down" />

            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden border-b border-amber-200/70 bg-white dark:border-zinc-700 dark:bg-zinc-900 relative !z-50" style="z-index: 9999;">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="bottom" align="end" class="z-50">
            <flux:profile class="cursor-pointer" :initials="auth()->user()->initials()" icon:trailing="chevron-down" />

            <flux:menu class="z-50">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @livewireStyles
    @fluxScripts
    @livewireScripts
    @stack('scripts')
</body>

</html>
