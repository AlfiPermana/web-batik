@use('App\Models\User')

<x-layouts.landing title="Shopping Cart - Batik Giri Alam Gumelem Wetan">
    <!-- Cart Section with full height background -->
    <section class="min-h-screen py-12" style="background-color: #f5f1e8;">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- White Container for Cart -->
            <div class="bg-white rounded-lg shadow-md p-6 md:p-8">
                <livewire:cart-view />
            </div>
        </div>
    </section>
</x-layouts.landing>
