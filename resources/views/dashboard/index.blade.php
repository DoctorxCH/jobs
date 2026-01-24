<x-dashboard.layout>
    <div class="flex flex-col gap-4">
        <div class="text-xs uppercase tracking-[0.2em] text-slate-500">
            Frontend
        </div>

        <h1 class="text-2xl font-bold">
            Hello {{ auth()->user()->name }}
        </h1>

        <p class="text-sm text-slate-600">
            Your dashboard is empty for now — coming next.
        </p>

        <div class="mt-4 inline-flex">
            <div class="pixel-outline px-4 py-2 text-xs uppercase tracking-[0.2em]">
                User ID: {{ auth()->id() }}
            </div>
        </div>
    </div>
</x-dashboard.layout>
