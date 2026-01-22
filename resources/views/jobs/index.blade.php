<x-layouts.pixel>
    <section class="mx-auto flex w-full max-w-6xl flex-col gap-8">
        <div class="pixel-frame p-8">
            <div class="flex flex-col gap-4">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Job Search</p>
                <h1 class="text-3xl font-bold">{{ count($jobs) }} Jobs gefunden</h1>
                <div class="flex flex-wrap gap-3 text-xs uppercase tracking-[0.2em] text-slate-500">
                    <span class="pixel-chip px-3 py-1">Remote</span>
                    <span class="pixel-chip px-3 py-1">Design</span>
                    <span class="pixel-chip px-3 py-1">Engineering</span>
                </div>
                <div class="mt-2 grid gap-4 md:grid-cols-2">
                    <label class="text-xs uppercase tracking-[0.2em] text-slate-500">
                        Standort
                        <input class="pixel-input mt-2 w-full px-4 py-3 text-sm" placeholder="Zürich, Remote" />
                    </label>
                    <label class="text-xs uppercase tracking-[0.2em] text-slate-500">
                        Skill
                        <input class="pixel-input mt-2 w-full px-4 py-3 text-sm" placeholder="Laravel, Product, UI" />
                    </label>
                </div>
            </div>
        </div>

        <div class="grid gap-6">
            @foreach ($jobs as $job)
                <article class="pixel-frame p-6">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ $job['company'] }}</p>
                            <h2 class="text-xl font-bold">{{ $job['title'] }}</h2>
                            <p class="mt-2 text-sm text-slate-600">{{ $job['summary'] }}</p>
                        </div>
                        <div class="flex flex-col items-start gap-3 text-xs uppercase tracking-[0.2em] text-slate-500 md:items-end">
                            <span class="pixel-chip px-3 py-1">{{ $job['location'] }}</span>
                            <span class="pixel-chip px-3 py-1">{{ $job['type'] }}</span>
                            <a class="pixel-button px-5 py-2 text-xs" href="{{ url('/jobs/' . $job['slug']) }}">Details</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
</x-layouts.pixel>
