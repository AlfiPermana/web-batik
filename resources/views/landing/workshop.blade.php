<x-layouts.landing title="Workshop - Batik Giri Alam Gumelem Wetan">
    <!-- Hero Section -->
    <section class="relative py-24 md:py-32 bg-gray-900">
        <!-- Background Image -->
        <div class="absolute inset-0 overflow-hidden">
            <img src="{{ asset('assets/workshop/heroworkshop.png') }}" alt="Workshop Background"
                class="w-full h-full object-cover">
        </div>

        <!-- Overlay -->
        <div class="absolute inset-0 bg-black opacity-50"></div>

        <!-- Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-white relative z-10">
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-6 leading-tight"
                style="font-family: 'Playfair Display', serif;">
                EDUWISATA DAN<br>WORKSHOP<br>BATIK GIRI ALAM
            </h1>
            <a href="#paket"
                class="inline-block bg-white text-gray-900 px-6 py-2 text-xs font-medium rounded hover:bg-gray-100 transition-colors"
                style="font-family: 'Poppins', sans-serif;">
                Daftar Workshop
            </a>
        </div>
    </section>

    <!-- Mengapa Belajar Batik Section -->
    <section class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-12"
                style="font-family: 'Playfair Display', serif; color: #8B4513;">
                Mengapa belajar batik ?
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-24 h-24 mx-auto mb-4 flex items-center justify-center">
                        <img src="{{ asset('assets/workshop/gambar1.png') }}" alt="UNESCO" class="w-20 h-20 object-contain">
                    </div>
                    <p class="text-sm text-gray-700 leading-relaxed" style="font-family: 'Poppins', sans-serif;">
                        Melestarikan warisan budaya Indonesia yang di akui <strong>UNESCO</strong>
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-24 h-24 mx-auto mb-4 flex items-center justify-center">
                        <img src="{{ asset('assets/workshop/gambar2.png') }}" alt="Budaya Batik" class="w-20 h-20 object-contain">
                    </div>
                    <p class="text-sm text-gray-700 leading-relaxed" style="font-family: 'Poppins', sans-serif;">
                        Meningkatkan rasa cinta dan peduli terhadap produk <strong>Budaya Batik asli Indonesia</strong>
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-24 h-24 mx-auto mb-4 flex items-center justify-center">
                        <img src="{{ asset('assets/workshop/gambar3.png') }}" alt="Kesabaran" class="w-20 h-20 object-contain">
                    </div>
                    <p class="text-sm text-gray-700 leading-relaxed" style="font-family: 'Poppins', sans-serif;">
                        Meningkatkan <strong>Kesabaran</strong> dan <strong>melatih motorik</strong>
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-24 h-24 mx-auto mb-4 flex items-center justify-center">
                        <img src="{{ asset('assets/workshop/gambar4.png') }}" alt="Pengalaman" class="w-20 h-20 object-contain">
                    </div>
                    <p class="text-sm text-gray-700 leading-relaxed" style="font-family: 'Poppins', sans-serif;">
                        Memiliki <strong>pengalaman</strong> dan <strong>cerita baru</strong>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-12"
                style="font-family: 'Playfair Display', serif; color: #8B4513;">
                Ngapain Aja Si?
            </h2>
            <div class="workshop-gallery">
                @foreach(collect($imageFiles)->take(12) as $filename)
                    <div class="gallery-item">
                        <img src="{{ asset('assets/workshop/' . $filename) }}" alt="Workshop Image">
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Paket Workshop Section -->
    <section id="paket" class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-12"
                style="font-family: 'Playfair Display', serif; color: #8B4513;">
                DAFTAR<br>WORKSHOP
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($workshops as $index => $workshop)
                    <div class="bg-gray-50 shadow-md hover:shadow-lg transition-shadow p-6">
                        <h3 class="text-base font-bold mb-2" style="font-family: 'Poppins', sans-serif;">
                            PAKET {{ $index + 1 }}
                        </h3>

                        <p class="text-gray-600 mb-4 text-xs" style="font-family: 'Poppins', sans-serif;">
                            {{ $workshop->title }}
                        </p>

                        <div class="border-t border-gray-300 mb-4"></div>

                        <div class="text-xs text-gray-700 mb-6 space-y-2 workshop-description" style="font-family: 'Poppins', sans-serif;">
                            {!! $workshop->description !!}
                        </div>

                        <div class="border-t border-gray-300 pt-4 mb-6">
                            <p class="text-base font-semibold text-center text-gray-800" style="font-family: 'Poppins', sans-serif;">
                                Rp {{ number_format($workshop->amount, 0, ',', '.') }}/orang
                            </p>
                        </div>

                        <button
                            onclick="bookWorkshop({{ $workshop->id }})"
                            class="block w-full bg-[#8B4513] text-white text-center py-2.5 text-xs font-semibold hover:bg-[#6B3410] transition-colors rounded-full"
                            style="font-family: 'Poppins', sans-serif;">
                            BOOK NOW
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Custom Styles for Rich Text -->
    <style>
        .workshop-gallery {
            /* kotak pas */
            display: grid;
            width: min(100%, 980px);
            aspect-ratio: 1 / 1;
            margin: 0 auto;
            padding: 8px;

            grid-template-columns: repeat(4, minmax(0, 1fr));
            grid-template-rows: repeat(6, minmax(0, 1fr));
            gap: 6px;

            /* layout "random" tanpa bolong */
            grid-template-areas:
                "a a b c"
                "a a b c"
                "d e e c"
                "d f g g"
                "h f i j"
                "h k k l";

            background: #f3f4f6;
            overflow: hidden;
        }

        /* Collage: ukuran beda-beda tapi nyatu rapat */
        .gallery-item {
            position: relative;
            border-radius: 0;
            overflow: hidden;
            box-shadow: none;
            transition: transform 0.25s ease;
            background: #e5e7eb;
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.06);
        }

        /* Mapping 12 gambar -> area (semua area terisi, tidak ada yang kosong) */
        .gallery-item:nth-child(1)  { grid-area: a; }
        .gallery-item:nth-child(2)  { grid-area: b; }
        .gallery-item:nth-child(3)  { grid-area: c; }
        .gallery-item:nth-child(4)  { grid-area: d; }
        .gallery-item:nth-child(5)  { grid-area: e; }
        .gallery-item:nth-child(6)  { grid-area: f; }
        .gallery-item:nth-child(7)  { grid-area: g; }
        .gallery-item:nth-child(8)  { grid-area: h; }
        .gallery-item:nth-child(9)  { grid-area: i; }
        .gallery-item:nth-child(10) { grid-area: j; }
        .gallery-item:nth-child(11) { grid-area: k; }
        .gallery-item:nth-child(12) { grid-area: l; }

        @media (max-width: 1024px) {
            .workshop-gallery {
                width: min(100%, 840px);
                grid-template-columns: repeat(3, minmax(0, 1fr));
                grid-template-rows: repeat(6, minmax(0, 1fr));
                gap: 6px;

                grid-template-areas:
                    "a a b"
                    "a a l"
                    "d e c"
                    "d f g"
                    "h f i"
                    "h j k";
            }
        }

        @media (max-width: 640px) {
            .workshop-gallery {
                width: 100%;
                padding: 6px;
                gap: 5px;

                /* mobile: SAMAKAN struktur dengan desktop biar konsisten */
                grid-template-columns: repeat(4, minmax(0, 1fr));
                grid-template-rows: repeat(6, minmax(0, 1fr));
                grid-template-areas:
                    "a a b c"
                    "a a b c"
                    "d e e c"
                    "d f g g"
                    "h f i j"
                    "h k k l";
            }
        }

        /* Overlay saat hover */
        .overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
            color: white;
            padding: 16px 12px 8px;
            font-size: 16px;
            font-weight: 600;
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .gallery-item:hover .overlay {
            opacity: 1;
            transform: translateY(0);
        }
        .workshop-description ul {
            list-style-type: disc;
            padding-left: 1.5rem;
            margin: 1rem 0;
        }

        .workshop-description ol {
            list-style-type: decimal;
            padding-left: 1.5rem;
            margin: 1rem 0;
        }

        .workshop-description li {
            margin: 0.5rem 0;
            line-height: 1.5;
        }

        .workshop-description blockquote {
            border-left: 4px solid #d1d5db;
            padding-left: 1rem;
            margin: 1rem 0;
            font-style: italic;
            color: #6b7280;
        }

        .workshop-description strong {
            font-weight: 700;
            color: #111827;
        }

        .workshop-description em {
            font-style: italic;
        }

        .workshop-description p {
            margin: 0.5rem 0;
        }

        .workshop-description div {
            margin: 0.25rem 0;
        }

        .workshop-description h1,
        .workshop-description h2,
        .workshop-description h3,
        .workshop-description h4 {
            font-weight: 700;
            margin: 1rem 0 0.5rem 0;
        }

        .workshop-description a {
            color: #2563eb;
            text-decoration: underline;
        }
    </style>

    <!-- JavaScript for Workshop Booking -->
    <script>
        function bookWorkshop(workshopId) {
            // Redirect ke halaman booking dengan workshop ID
            window.location.href = `/workshop/${workshopId}/booking`;
        }
    </script>
</x-layouts.landing>
