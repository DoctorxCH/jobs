<x-layouts.pixel :title="$company->legal_name">
    <section class="mx-auto flex w-full max-w-6xl flex-col gap-6">
        <div class="pixel-frame p-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">{{ $company->legal_name }}</h1>
                    @if ($locationLine)
                        <p class="mt-2 text-sm text-slate-600">{{ $locationLine }}</p>
                    @endif
                    @if ($company->is_top_partner)
                        <span class="pixel-chip mt-3 inline-flex px-3 py-1 text-xs uppercase tracking-[0.2em]">TOP</span>
                    @endif
                </div>

                <div class="flex h-20 w-20 items-center justify-center">
                    @if ($logoUrl)
                        <img class="max-h-20 w-auto" src="{{ $logoUrl }}" alt="{{ $company->legal_name }}" />
                    @else
                        <div class="h-20 w-20 opacity-0"></div>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
            <div class="flex flex-col gap-6 lg:col-span-3">
                @if ($company->description_short)
                    <div class="pixel-frame p-6">
                        <h2 class="text-lg font-bold text-slate-900">{{ __('main.company_short_description') }}</h2>
                        <p class="mt-3 text-sm text-slate-600">{{ $company->description_short }}</p>
                    </div>
                @endif

                @if ($company->bio)
                    <div class="pixel-frame p-6">
                        <h2 class="text-lg font-bold text-slate-900">{{ __('main.about_us') }}</h2>
                        <div class="prose prose-sm mt-4 max-w-none text-slate-600">
                            {!! nl2br(e($company->bio)) !!}
                        </div>
                    </div>
                @endif

                @if (count($socialLinks) > 0)
                    <div class="pixel-frame p-6">
                        <h2 class="text-lg font-bold text-slate-900">{{ __('main.social_links') }}</h2>
                        <div class="mt-4 flex flex-wrap gap-3">
                            @foreach ($socialLinks as $label => $url)
                                <a class="pixel-outline px-3 py-2 text-xs uppercase tracking-[0.2em]" href="{{ $url }}" target="_blank" rel="noopener">
                                    {{ is_string($label) ? strtoupper($label) : 'LINK' }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <aside class="flex flex-col gap-6 lg:col-span-1">
                <div class="pixel-frame p-6">
                    <h3 class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.contact') }}</h3>
                    <div class="mt-4 space-y-2 text-sm text-slate-600">
                        <p class="font-semibold text-slate-900">
                            {{ trim($company->contact_first_name . ' ' . $company->contact_last_name) }}
                        </p>
                        @if ($company->contact_email || $company->general_email)
                            <a class="pixel-outline inline-block px-2 py-1 text-xs" href="mailto:{{ $company->contact_email ?: $company->general_email }}">
                                {{ $company->contact_email ?: $company->general_email }}
                            </a>
                        @endif
                        @if ($company->contact_phone || $company->phone)
                            <a class="pixel-outline inline-block px-2 py-1 text-xs" href="tel:{{ $company->contact_phone ?: $company->phone }}">
                                {{ $company->contact_phone ?: $company->phone }}
                            </a>
                        @endif
                        @if ($company->website_url)
                            <a class="pixel-outline inline-block px-2 py-1 text-xs" href="{{ $company->website_url }}" target="_blank" rel="noopener">
                                {{ $company->website_url }}
                            </a>
                        @endif
                    </div>
                </div>

                <div class="pixel-frame p-6">
                    <h3 class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.company_facts') }}</h3>
                    <dl class="mt-4 space-y-3 text-sm">
                        @if ($company->team_size)
                            <div>
                                <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.team_size_label') }}</dt>
                                <dd class="mt-1 font-semibold text-slate-900">{{ $company->team_size }}</dd>
                            </div>
                        @endif
                        @if ($company->founded_year)
                            <div>
                                <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.founded_year_label') }}</dt>
                                <dd class="mt-1 font-semibold text-slate-900">{{ $company->founded_year }}</dd>
                            </div>
                        @endif
                        @if ($company->category)
                            <div>
                                <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.category') }}</dt>
                                <dd class="mt-1 font-semibold text-slate-900">{{ $company->category->name }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </aside>
        </div>

        <div class="pixel-frame p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-900">{{ __('main.open_jobs') }}</h2>
                <span class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ $openJobsCount }}</span>
            </div>
            <div class="mt-4 grid gap-4">
                @forelse ($openJobs as $job)
                    <div class="pixel-outline p-4">
                        <a class="text-sm font-semibold text-green-700 hover:underline" href="{{ route('jobs.show', $job) }}">
                            {{ $job->title }}
                        </a>
                        @if ($job->published_at)
                            <p class="mt-1 text-xs text-slate-500">{{ $job->published_at->format('d.m.Y') }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-slate-600">{{ __('main.no_open_jobs') }}</p>
                @endforelse
            </div>
        </div>
    </section>
</x-layouts.pixel>
