<x-layouts.pixel>
    <section class="mx-auto flex w-full max-w-6xl flex-col gap-6">
        <div class="pixel-outline p-8">
            <div class="text-s uppercase tracking-[0.2em] text-slate-800 font-bold">
                Listing of available jobs
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-[1fr_auto] md:items-center">
                {{-- SEARCH (full width until count) --}}
                <form method="GET"
                      action="{{ route('jobs.index') }}"
                      class="w-full flex flex-col gap-3 md:flex-row md:items-center">

                    {{-- keep filters --}}
                    @if(!empty($selectedRegion))
                        <input type="hidden" name="region" value="{{ (int) $selectedRegion }}">
                    @endif
                    @if(!empty($selectedCity))
                        <input type="hidden" name="city" value="{{ (int) $selectedCity }}">
                    @endif
                    <input type="hidden" name="country" value="{{ $countryCode ?? 'SK' }}">

                    <div class="flex w-full gap-3">
                        <input
                            name="q"
                            value="{{ $search ?? '' }}"
                            class="pixel-input w-full px-4 py-3 text-sm"
                            placeholder="Search job title…"
                        />

                        <button type="submit"
                                class="pixel-button px-5 py-3 text-xs shrink-0">
                            Search
                        </button>
                    </div>
                </form>

                {{-- JOB COUNT --}}
                <div class="text-l font-bold whitespace-nowrap">
                    {{ $jobs->total() }} Jobs found
                </div>
            </div>
        </div>

        {{-- JOB Filter --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
            <aside class="pixel-frame p-6 lg:col-span-1">
                <form class="space-y-4" method="get" action="{{ route('jobs.index') }}">
                    <input type="hidden" name="country" value="{{ $countryCode }}">

                    <label class="block text-xs uppercase tracking-[0.2em] text-slate-500">
                        Region
                        <select class="pixel-input mt-2 w-full px-4 py-3 text-sm" name="region">
                            <option value="">All regions</option>
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
                            <option value="">All cities</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}" @selected($selectedCity === $city->id)>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <div class="border-t border-slate-200 pt-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">More filters</p>
                        <label class="mt-3 block text-xs uppercase tracking-[0.2em] text-slate-500">
                            Salary from
                            <input
                                class="pixel-input mt-2 w-full px-4 py-3 text-sm text-slate-900"
                                placeholder="e.g. 1,800 €"
                                type="text"
                            />
                        </label>
                    </div>

                    <div class="mt-4 flex gap-3">
                        <button type="submit" class="pixel-button w-full px-6 py-3 text-xs">
                            Apply filters
                        </button>

                        <a href="{{ route('jobs.index', ['country' => $countryCode ?? 'SK']) }}"
                           class="pixel-outline px-6 py-3 text-xs uppercase tracking-[0.2em] text-slate-800 whitespace-nowrap flex items-center justify-center">
                            Reset
                        </a>
                    </div>
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
                                $salary = trim(sprintf('from %s %s', $min, $currency));
                            } elseif ($max) {
                                $salary = trim(sprintf('up to %s %s', $max, $currency));
                            }
                        }

                        $jobUrl = route('jobs.show', $job); // id-binding
                        $companyUrl = $company ? url('/company/' . $company->id) : null;
                    @endphp

                    {{-- CLICKABLE CARD (no outer <a>) --}}
                    <article
                        class="job-card pixel-frame bg-white p-6 cursor-pointer"
                        data-job-url="{{ $jobUrl }}"
                        role="link"
                        tabindex="0"
                    >
                        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                            <div class="space-y-2 min-w-0">
                                {{-- Title can be normal text; card click navigates --}}
                                <div class="text-lg font-bold text-green-700">
                                    {{ $job->title }}
                                </div>

                                @if ($company)
                                    {{-- Company name -> company profile (must NOT trigger card click) --}}
                                    <a href="{{ $companyUrl }}"
                                       class="no-card-click text-sm text-slate-600 hover:underline inline-block">
                                        {{ $company->legal_name }}
                                    </a>
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

                            <div class="flex items-center gap-4 md:justify-end shrink-0">
                                {{-- Favorite (cookie 6 months) --}}
                                <button
                                    class="no-card-click pixel-outline grid h-10 w-10 place-items-center"
                                    type="button"
                                    aria-label="Favorite"
                                    data-fav-btn
                                    data-job-id="{{ $job->id }}"
                                    aria-pressed="false"
                                >
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/>
                                    </svg>
                                </button>

                                {{-- Logo -> company profile; placeholder keeps layout but invisible --}}
                                <div class="flex h-16 w-16 items-center justify-center">
                                    @if ($company && $companyUrl)
                                        <a href="{{ $companyUrl }}" class="no-card-click block h-16 w-16">
                                            @if ($logoUrl)
                                                <img class="max-h-16 w-auto" src="{{ $logoUrl }}" alt="{{ $company?->legal_name }}" />
                                            @else
                                                <div class="h-16 w-16 opacity-0"></div>
                                            @endif
                                        </a>
                                    @else
                                        <div class="h-16 w-16 opacity-0"></div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="pixel-frame bg-white p-6">
                        <p class="text-sm text-slate-600">No jobs found.</p>
                    </div>
                @endforelse

                <div>
                    {{ $jobs->links() }}
                </div>
            </div>
        </div>
    </section>

    {{-- Card click handling + favorites cookie --}}
    <script>
    (function () {
        function shouldIgnoreCardClick(target) {
            return !!target.closest('.no-card-click');
        }

        function openUrl(url) {
            if (!url) return;
            window.location.href = url;
        }

        document.addEventListener('click', function (e) {
            const card = e.target.closest('.job-card[data-job-url]');
            if (!card) return;

            if (shouldIgnoreCardClick(e.target)) return;

            openUrl(card.getAttribute('data-job-url'));
        });

        document.addEventListener('keydown', function (e) {
            const card = e.target.closest && e.target.closest('.job-card[data-job-url]');
            if (!card) return;

            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                openUrl(card.getAttribute('data-job-url'));
            }
        });

        // Favorites cookie (6 months)
        const COOKIE_NAME = 'job_favs_v1';
        const MAX_AGE = 60 * 60 * 24 * 180; // 180 days

        function getCookie(name) {
            const m = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/([.$?*|{}()[\]\\/+^])/g,'\\$1') + '=([^;]*)'));
            return m ? decodeURIComponent(m[1]) : null;
        }

        function setCookie(name, value, maxAgeSeconds) {
            document.cookie = name + '=' + encodeURIComponent(value)
                + '; Max-Age=' + maxAgeSeconds
                + '; Path=/; SameSite=Lax; Secure';
        }

        function readFavs() {
            try {
                const raw = getCookie(COOKIE_NAME);
                if (!raw) return [];
                const arr = JSON.parse(raw);
                return Array.isArray(arr) ? arr.map(Number) : [];
            } catch (e) {
                return [];
            }
        }

        function writeFavs(ids) {
            setCookie(COOKIE_NAME, JSON.stringify(ids), MAX_AGE);
        }

        function renderFavButtons() {
            const favs = readFavs();
            document.querySelectorAll('[data-fav-btn]').forEach(btn => {
                const id = Number(btn.getAttribute('data-job-id'));
                const active = favs.includes(id);
                btn.setAttribute('aria-pressed', active ? 'true' : 'false');
                btn.classList.toggle('is-fav', active);
            });
        }

        document.addEventListener('click', function (e) {
            const btn = e.target.closest && e.target.closest('[data-fav-btn]');
            if (!btn) return;

            // stop card navigation
            e.preventDefault();
            e.stopPropagation();

            const id = Number(btn.getAttribute('data-job-id'));
            let favs = readFavs();

            if (favs.includes(id)) favs = favs.filter(x => x !== id);
            else favs.push(id);

            writeFavs(favs);
            renderFavButtons();
        });

        document.addEventListener('DOMContentLoaded', renderFavButtons);
    })();
    </script>
</x-layouts.pixel>
