<div class="flex flex-col gap-6">
    <x-auth-header 
        :title="__('Forgot your password?')" 
        :description="__('Enter your email address and we\'ll send you a link to reset your password')" 
    />

    <!-- Success Message -->
    @if ($submitted)
        <div class="rounded-lg bg-green-50 border border-green-200 p-4">
            <p class="text-green-800 text-sm font-medium mb-2">✓ {{ __('Password reset link sent') }}</p>
            <p class="text-green-700 text-sm">{{ $status }}</p>
            <div class="mt-4">
                <a href="{{ route('login') }}" class="text-green-600 hover:text-green-800 text-sm font-semibold underline">
                    ← {{ __('Back to login') }}
                </a>
            </div>
        </div>
    @else
        <form wire:submit="sendResetLink" class="flex flex-col gap-6">
            <!-- Error Message -->
            @if ($error)
                <div class="rounded-lg bg-red-50 border border-red-200 p-4">
                    <p class="text-red-800 text-sm">{{ $error }}</p>
                </div>
            @endif

            <!-- Email Address -->
            <flux:input
                wire:model="email"
                :label="__('Email address')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
            />
            @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror

            <flux:button variant="primary" type="submit" class="w-full" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('Send Reset Link') }}</span>
                <span wire:loading>{{ __('Sending...') }}</span>
            </flux:button>

            <div class="text-center">
                <p class="text-sm text-gray-600">
                    {{ __('Remember your password?') }}
                    <a href="{{ route('login') }}" class="font-medium text-primary-600 hover:text-primary-500">
                        {{ __('Log in') }}
                    </a>
                </p>
            </div>
        </form>
    @endif
</div>
