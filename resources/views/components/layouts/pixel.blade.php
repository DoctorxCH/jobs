<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? config('app.name', 'Jobportal') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-mono:400,700&display=swap" rel="stylesheet" />

    {{-- No Vite build on this server --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root{
            color-scheme: light;

            /* Blueprint / brutal */
            --paper: #ffffff;
            --bg: #eef3ff;
            --ink: #08162f;
            --muted: #3d4b66;

            /* Blueprint lines */
            --grid: rgba(8, 22, 47, 0.09);
            --grid2: rgba(8, 22, 47, 0.05);

            /* Accent: navy + electric */
            --frame: #08162f;
            --accent: #1439ff;   /* electric */
            --warn: #ffbf00;     /* optional "brutal" accent */

            --border: 3px;
            --radius: 0px;

            /* Brutal shadows */
            --shadow: 0 0 0 var(--border) var(--frame), 18px 18px 0 rgba(8, 22, 47, 0.18);
            --shadow-soft: 0 0 0 var(--border) var(--frame), 10px 10px 0 rgba(8, 22, 47, 0.14);

            --focus: 0 0 0 4px rgba(20, 57, 255, 0.20);
        }

        body{
            font-family: 'Space Mono', ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;
            background: var(--bg);
            color: var(--ink);
        }

        /* Double grid like blueprint paper */
        .pixel-grid{
            background:
                linear-gradient(90deg, var(--grid2) 1px, transparent 1px),
                linear-gradient(var(--grid2) 1px, transparent 1px),
                linear-gradient(90deg, var(--grid) 1px, transparent 1px),
                linear-gradient(var(--grid) 1px, transparent 1px);
            background-size: 18px 18px, 18px 18px, 90px 90px, 90px 90px;
            background-position: 0 0, 0 0, 0 0, 0 0;
        }

        .pixel-frame{
            background: var(--paper);
            box-shadow: var(--shadow);
        }

        .pixel-outline{
            background: var(--paper);
            box-shadow: inset 0 0 0 var(--border) var(--frame);
        }

        .pixel-chip{
            background: #fff;
            box-shadow: 0 0 0 var(--border) var(--frame);
            position: relative;
        }
        .pixel-chip::after{
            content:"";
            position:absolute;
            inset: -2px;
            background: linear-gradient(90deg, rgba(20,57,255,0.15), rgba(255,191,0,0.10));
            z-index:-1;
        }

        .accent{ color: var(--accent); }

        .pixel-input{
            background: #fff;
            box-shadow: inset 0 0 0 var(--border) var(--frame);
            transition: box-shadow .15s ease;
        }
        .pixel-input:focus{
            outline: none;
            box-shadow: inset 0 0 0 var(--border) var(--frame), var(--focus);
        }

        .pixel-button{
            background: var(--accent);
            color: #fff;
            text-transform: uppercase;
            letter-spacing: .14em;
            box-shadow: 0 0 0 var(--border) var(--frame), 12px 12px 0 rgba(8, 22, 47, 0.18);
            transition: transform .12s ease, box-shadow .12s ease, filter .12s ease;
        }
        .pixel-button:hover{
            transform: translate(-2px, -2px);
            box-shadow: 0 0 0 var(--border) var(--frame), 16px 16px 0 rgba(8, 22, 47, 0.18);
            filter: brightness(1.03);
        }
        .pixel-button:active{
            transform: translate(2px, 2px);
            box-shadow: 0 0 0 var(--border) var(--frame), 8px 8px 0 rgba(8, 22, 47, 0.18);
        }

        a{ color: inherit; text-decoration: none; }
        a:hover{ color: var(--accent); }

        h1, h2, h3{
            letter-spacing: -0.04em;
        }
        p{ color: var(--muted); }

        /* Add a tiny "engineering label" vibe without security hints */
        .top-mark{
            letter-spacing: .26em;
            opacity: .9;
        }
    </style>
</head>

<body class="min-h-screen">
    <div class="pixel-grid min-h-screen">
        <header class="px-6 py-6">
            <div class="mx-auto flex w-full max-w-6xl items-center justify-between gap-6">
                <!-- Header disabled on this login page -->
            </div>
        </header>

        <main class="px-6 pb-16">
            {{ $slot }}
        </main>

        <footer class="px-6 pb-12 text-xs uppercase tracking-[0.26em] text-slate-600">
            <div class="mx-auto flex w-full max-w-6xl flex-wrap items-center justify-between gap-4 border-t border-slate-400/50 pt-6">
            </div>
        </footer>
    </div>
</body>
</html>
