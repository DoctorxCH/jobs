@php
    $items = [
        ['label' => 'Dashboard', 'route' => 'frontend.dashboard'],
        ['label' => 'Profile',   'route' => 'frontend.profile'],
        ['label' => 'Security',  'route' => 'frontend.security'],
        ['label' => 'Team Invitations', 'route' => 'frontend.team'],
    ];

    $user = auth()->user();
    $companyId = $user && method_exists($user, 'effectiveCompanyId')
        ? $user->effectiveCompanyId()
        : ($user->company_id ?? null);

    $billingItems = [
        ['label' => 'Products', 'route' => 'frontend.billing.products.index'],
        ['label' => 'Orders', 'route' => 'frontend.billing.orders.index'],
        ['label' => 'Invoices', 'route' => 'frontend.billing.invoices.index'],
        ['label' => 'Payments', 'route' => 'frontend.billing.payments.index'],
    ];
@endphp

<nav class="pixel-outline px-4 py-3">
    <div class="flex flex-col gap-4">
        <div class="flex flex-wrap items-center gap-2">
            @foreach ($items as $item)
                @php
                    $active = request()->routeIs($item['route']);
                @endphp

                <a
                    href="{{ route($item['route']) }}"
                    class="px-3 py-2 text-xs uppercase tracking-[0.2em]
                           {{ $active ? 'accent font-bold' : 'text-slate-600 hover:text-slate-900' }}"
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>

        @if ($companyId)
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Billing</span>
                @foreach ($billingItems as $item)
                    @php
                        $active = request()->routeIs($item['route']);
                    @endphp

                    <a
                        href="{{ route($item['route']) }}"
                        class="px-3 py-2 text-xs uppercase tracking-[0.2em]
                               {{ $active ? 'accent font-bold' : 'text-slate-600 hover:text-slate-900' }}"
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</nav>
