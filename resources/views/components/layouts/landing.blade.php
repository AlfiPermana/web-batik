<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Batik Giri Alam Gumelem Wetan' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700&family=Figtree:wght@400;500;600&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-white">
        <!-- Navigation -->
        <nav class="fixed top-0 left-0 right-0 bg-white shadow-sm z-50">
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
                            class="text-[#8B4513] hover:text-[#6B3410] font-medium transition-colors {{ request()->routeIs('landing.home') ? 'font-bold' : '' }}">
                            Beranda
                        </a>
                        <a href="{{ route('landing.about') }}"
                            class="text-[#8B4513] hover:text-[#6B3410] font-medium transition-colors {{ request()->routeIs('landing.about') ? 'font-bold' : '' }}">
                            Sejarah
                        </a>
                        <a href="{{ route('landing.shop') }}"
                            class="text-[#8B4513] hover:text-[#6B3410] font-medium transition-colors {{ request()->routeIs('landing.shop') ? 'font-bold' : '' }}">
                            Produk
                        </a>
                        <a href="{{ route('landing.workshop') }}"
                            class="text-[#8B4513] hover:text-[#6B3410] font-medium transition-colors {{ request()->routeIs('landing.workshop') ? 'font-bold' : '' }}">
                            Workshop
                        </a>
                        <a href="{{ route('landing.contact') }}"
                            class="text-[#8B4513] hover:text-[#6B3410] font-medium transition-colors {{ request()->routeIs('landing.contact') ? 'font-bold' : '' }}">
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
                        <button type="button" onclick="toggleMobileMenu()"
                            class="text-gray-700 hover:text-gray-900 focus:outline-none">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div id="mobile-menu" class="hidden md:hidden border-t border-gray-200"
                style="font-family: 'Poppins', sans-serif;">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <a href="{{ route('landing.home') }}"
                        class="block px-3 py-2 text-[#8B4513] hover:bg-[#f5f1e8] rounded-md {{ request()->routeIs('landing.home') ? 'bg-[#f5f1e8] font-bold' : '' }}">
                        Beranda
                    </a>
                    <a href="{{ route('landing.about') }}"
                        class="block px-3 py-2 text-[#8B4513] hover:bg-[#f5f1e8] rounded-md {{ request()->routeIs('landing.about') ? 'bg-[#f5f1e8] font-bold' : '' }}">
                        Sejarah
                    </a>
                    <a href="{{ route('landing.shop') }}"
                        class="block px-3 py-2 text-[#8B4513] hover:bg-[#f5f1e8] rounded-md {{ request()->routeIs('landing.shop') ? 'bg-[#f5f1e8] font-bold' : '' }}">
                        Produk
                    </a>
                    <a href="{{ route('landing.workshop') }}"
                        class="block px-3 py-2 text-[#8B4513] hover:bg-[#f5f1e8] rounded-md {{ request()->routeIs('landing.workshop') ? 'bg-[#f5f1e8] font-bold' : '' }}">
                        Workshop
                    </a>
                    <a href="{{ route('landing.contact') }}"
                        class="block px-3 py-2 text-[#8B4513] hover:bg-[#f5f1e8] rounded-md {{ request()->routeIs('landing.contact') ? 'bg-[#f5f1e8] font-bold' : '' }}">
                        Kontak
                    </a>

                    <!-- Mobile Auth Buttons -->
                    <div class="border-t border-gray-200 pt-3 mt-3 space-y-2">
                        @auth
                            <a href="{{ route('cart.index') }}"
                                class="flex items-center gap-2 px-3 py-2 text-[#8B4513] hover:bg-[#f5f1e8] rounded-md transition-colors">
                                <span>🛒</span>
                                <span>Keranjang</span>
                                <span
                                    class="ml-auto bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-semibold">
                                    {{ Auth::user()->cart ? Auth::user()->cart->items->count() : 0 }}
                                </span>
                            </a>
                            <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('customer.dashboard') }}"
                                class="flex items-center gap-2 px-3 py-2 bg-[#8B4513] text-white hover:bg-[#6B3410] rounded-md transition-colors font-medium">
                                <span>Dashboard</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="block px-3 py-2 text-[#8B4513] hover:bg-[#f5f1e8] rounded-md">
                                Masuk
                            </a>
                            <a href="{{ route('register') }}"
                                class="block px-3 py-2 bg-[#8B4513] text-white hover:bg-[#6B3410] rounded-md text-center font-medium transition-colors">
                                Daftar
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="pt-16">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="bg-[#f5f1e8] text-gray-700 py-12">
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
                                    class="hover:text-[#8B4513] transition-colors">Tentami</a></li>
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
                                <span>+62 851-1123-0011</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#8B4513] flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span>girialamsupport@gmail.com</span>
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

    <!-- Floating WhatsApp Button -->
    <div id="whatsapp-float" class="fixed bottom-6 right-6 z-50 flex items-center gap-3">
        <!-- Tooltip -->
        <div id="whatsapp-tooltip"
            class="opacity-0 translate-x-4 pointer-events-none transition-all duration-300 ease-out">
            <div class="bg-white px-4 py-2 rounded-lg shadow-lg border border-gray-200">
                <p class="text-sm font-medium text-gray-900 whitespace-nowrap">
                    Hubungi Admin
                </p>
            </div>
        </div>

        <!-- WhatsApp Button -->
        <button id="whatsapp-button" onclick="openWhatsApp()"
            class="group relative w-16 h-16 rounded-full bg-green-500 hover:bg-green-600 shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center hover:scale-110"
            aria-label="Hubungi Admin via WhatsApp">
            <!-- WhatsApp Icon -->
            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
            </svg>

            <!-- Badge "Butuh Bantuan?" -->
            <div
                class="absolute -top-5 bg-white text-green-600 text-xs font-bold px-2 py-1 rounded-full shadow-md whitespace-nowrap">
                Butuh Bantuan?
            </div>

            <!-- Ripple effect on hover -->
            <span
                class="absolute inset-0 rounded-full bg-green-400 opacity-0 group-hover:opacity-30 group-hover:scale-150 transition-all duration-500"></span>
        </button>
    </div>

    @stack('scripts')
    
    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed top-20 right-4 z-40 space-y-3 pointer-events-none"></div>

    <script>
        // Livewire event listener for notifications
        
        // Initialize listeners immediately
        function initializeLivewireListeners() {
            Livewire.on('alert', (data) => {
                const message = typeof data === 'object' && !Array.isArray(data) ? data.message : (Array.isArray(data) ? data[0] : data);
                const type = typeof data === 'object' && !Array.isArray(data) ? data.type : (Array.isArray(data) ? data[1] : 'info');
                
                // Show browser alert
                const fullMessage = type === 'success' ? '✅ ' + message : type === 'error' ? '❌ ' + message : 'ℹ️ ' + message;
                alert(fullMessage);
                
                // Show toast notification
                showToast(message, type);
            });
            
            // Handle cart item count update
            Livewire.on('cart-item-count', (data) => {
                const count = typeof data === 'object' && !Array.isArray(data) ? data.count : (Array.isArray(data) ? data[0] : data);
                const badge = document.getElementById('cart-badge');
                if (badge) {
                    badge.textContent = count;
                }
            });

            // Handle notify event (for backward compatibility)
            Livewire.on('notify', (data) => {
                const message = typeof data === 'object' && !Array.isArray(data) ? data.message : (Array.isArray(data) ? data[0] : data);
                const type = typeof data === 'object' && !Array.isArray(data) ? data.type : (Array.isArray(data) ? data[1] : 'info');
                showToast(message, type);
            });

            // Update cart badge count
            Livewire.on('cart-updated', () => {
                updateCartBadge();
            });
        }
        
        // Initialize on livewire init
        document.addEventListener('livewire:init', () => {
            initializeLivewireListeners();
        });
        
        // Also initialize immediately in case Livewire is already ready
        if (typeof Livewire !== 'undefined' && Livewire.initialized) {
            initializeLivewireListeners();
        }

        function updateCartBadge() {
            // This function is called when cart-updated event is fired
            // No need to reload - badge is updated via cart-item-count event
            // This function is here for backward compatibility
        }

        function showToast(message, type = 'info') {
            const container = document.getElementById('toast-container');
            
            const bgColor = {
                'success': 'bg-green-500',
                'error': 'bg-red-500',
                'warning': 'bg-yellow-500',
                'info': 'bg-blue-500'
            }[type] || 'bg-blue-500';

            const toast = document.createElement('div');
            toast.className = `${bgColor} text-white px-6 py-3 rounded-lg shadow-lg pointer-events-auto animate-slide-in`;
            toast.innerHTML = `
                <div class="flex items-center gap-2">
                    ${type === 'success' ? '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>' : ''}
                    ${type === 'error' ? '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>' : ''}
                    <span>${message}</span>
                </div>
            `;
            
            container.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'slide-out 0.3s ease-out forwards';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }

        // Floating WhatsApp Button
        const whatsappButton = document.getElementById('whatsapp-button');
        const whatsappTooltip = document.getElementById('whatsapp-tooltip');

        whatsappButton.addEventListener('mouseenter', () => {
            whatsappTooltip.classList.remove('opacity-0', 'translate-x-4', 'pointer-events-none');
            whatsappTooltip.classList.add('opacity-100', 'translate-x-0');
        });

        whatsappButton.addEventListener('mouseleave', () => {
            whatsappTooltip.classList.remove('opacity-100', 'translate-x-0');
            whatsappTooltip.classList.add('opacity-0', 'translate-x-4', 'pointer-events-none');
        });

        function openWhatsApp() {
            const phoneNumber = '6285111230011';
            const message = 'Halo, saya butuh bantuan tentang produk Batik Giri Alam';
            const formattedNumber = phoneNumber.replace(/\D/g, '');
            const encodedMessage = encodeURIComponent(message);
            const whatsappUrl = `https://wa.me/${formattedNumber}?text=${encodedMessage}`;
            window.open(whatsappUrl, '_blank');
        }
    </script>

    <style>
        @keyframes slide-in {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slide-out {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }

        .animate-slide-in {
            animation: slide-in 0.3s ease-out;
        }
    </style>
    @livewireScripts
    @stack('scripts')
</body>

</html>
