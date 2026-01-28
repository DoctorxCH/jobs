<x-dashboard.layout title="Jobs">
    <div class="flex items-center justify-between mb-6">
        <div class="text-sm text-slate-600">Manage your job postings.</div>
        <a href="{{ route('frontend.jobs.create') }}" class="pixel-outline px-4 py-2 text-xs uppercase tracking-[0.2em]">New job</a>
    </div>

    <div class="space-y-3">
        @forelse ($jobs as $job)
            <div class="pixel-outline px-4 py-3 flex items-center justify-between gap-4">
                <div>
                    <div class="font-semibold">{{ $job->title ?? 'Untitled job' }}</div>
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-500">Status: {{ $job->status }}</div>
                    @if ($job->expires_at)
                        <div class="text-xs text-slate-500">Expires: {{ $job->expires_at->format('Y-m-d') }}</div>
                    @endif
                </div>
                <a href="{{ route('frontend.jobs.edit', $job) }}" class="pixel-outline px-3 py-2 text-xs uppercase tracking-[0.2em]">Edit</a>
            </div>
        @empty
            <div class="pixel-outline px-4 py-6 text-center text-sm text-slate-600">
                No jobs yet. Create your first posting.
            </div>
        @endforelse
    </div>
</x-dashboard.layout>
