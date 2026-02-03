{{-- resources/views/jobs/favorites.blade.php --}}

<x-layouts.pixel :title="__('main.favorites')">
    <section class="mx-auto flex w-full max-w-6xl flex-col gap-6">
        {{-- HEADER --}}
        <div class="pixel-outline p-8">
            <div class="text-s uppercase tracking-[0.2em] text-slate-800 font-bold">
                {{ __('main.favorites') }}
            </div>

            <div class="mt-4 flex items-center justify-between">
                <p class="text-sm text-slate-600">
                    {{ __('main.jobs_found', ['count' => $jobs->count()]) }}
                </p>

                <a href="{{ route('jobs.index') }}" class="pixel-button px-4 py-2 text-xs">
                    {{ __('main.search') }}
                </a>
            </div>
        </div>

        {{-- LIST --}}
        <div class="flex flex-col gap-4">
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
                            $salary = __('main.salary_range', ['min' => $min, 'max' => $max, 'currency' => $currency]);
                        } elseif ($min) {
                            $salary = __('main.salary_from_value', ['min' => $min, 'currency' => $currency]);
                        } elseif ($max) {
                            $salary = __('main.salary_up_to', ['max' => $max, 'currency' => $currency]);
                        }
                    }

                    $jobUrl = route('jobs.show', $job);
                    $companyUrl = $company ? url('/company/' . $company->id) : null;
                @endphp

                {{-- CLICKABLE CARD (job link overlay + separate company link) --}}
                <article class="job-card pixel-frame pixel-card bg-white p-6 relative">
                    <a href="{{ $jobUrl }}" class="absolute inset-0 z-0" aria-label="{{ $job->title }}"></a>

                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between relative z-10 pointer-events-none">
                        <div class="space-y-2 min-w-0">
                            <div class="text-lg font-bold text-blue-700">
                                {{ $job->title }}
                            </div>

                            @if ($company)
                                <a
                                    href="{{ $companyUrl }}"
                                    class="pointer-events-auto text-sm text-slate-600 hover:underline inline-block"
                                >
                                    {{ $company->legal_name }}
                                </a>
                            @endif

                            @if ($location)
                                <p class="text-xs text-slate-500">{{ $location }}</p>
                            @endif

                            <div class="flex flex-wrap gap-2 text-xs uppercase tracking-[0.2em] text-slate-500">
                                @if ($company?->is_top_partner)
                                    <span class="px-2 py-1 flex items-center">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 16L3 5l5.5 4L12 4l3.5 5L21 5l-2 11H5z"/>
                                            <path d="M19 6l-2 4m-6-4l2 4m-6-4l2 4"/>
                                        </svg>
                                    </span>
                                @endif
                                @if ($salary)
                                    <span class="px-2 py-1">{{ $salary }}</span>
                                @endif
                                @if ($jobDate)
                                    <span class="px-2 py-1">{{ $jobDate->diffForHumans() }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-col items-center gap-2 md:justify-end shrink-0">
                            {{-- Logo placeholder keeps layout --}}
                            <div class="flex h-20 w-20 items-center justify-center">
                                @if ($logoUrl)
                                    <a href="{{ $companyUrl }}" class="pointer-events-auto block">
                                        <img class="max-h-20 w-auto" src="{{ $logoUrl }}" alt="{{ $company?->legal_name }}" />
                                    </a>
                                @else
                                    <div class="h-20 w-20 opacity-0"></div>
                                @endif
                            </div>

                            {{-- Remove favorite button --}}
                            <button
                                class="pixel-outline shadow-none grid h-10 w-10 place-items-center pointer-events-auto text-slate-700 hover:text-red-500 transition-colors is-fav"
                                type="button"
                                aria-label="{{ __('main.favorite') }}"
                                data-fav-btn
                                data-job-id="{{ $job->id }}"
                                aria-pressed="true"
                            >
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </article>
            @empty
                <div class="pixel-frame bg-white p-8 text-center">
                    <p class="text-sm text-slate-600">{{ __('main.no_favorites') }}</p>
                    <a href="{{ route('jobs.index') }}" class="pixel-button mt-4 inline-block px-4 py-2 text-xs">
                        {{ __('main.search') }}
                    </a>
                </div>
            @endforelse
        </div>
    </section>

    {{-- Favorites cookie handler --}}
    <script>
    (function () {
        const COOKIE_NAME = 'job_favs_v1';
        const MAX_AGE = 60 * 60 * 24 * 180; // 180 days

        function getCookie(name) {
            const m = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/([.$?*|{}()[\]\\/+^])/g,'\\$1') + '=([^;]*)'));
            return m ? decodeURIComponent(m[1]) : null;
        }

        function setCookie(name, value, maxAgeSeconds) {
            const secure = location.protocol === 'https:' ? '; Secure' : '';
            document.cookie = name + '=' + encodeURIComponent(value)
                + '; Max-Age=' + maxAgeSeconds
                + '; Path=/; SameSite=Lax' + secure;
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
                if (active) {
                    btn.style.color = '#ef4444';
                }
            });
        }

        document.addEventListener('click', function (e) {
            const btn = e.target.closest && e.target.closest('[data-fav-btn]');
            if (!btn) return;

            e.preventDefault();
            e.stopPropagation();

            const id = Number(btn.getAttribute('data-job-id'));
            let favs = readFavs();

            // Remove from favorites on favorites page
            if (favs.includes(id)) {
                favs = favs.filter(x => x !== id);
                writeFavs(favs);
                
                // Remove the card from view
                const card = btn.closest('.job-card');
                if (card) {
                    card.style.opacity = '0.5';
                    setTimeout(() => {
                        location.reload();
                    }, 300);
                }
            }
        });

        document.addEventListener('DOMContentLoaded', renderFavButtons);
        renderFavButtons();
    })();
    </script>
</x-layouts.pixel>
