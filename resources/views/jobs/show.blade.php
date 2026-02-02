<x-layouts.pixel :title="$job->title">
    @push('head')
        <link
            rel="stylesheet"
            href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
            crossorigin=""
        />
    @endpush

    <section class="mx-auto flex w-full max-w-7xl flex-col gap-6">
        <div class="pixel-frame p-8">
            <h1 class="text-3xl font-bold text-slate-900">{{ $job->title }}</h1>
            <p class="mt-2 text-sm text-slate-600">
                @if ($periodStart && $periodEnd)
                    {{ __('main.online_from_to', ['from' => $periodStart->format('d.m.Y'), 'to' => $periodEnd->format('d.m.Y')]) }}
                @elseif ($periodStart)
                    {{ __('main.online_since', ['date' => $periodStart->format('d.m.Y')]) }}
                @elseif ($periodEnd)
                    {{ __('main.online_until', ['date' => $periodEnd->format('d.m.Y')]) }}
                @else
                    {{ __('main.online_open_ended') }}
                @endif
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
            <div class="flex flex-col gap-6 lg:col-span-3">
                <div class="pixel-frame p-6">
                    <h2 class="text-lg font-bold text-slate-900">{{ __('main.description') }}</h2>
                    <div class="prose prose-sm mt-4 max-w-none text-slate-600">
                        {!! nl2br(e($job->description)) !!}
                    </div>
                </div>

                <div class="pixel-frame p-6">
                    <h2 class="text-lg font-bold text-slate-900">{{ __('main.hr_contact') }}</h2>
                    <div class="mt-4 space-y-2 text-sm text-slate-600">
                        @if ($contact['name'])
                            <p class="font-semibold text-slate-900">{{ $contact['name'] }}</p>
                        @endif
                        @if ($contact['email'])
                            <p>
                                <a class="pixel-outline px-2 py-1 text-xs" href="mailto:{{ $contact['email'] }}">
                                    {{ $contact['email'] }}
                                </a>
                            </p>
                        @endif
                        @if ($contact['phone'])
                            <p>
                                <a class="pixel-outline px-2 py-1 text-xs" href="tel:{{ $contact['phone'] }}">
                                    {{ $contact['phone'] }}
                                </a>
                            </p>
                        @endif
                        @if (! $contact['name'] && ! $contact['email'] && ! $contact['phone'])
                            <p>{{ __('main.contact_details_soon') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <aside class="flex flex-col gap-6 lg:col-span-1">
                <div class="pixel-frame space-y-4 p-6">
                    <h3 class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.job_info') }}</h3>
                    <dl class="space-y-4 text-sm">
                        @if ($location['line'])
                            <div class="flex gap-3">
                                <div class="mt-0.5 text-slate-400">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-6.2 7-11a7 7 0 10-14 0c0 4.8 7 11 7 11z" />
                                        <circle cx="12" cy="10" r="2.5" />
                                    </svg>
                                </div>
                                <div>
                                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.workplace') }}</dt>
                                    <dd class="mt-1 font-semibold text-slate-900">{{ $location['line'] }}</dd>
                                </div>
                            </div>
                        @endif

                        @if ($employmentType)
                            <div class="flex gap-3">
                                <div class="mt-0.5 text-slate-400">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2" />
                                        <rect x="3" y="7" width="18" height="14" rx="2" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18" />
                                    </svg>
                                </div>
                                <div>
                                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.employment_type') }}</dt>
                                    <dd class="mt-1 font-semibold text-slate-900">{{ $employmentType }}</dd>
                                </div>
                            </div>
                        @endif

                        @if ($workload)
                            <div class="flex gap-3">
                                <div class="mt-0.5 text-slate-400">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <circle cx="12" cy="12" r="9" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v6l4 2" />
                                    </svg>
                                </div>
                                <div>
                                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.workload') }}</dt>
                                    <dd class="mt-1 font-semibold text-slate-900">{{ $workload }}</dd>
                                </div>
                            </div>
                        @endif

                        @if ($salary)
                            <div class="flex gap-3">
                                <div class="mt-0.5 text-slate-400">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <rect x="3" y="6" width="18" height="12" rx="2" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 12h10" />
                                    </svg>
                                </div>
                                <div>
                                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.salary') }}</dt>
                                    <dd class="mt-1 font-semibold text-slate-900">{{ $salary }}</dd>
                                    @if (!empty($salaryNote))
                                        <dd class="mt-1 text-xs text-slate-500">{{ $salaryNote }}</dd>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if ($job->available_from)
                            <div class="flex gap-3">
                                <div class="mt-0.5 text-slate-400">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <rect x="3" y="5" width="18" height="16" rx="2" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 3v4M16 3v4M3 11h18" />
                                    </svg>
                                </div>
                                <div>
                                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.start_date') }}</dt>
                                    <dd class="mt-1 font-semibold text-slate-900">{{ $job->available_from->format('d.m.Y') }}</dd>
                                </div>
                            </div>
                        @endif

                        @if ($job->application_deadline)
                            <div class="flex gap-3">
                                <div class="mt-0.5 text-slate-400">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <rect x="3" y="5" width="18" height="16" rx="2" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 3v4M16 3v4M3 11h18" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15l2 2 4-4" />
                                    </svg>
                                </div>
                                <div>
                                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.application_deadline') }}</dt>
                                    <dd class="mt-1 font-semibold text-slate-900">{{ $job->application_deadline->format('d.m.Y') }}</dd>
                                </div>
                            </div>
                        @endif

                        @if ($job->sknacePosition)
                            <div class="flex gap-3">
                                <div class="mt-0.5 text-slate-400">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10l2 5-2 5H7L5 12l2-5z" />
                                    </svg>
                                </div>
                                <div>
                                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.category') }}</dt>
                                    <dd class="mt-1 font-semibold text-slate-900">{{ $job->sknacePosition->title }}</dd>
                                </div>
                            </div>
                        @endif

                        @if ($job->educationLevel)
                            <div class="flex gap-3">
                                <div class="mt-0.5 text-slate-400">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10l9-4 9 4-9 4-9-4z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 12v5l5 2 5-2v-5" />
                                    </svg>
                                </div>
                                <div>
                                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.level') }}</dt>
                                    <dd class="mt-1 font-semibold text-slate-900">{{ $job->educationLevel->label }}</dd>
                                </div>
                            </div>
                        @endif

                        @if ($job->jobLanguages->isNotEmpty())
                            <div class="flex gap-3">
                                <div class="mt-0.5 text-slate-400">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <circle cx="12" cy="12" r="9" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18M12 3c3 3.5 3 14 0 18M12 3c-3 3.5-3 14 0 18" />
                                    </svg>
                                </div>
                                <div>
                                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.languages') }}</dt>
                                    <dd class="mt-1 text-slate-600">
                                        {{ $job->jobLanguages->map(fn ($lang) => ($languageOptions[$lang->language_code] ?? $lang->language_code) . ' ' . $lang->level)->implode(', ') }}
                                    </dd>
                                </div>
                            </div>
                        @endif

                        @if ($job->jobSkills->isNotEmpty())
                            <div class="flex gap-3">
                                <div class="mt-0.5 text-slate-400">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l2.6 5.3 5.8.8-4.2 4.1 1 5.8L12 16.8 6.8 19l1-5.8L3.6 9.1l5.8-.8L12 3z" />
                                    </svg>
                                </div>
                                <div>
                                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.skills') }}</dt>
                                    <dd class="mt-1 text-slate-600">
                                        {{ $job->jobSkills->map(fn ($skill) => $skill->skill?->name ?: null)->filter()->implode(', ') }}
                                    </dd>
                                </div>
                            </div>
                        @endif

                        @if ($job->benefits->isNotEmpty())
                            <div class="flex gap-3">
                                <div class="mt-0.5 text-slate-400">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <rect x="3" y="8" width="18" height="12" rx="2" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18M12 8v12" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8a2.5 2.5 0 115 0" />
                                    </svg>
                                </div>
                                <div>
                                    <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.benefits') }}</dt>
                                    <dd class="mt-1 text-slate-600">
                                        {{ $job->benefits->pluck('label')->implode(', ') }}
                                    </dd>
                                </div>
                            </div>
                        @endif
                    </dl>
                </div>

                <div class="pixel-frame p-6">
                    <h3 class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.open_source_map') }}</h3>
                    @if ($map['hasCoordinates'])
                        <div id="job-map" class="mt-4 h-56 w-full pixel-outline"></div>
                    @else
                        <p class="mt-3 text-sm text-slate-600">{{ __('main.map_unavailable') }}</p>
                    @endif
                </div>
            </aside>
        </div>
    </section>

    @push('scripts')
        <script
            src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""
        ></script>
        @if ($map['hasCoordinates'])
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const map = L.map('job-map').setView([{{ $map['lat'] }}, {{ $map['lng'] }}], 13);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors',
                    }).addTo(map);
                    L.marker([{{ $map['lat'] }}, {{ $map['lng'] }}]).addTo(map);
                });
            </script>
        @endif
    @endpush
</x-layouts.pixel>
