<x-layouts.pixel>
    <section class="mx-auto w-full max-w-6xl">
        <div class="grid gap-8 md:grid-cols-[220px_1fr]">
            <aside class="pixel-frame p-4">
                <x-dashboard.menu />
            </aside>

            <main class="pixel-frame p-8">
                {{ $slot }}
            </main>
        </div>
    </section>
</x-layouts.pixel>
