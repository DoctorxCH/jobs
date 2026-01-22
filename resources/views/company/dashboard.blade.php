<x-layouts.pixel>
    <section class="mx-auto flex w-full max-w-6xl flex-col gap-8">
        <div class="pixel-frame p-8">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Company Dashboard</p>
                    <h1 class="text-3xl font-bold">Hallo, {{ $company['name'] }}</h1>
                    <p class="mt-2 text-sm text-slate-600">Dein Recruiting in einem klaren, pixelgenauen Überblick.</p>
                </div>
                <button class="pixel-button px-6 py-3 text-xs">Neue Stelle</button>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-3">
            <div class="pixel-frame p-6">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Aktive Jobs</p>
                <p class="mt-3 text-3xl font-bold">{{ $company['stats']['active_jobs'] }}</p>
            </div>
            <div class="pixel-frame p-6">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Neue Bewerbungen</p>
                <p class="mt-3 text-3xl font-bold">{{ $company['stats']['applications'] }}</p>
            </div>
            <div class="pixel-frame p-6">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Antwortzeit</p>
                <p class="mt-3 text-3xl font-bold">{{ $company['stats']['response_time'] }}</p>
            </div>
        </div>

        <div class="pixel-frame p-8">
            <h2 class="text-lg font-bold">Aktuelle Stellenausschreibungen</h2>
            <div class="mt-6 space-y-4">
                @foreach ($company['postings'] as $posting)
                    <div class="pixel-outline flex flex-col gap-3 px-4 py-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ $posting['department'] }}</p>
                            <h3 class="text-base font-bold">{{ $posting['title'] }}</h3>
                            <p class="mt-1 text-sm text-slate-600">{{ $posting['status'] }} · {{ $posting['location'] }}</p>
                        </div>
                        <div class="flex flex-wrap gap-2 text-xs uppercase tracking-[0.2em] text-slate-500">
                            <span class="pixel-chip px-3 py-1">{{ $posting['candidates'] }} Kandidaten</span>
                            <span class="pixel-chip px-3 py-1">{{ $posting['stage'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-layouts.pixel>
