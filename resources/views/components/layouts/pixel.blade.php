<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? config('app.name', 'Jobportal') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-mono:400,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            color-scheme: light;
        }

        body {
            font-family: 'Space Mono', ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;
            background-color: #f8fafc;
            color: #0f172a;
        }

        .pixel-frame {
            box-shadow: 0 0 0 2px #0f172a, 6px 6px 0 0 rgba(15, 23, 42, 0.12);
            border-radius: 0;
            background-color: #ffffff;
        }

        .pixel-outline {
            box-shadow: inset 0 0 0 2px #0f172a;
            border-radius: 0;
        }

        .pixel-chip {
            box-shadow: 0 0 0 2px #0f172a;
            background-color: #e2e8f0;
            color: #0f172a;
        }

        .accent {
            color: #1d4ed8;
        }

        .pixel-grid {
            background-image: linear-gradient(90deg, rgba(15, 23, 42, 0.08) 1px, transparent 1px),
                linear-gradient(rgba(15, 23, 42, 0.08) 1px, transparent 1px);
            background-size: 16px 16px;
        }

        .pixel-button {
            box-shadow: 0 0 0 2px #0f172a;
            background-color: #1d4ed8;
            color: #f8fafc;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .pixel-input {
            box-shadow: inset 0 0 0 2px #0f172a;
            background-color: #f8fafc;
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="pixel-grid min-h-screen">
        <header class="px-6 py-6">
            <div class="mx-auto flex w-full max-w-6xl items-center justify-between gap-6">
                <div class="flex items-center gap-3">
                    <div class="pixel-outline flex h-10 w-10 items-center justify-center bg-white text-lg font-bold">JP</div>
                    <span class="text-sm uppercase tracking-[0.2em]">Kurka Jobs</span>
                </div>
                <nav class="flex items-center gap-6 text-xs uppercase tracking-[0.2em]">
                    <a class="hover:text-blue-700" href="{{ url('/') }}">Home</a>
                    <a class="hover:text-blue-700" href="{{ url('/jobs') }}">Jobs</a>
                    <a class="hover:text-blue-700" href="{{ url('/company/dashboard') }}">Company</a>
                </nav>
            </div>
        </header>

        <main class="px-6 pb-16">
            {{ $slot }}
        </main>

        <footer class="px-6 pb-12 text-xs uppercase tracking-[0.2em] text-slate-500">
            <div class="mx-auto flex w-full max-w-6xl flex-wrap items-center justify-between gap-4 border-t border-slate-300 pt-6">
                <span>Minimal Jobportal · Pixel Art UI</span>
                <span>Remote · Lokal · Klar</span>
            </div>
        </footer>
    </div>
</body>
</html>
