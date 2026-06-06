<x-layouts.app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Customer Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-col gap-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium">
                                {{ __('Welcome back, :name!', ['name' => auth()->user()->name]) }}
                            </h3>
                        </div>

                        <!-- Quick Stats -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-900/50 p-6 rounded-lg border border-blue-200 dark:border-blue-800">
                                <h4 class="font-medium text-blue-600 dark:text-blue-400 mb-2">{{ __('Total Orders') }}</h4>
                                <p class="text-3xl font-bold text-blue-900 dark:text-blue-100">{{ $user->orders()->count() ?? 0 }}</p>
                            </div>
                            <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-900/50 p-6 rounded-lg border border-green-200 dark:border-green-800">
                                <h4 class="font-medium text-green-600 dark:text-green-400 mb-2">{{ __('Pending Orders') }}</h4>
                                <p class="text-3xl font-bold text-green-900 dark:text-green-100">{{ $user->orders()->where('status', 'pending')->count() ?? 0 }}</p>
                            </div>
                            <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/30 dark:to-purple-900/50 p-6 rounded-lg border border-purple-200 dark:border-purple-800">
                                <h4 class="font-medium text-purple-600 dark:text-purple-400 mb-2">{{ __('Completed Orders') }}</h4>
                                <p class="text-3xl font-bold text-purple-900 dark:text-purple-100">{{ $user->orders()->where('status', 'completed')->count() ?? 0 }}</p>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 p-4 bg-primary-50 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300 rounded-lg hover:bg-primary-100 dark:hover:bg-primary-900 transition-colors">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                                <span>{{ __('Edit Profile') }}</span>
                            </a>
                            <a href="{{ route('landing.shop') }}" class="flex items-center gap-2 p-4 bg-primary-50 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300 rounded-lg hover:bg-primary-100 dark:hover:bg-primary-900 transition-colors">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                                <span>{{ __('Browse Products') }}</span>
                            </a>
                            <a href="{{ route('landing.workshop') }}" class="flex items-center gap-2 p-4 bg-primary-50 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300 rounded-lg hover:bg-primary-100 dark:hover:bg-primary-900 transition-colors">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                                </svg>
                                <span>{{ __('View Workshops') }}</span>
                            </a>
                            <a href="{{ route('customer.orders') }}" class="flex items-center gap-2 p-4 bg-primary-50 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300 rounded-lg hover:bg-primary-100 dark:hover:bg-primary-900 transition-colors">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .984.695 1.794 1.592 1.794.892 0 1.592-.81 1.592-1.794 0-.23-.035-.454-.1-.664m0 0H9m0 0V3.75m0 0c0-1.135.845-2.098 1.976-2.192A48.42 48.42 0 0115 2.25H9z" />
                                </svg>
                                <span>{{ __('My Orders') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>