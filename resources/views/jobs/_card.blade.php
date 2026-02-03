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
            <div class="flex h-20 w-20 items-center justify-center">
                @if ($logoUrl)
                    <a href="{{ $companyUrl }}" class="pointer-events-auto block">
                        <img class="max-h-20 w-auto" src="{{ $logoUrl }}" alt="{{ $company?->legal_name }}" />
                    </a>
                @else
                    <div class="h-20 w-20 opacity-0"></div>
                @endif
            </div>

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
