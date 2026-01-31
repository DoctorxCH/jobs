<x-layouts.pixel title="Terms & Conditions">
    <section class="mx-auto flex w-full max-w-4xl flex-col gap-6">
        <div class="pixel-frame p-8">
            <h1 class="text-3xl font-bold text-slate-900">
                {{ $page?->title ?? 'Terms & Conditions (AGB)' }}
            </h1>
            <p class="mt-2 text-sm text-slate-600">Last updated: {{ $page?->updated_at?->format('d.m.Y') ?? now()->format('d.m.Y') }}</p>
        </div>

        <div class="pixel-frame p-6">
            @if ($page)
                <div class="prose prose-sm max-w-none text-slate-700">
                    {!! $page->content !!}
                </div>
            @else
                <p class="text-sm text-slate-600">
                    Terms & Conditions content is not yet configured. Please contact support.
                </p>
            @endif
        </div>
    </section>
</x-layouts.pixel>
