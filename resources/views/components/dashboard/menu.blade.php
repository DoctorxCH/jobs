@php
    $items = [
        [
            'label' => 'Profile',
            'route' => 'frontend.profile',
        ],
    ];

    $current = request()->route()?->getName();
@endphp

<nav class="flex flex-col gap-2">
    <div class="mb-2 text-[10px] uppercase tracking-[0.28em] text-slate-500">
        Menu
    </div>

    @foreach($items as $item)
        @php
            $active = $current === $item['route'];
        @endphp

        <a
            href="{{ route($item['route']) }}"
            class="pixel-outline px-3 py-2 text-xs uppercase tracking-[0.2em] {{ $active ? 'text-slate-900' : 'text-slate-600' }}"
        >
            {{ $active ? '▸ ' : '' }}{{ $item['label'] }}
        </a>
    @endforeach

    <div class="mt-4 border-t border-slate-200 pt-4">
        <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">
            Soon
        </div>
        <div class="mt-2 flex flex-col gap-2 text-xs uppercase tracking-[0.2em] text-slate-400">
            <span class="pixel-outline px-3 py-2 opacity-60">Jobs</span>
            <span class="pixel-outline px-3 py-2 opacity-60">Applications</span>
            <span class="pixel-outline px-3 py-2 opacity-60">Billing</span>
        </div>
    </div>
</nav>
