<x-dashboard.layout>
@php
    $c = $company ?? null;
    $tab = request('tab', 'overview');

    $seatsPurchased = (int) ($seatsPurchased ?? ($c->seats_purchased ?? 0));
    $seatsLocked = (int) ($seatsLocked ?? ($c->seats_locked ?? 0));
    $seatsFree = (int) ($seatsFree ?? max(0, $seatsPurchased - $seatsLocked));

    $tpEnabled = (bool) ($c->is_top_partner ?? false);
    $tpActive  = (bool) ($c->is_top_partner_active ?? false);
    $tpUntil   = $c?->is_top_partner_until;

    $jobCounts     = $jobCounts ?? ['total'=>0,'active'=>0,'draft'=>0,'inactive'=>0,'expired'=>0];
    $invoiceCounts = $invoiceCounts ?? ['open'=>0,'pending'=>0,'overdue'=>0];

    $creditsAvailable = $creditsAvailable ?? null;

    $members     = $members ?? collect();
    $invitations = $invitations ?? collect();
    $todos       = $todos ?? [];
    $activity    = $activity ?? collect(); // can be paginator
    $jobs        = $jobs ?? collect();
    $invoices    = $invoices ?? collect(); // can be paginator

    $tabs = [
        'overview' => __('main.overview'),
        'jobs'     => __('main.jobs'),
        'invoices' => __('main.invoices'),
        'team'     => __('main.members'),
        'company'  => __('main.company'),
        'activity' => __('main.activity'),
    ];

    $isActivityPaginator = $activity instanceof \Illuminate\Contracts\Pagination\Paginator;
    $isInvoicesPaginator = $invoices instanceof \Illuminate\Contracts\Pagination\Paginator;

    // if paginator: render links and iterate over items()
    $activityItems = $isActivityPaginator ? collect($activity->items()) : collect($activity);
    $invoiceItems  = $isInvoicesPaginator ? collect($invoices->items()) : collect($invoices);
