<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Checkout' }} - Batik Giri Alam</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700&family=Figtree:wght@400;500;600&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-white">
        <!-- Navigation -->
        <nav class="fixed top-0 left-0 right-0 bg-white shadow-sm z-50" x-data="{ mobileOpen: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="{{ route('landing.home') }}" class="flex items-center">
                            <img src="{{ asset('assets/logo.png') }}" alt="Batik Giri Alam"
                                class="h-10 w-auto object-contain">
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden md:flex items-center space-x-8" style="font-family: 'Poppins', sans-serif;">
                        <a href="{{ route('landing.home') }}"
                            class="text-[#8B4513] hover:text-[#6B3410] font-medium transition-colors">
                            Beranda
                        </a>
                        <a href="{{ route('landing.about') }}"
                            class="text-[#8B4513] hover:text-[#6B3410] font-medium transition-colors">
                            Sejarah
                        </a>
                        <a href="{{ route('landing.shop') }}"
                            class="text-[#8B4513] hover:text-[#6B3410] font-medium transition-colors">
                            Produk
                        </a>
                        <a href="{{ route('landing.workshop') }}"
                            class="text-[#8B4513] hover:text-[#6B3410] font-medium transition-colors">
                            Workshop
                        </a>
                        <a href="{{ route('landing.contact') }}"
                            class="text-[#8B4513] hover:text-[#6B3410] font-medium transition-colors">
                            Kontak
                        </a>
                    </div>

                    <!-- Auth Buttons & Cart (Desktop) -->
                    <div class="hidden md:flex items-center gap-6" style="font-family: 'Poppins', sans-serif;">
                        @auth
                            <!-- Cart Icon with Badge -->
                            <a href="{{ route('cart.index') }}"
                                class="relative text-[#8B4513] hover:text-[#6B3410] transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 4a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span
                                    class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-semibold"
                                    id="cart-badge">
                                    {{ Auth::user()->cart ? Auth::user()->cart->items->count() : 0 }}
                                </span>
                            </a>
                            <!-- Dashboard Button -->
                            <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('customer.dashboard') }}"
                                class="flex items-center gap-2 px-4 py-2 bg-[#8B4513] text-white rounded hover:bg-[#6B3410] font-medium transition-colors">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="px-4 py-2 text-[#8B4513] hover:text-[#6B3410] font-medium transition-colors">
                                Masuk
                            </a>
                            <a href="{{ route('register') }}"
                                class="px-4 py-2 bg-[#8B4513] text-white rounded hover:bg-[#6B3410] font-medium transition-colors">
                                Daftar
                            </a>
                        @endauth
                    </div>

                    <!-- Mobile menu button -->
                    <div class="md:hidden">
                        <button type="button" @click="mobileOpen = !mobileOpen"
                            class="text-gray-700 hover:text-gray-900 focus:outline-none">
                            <!-- Hamburger icon -->
                            <svg x-show="!mobileOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <!-- Close icon -->
                            <svg x-show="mobileOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu Panel -->
            <div x-show="mobileOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="md:hidden bg-white border-t border-gray-100 shadow-md"
                 style="font-family: 'Poppins', sans-serif;">
                <div class="px-4 py-3 space-y-1">
                    <a href="{{ route('landing.home') }}"
                        class="block py-2 px-3 rounded text-[#8B4513] hover:bg-[#FDF8F3] font-medium transition-colors">
                        Beranda
                    </a>
                    <a href="{{ route('landing.about') }}"
                        class="block py-2 px-3 rounded text-[#8B4513] hover:bg-[#FDF8F3] font-medium transition-colors">
                        Sejarah
                    </a>
                    <a href="{{ route('landing.shop') }}"
                        class="block py-2 px-3 rounded text-[#8B4513] hover:bg-[#FDF8F3] font-medium transition-colors">
                        Produk
                    </a>
                    <a href="{{ route('landing.workshop') }}"
                        class="block py-2 px-3 rounded text-[#8B4513] hover:bg-[#FDF8F3] font-medium transition-colors">
                        Workshop
                    </a>
                    <a href="{{ route('landing.contact') }}"
                        class="block py-2 px-3 rounded text-[#8B4513] hover:bg-[#FDF8F3] font-medium transition-colors">
                        Kontak
                    </a>

                    <div class="border-t border-gray-100 pt-3 mt-2 flex flex-col gap-2">
                        @auth
                            <a href="{{ route('cart.index') }}"
                                class="flex items-center gap-2 py-2 px-3 rounded text-[#8B4513] hover:bg-[#FDF8F3] font-medium transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 4a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Keranjang
                                <span class="ml-auto bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-semibold">
                                    {{ Auth::user()->cart ? Auth::user()->cart->items->count() : 0 }}
                                </span>
                            </a>
                            <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('customer.dashboard') }}"
                                class="block py-2 px-3 rounded bg-[#8B4513] text-white font-medium text-center hover:bg-[#6B3410] transition-colors">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="block py-2 px-3 rounded text-[#8B4513] hover:bg-[#FDF8F3] font-medium transition-colors text-center">
                                Masuk
                            </a>
                            <a href="{{ route('register') }}"
                                class="block py-2 px-3 rounded bg-[#8B4513] text-white font-medium text-center hover:bg-[#6B3410] transition-colors">
                                Daftar
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content with padding for fixed navbar -->
        <main class="pt-16">
            @if(View::hasSection('content'))
                @yield('content')
            @else
                {{ $slot }}
            @endif
        </main>

        <!-- Footer -->
        <footer class="bg-[#f5f1e8] text-gray-700 py-12 mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
                    <!-- Logo & Description -->
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <img src="{{ asset('assets/logo.png') }}" alt="Batik Giri Alam" class="h-10">
                        </div>
                        <p class="text-sm leading-relaxed" style="font-family: 'Poppins', sans-serif;">
                            Batik Giri Alam adalah warisan budaya yang dikembangkan dari desain asli Indonesia yang
                            tradisional dan menggambarkan filosofi masyarakat.
                        </p>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h3 class="font-bold text-sm mb-4 uppercase" style="font-family: 'Poppins', sans-serif;">Quick
                            Links</h3>
                        <ul class="space-y-2 text-sm" style="font-family: 'Poppins', sans-serif;">
                            <li><a href="{{ route('landing.home') }}"
                                    class="hover:text-[#8B4513] transition-colors">Beranda</a></li>
                            <li><a href="{{ route('landing.about') }}"
                                    class="hover:text-[#8B4513] transition-colors">Sejarah</a></li>
                            <li><a href="{{ route('landing.shop') }}"
                                    class="hover:text-[#8B4513] transition-colors">Produk</a></li>
                            <li><a href="{{ route('landing.workshop') }}"
                                    class="hover:text-[#8B4513] transition-colors">Workshop</a></li>
                            <li><a href="{{ route('landing.contact') }}"
                                    class="hover:text-[#8B4513] transition-colors">Kontak</a></li>
                        </ul>
                    </div>

                    <!-- Customer Service -->
                    <div>
                        <h3 class="font-bold text-sm mb-4 uppercase" style="font-family: 'Poppins', sans-serif;">
                            Customer Service</h3>
                        <ul class="space-y-2 text-sm" style="font-family: 'Poppins', sans-serif;">
                            <li><a href="#" class="hover:text-[#8B4513] transition-colors">FAQ</a></li>
                            <li><a href="#" class="hover:text-[#8B4513] transition-colors">Shipping &
                                    Returns</a>
                            </li>
                            <li><a href="#" class="hover:text-[#8B4513] transition-colors">Terms &
                                    Conditions</a>
                            </li>
                            <li><a href="#" class="hover:text-[#8B4513] transition-colors">Privacy Policy</a>
                            </li>
                        </ul>
                    </div>

                    <!-- Contact Us -->
                    <div>
                        <h3 class="font-bold text-sm mb-4 uppercase" style="font-family: 'Poppins', sans-serif;">
                            Contact
                            Us</h3>
                        <ul class="space-y-3 text-sm" style="font-family: 'Poppins', sans-serif;">
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-[#8B4513] flex-shrink-0 mt-0.5" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>Desa Gumelem Wetan, Kecamatan Susukan, Kabupaten Banjarnegara, Jawa Tengah</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#8B4513] flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                    </path>
                                </svg>
                                <span>+62 821-3530-5328</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#8B4513] flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span>info@batikgirialam.com</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Copyright -->
                <div class="border-t border-gray-300 pt-6 text-center">
                    <p class="text-sm" style="font-family: 'Poppins', sans-serif;">
                        &copy; {{ date('Y') }} Batikgirialam. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>
    </div>

    @livewireScripts
</body>

</html>
