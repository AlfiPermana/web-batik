<div class="flex flex-col gap-6">
    @if ($invalidToken)
        <x-auth-header 
            :title="__('Invalid or Expired Link')" 
            :description="__('The password reset link is invalid or has expired')" 
        />
        
        <div class="rounded-lg bg-red-50 border border-red-200 p-4">
            <p class="text-red-800 text-sm mb-4">
                Token reset password tidak valid atau telah kadaluarsa.
            </p>
            <a href="{{ route('forgot-password') }}" class="text-red-600 hover:text-red-800 text-sm font-semibold underline">
                ← Minta ulang link reset password
            </a>
        </div>
    @else
        <x-auth-header 
            :title="__('Reset Your Password')" 
            :description="__('Enter your new password below')" 
        />

        <form wire:submit="resetPassword" class="flex flex-col gap-6">
            <!-- Error Message -->
            @if ($error)
                <div class="rounded-lg bg-red-50 border border-red-200 p-4">
                    <p class="text-red-800 text-sm">{{ $error }}</p>
                </div>
            @endif

            <!-- Email Address (hidden) -->
            <input type="hidden" wire:model="email" />

            <!-- New Password -->
            <flux:input
                wire:model="password"
                :label="__('New Password')"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Masukkan password baru"
                viewable
            />
            @error('password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror

            <!-- Confirm Password -->
            <flux:input
                wire:model="password_confirmation"
                :label="__('Confirm Password')"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Ulangi password baru"
                viewable
            />
            @error('password_confirmation') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror

            <flux:button variant="primary" type="submit" class="w-full" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('Reset Password') }}</span>
                <span wire:loading>{{ __('Resetting...') }}</span>
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
