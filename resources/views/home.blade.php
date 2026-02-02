<x-layouts.pixel>
    <section class="mx-auto flex w-full max-w-6xl flex-col gap-10">
        <div class="pixel-section pixel-hero">
            <div class="flex flex-wrap items-center gap-3 text-xs uppercase tracking-[0.2em] text-slate-500">
                <span class="pixel-chip px-3 py-1">{{ content('home.chips.minimal') }}</span>
                <span class="pixel-chip px-3 py-1">{{ content('home.chips.pixel') }}</span>
                <span class="pixel-chip px-3 py-1">{{ content('home.chips.hiring') }}</span>
            </div>

            <h1 class="pixel-hero__title">
                {{ content('home.hero.title') }}
                <span class="accent">{{ content('home.hero.highlight') }}</span>
                {{ content('home.hero.title_after') }}
            </h1>

            <p class="pixel-hero__lead">
                {{ content('home.hero.lead') }}
            </p>

            <div class="grid gap-4 md:grid-cols-[1fr_auto] items-end">
                <label class="pixel-field">
                    <span class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ content('home.search.label') }}</span>
                    <input
                        class="pixel-input pixel-input--soft w-full text-sm text-slate-900 outline-none"
                        placeholder="{{ content('home.search.placeholder') }}"
                        type="text"
                    />
                </label>
                <button class="pixel-button pixel-button--soft text-xs">{{ content('home.search.button') }}</button>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-3">
            @foreach (content('home.features', []) as $feature)
                <div class="pixel-card pixel-card--outline">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ $feature['tag'] ?? '' }}</p>
                    <h2 class="mt-3 text-lg font-bold">{{ $feature['title'] ?? '' }}</h2>
                    <p class="mt-2 text-sm text-slate-600">{{ $feature['text'] ?? '' }}</p>
                </div>
            @endforeach
        </div>

@php
    $partners = \Illuminate\Support\Facades\Cache::remember('home_top_partners_v1', 3600, function () {
        return \App\Models\Company::activeTopPartners()
            ->get([
                'id',
                'legal_name',
                'slug',
                'website_url',
                'top_partner_logo_path',
                'top_partner_sort',
            ]);
    });
@endphp

@if($partners->count())
    <div class="pixel-section pixel-section--tight">
        <div class="flex flex-col gap-6">
            <h3 class="text-xs uppercase tracking-[0.2em] text-slate-500">
                {{ content('home.sponsors.title') }}
            </h3>

            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-6 items-center">
                @foreach ($partners as $c)
                    @php
                        $href = $c->website_url ?: url('/company/' . $c->slug);
                        $src = $c->top_partner_logo_path
                            ? \Illuminate\Support\Facades\Storage::disk('public')->url($c->top_partner_logo_path)
                            : null;
                    @endphp

                    <a href="{{ $href }}"
                       class="pixel-outline flex items-center justify-center px-4 py-3"
                       title="{{ $c->legal_name }}"
                       rel="nofollow sponsored">
                        @if($src)
                            <img src="{{ $src }}"
                                 alt="{{ $c->legal_name }}"
                                 class="max-h-10 w-auto"
                                 loading="lazy" />
                        @else
                            <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">
                                {{ $c->legal_name }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@else
    {{-- Fallback: alter statischer Sponsor-Content --}}
    <div class="pixel-section pixel-section--tight">
        <div class="flex flex-col gap-6">
            <h3 class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ content('home.sponsors.title') }}</h3>

            <div class="grid gap-4 text-sm font-semibold uppercase tracking-[0.2em] text-slate-600 sm:grid-cols-2 md:grid-cols-4">
                @foreach (content('home.sponsors.items', []) as $sponsor)
                    <div class="pixel-outline px-4 py-3 text-center">{{ $sponsor }}</div>
                @endforeach
            </div>
        </div>
    </div>
@endif

        <div class="pixel-section">
            <div class="flex flex-col gap-4">
                <h3 class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ content('home.blog.kicker') }}</h3>
                <h2 class="text-2xl font-bold">{{ content('home.blog.title') }}</h2>
                <p class="text-sm text-slate-600">{{ content('home.blog.text') }}</p>

                <div class="flex flex-wrap gap-3 text-xs uppercase tracking-[0.2em] text-slate-500">
                    @foreach (content('home.blog.chips', []) as $chip)
                        <span class="pixel-chip px-3 py-1">{{ $chip }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</x-layouts.pixel>
