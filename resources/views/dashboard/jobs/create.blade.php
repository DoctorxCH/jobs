<x-dashboard.layout title="Create job">
    <form method="POST" action="{{ route('frontend.jobs.store') }}" class="space-y-6">
        @csrf
        @include('dashboard.jobs._form')

        <div class="flex justify-end">
            <button type="submit" class="pixel-outline px-6 py-2 text-xs uppercase tracking-[0.2em]">Save draft</button>
        </div>
    </form>
</x-dashboard.layout>
