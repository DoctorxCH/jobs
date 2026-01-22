<x-layouts.pixel>
    <section class="mx-auto flex w-full max-w-6xl flex-col gap-8">
        <div class="pixel-frame p-10">
            <div class="flex flex-col gap-4">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ $job['company'] }}</p>
                <h1 class="text-4xl font-bold">{{ $job['title'] }}</h1>
                <p class="text-sm text-slate-600">{{ $job['summary'] }}</p>
                <div class="flex flex-wrap gap-3 text-xs uppercase tracking-[0.2em] text-slate-500">
                    <span class="pixel-chip px-3 py-1">{{ $job['location'] }}</span>
                    <span class="pixel-chip px-3 py-1">{{ $job['type'] }}</span>
                    <span class="pixel-chip px-3 py-1">{{ $job['salary'] }}</span>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="pixel-frame p-6 lg:col-span-2">
                <h2 class="text-lg font-bold">Über die Rolle</h2>
                <p class="mt-3 text-sm text-slate-600">
                    {{ $job['description'] }}
                </p>
                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <div>
                        <h3 class="text-xs uppercase tracking-[0.2em] text-slate-500">Deine Aufgaben</h3>
                        <ul class="mt-3 space-y-2 text-sm text-slate-600">
                            @foreach ($job['responsibilities'] as $item)
                                <li>— {{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-xs uppercase tracking-[0.2em] text-slate-500">Dein Profil</h3>
                        <ul class="mt-3 space-y-2 text-sm text-slate-600">
                            @foreach ($job['requirements'] as $item)
                                <li>— {{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <aside class="pixel-frame p-6">
                <h3 class="text-xs uppercase tracking-[0.2em] text-slate-500">Schnellinfo</h3>
                <dl class="mt-4 space-y-4 text-sm">
                    <div>
                        <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Arbeitsmodell</dt>
                        <dd class="mt-1 font-semibold text-slate-900">{{ $job['work_mode'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Teamgröße</dt>
                        <dd class="mt-1 font-semibold text-slate-900">{{ $job['team_size'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Bewerbungsfrist</dt>
                        <dd class="mt-1 font-semibold text-slate-900">{{ $job['deadline'] }}</dd>
                    </div>
                </dl>
                <button class="pixel-button mt-6 w-full px-6 py-3 text-xs">Jetzt bewerben</button>
            </aside>
        </div>
    </section>
</x-layouts.pixel>
