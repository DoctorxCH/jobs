@php
    $items = [
        ['label' => 'Dashboard', 'route' => 'frontend.dashboard'],
        ['label' => 'Profile',   'route' => 'frontend.profile'],
        ['label' => 'Security',  'route' => 'frontend.security'],
        ['label' => 'Jobs',      'route' => 'frontend.jobs.index'],
        ['label' => 'Team',      'route' => 'frontend.team'],
    ];

    $user = auth()->user();
    $companyId = $user && method_exists($user, 'effectiveCompanyId')
        ? $user->effectiveCompanyId()
        : ($user->company_id ?? null);

    $billingItems = [
        ['label' => 'Products', 'route' => 'frontend.billing.products.index'],
        ['label' => 'Orders',   'route' => 'frontend.billing.orders.index'],
        ['label' => 'Invoices', 'route' => 'frontend.billing.invoices.index'],
        ['label' => 'Payments', 'route' => 'frontend.billing.payments.index'],
    ];
@endphp

<nav class="pixel-outline px-3 py-2 bg-white">
    <div class="flex flex-col gap-2">

        {{-- main --}}
        @foreach ($items as $item)
            @php($active = request()->routeIs($item['route']))
            <a href="{{ route($item['route']) }}"
               class="px-3 py-2 text-xs uppercase tracking-[0.2em]
                      {{ $active ? 'accent font-bold' : 'text-slate-600 hover:text-slate-900' }}">
                {{ $item['label'] }}
            </a>
        @endforeach

        {{-- divider --}}
        @if ($companyId)
            <span class="my-2 h-px w-full bg-slate-200"></span>


            {{-- billing --}}
            @foreach ($billingItems as $item)
                @php($active = request()->routeIs($item['route']))
                <a href="{{ route($item['route']) }}"
                   class="px-3 py-2 text-xs uppercase tracking-[0.2em]
                          {{ $active ? 'accent font-bold' : 'text-slate-600 hover:text-slate-900' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        @endif

    </div>
</nav>
