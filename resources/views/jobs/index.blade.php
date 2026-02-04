{{-- resources/views/jobs/index.blade.php --}}

<x-layouts.pixel>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">

    <section class="mx-auto flex w-full max-w-6xl flex-col gap-6">
        {{-- HEADER --}}
        <div class="pixel-outline p-8">
            <div class="text-s uppercase tracking-[0.2em] text-slate-800 font-bold">
                {{ __('main.jobs_listing_title') }}
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-[1fr_auto] md:items-center">
                {{-- SEARCH (job title only) --}}
                <form
                    method="GET"
                    action="{{ route('jobs.index') }}"
                    class="w-full flex flex-col gap-3 md:flex-row md:items-center"
                >
                    {{-- keep filters --}}
                    <input type="hidden" name="country" value="{{ $countryCode ?? 'SK' }}">
                    @if(!empty($selectedRegion))
                        <input type="hidden" name="region" value="{{ (int) $selectedRegion }}">
                    @endif
                    @if(!empty($selectedCity))
                        <input type="hidden" name="city" value="{{ (int) $selectedCity }}">
                    @endif
                    @if(!empty($salaryMin))
                        <input type="hidden" name="salary_min" value="{{ (int) $salaryMin }}">
                    @endif
                    @if(!empty($salaryMax))
                        <input type="hidden" name="salary_max" value="{{ (int) $salaryMax }}">
                    @endif

                    <div class="flex w-full gap-3">
                        <input
                            name="q"
                            value="{{ $search ?? '' }}"
                            class="pixel-input w-full px-4 py-3 text-sm"
                            placeholder="{{ __('main.search_job_title_placeholder') }}"
                        />

                        <button type="submit" class="pixel-button px-5 py-3 text-xs shrink-0">
                            {{ __('main.search') }}
                        </button>
                    </div>
                </form>

                {{-- JOB COUNT --}}
                <div class="text-l font-bold whitespace-nowrap">
                    {{ __('main.jobs_found', ['count' => $jobs->total()]) }}
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between gap-3 lg:hidden">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.filters') }}</div>
            <button
                type="button"
                class="pixel-outline px-4 py-2 text-xs uppercase tracking-[0.2em]"
                data-filter-toggle
                aria-expanded="false"
                aria-controls="job-filters"
            >
                {{ __('main.show_filters') }}
            </button>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
            {{-- FILTERS --}}
            <aside class="pixel-frame p-6 hidden lg:block lg:col-span-1" id="job-filters" data-filter-panel>
                <form class="space-y-4" method="get" action="{{ route('jobs.index') }}">
                    <input type="hidden" name="country" value="{{ $countryCode ?? 'SK' }}">
                    {{-- keep search when applying filters --}}
                    @if(!empty($search))
                        <input type="hidden" name="q" value="{{ $search }}">
                    @endif

                    <label class="block text-xs uppercase tracking-[0.2em] text-slate-500">
                        {{ __('main.region') }}
                        <select class="pixel-input mt-2 w-full px-4 py-3 text-sm" name="region">
                            <option value="">{{ __('main.all_regions') }}</option>
                            @foreach ($regions as $region)
                                <option value="{{ $region->id }}" @selected((int) $selectedRegion === (int) $region->id)>
                                    {{ $region->name }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <label class="block text-xs uppercase tracking-[0.2em] text-slate-500">
                        {{ __('main.city') }}
                        <select class="pixel-input mt-2 w-full px-4 py-3 text-sm" name="city" @disabled(! $selectedRegion)>
                            <option value="">{{ __('main.all_cities') }}</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}" @selected((int) $selectedCity === (int) $city->id)>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <div class="border-t border-slate-200 pt-4 space-y-6">
                        <div class="space-y-3">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">{{ __('main.more_filters') }}</p>

                            <label class="block text-xs uppercase tracking-[0.2em] text-slate-500">
                                {{ __('main.salary_from') }}
                                <input
                                    class="pixel-input mt-2 w-full px-4 py-3 text-sm text-slate-900"
                                    placeholder="{{ __('main.salary_placeholder') }}"
                                    type="number"
                                    name="salary_min"
                                    value="{{ $salaryMin ?? '' }}"
                                />
                            </label>

                            <label class="block text-xs uppercase tracking-[0.2em] text-slate-500">
                                {{ __('main.salary_to') }}
                                <input
                                    class="pixel-input mt-2 w-full px-4 py-3 text-sm text-slate-900"
                                    placeholder="{{ __('main.salary_placeholder') }}"
                                    type="number"
                                    name="salary_max"
                                    value="{{ $salaryMax ?? '' }}"
                                />
                            </label>
                        </div>

                        <div class="space-y-3">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">{{ __('main.filter_section_job_type') }}</p>

                            <label class="block text-xs uppercase tracking-[0.2em] text-slate-500">
                                {{ __('main.job_type') }}
                                <select class="pixel-input mt-2 w-full px-4 py-3 text-sm" name="employment_type">
                                    <option value="">{{ __('main.select') }}</option>
                                    @foreach ($employmentTypeOptions ?? [] as $typeValue => $typeLabel)
                                        <option value="{{ $typeValue }}" @selected($employmentType === $typeValue)>{{ $typeLabel }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <div class="grid grid-cols-2 gap-3">
                                <label class="block text-xs uppercase tracking-[0.2em] text-slate-500">
                                    {{ __('main.workload_min') }}
                                    <input
                                        class="pixel-input mt-2 w-full px-4 py-3 text-sm text-slate-900"
                                        type="number"
                                        name="workload_min"
                                        min="0"
                                        max="100"
                                        value="{{ $workloadMinFilter ?? '' }}"
                                    />
                                </label>

                                <label class="block text-xs uppercase tracking-[0.2em] text-slate-500">
                                    {{ __('main.workload_max') }}
                                    <input
                                        class="pixel-input mt-2 w-full px-4 py-3 text-sm text-slate-900"
                                        type="number"
                                        name="workload_max"
                                        min="0"
                                        max="100"
                                        value="{{ $workloadMaxFilter ?? '' }}"
                                    />
                                </label>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">{{ __('main.filter_section_experience') }}</p>

                            <label class="block text-xs uppercase tracking-[0.2em] text-slate-500">
                                {{ __('main.education_level') }}
                                <select class="pixel-input mt-2 w-full px-4 py-3 text-sm" name="education_level">
                                    <option value="">{{ __('main.select') }}</option>
                                    @foreach ($educationLevels as $level)
                                        <option value="{{ $level->id }}" @selected((int) $educationLevelId === (int) $level->id)>{{ $level->label }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <label class="block text-xs uppercase tracking-[0.2em] text-slate-500">
                                {{ __('main.education_field') }}
                                <select class="pixel-input mt-2 w-full px-4 py-3 text-sm" name="education_field">
                                    <option value="">{{ __('main.select') }}</option>
                                    @foreach ($educationFields as $field)
                                        <option value="{{ $field->id }}" @selected((int) $educationFieldId === (int) $field->id)>{{ $field->label }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <label class="block text-xs uppercase tracking-[0.2em] text-slate-500">
                                {{ __('main.min_years_experience') }}
                                <input
                                    type="number"
                                    min="0"
                                    class="pixel-input mt-2 w-full px-4 py-3 text-sm text-slate-900"
                                    name="experience_min"
                                    value="{{ $experienceMin ?? '' }}"
                                />
                            </label>
                        </div>

                        <div class="space-y-3">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">{{ __('main.filter_section_special_attributes') }}</p>

                            <div class="flex flex-col gap-2">
                                <label class="flex items-center gap-2 rounded border border-slate-200 px-3 py-2 text-xs uppercase tracking-[0.2em] text-slate-500">
                                    <input type="checkbox" name="is_remote" value="1" @checked($filterRemote) />
                                    {{ __('main.remote') }}
                                </label>
                                <label class="flex items-center gap-2 rounded border border-slate-200 px-3 py-2 text-xs uppercase tracking-[0.2em] text-slate-500">
                                    <input type="checkbox" name="is_hybrid" value="1" @checked($filterHybrid) />
                                    {{ __('main.hybrid') }}
                                </label>
                                <label class="flex items-center gap-2 rounded border border-slate-200 px-3 py-2 text-xs uppercase tracking-[0.2em] text-slate-500">
                                    <input type="checkbox" name="travel_required" value="1" @checked($filterTravel) />
                                    {{ __('main.travel_required') }}
                                </label>
                                <label class="flex items-center gap-2 rounded border border-slate-200 px-3 py-2 text-xs uppercase tracking-[0.2em] text-slate-500">
                                    <input type="checkbox" name="is_for_graduates" value="1" @checked($filterGraduates) />
                                    {{ __('main.for_graduates') }}
                                </label>
                                <label class="flex items-center gap-2 rounded border border-slate-200 px-3 py-2 text-xs uppercase tracking-[0.2em] text-slate-500">
                                    <input type="checkbox" name="is_for_disabled" value="1" @checked($filterDisabled) />
                                    {{ __('main.for_disabled_candidates') }}
                                </label>
                                <label class="flex items-center gap-2 rounded border border-slate-200 px-3 py-2 text-xs uppercase tracking-[0.2em] text-slate-500">
                                    <input type="checkbox" name="has_company_car" value="1" @checked($filterCompanyCar) />
                                    {{ __('main.company_car') }}
                                </label>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">{{ __('main.filter_section_skills') }}</p>

                            @php
                                $selectedSkillIds = $selectedSkills ?? [];
                                $selectedBenefitIds = $selectedBenefits ?? [];
                                $selectedLanguageCodes = $selectedLanguages ?? [];
                            @endphp

                            <label class="block text-xs uppercase tracking-[0.2em] text-slate-500">
                                {{ __('main.skills') }}
                                <select
                                    class="pixel-input mt-2 w-full px-4 py-3 text-sm text-slate-900 js-multi-select"
                                    name="skills[]"
                                    multiple
                                >
                                    @foreach ($skills as $id => $label)
                                        <option value="{{ $id }}" @selected(in_array($id, $selectedSkillIds))>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>

                            <label class="block text-xs uppercase tracking-[0.2em] text-slate-500">
                                {{ __('main.benefits') }}
                                <select
                                    class="pixel-input mt-2 w-full px-4 py-3 text-sm text-slate-900 js-multi-select"
                                    name="benefits[]"
                                    multiple
                                >
                                    @foreach ($benefits as $groupLabel => $items)
                                        @if (is_array($items))
                                            <optgroup label="{{ $groupLabel }}">
                                                @foreach ($items as $id => $label)
                                                    <option value="{{ $id }}" @selected(in_array($id, $selectedBenefitIds))>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @else
                                            <option value="{{ $groupLabel }}" @selected(in_array($groupLabel, $selectedBenefitIds))>
                                                {{ $items }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </label>

                            <label class="block text-xs uppercase tracking-[0.2em] text-slate-500">
                                {{ __('main.languages') }}
                                <select
                                    class="pixel-input mt-2 w-full px-4 py-3 text-sm text-slate-900 js-multi-select"
                                    name="languages[]"
                                    multiple
                                >
                                    @foreach ($languageOptions as $code => $label)
                                        <option value="{{ $code }}" @selected(in_array($code, $selectedLanguageCodes))>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>
                        </div>
                    </div>

                    <div class="mt-4 flex gap-3">
                        <button type="submit" class="pixel-button w-full px-6 py-3 text-xs">
                            {{ __('main.apply_filters') }}
                        </button>

                        <a
                            href="{{ route('jobs.index', ['country' => $countryCode ?? 'SK', 'q' => $search ?: null]) }}"
                            class="pixel-outline px-6 py-3 text-xs uppercase tracking-[0.2em] text-slate-800 whitespace-nowrap flex items-center justify-center"
                        >
                            {{ __('main.reset') }}
                        </a>
                    </div>
                </form>
            </aside>

            {{-- LIST --}}
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
                                $salary = __('main.salary_range', ['min' => $min, 'max' => $max, 'currency' => $currency]);
                            } elseif ($min) {
                                $salary = __('main.salary_from_value', ['min' => $min, 'currency' => $currency]);
                            } elseif ($max) {
                                $salary = __('main.salary_up_to', ['max' => $max, 'currency' => $currency]);
                            }
                        }

                        $jobUrl = route('jobs.show', $job); // id-binding
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

                                {{-- Favorite (cookie 6 months) --}}
                                <button
                                    class="pixel-outline shadow-none grid h-10 w-10 place-items-center pointer-events-auto text-slate-700 hover:text-red-500 transition-colors"
                                    type="button"
                                    aria-label="{{ __('main.favorite') }}"
                                    data-fav-btn
                                    data-job-id="{{ $job->id }}"
                                    aria-pressed="false"
                                >
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="pixel-frame bg-white p-6">
                        <p class="text-sm text-slate-600">{{ __('main.no_jobs_found') }}</p>
                    </div>
                @endforelse

                <div>
                    {{ $jobs->links() }}
                </div>
            </div>
        </div>
    </section>

    {{-- Card click handling + favorites cookie --}}
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
    (function () {
        const filterToggle = document.querySelector('[data-filter-toggle]');
        const filterPanel = document.querySelector('[data-filter-panel]');

        if (filterToggle && filterPanel) {
            const showLabel = {{ Js::from(__('main.show_filters')) }};
            const hideLabel = {{ Js::from(__('main.hide_filters')) }};

            filterToggle.addEventListener('click', () => {
                const isHidden = filterPanel.classList.contains('hidden');
                filterPanel.classList.toggle('hidden', !isHidden);
                filterToggle.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
                filterToggle.textContent = isHidden ? hideLabel : showLabel;
            });
        }

        function initMultiSelects() {
            if (!window.Choices) return;

            document.querySelectorAll('.js-multi-select').forEach((select) => {
                if (select.dataset.choicesInitialized) return;

                new Choices(select, {
                    removeItemButton: true,
                    searchEnabled: true,
                    shouldSort: false,
                    itemSelectText: '',
                    placeholder: true,
                    placeholderValue: select.getAttribute('placeholder') || '',
                });

                select.dataset.choicesInitialized = 'true';
            });
        }

        // Favorites cookie (6 months)
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
                const svg = btn.querySelector('svg');
                btn.setAttribute('aria-pressed', active ? 'true' : 'false');
                btn.classList.toggle('is-fav', active);
                if (active) {
                    btn.style.color = '#ef4444';
                    if (svg) svg.setAttribute('fill', 'currentColor');
                } else {
                    btn.style.color = '';
                    if (svg) svg.setAttribute('fill', 'none');
                }
            });
        }

        document.addEventListener('click', function (e) {
            const btn = e.target.closest && e.target.closest('[data-fav-btn]');
            if (!btn) return;

            // stop card navigation (button inside <a>)
            e.preventDefault();
            e.stopPropagation();

            const id = Number(btn.getAttribute('data-job-id'));
            let favs = readFavs();

            if (favs.includes(id)) favs = favs.filter(x => x !== id);
            else favs.push(id);

            writeFavs(favs);
            renderFavButtons();
            
            // Trigger header icon update
            if (window.updateHeaderFavIcon) {
                window.updateHeaderFavIcon();
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            initMultiSelects();
            renderFavButtons();
        });
        initMultiSelects();
        renderFavButtons();
    })();
    </script>
</x-layouts.pixel>