@endphp

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-6">
        <div>
            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.dashboard') }}</div>
            <h1 class="mt-2 text-2xl font-bold">{{ __('main.hello') }} {{ $user->name ?? auth()->user()->name }}</h1>

            @if($c)
                <div class="mt-2 text-sm text-slate-600">
                    {{ $c->legal_name ?? '—' }}
                    <span class="mx-2 text-slate-300">•</span>
                    @if(strtolower($c->status ?? '') === 'active')
                        <span class="text-blue-600 font-semibold">✓ {{ __('main.active') }}</span>
                    @elseif(strtolower($c->status ?? '') === 'pending')
                        <span class="text-orange-500 font-semibold">⏰ {{ __('main.pending') }}</span>
                    @elseif(strtolower($c->status ?? '') === 'suspended')
                        <span class="text-red-500 font-semibold">✗ {{ __('main.suspended') }}</span>
                    @else
                        <span class="font-semibold">{{ $c->status ?? '—' }}</span>
                    @endif
                    <span class="mx-2 text-slate-300">•</span>
                    {{ __('main.verified') }}: <span class="font-semibold">{{ $c->verified_at ? __('main.yes') : __('main.no') }}</span>
                </div>
            @else
                <div class="mt-2 text-sm text-slate-600">{{ __('main.no_company_assigned') }}</div>
            @endif
        </div>

        <div class="px-3 py-2 text-xs uppercase tracking-[0.2em]">
            @if($c->logo_path)
                <img src="{{ asset('storage/'.$c->logo_path) }}" class="mt-2 max-h-32 w-auto bg-white p-2" alt="Logo">
            @else
                <div class="text-sm text-slate-600">{{ __('main.upload_logo_hint') }}</div>
            @endif        </div>
        </div>

    {{-- KPI row --}}
    <div class="grid gap-3 md:grid-cols-4">
        <div class="pixel-outline p-4">
            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.credits') }}</div>
            <div class="mt-2 text-xl font-bold">@if($creditsAvailable!==null) {{ $creditsAvailable }} @else — @endif</div>
            <div class="mt-1 text-xs text-slate-600">{{ __('main.available') }}</div>
        </div>

        <div class="pixel-outline p-4">
            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.jobs') }}</div>
            <div class="mt-2 text-xl font-bold">{{ (int) $jobCounts['active'] }}</div>
            <div class="mt-1 text-xs text-slate-600 uppercase tracking-[0.2em]">
                {{ (int) $jobCounts['draft'] }} {{ __('main.draft') }} · {{ (int) $jobCounts['expired'] }} {{ __('main.expired') }}
            </div>
        </div>

        <div class="pixel-outline p-4">
            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.invoices') }}</div>
            <div class="mt-2 text-xl font-bold">{{ (int) ($invoiceCounts['open'] ?? 0) }}</div>
            <div class="mt-1 text-xs text-slate-600 uppercase tracking-[0.2em]">
                {{ (int) ($invoiceCounts['pending'] ?? 0) }} {{ __('main.sync_pending') }} · {{ (int) ($invoiceCounts['overdue'] ?? 0) }} {{ __('main.overdue') }}
            </div>
        </div>

        <div class="pixel-outline p-4">
            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.service') }}</div>
            @if($tpEnabled && $tpActive)
                <div class="mt-2 text-xl font-bold">{{ __('main.top_partner') }}</div>
                <div class="mt-1 text-xs text-slate-600">@if($tpUntil) {{ __('main.until') }} {{ $tpUntil->format('d.m.Y') }} @else {{ __('main.no_end_date') }} @endif</div>
            @elseif($tpEnabled)
                <div class="mt-2 text-xl font-bold">{{ __('main.top_partner') }}</div>
                <div class="mt-1 text-xs text-slate-600">{{ __('main.paused') }}</div>
            @else
                <div class="mt-2 text-xl font-bold">—</div>
                <div class="mt-1 text-xs text-slate-600">{{ __('main.no_service') }}</div>
            @endif
        </div>
    </div>

    {{-- Tabs --}}
    <div class="grid gap-3 md:grid-cols-6">
        @foreach($tabs as $key => $label)
            <a href="{{ route('frontend.dashboard', ['tab' => $key]) }}"
            class="px-3 py-2 text-xs uppercase tracking-[0.2em] {{ $tab===$key ? 'pixel-button' : 'pixel-outline hover:underline' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Tab content --}}
    <div class="pixel-outline p-6">
        @if($tab === 'overview')
            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.todos') }}</div>

                    @if(empty($todos))
                        <div class="mt-3 text-sm text-slate-600">{{ __('main.all_good') }}</div>
                    @else
                        <ul class="mt-3 space-y-2 text-sm">
                            @foreach(array_slice($todos, 0, 5) as $t)
                                <li class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="font-semibold">{{ $t['label'] ?? __('main.todo') }}</div>
                                        @if(!empty($t['hint'])) <div class="text-xs text-slate-600">{{ $t['hint'] }}</div> @endif
                                    </div>
                                    @if(!empty($t['url']))
                                        <a href="{{ $t['url'] }}" class="pixel-outline px-3 py-2 text-xs uppercase tracking-[0.2em]">{{ __('main.open') }}</a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div>
                    <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.company_quick') }}</div>

                    @if($c)
                        <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <div class="text-xs text-slate-600">{{ __('main.company_id') }}</div>
                                <div class="font-semibold">{{ $c->id }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-slate-600">{{ __('main.seats_free') }}</div>
                                <div class="font-semibold">{{ $seatsFree }}</div>
                            </div>
                            <div class="col-span-2">
                                <div class="text-xs text-slate-600">{{ __('main.logo') }}</div>
                                @if($c->logo_path)
                                    <img src="{{ asset('storage/'.$c->logo_path) }}" class="mt-2 h-20 w-auto bg-white p-2" alt="Logo">
                                @else
                                    <div class="text-sm text-slate-600">—</div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('frontend.profile') }}" class="pixel-outline px-4 py-2 text-xs uppercase tracking-[0.2em]">{{ __('main.edit_company') }}</a>
                        </div>
                    @else
                        <div class="mt-3 text-sm text-slate-600">{{ __('main.no_company_assigned') }}</div>
                    @endif
                </div>
            </div>

        @elseif($tab === 'jobs')
            <div class="flex items-center justify-between gap-4">
                <div>
                    <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.jobs') }}</div>
                    <div class="mt-1 text-sm text-slate-600">{{ __('main.all_postings') }}</div>
                </div>
                <a href="{{ route('frontend.jobs.create') }}" class="pixel-outline px-4 py-2 text-xs uppercase tracking-[0.2em]">{{ __('main.new_job') }}</a>
            </div>

            <div class="mt-4 space-y-2">
                @forelse ($jobs as $job)
                    <div class="flex items-start justify-between gap-4 border border-slate-200 p-3">
                        <div>
                            <div class="font-semibold">{{ $job->title ?? __('main.untitled_job') }}</div>
                            <div class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.status') }}: {{ $job->status ?? '—' }}</div>
                            @if ($job->expires_at)
                                <div class="text-xs text-slate-500">{{ __('main.expires') }}: {{ $job->expires_at->format('Y-m-d') }}</div>
                            @endif
                        </div>
                        <a href="{{ route('frontend.jobs.edit', $job) }}" class="pixel-outline px-3 py-2 text-xs uppercase tracking-[0.2em]">{{ __('main.edit') }}</a>
                    </div>
                @empty
                    <div class="text-sm text-slate-600">{{ __('main.no_jobs_yet') }}</div>
                @endforelse
            </div>

        @elseif($tab === 'invoices')
            <div class="flex items-center justify-between gap-4">
                <div>
                    <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.invoices') }}</div>
                    <div class="mt-1 text-sm text-slate-600">{{ __('main.latest_invoices') }}</div>
                </div>
                <a href="{{ route('frontend.billing.invoices.index') }}" class="pixel-outline px-4 py-2 text-xs uppercase tracking-[0.2em]">{{ __('main.all_invoices') }}</a>
            </div>

            <div class="mt-4 space-y-2">
                @forelse($invoiceItems as $inv)
                    <div class="flex items-start justify-between gap-4 border border-slate-200 p-3">
                        <div>
                            <div class="font-semibold">{{ __('main.invoice_number', ['id' => $inv->id]) }}</div>
                            <div class="text-xs uppercase tracking-[0.2em] text-slate-500">
                                {{ __('main.status') }}: {{ $inv->status ?? '—' }}
                                @if($inv->external?->sync_status)
                                    <span class="ml-2 text-slate-400">• {{ __('main.sync') }}: {{ $inv->external->sync_status }}</span>
                                @endif
                            </div>
                            <div class="text-xs text-slate-500">
                                {{ __('main.issued') }}: {{ $inv->issued_at?->format('Y-m-d') ?? '—' }}
                                @if($inv->due_at) · {{ __('main.due') }}: {{ $inv->due_at->format('Y-m-d') }} @endif
                            </div>
                        </div>
                        <a href="{{ route('frontend.billing.invoices.show', $inv) }}" class="pixel-outline px-3 py-2 text-xs uppercase tracking-[0.2em]">{{ __('main.view') }}</a>
                    </div>
                @empty
                    <div class="text-sm text-slate-600">{{ __('main.no_invoices_yet') }}</div>
                @endforelse
            </div>

            @if($isInvoicesPaginator)
                <div class="mt-4">
                    {{ $invoices->onEachSide(1)->links() }}
                </div>
            @endif

        @elseif($tab === 'team')
            <div class="flex items-center justify-between gap-4">
                <div>
                    <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.members') }}</div>
                    <div class="mt-1 text-sm text-slate-600">{{ __('main.seats_free_summary', ['free' => $seatsFree, 'locked' => $seatsLocked, 'purchased' => $seatsPurchased]) }}</div>
                </div>
                <a href="{{ route('frontend.team') }}" class="pixel-outline px-4 py-2 text-xs uppercase tracking-[0.2em]">{{ __('main.team_page') }}</a>
            </div>

            <div class="mt-4 space-y-2">
                @forelse($members as $m)
                    <div class="flex items-start justify-between gap-4 border border-slate-200 p-3">
                        <div>
                            <div class="font-semibold">{{ $m->name ?? $m->email }}</div>
                            <div class="text-xs text-slate-500">{{ $m->email }}</div>
                            @if(isset($m->pivot))
                                <div class="text-xs uppercase tracking-[0.2em] text-slate-500 mt-1">
                                    {{ __('main.role') }}: {{ $m->pivot->role ?? '—' }} · {{ __('main.status') }}: {{ $m->pivot->status ?? '—' }}
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-sm text-slate-600">{{ __('main.no_members') }}</div>
                @endforelse
            </div>

            @if($invitations->isNotEmpty())
                <div class="mt-6">
                    <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.pending_invitations') }}</div>
                    <div class="mt-3 space-y-2">
                        @foreach($invitations as $inv)
                            <div class="border border-slate-200 p-3">
                                <div class="font-semibold">{{ $inv->email ?? __('main.invitation') }}</div>
                                <div class="text-xs text-slate-500">{{ __('main.created') }}: {{ $inv->created_at?->format('Y-m-d H:i') ?? '—' }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        @elseif($tab === 'activity')
            <div class="flex items-center justify-between gap-4">
                <div>
                    <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.activity') }}</div>
                    <div class="mt-1 text-sm text-slate-600">{{ __('main.latest_events') }}</div>
                </div>
            </div>

            @if($activityItems->isEmpty())
                <div class="mt-3 text-sm text-slate-600">{{ __('main.no_recent_activity') }}</div>
            @else
                <div class="mt-4 space-y-2">
                    @foreach($activityItems as $a)
                        <a href="{{ $a['url'] ?? '#' }}" class="block border border-slate-200 p-3">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-sm font-semibold">{{ $a['title'] ?? __('main.activity') }}</div>
                                    <div class="mt-1 text-xs text-slate-600">{{ $a['hint'] ?? '' }}</div>
                                </div>
                                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500 whitespace-nowrap">
                                    {{ \Illuminate\Support\Carbon::parse($a['at'])->format('d.m.Y H:i') }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                @if($isActivityPaginator)
                    <div class="mt-4">
                        {{ $activity->onEachSide(1)->links() }}
                    </div>
                @endif
            @endif

        @elseif($tab === 'company')
            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Company</div>

            @if($c)
                <div class="mt-2 text-lg font-bold">{{ $c->legal_name ?? 'Company' }}</div>

                <div class="mt-4 grid gap-4 md:grid-cols-3 text-sm">
                    <div>
                        <div class="text-xs text-slate-600">Company ID</div>
                        <div class="font-semibold">{{ $c->id }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-600">Status</div>
                        <div class="font-semibold">{{ $c->status ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-600">Verified</div>
                        <div class="font-semibold">{{ $c->verified_at ? 'YES' : 'NO' }}</div>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="{{ route('frontend.profile') }}" class="pixel-outline px-4 py-2 text-xs uppercase tracking-[0.2em]">Edit company</a>
                </div>
            @else
                <div class="mt-3 text-sm text-slate-600">No company assigned.</div>
            @endif
        @endif
    </div>

</div>
</x-dashboard.layout>
