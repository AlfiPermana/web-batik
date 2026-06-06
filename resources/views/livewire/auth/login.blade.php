<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email address')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="current-password"
                :placeholder="__('Password')"
                viewable
            />

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
                <flux:checkbox name="remember" :label="__('Remember me')" :checked="old('remember')" />
                <a href="{{ route('forgot-password') }}" class="text-sm text-primary-600 hover:text-primary-500 font-medium">
                    {{ __('Forgot password?') }}
                </a>
            </div>

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                    {{ __('Log in') }}
                </flux:button>
            </div>
        </form>

        <div class="text-center">
            <p class="text-sm text-gray-600">
                {{ __('Don\'t have an account?') }}
                <a href="{{ route('register') }}" class="font-medium text-primary-600 hover:text-primary-500">
                    {{ __('Sign up') }}
                </a>
            </p>
        </div>
    </div>
</x-layouts.auth>
