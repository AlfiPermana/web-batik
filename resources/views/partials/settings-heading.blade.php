<div class="relative mb-6 w-full">
    <flux:heading size="xl" level="1">{{ __('Settings') }}</flux:heading>
    <flux:subheading size="lg" class="mb-6">{{ __('Manage your profile and account settings') }}</flux:subheading>
    
    <!-- Settings Navigation Tabs -->
    <nav class="flex gap-6 mb-6 border-b border-gray-200 dark:border-gray-700" aria-label="Settings tabs">
        <a href="{{ route('profile.edit') }}" 
           class="pb-3 px-1 border-b-2 transition-colors {{ request()->routeIs('profile.edit') ? 'border-amber-600 text-amber-600 dark:text-amber-400 font-medium' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">
            {{ __('Profile') }}
        </a>
        <a href="{{ route('user-password.edit') }}" 
           class="pb-3 px-1 border-b-2 transition-colors {{ request()->routeIs('user-password.edit') ? 'border-amber-600 text-amber-600 dark:text-amber-400 font-medium' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">
            {{ __('Security') }}
        </a>
    </nav>
    
    <flux:separator variant="subtle" />
</div>
