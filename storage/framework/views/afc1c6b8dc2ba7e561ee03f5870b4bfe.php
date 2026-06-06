<?php if (isset($component)) { $__componentOriginald813adaee0594521b0d5afea3d810d12 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald813adaee0594521b0d5afea3d810d12 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.landing','data' => ['title' => 'Home - Batik Giri Alam Gumelem Wetan']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.landing'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Home - Batik Giri Alam Gumelem Wetan']); ?>
    <!-- Hero Section -->
    <section class="relative py-12 md:py-20 bg-[#f5f1e8]">
        <!-- Background Image -->
        <div class="absolute inset-0 overflow-hidden">
            <img src="<?php echo e(asset('assets/herobgbatik.png')); ?>" alt="Background Batik" class="w-full h-full object-cover">
        </div>

        <!-- Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-2xl">
                <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold mb-6 leading-tight"
                    style="font-family: 'Playfair Display', serif; color: #8B4513;">
                    Batik Giri Alam<br>Gumelem Wetan
                </h1>
                <p class="text-base md:text-lg mb-8 leading-relaxed text-gray-700 max-w-xl"
                    style="font-family: 'Poppins', sans-serif;">
                    Temukan keindahan batik tradisional<br>
                    dengan motif dan filosofi mendalam yang telah<br>
                    diwariskan dari generasi ke generasi
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="<?php echo e(route('landing.shop')); ?>"
                        class="text-center inline-block bg-[#8B4513] text-white px-8 py-3 text-sm font-medium rounded-lg hover:bg-[#6B3410] transition-all duration-300"
                        style="font-family: 'Poppins', sans-serif;">
                        Lihat Produk
                    </a>
                    <a href="<?php echo e(route('landing.workshop')); ?>"
                        class=" text-center inline-block bg-transparent text-[#8B4513] px-8 py-3 text-sm font-medium rounded-lg border-2 border-[#8B4513] hover:bg-[#8B4513] hover:text-white transition-all duration-300"
                        style="font-family: 'Poppins', sans-serif;">
                        Daftar Workshop
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Section -->
    <section class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold mb-3"
                    style="font-family: 'Playfair Display', serif; color: #8B4513;">
                    Produk Batik Giri Alam
                </h2>
                <p class="text-gray-600 text-sm md:text-base" style="font-family: 'Poppins', sans-serif;">
                    Karya terbaik dari penjaga tradisi, dibuat dengan desain kualitas premium dan<br
                        class="hidden md:block">
                    penuh dedikasi dari para artisan
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $products->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('landing.product.detail', $product->id)); ?>"
                        class="group shadow-md hover:shadow-lg transition-shadow duration-300 rounded-lg">
                        <div class="overflow-hidden rounded-lg mb-4 ">
                            <div class="aspect-square bg-gray-100 overflow-hidden">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->images->count() > 0): ?>
                                    <img src="<?php echo e(asset('storage/' . $product->images->first()->photo)); ?>"
                                        alt="<?php echo e($product->title); ?>"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <?php else: ?>
                                    <img src="<?php echo e(asset('storage/' . $product->photo)); ?>" alt="<?php echo e($product->title); ?>"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                        <h3 class="text-gray-800 font-medium mb-1 mx-2 text-sm"
                            style="font-family: 'Poppins', sans-serif;">
                            <?php echo e($product->title); ?>

                        </h3>
                        <p class="text-gray-900 font-semibold text-sm mx-2  mb-4"
                            style="font-family: 'Poppins', sans-serif;">
                            Rp <?php echo e(number_format($product->amount, 0, ',', '.')); ?>

                        </p>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="text-center">
                <a href="<?php echo e(route('landing.shop')); ?>"
                    class="inline-flex items-center gap-2 bg-[#8B4513] text-white px-6 py-3 text-sm font-medium rounded-lg hover:bg-[#6B3410] transition-all duration-300"
                    style="font-family: 'Poppins', sans-serif;">
                    <span>Lihat Semua Produk</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Warisan Budaya Section -->
    <section class="py-16 bg-[#f5f1e8]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Image -->
                <div class="order-2 lg:order-1">
                    <div class="rounded-2xl overflow-hidden shadow-lg">
                        <img src="<?php echo e(asset('assets/warisanbudaya.png')); ?>" alt="Warisan Budaya Batik Giri Alam"
                            class="w-full h-auto object-cover">
                    </div>
                </div>

                <!-- Content -->
                <div class="order-1 lg:order-2">
                    <h2 class="text-3xl md:text-4xl font-bold mb-6"
                        style="font-family: 'Playfair Display', serif; color: #8B4513;">
                        Warisan Budaya yang<br>Berharga
                    </h2>

                    <p class="text-gray-700 mb-6 leading-relaxed" style="font-family: 'Poppins', sans-serif;">
                        Batik Giri Alam adalah warisan budaya dari Desa Gumelem,<br class="hidden md:block">
                        Banjarnegara.
                    </p>

                    <p class="text-gray-700 mb-6 leading-relaxed" style="font-family: 'Poppins', sans-serif;">
                        Keunikan Batik Giri Alam terletak pada motif yang terinspirasi dari<br class="hidden md:block">
                        lingkungan alam sekitar dan filosofi kehidupan masyarakat<br class="hidden md:block">
                        setempat.
                    </p>

                    <ul class="space-y-3 mb-8">
                        <li class="flex items-start gap-3">
                            <div
                                class="flex-shrink-0 w-5 h-5 rounded-full bg-[#8B4513] flex items-center justify-center mt-0.5">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="text-gray-700 text-sm" style="font-family: 'Poppins', sans-serif;">
                                Dibuat dengan metode tulis dan cap tradisional
                            </span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div
                                class="flex-shrink-0 w-5 h-5 rounded-full bg-[#8B4513] flex items-center justify-center mt-0.5">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="text-gray-700 text-sm" style="font-family: 'Poppins', sans-serif;">
                                Menggunakan pewarna alami dari tumbuhan
                            </span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div
                                class="flex-shrink-0 w-5 h-5 rounded-full bg-[#8B4513] flex items-center justify-center mt-0.5">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="text-gray-700 text-sm" style="font-family: 'Poppins', sans-serif;">
                                Motif yang kaya akan filosofi dan sejarah
                            </span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div
                                class="flex-shrink-0 w-5 h-5 rounded-full bg-[#8B4513] flex items-center justify-center mt-0.5">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="text-gray-700 text-sm" style="font-family: 'Poppins', sans-serif;">
                                Hasil karya pengrajin yang berpengalaman
                            </span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div
                                class="flex-shrink-0 w-5 h-5 rounded-full bg-[#8B4513] flex items-center justify-center mt-0.5">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="text-gray-700 text-sm" style="font-family: 'Poppins', sans-serif;">
                                Bahan berkualitas tinggi untuk kenyamanan pemakai
                            </span>
                        </li>
                    </ul>

                    <a href="<?php echo e(route('landing.about')); ?>"
                        class="inline-block bg-[#8B4513] text-white px-6 py-3 text-sm font-medium rounded-lg hover:bg-[#6B3410] transition-all duration-300"
                        style="font-family: 'Poppins', sans-serif;">
                        Pelajari Lebih Lanjut
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-12 md:py-16 bg-[#8B4513]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="text-white text-center md:text-left">
                    <h2 class="text-2xl md:text-3xl font-bold mb-2" style="font-family: 'Playfair Display', serif;">
                        Siap untuk memiliki Batik Giri Alam?
                    </h2>
                    <p class="text-lg md:text-xl" style="font-family: 'Playfair Display', serif;">
                        Jelajahi koleksi Produk kami sekarang.
                    </p>
                </div>
                <div class="flex-shrink-0">
                    <a href="<?php echo e(route('landing.shop')); ?>"
                        class="inline-block bg-white text-[#8B4513] px-8 py-3 text-sm font-medium rounded-lg hover:bg-gray-100 transition-all duration-300"
                        style="font-family: 'Poppins', sans-serif;">
                        Belanja Sekarang
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Custom Styles for Rich Text and Animations -->
    <style>
        /* Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }

        /* Hero animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes bounceSlow {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .animate-fade-in-down {
            animation: fadeInDown 1s ease-out forwards;
        }

        .animate-fade-in-up {
            animation: fadeInUp 1s ease-out forwards;
        }

        .animate-fade-in {
            animation: fadeIn 1s ease-out forwards 0.3s;
            opacity: 0;
        }

        .animate-fade-in-up-delayed {
            animation: fadeInUp 1s ease-out forwards 0.5s;
            opacity: 0;
        }

        .animate-bounce-slow {
            animation: bounceSlow 2s ease-in-out infinite;
        }

        /* Hero image parallax effect */
        .hero-image {
            transition: transform 0.3s ease-out;
        }

        /* Loading optimization */
        img {
            image-rendering: -webkit-optimize-contrast;
        }

        /* Smooth transitions for all interactive elements */
        a,
        button {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald813adaee0594521b0d5afea3d810d12)): ?>
<?php $attributes = $__attributesOriginald813adaee0594521b0d5afea3d810d12; ?>
<?php unset($__attributesOriginald813adaee0594521b0d5afea3d810d12); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald813adaee0594521b0d5afea3d810d12)): ?>
<?php $component = $__componentOriginald813adaee0594521b0d5afea3d810d12; ?>
<?php unset($__componentOriginald813adaee0594521b0d5afea3d810d12); ?>
<?php endif; ?>
<?php /**PATH C:\web-batik\resources\views/landing/home.blade.php ENDPATH**/ ?>