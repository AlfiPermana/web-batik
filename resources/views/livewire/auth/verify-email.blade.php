<div class="flex flex-col gap-6">
    <x-auth-header
        :title="__('Verify your email')"
        :description="!Auth::user()->verification_code ? __('Please request a verification code to activate your account.') : __('Enter the 6-digit verification code sent to your email.')"
    />

    <!-- Session Status -->
    @if (session('success'))
        <div class="p-3 bg-green-50 border border-green-200 rounded-lg text-xs font-semibold text-green-800 text-center dark:bg-green-950 dark:border-green-900 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-xs font-semibold text-red-800 text-center dark:bg-red-950 dark:border-red-900 dark:text-red-300">
            {{ session('error') }}
        </div>
    @endif

    @if(!Auth::user()->verification_code)
        <!-- State 1: Request Code Button -->
        <div class="flex flex-col gap-4">
            <flux:button type="button"
                         wire:click="resendCode"
                         variant="primary"
                         class="w-full">
                {{ __('Send Verification Code') }}
            </flux:button>
        </div>
    @else
        <!-- State 2: Code Verification Form -->
        <form wire:submit.prevent="verify" class="flex flex-col gap-6">
            
            <div class="flex flex-col gap-2">
                <flux:input
                    wire:model="code"
                    :label="__('Verification Code')"
                    type="text"
                    maxlength="6"
                    placeholder="------"
                    class="text-center font-mono tracking-widest text-lg"
                    required
                    autofocus
                />

                @error('code')
                    <span class="block text-xs text-red-600 font-semibold mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Verify Button -->
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Verify Account') }}
            </flux:button>
        </form>

        <!-- Resend & Support -->
        <div class="pt-4 border-t border-gray-150 dark:border-neutral-800 flex flex-col items-center justify-center gap-3 text-xs text-gray-500">
            <p>{{ __('Didn\'t receive the code?') }}</p>
            
            <!-- Countdown timer for resending code -->
            <div x-data="{ countdown: 0, timer: null, startTimer() { this.countdown = 60; if(this.timer) clearInterval(this.timer); this.timer = setInterval(() => { this.countdown--; if(this.countdown <= 0) clearInterval(this.timer); }, 1000); } }" 
                 x-init="startTimer()"
                 class="text-center">
                
                <button type="button" 
                        x-bind:disabled="countdown > 0"
                        x-on:click="startTimer(); $wire.resendCode()" 
                        class="font-semibold text-primary-650 hover:text-primary-550 disabled:text-gray-400 disabled:cursor-not-allowed transition-colors">
                    <span x-show="countdown <= 0">{{ __('Resend Code') }}</span>
                    <span x-show="countdown > 0">{{ __('Resend in') }} <span x-text="countdown"></span> {{ __('seconds') }}</span>
                </button>
            </div>
        </div>
    @endif
</div>
