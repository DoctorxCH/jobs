@php
    $items = [
        ['label' => 'Dashboard', 'route' => 'frontend.dashboard'],
        ['label' => 'Profile', 'route' => 'frontend.profile'],
    ];
@endphp

<nav class="pixel-outline px-4 py-3">
    <div class="flex flex-wrap items-center gap-2">
        @foreach ($items as $item)
            @php
                $active = request()->routeIs($item['route']);
            @endphp

            <a
                href="{{ route($item['route']) }}"
                class="px-3 py-2 text-xs uppercase tracking-[0.2em] {{ $active ? 'accent font-bold' : 'text-slate-600 hover:text-slate-900' }}"
            >
                {{ $item['label'] }}
            </a>
        @endforeach
    </div>
</nav>
