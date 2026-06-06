<div class="flex flex-col gap-6">
    <x-auth-header
        :title="__('Create your account')"
        :description="__('Fill in your details below to create your customer account')"
    />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

        <form wire:submit="register" class="flex flex-col gap-6">
            <!-- Name -->
            <flux:input
                wire:model="name"
                :label="__('Full Name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Enter your full name')"
            />

            <!-- Email Address -->
            <flux:input
                wire:model="email"
                :label="__('Email address')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Phone Number -->
            <flux:input
                wire:model="phone_number"
                :label="__('Phone Number')"
                type="tel"
                required
                autocomplete="tel"
                :placeholder="__('Enter your phone number')"
            />

            <!-- Address -->
            <flux:input
                wire:model="address"
                :label="__('Address')"
                type="text"
                required
                :placeholder="__('Enter your address')"
            />

            <!-- Password -->
            <flux:input
                wire:model="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Create a password')"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                wire:model="password_confirmation"
                :label="__('Confirm Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm your password')"
                viewable
            />

            <div class="flex flex-col gap-4">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-button">
                    {{ __('Create Account') }}
                </flux:button>

                <div class="text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Already have an account?') }}
                        <a href="{{ route('login') }}" class="font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300" wire:navigate>
                            {{ __('Log in') }}
                        </a>
                    </p>
                </div>
            </div>
        </form>
</div>