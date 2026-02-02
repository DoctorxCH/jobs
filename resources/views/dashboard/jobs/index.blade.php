<x-dashboard.layout title="{{ __('main.jobs') }}">
    <div class="flex items-center justify-between mb-6">
        <div class="text-sm text-slate-600">{{ __('main.manage_job_postings') }}</div>
        <a href="{{ route('frontend.jobs.create') }}" class="pixel-outline px-4 py-2 text-xs uppercase tracking-[0.2em]">{{ __('main.new_job') }}</a>
    </div>

    <div class="space-y-3">
        @forelse ($jobs as $job)
            <div class="pixel-outline px-4 py-3 flex items-center justify-between gap-4">
                <div>
                    <div class="font-semibold">{{ $job->title ?? __('main.untitled_job') }}</div>
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.status') }}: {{ $job->status }}</div>
                    @if ($job->expires_at)
                        <div class="text-xs text-slate-500">{{ __('main.expires') }}: {{ $job->expires_at->format('Y-m-d') }}</div>
                    @endif
                </div>
                <a href="{{ route('frontend.jobs.edit', $job) }}" class="pixel-outline px-3 py-2 text-xs uppercase tracking-[0.2em]">{{ __('main.edit') }}</a>
            </div>
        @empty
            <div class="pixel-outline px-4 py-6 text-center text-sm text-slate-600">
                {{ __('main.no_jobs_yet_create') }}
            </div>
        @endforelse
    </div>
</x-dashboard.layout>
