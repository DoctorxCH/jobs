@props([
    'title' => 'Dashboard',
])

<x-layouts.pixel :title="$title">
    <section class="mx-auto w-full max-w-6xl">
        <div class="grid gap-8 md:grid-cols-[220px,1fr]">
            {{-- Left menu --}}
            <aside class="pixel-frame p-5">
                <div class="text-xs uppercase tracking-[0.2em] text-slate-500">
                    Menu
                </div>

                <nav class="mt-4 flex flex-col gap-2 text-xs uppercase tracking-[0.2em]">
                    <a
                        href="{{ route('frontend.profile') }}"
                        class="pixel-outline px-4 py-3 hover:text-slate-900 {{ request()->routeIs('frontend.profile') ? 'accent' : '' }}"
                    >
                        Profile
                    </a>
                </nav>
            </aside>

            {{-- Right content --}}
            <div class="pixel-frame p-8">
                <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <div class="text-xs uppercase tracking-[0.2em] text-slate-500">
                            Dashboard
                        </div>
                        <h1 class="mt-2 text-2xl font-bold">{{ $title }}</h1>
                    </div>

                    <div class="text-xs uppercase tracking-[0.2em] text-slate-500">
                        Signed in as <span class="text-slate-900">{{ auth()->user()->name ?? auth()->user()->email }}</span>
                    </div>
                </div>

                {{ $slot }}
            </div>
        </div>
    </section>
</x-layouts.pixel>
