{{-- resources/views/jobs/show.blade.php --}}

<x-layouts.pixel :title="$job->title">
    @push('head')
        <link
            rel="stylesheet"
            href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
            crossorigin=""
        />
    @endpush

    <section class="mx-auto flex w-full max-w-6xl flex-col gap-6">
        {{-- HEADER --}}
        <div class="pixel-frame p-8">
            @php
                $companyLogoPath = $company?->logo_path ?: $company?->top_partner_logo_path;
                $companyLogoUrl = $companyLogoPath ? asset('storage/' . ltrim($companyLogoPath, '/')) : null;
                $companyUrl = $company ? url('/company/' . $company->id) : null;
            @endphp

            <h1 class="text-3xl font-bold text-slate-900">{{ $job->title }}</h1>

            @if ($company)
                <div class="mt-3 flex items-center gap-3">
                    <a class="flex h-12 w-12 items-center justify-center" href="{{ $companyUrl }}">
                        @if ($companyLogoUrl)
                            <img class="max-h-12 w-auto" src="{{ $companyLogoUrl }}" alt="{{ $company->legal_name }}" />
                        @else
                            <div class="h-12 w-12 opacity-0"></div>
                        @endif
                    </a>

                    <a class="text-sm font-semibold text-slate-700 hover:underline" href="{{ $companyUrl }}">
                        {{ $company->legal_name }}
                    </a>
                </div>
            @endif

            @if ($periodStart || $periodEnd)
                <p class="mt-2 text-sm text-slate-600">
                    @if ($periodStart && $periodEnd)
                        Online von {{ $periodStart->format('d.m.Y') }} bis {{ $periodEnd->format('d.m.Y') }}
                    @elseif ($periodStart)
                        Online seit {{ $periodStart->format('d.m.Y') }}
                    @else
                        Online bis {{ $periodEnd->format('d.m.Y') }}
                    @endif
                </p>
            @endif
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
            <div class="flex flex-col gap-6 lg:col-span-3">
                <div class="pixel-frame p-6">
                    <h2 class="text-lg font-bold text-slate-900">Beschreibung</h2>
                    <div class="prose prose-sm mt-4 max-w-none text-slate-600">
                        {!! nl2br(e($job->description)) !!}
                    </div>
                </div>

                <div class="pixel-frame p-6">
                    <h2 class="text-lg font-bold text-slate-900">Kontakt HR</h2>
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
                            <p>Kontaktangaben folgen.</p>
                        @endif
                    </div>
                </div>
            </div>

            <aside class="flex flex-col gap-6 lg:col-span-1">
                <div class="pixel-frame space-y-4 p-6">
                    <h3 class="text-xs uppercase tracking-[0.2em] text-slate-500">Job Infos</h3>

                    <dl class="space-y-4 text-sm">
                        @if ($location['line'])
                            <div>
                                <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Arbeitsort</dt>
                                <dd class="mt-1 font-semibold text-slate-900">{{ $location['line'] }}</dd>

                                @if ($location['street'] || $location['postal'])
                                    <dd class="mt-1 text-xs text-slate-500">
                                        {{ $location['street'] }}
                                        @if ($location['street'] && $location['postal'])
                                            ·
                                        @endif
                                        {{ $location['postal'] }}
                                    </dd>
                                @endif
                            </div>
                        @endif

                        @if ($employmentType)
                            <div>
                                <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Beschäftigungsart</dt>
                                <dd class="mt-1 font-semibold text-slate-900">{{ $employmentType }}</dd>
                            </div>
                        @endif

                        @if ($workload)
                            <div>
                                <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Pensum</dt>
                                <dd class="mt-1 font-semibold text-slate-900">{{ $workload }}</dd>
                            </div>
                        @endif

                        @if ($salary)
                            <div>
                                <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Salary</dt>
                                <dd class="mt-1 font-semibold text-slate-900">{{ $salary }}</dd>
                            </div>
                        @endif

                        @if ($job->available_from)
                            <div>
                                <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Startdatum</dt>
                                <dd class="mt-1 font-semibold text-slate-900">{{ $job->available_from->format('d.m.Y') }}</dd>
                            </div>
                        @endif

                        @if ($job->application_deadline)
                            <div>
                                <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Bewerbungsschluss</dt>
                                <dd class="mt-1 font-semibold text-slate-900">{{ $job->application_deadline->format('d.m.Y') }}</dd>
                            </div>
                        @endif

                        @if ($job->sknicePosition)
                            <div>
                                <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Kategorie</dt>
                                <dd class="mt-1 font-semibold text-slate-900">{{ $job->sknicePosition->title }}</dd>
                            </div>
                        @endif

                        @if ($job->educationLevel)
                            <div>
                                <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Level</dt>
                                <dd class="mt-1 font-semibold text-slate-900">{{ $job->educationLevel->label }}</dd>
                            </div>
                        @endif

                        @if ($job->jobLanguages->isNotEmpty())
                            <div>
                                <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Sprachen</dt>
                                <dd class="mt-1 text-slate-600">
                                    {{ $job->jobLanguages->map(fn ($lang) => strtoupper($lang->language_code) . ' ' . $lang->level)->implode(', ') }}
                                </dd>
                            </div>
                        @endif

                        @if ($job->jobSkills->isNotEmpty())
                            <div>
                                <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Skills</dt>
                                <dd class="mt-1 text-slate-600">
                                    {{ $job->jobSkills->map(fn ($skill) => $skill->skill?->name ?: null)->filter()->implode(', ') }}
                                </dd>
                            </div>
                        @endif

                        @if ($job->benefits->isNotEmpty())
                            <div>
                                <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Benefits</dt>
                                <dd class="mt-1 text-slate-600">
                                    {{ $job->benefits->pluck('label')->implode(', ') }}
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <div class="pixel-frame p-6">
                    <h3 class="text-xs uppercase tracking-[0.2em] text-slate-500">Open-Source Karte</h3>
                    @if ($map['hasCoordinates'])
                        <div id="job-map" class="mt-4 h-56 w-full pixel-outline"></div>
                    @else
                        <p class="mt-3 text-sm text-slate-600">Standortkarte derzeit nicht verfügbar.</p>
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
