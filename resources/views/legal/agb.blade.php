<x-layouts.pixel title="{{ __('main.terms_title') }}">
    <section class="mx-auto flex w-full max-w-4xl flex-col gap-6">
        <div class="pixel-frame p-8">
            <h1 class="text-3xl font-bold text-slate-900">
                {{ $page?->title ?? __('main.terms_default_title') }}
            </h1>
            <p class="mt-2 text-sm text-slate-600">{{ __('main.last_updated') }} {{ $page?->updated_at?->format('d.m.Y') ?? now()->format('d.m.Y') }}</p>
        </div>

        <div class="pixel-frame p-6">
            @if ($page)
                <div class="prose prose-sm max-w-none text-slate-700">
                    {!! $page->content !!}
                </div>

                @if ($page->effective_from)
                    <p class="mt-6 text-xs text-slate-500">
                        {{ __('main.terms_effective_from', ['date' => $page->effective_from->format('d.m.Y')]) }}
                    </p>
                @endif
            @else
                <p class="text-sm text-slate-600">
                    {{ __('main.terms_missing') }}
                </p>
            @endif
        </div>
    </section>
</x-layouts.pixel>
