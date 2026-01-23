<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>365Gate · Admin Login</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-mono:400,700&display=swap" rel="stylesheet" />

    {{-- kein Vite auf Server --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root{
            color-scheme: light;
            --ink:#08162f;
            --muted:#55647f;
            --paper:#ffffff;
            --bg:#f6f8fc;
            --grid:rgba(8,22,47,.06);
            --accent:#1b4fd6;
            --border:3px;
            --radius:0px;
            --shadow: 0 0 0 var(--border) var(--ink), 18px 18px 0 rgba(8, 22, 47, 0.18);
            --focus: 0 0 0 3px rgba(27,79,214,.22);
        }
        html, body { height:100%; }
        body{
            font-family:'Space Mono', ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono','Courier New', monospace;
            background:var(--bg);
            color:var(--ink);
        }
        .pixel-grid{
            background-color:var(--bg);
            background-image:
                linear-gradient(90deg, var(--grid) 1px, transparent 1px),
                linear-gradient(var(--grid) 1px, transparent 1px);
            background-size:18px 18px;
        }
        .pixel-frame{ background:var(--paper); border-radius:var(--radius); box-shadow:var(--shadow); }
        .pixel-input{
            box-shadow: inset 0 0 0 var(--border) var(--ink);
            border-radius:var(--radius);
            background:#fbfdff;
        }
        .pixel-input:focus{ outline:none; box-shadow: inset 0 0 0 var(--border) var(--ink), var(--focus); }
        .pixel-button{
            box-shadow: 0 0 0 var(--border) var(--ink), 10px 10px 0 rgba(8,22,47,.16);
            border-radius:var(--radius);
            background:var(--accent);
            color:#fff;
            text-transform:uppercase;
            letter-spacing:.10em;
        }
        .pixel-button:hover{ filter:brightness(1.03); transform:translate(-1px,-1px); }
        .pixel-button:active{ transform:translate(1px,1px); }
        .help{ color:var(--muted); }
    </style>
</head>

<body class="pixel-grid">
    <main class="min-h-screen flex items-center justify-center px-6 py-12">
        <section class="w-full max-w-md pixel-frame p-8">
            <div class="mb-6">
                <div class="text-xs uppercase tracking-[0.25em] help">365jobs.sk</div>
                <h1 class="text-2xl font-bold tracking-tight mt-2">Admin Gate</h1>
                <p class="mt-3 text-sm help">Login nur fuer Platform-Rollen. Kein /admin/login.</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 px-4 py-3 text-sm" style="box-shadow: inset 0 0 0 var(--border) #b91c1c; background:#fff5f5;">
                    <div class="font-bold text-red-700 mb-1">Fehler</div>
                    <ul class="list-disc pl-5 text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('365gate.authenticate') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs uppercase tracking-[0.2em] mb-2 help" for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 pixel-input">
                </div>

                <div>
                    <label class="block text-xs uppercase tracking-[0.2em] mb-2 help" for="password">Passwort</label>
                    <input id="password" name="password" type="password" required
                        class="w-full px-4 py-3 pixel-input">
                </div>

                <label class="flex items-center gap-3 text-sm help">
                    <input type="checkbox" name="remember" value="1" class="h-4 w-4">
                    Remember me
                </label>

                <button type="submit" class="w-full px-4 py-3 pixel-button">
                    Enter
                </button>
            </form>
        </section>
    </main>
</body>
</html>
