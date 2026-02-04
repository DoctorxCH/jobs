@php
    $items = [
        ['label' => __('main.dashboard'), 'route' => 'frontend.dashboard'],
        ['label' => __('main.profile'),   'route' => 'frontend.profile'],
        ['label' => __('main.security'),  'route' => 'frontend.security'],
        ['label' => __('main.jobs'),  'route' => 'frontend.jobs.index'],
        ['label' => __('main.team_invitations'), 'route' => 'frontend.team'],
        ['label' => __('main.verification'), 'route' => 'frontend.company.verification.index'],
        ['label' => __('main.contact'), 'route' => 'frontend.contact'],
    ];

    $user = auth()->user();
    $companyId = $user && method_exists($user, 'effectiveCompanyId')
        ? $user->effectiveCompanyId()
        : ($user->company_id ?? null);

    $billingItems = [
        ['label' => __('main.products'), 'route' => 'frontend.billing.products.index'],
        ['label' => __('main.orders'), 'route' => 'frontend.billing.orders.index'],
        ['label' => __('main.invoices'), 'route' => 'frontend.billing.invoices.index'],
        ['label' => __('main.payments'), 'route' => 'frontend.billing.payments.index'],
    ];
@endphp

<nav class="pixel-outline px-4 py-3">
    <div class="flex flex-col gap-4">
        <button
            type="button"
            class="md:hidden pixel-outline px-3 py-2 text-xs uppercase tracking-[0.2em] text-slate-700"
            data-dashboard-menu-toggle
            aria-expanded="false"
        >
            {{ __('main.show_menu') }}
        </button>

        <div class="flex flex-col gap-4 hidden md:flex" data-dashboard-menu>
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
                    <span class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.billing') }}</span>
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
    </div>
</nav>

<script>
    (function () {
        const toggle = document.querySelector('[data-dashboard-menu-toggle]');
        const menu = document.querySelector('[data-dashboard-menu]');
        if (!toggle || !menu) return;

        const showLabel = {{ Js::from(__('main.show_menu')) }};
        const hideLabel = {{ Js::from(__('main.hide_menu')) }};

        toggle.addEventListener('click', () => {
            const isHidden = menu.classList.contains('hidden');
            menu.classList.toggle('hidden', !isHidden);
            toggle.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
            toggle.textContent = isHidden ? hideLabel : showLabel;
        });
    })();
</script>
