<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your profile information')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full">
            <!-- Personal Information Section -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold mb-6 text-gray-900 dark:text-white border-b pb-3 border-gray-200 dark:border-gray-700">{{ __('Personal Information') }}</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <flux:input wire:model="name" :label="__('Full Name')" type="text" required autofocus autocomplete="name" :placeholder="__('Enter your full name')" />

                    <!-- Email Address -->
                    <div>
                        <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" :placeholder="__('Enter your email')" />

                        @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&! auth()->user()->hasVerifiedEmail())
                            <flux:text class="mt-3 text-sm text-amber-600 dark:text-amber-400">
                                {{ __('Your email address is unverified.') }}
                                <flux:link class="text-sm cursor-pointer font-medium" wire:click.prevent="resendVerificationNotification">
                                    {{ __('Click here to re-send the verification email.') }}
                                </flux:link>
                            </flux:text>

                            @if (session('status') === 'verification-link-sent')
                                <flux:text class="mt-2 text-sm font-medium text-green-600 dark:text-green-400">
                                    {{ __('A new verification link has been sent to your email address.') }}
                                </flux:text>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- Contact Information Section -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold mb-6 text-gray-900 dark:text-white border-b pb-3 border-gray-200 dark:border-gray-700">{{ __('Contact Information') }}</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Phone Number -->
                    <flux:input wire:model="phone_number" :label="__('Phone Number')" type="tel" required autocomplete="tel" :placeholder="__('08xxxxxxxxxx')" />

                    <!-- Address (full width) -->
                    <div class="md:col-span-2">
                        <flux:input wire:model="address" :label="__('Address')" type="text" required :placeholder="__('Enter your complete address')" />
                    </div>
                </div>
            </div>

            <!-- Password Section -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold mb-6 text-gray-900 dark:text-white border-b pb-3 border-gray-200 dark:border-gray-700">{{ __('Change Password') }}</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input wire:model="password" :label="__('New Password')" type="password" autocomplete="new-password" :placeholder="__('Leave blank to keep current password')" viewable />

                    <flux:input wire:model="password_confirmation" :label="__('Confirm Password')" type="password" autocomplete="new-password" :placeholder="__('Confirm new password')" viewable />
                </div>
                
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-3">{{ __('Password must be at least 8 characters long. Leave blank if you don\'t want to change it.') }}</p>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                <flux:button variant="primary" type="submit">{{ __('Save Changes') }}</flux:button>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('✓ Saved successfully!') }}
                </x-action-message>
            </div>
        </form>

        <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-8">
            <livewire:settings.delete-user-form />
        </div>
    </x-settings.layout>
</section>
