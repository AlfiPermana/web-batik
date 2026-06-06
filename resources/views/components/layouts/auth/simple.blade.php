<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900 auth-page">
        <style>
            /* Hide the site header/sidebar logo when on authentication pages (we show the auth-logo instead) */
            .auth-page .site-logo { display: none !important; }
        </style>
        <div class="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-sm flex-col gap-2">
                <a href="{{ route('landing.home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                    <img src="{{ asset('assets/logo.png') }}" alt="{{ config('app.name', 'Laravel') }}" class="auth-logo h-16 mb-1">
                    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                </a>
                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
