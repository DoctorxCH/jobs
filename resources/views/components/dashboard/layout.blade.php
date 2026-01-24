@props([
    'title' => 'Dashboard',
])

<x-layouts.pixel :title="$title">
    <section class="mx-auto w-full max-w-6xl">
        <div class="grid gap-8 md:grid-cols-[220px_1fr]">
            <aside class="pixel-frame p-4">
                <x-dashboard.menu />
            </aside>

            <main class="pixel-frame p-8">
                {{-- Alerts --}}
                @if (session('status'))
                    <div class="mb-6 pixel-outline px-4 py-3 text-sm border-2 border-green-600 bg-green-50 text-green-800">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 pixel-outline px-4 py-3 text-sm border-2 border-red-600 bg-red-50 text-red-800">
                        <div class="font-bold uppercase tracking-[0.2em] text-xs mb-2">Please fix the errors</div>
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </section>
</x-layouts.pixel>
