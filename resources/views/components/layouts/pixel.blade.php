<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', '365jobs') }}</title>

    {{-- Font --}}

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Pixel CSS (separate file) --}}
    <link rel="stylesheet" href="{{ asset('assets/pixel.css') }}">

    @stack('head')
    @php($consent = request()->cookie('cookie_consent'))
    @if(isset($cookieSettings) && $cookieSettings->ga_enabled && $consent === 'stats' && filled($cookieSettings->ga_measurement_id))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $cookieSettings->ga_measurement_id }}"></script>

    @endif

</head>

<body class="min-h-screen">
    <div class="pixel-grid min-h-screen">
        @php($siteSettings = \App\Models\SiteSetting::current())
        @if ($siteSettings?->maintenance_banner_enabled && filled($siteSettings->maintenance_banner_text))
            <div class="px-6 pt-6">
                <div class="pixel-outline px-4 py-3 text-sm border-2 border-amber-500 bg-amber-50 text-amber-900">
                    {{ $siteSettings->maintenance_banner_text }}
                </div>
            </div>
        @endif

        <x-pixel-header />

        <main class="px-6 pt-10 pb-20">
            <div class="pixel-container">
                <div class="pixel-page">
                    {{ $slot }}
                </div>
            </div>
        </main>

        <x-pixel.footer />
    </div>

    {{-- Pixel JS (separate file) --}}
    <script src="{{ asset('assets/pixel.js') }}" defer></script>

    @stack('scripts')
    @include('partials.cookie-banner')

</body>
</html>
