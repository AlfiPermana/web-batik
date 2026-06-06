<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Booking Workshop Batik</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-white">
    <div class="workshop-booking-page">
        <!-- Header Section -->
        <div class="workshop-hero">
            <div class="container mx-auto px-4 py-12">
                <h1 class="text-4xl font-bold text-center mb-4">Booking Workshop Batik</h1>
                <p class="text-center text-amber-100 text-lg mb-8">
                    Pilih workshop dan slot waktu yang sesuai
                </p>
            </div>
        </div>

        <!-- Booking Form Container -->
        <div class="container mx-auto px-4 py-12 max-w-4xl">
            <!-- Workshop Selection Tabs -->
            <div class="mb-8">
                <div class="flex flex-wrap gap-2 mb-6">
                    @forelse($workshops as $workshop)
                        <a href="{{ route('workshop.index', ['id' => $workshop->id]) }}"
                            class="px-6 py-2 rounded-lg font-semibold transition
                            {{ request('id') == $workshop->id || (request('id') == null && $loop->first) ? 'bg-amber-700 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                            {{ $workshop->title }}
                        </a>
                    @empty
                        <p class="text-gray-500">Tidak ada workshop tersedia</p>
                    @endforelse
                </div>
            </div>

            <!-- Livewire Booking Form -->
            <div class="bg-white rounded-lg shadow-lg p-8">
                @php
                    $workshopId = request('id') ?? $workshops->first()?->id ?? 1;
                @endphp
                
                @if($workshops->isNotEmpty())
                    @livewire('workshop-booking-form', ['workshopId' => $workshopId])
                @else
                    <div class="text-center py-12">
                        <p class="text-gray-500 text-lg">Tidak ada workshop tersedia saat ini</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .workshop-hero {
            background: linear-gradient(135deg, #b45309 0%, #92400e 100%);
            color: white;
        }
    </style>

    @livewireScripts

    <script>
        // Listen for booking-success event from WorkshopBookingForm component
        document.addEventListener('livewire:init', () => {
            Livewire.on('booking-success', (data) => {
                console.log('Booking created successfully:', data);
                
                // Redirect to payment page
                const bookingId = data.booking_id;
                if (bookingId) {
                    window.location.href = `/workshop/booking/${bookingId}/payment`;
                }
            });
        });

        // Fallback if Livewire already initialized
        if (typeof Livewire !== 'undefined' && Livewire.initialized) {
            Livewire.on('booking-success', (data) => {
                console.log('Booking created successfully:', data);
                
                // Redirect to payment page
                const bookingId = data.booking_id;
                if (bookingId) {
                    window.location.href = `/workshop/booking/${bookingId}/payment`;
                }
            });
        }
    </script>
</body>
</html>
