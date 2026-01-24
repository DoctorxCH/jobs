<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', '365jobs') }}</title>

    {{-- Font --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-mono:400,700&display=swap" rel="stylesheet" />

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Pixel CSS (separate file) --}}
    <link rel="stylesheet" href="{{ asset('assets/pixel.css') }}">

    @stack('head')
</head>

<body class="min-h-screen">
    <div class="pixel-grid min-h-screen">
        <x-pixel-header />

        <main class="px-6 pt-8 pb-16">
            {{ $slot }}
        </main>

        <x-pixel.footer />
    </div>

    {{-- Pixel JS (separate file) --}}
    <script src="{{ asset('assets/pixel.js') }}" defer></script>

    @stack('scripts')
</body>
</html>
