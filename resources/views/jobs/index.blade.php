<x-layouts.pixel>
    <section class="mx-auto flex w-full max-w-6xl flex-col gap-6">
        <div class="pixel-frame p-8">
            <div class="flex flex-col gap-2">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Jobs</p>
                <h1 class="text-3xl font-bold">{{ $jobs->total() }} Jobs gefunden</h1>
                @if ($search)
                    <p class="text-sm text-slate-600">Suchbegriff: <span class="font-semibold">{{ $search }}</span></p>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
            <aside class="pixel-frame p-6 lg:col-span-1">
                <form class="space-y-4" method="get" action="{{ route('jobs.index') }}">
                    <input type="hidden" name="country" value="{{ $countryCode }}">

                    <label class="block text-xs uppercase tracking-[0.2em] text-slate-500">
                        Suche
                        <input
                            class="pixel-input mt-2 w-full px-4 py-3 text-sm text-slate-900"
                            placeholder="Pozícia / odvetvie / firma"
                            type="text"
                            name="q"
                            value="{{ $search }}"
                        />
                    </label>

                    <label class="block text-xs uppercase tracking-[0.2em] text-slate-500">
                        Region
                        <select class="pixel-input mt-2 w-full px-4 py-3 text-sm" name="region">
                            <option value="">Alle Regionen</option>
                            @foreach ($regions as $region)
                                <option value="{{ $region->id }}" @selected($selectedRegion === $region->id)>
                                    {{ $region->name }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <label class="block text-xs uppercase tracking-[0.2em] text-slate-500">
                        City
                        <select class="pixel-input mt-2 w-full px-4 py-3 text-sm" name="city" @disabled(! $selectedRegion)>
                            <option value="">Alle Cities</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}" @selected($selectedCity === $city->id)>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <div class="border-t border-slate-200 pt-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Weitere Filter</p>
                        <label class="mt-3 block text-xs uppercase tracking-[0.2em] text-slate-500">
                            Plat od
                            <input
                                class="pixel-input mt-2 w-full px-4 py-3 text-sm text-slate-900"
                                placeholder="z.B. 1 800 €"
                                type="text"
                                disabled
                            />
                        </label>
                    </div>

                    <button class="pixel-button w-full px-5 py-3 text-xs" type="submit">Filter anwenden</button>
                </form>
            </aside>

            <div class="flex flex-col gap-4 lg:col-span-3">
                @forelse ($jobs as $job)
                    @php
                        $company = $job->company;
                        $logoPath = $company?->logo_path ?: $company?->top_partner_logo_path;
                        $logoUrl = $logoPath ? asset('storage/' . ltrim($logoPath, '/')) : null;
                        $locationParts = array_filter([$job->city?->name, $job->region?->name]);
                        $location = $locationParts ? implode(', ', $locationParts) : null;
                        $jobDate = $job->published_at ?? $job->created_at;
                        $salary = null;
                        if ($job->salary_min_gross_month || $job->salary_max_gross_month) {
                            $min = $job->salary_min_gross_month ? number_format((float) $job->salary_min_gross_month, 0, ',', ' ') : null;
                            $max = $job->salary_max_gross_month ? number_format((float) $job->salary_max_gross_month, 0, ',', ' ') : null;
                            $currency = $job->salary_currency ? strtoupper($job->salary_currency) : null;
                            if ($min && $max) {
                                $salary = trim(sprintf('%s–%s %s', $min, $max, $currency));
                            } elseif ($min) {
                                $salary = trim(sprintf('ab %s %s', $min, $currency));
                            } elseif ($max) {
                                $salary = trim(sprintf('bis %s %s', $max, $currency));
                            }
                        }
                    @endphp

                    <article class="pixel-frame bg-white p-6">
                        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                            <div class="space-y-2">
                                <a class="text-lg font-bold text-green-700 hover:underline" href="{{ route('jobs.show', $job) }}">
                                    {{ $job->title }}
                                </a>
                                @if ($company)
                                    <p class="text-sm text-slate-600">{{ $company->legal_name }}</p>
                                @endif
                                @if ($location)
                                    <p class="text-xs text-slate-500">{{ $location }}</p>
                                @endif
                                <div class="flex flex-wrap gap-2 text-xs uppercase tracking-[0.2em] text-slate-500">
                                    @if ($company?->is_top_partner)
                                        <span class="pixel-chip px-2 py-1">TOP</span>
                                    @endif
                                    @if ($salary)
                                        <span class="pixel-chip px-2 py-1">{{ $salary }}</span>
                                    @endif
                                    @if ($jobDate)
                                        <span class="pixel-chip px-2 py-1">{{ $jobDate->format('d.m.Y') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-4 md:justify-end">
                                <button class="pixel-outline grid h-10 w-10 place-items-center" type="button" aria-label="Favorit">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/>
                                    </svg>
                                </button>

                                <div class="flex h-16 w-16 items-center justify-center">
                                    @if ($logoUrl)
                                        <img class="max-h-16 w-auto" src="{{ $logoUrl }}" alt="{{ $company?->legal_name }}" />
                                    @else
                                        <div class="h-16 w-16 opacity-0"></div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="pixel-frame bg-white p-6">
                        <p class="text-sm text-slate-600">Keine Jobs gefunden.</p>
                    </div>
                @endforelse

                <div>
                    {{ $jobs->links() }}
                </div>
            </div>
        </div>
    </section>
</x-layouts.pixel>
