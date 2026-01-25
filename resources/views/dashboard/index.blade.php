<x-dashboard.layout>
    @php
        $c = $company ?? null;

        $seatsPurchased = (int) ($c->seats_purchased ?? 0);
        $seatsLocked = (int) ($c->seats_locked ?? 0);
        $seatsFree = max(0, $seatsPurchased - $seatsLocked);

        $tpEnabled = (bool) ($c->is_top_partner ?? false);
        $tpActive = (bool) ($c->is_top_partner_active ?? false);
        $tpUntil = $c?->is_top_partner_until;
    @endphp

    <div class="flex flex-col gap-8">
        <div>
            <div class="text-xs uppercase tracking-[0.2em] text-slate-500">Dashboard</div>
            <h1 class="mt-2 text-2xl font-bold">Hello {{ auth()->user()->name }}</h1>
            <p class="mt-2 text-sm text-slate-600">
                Quick overview of your company and account.
            </p>
        </div>

        {{-- Quick cards --}}
        <div class="grid gap-4 md:grid-cols-3">
            {{-- Profile --}}
            <div class="pixel-outline p-6">
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Profile</div>
                <div class="mt-2 text-sm text-slate-700">
                    Keep your company details up to date.
                </div>
                <a href="{{ url('/dashboard/profile') }}"
                   class="mt-4 inline-flex pixel-outline px-4 py-2 text-xs uppercase tracking-[0.2em]">
                    Edit profile
                </a>
            </div>

            {{-- Seats --}}
            <div class="pixel-outline p-6">
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Seats</div>
                <div class="mt-2 text-2xl font-bold">{{ $seatsFree }}</div>
                <div class="mt-1 text-xs text-slate-600">
                    free ({{ $seatsLocked }} locked / {{ $seatsPurchased }} purchased)
                </div>
            </div>

            {{-- Top partner --}}
            <div class="pixel-outline p-6">
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Top partner</div>

                @if($tpEnabled && $tpActive)
                    <div class="mt-2 text-2xl font-bold text-green-700">ACTIVE</div>
                    <div class="mt-1 text-xs text-slate-600">
                        @if($tpUntil)
                            until {{ $tpUntil->format('d.m.Y') }}
                        @else
                            no end date
                        @endif
                    </div>
                @elseif($tpEnabled)
                    <div class="mt-2 text-2xl font-bold text-amber-700">PAUSED</div>
                    <div class="mt-1 text-xs text-slate-600">enabled but not active</div>
                @else
                    <div class="mt-2 text-2xl font-bold text-slate-500">NO</div>
                    <div class="mt-1 text-xs text-slate-600">not enabled</div>
                @endif
            </div>
        </div>

{{-- Technical details --}}
@if($c)
    <div class="pixel-outline p-6">
        <div class="text-s uppercase tracking-[0.2em] text-slate-800 font-bold">
            Technical details
        </div>

        <div class="mt-4 grid gap-4 md:grid-cols-3 text-sm">
            {{-- IDs --}}
            <div>
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Company ID</div>
                <div class="mt-1 font-bold">{{ $c->id }}</div>
            </div>

            <div>
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Slug</div>
                <div class="mt-1 font-bold">{{ $c->slug }}</div>
            </div>

            <div>
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Owner user ID</div>
                <div class="mt-1 font-bold">{{ $c->owner_user_id }}</div>
            </div>

            {{-- Status --}}
            <div>
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Status</div>
                <div class="mt-1 font-bold">{{ $c->status }}</div>
                <div class="mt-1 text-xs text-slate-600">
                    Active: <span class="font-bold">{{ $c->active ? 'YES' : 'NO' }}</span>
                </div>
            </div>

            {{-- Verification --}}
            <div>
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Verified</div>
                <div class="mt-1 font-bold">{{ $c->verified_at ? 'YES' : 'NO' }}</div>
                <div class="mt-1 text-xs text-slate-600">
                    {{ $c->verified_at ? $c->verified_at->format('d.m.Y H:i') : '—' }}
                </div>
            </div>

            {{-- Seats --}}
            <div>
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Seats</div>
                <div class="mt-1 text-xs text-slate-600">
                    Purchased: <span class="font-bold">{{ $c->seats_purchased }}</span><br>
                    Locked: <span class="font-bold">{{ $c->seats_locked }}</span><br>
                    Free:
                    <span class="font-bold">
                        {{ max(0, (int)$c->seats_purchased - (int)$c->seats_locked) }}
                    </span>
                </div>
            </div>

            {{-- Timestamps --}}
            <div>
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Created</div>
                <div class="mt-1 font-bold">{{ $c->created_at?->format('d.m.Y H:i') ?? '—' }}</div>
            </div>

            <div>
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Updated</div>
                <div class="mt-1 font-bold">{{ $c->updated_at?->format('d.m.Y H:i') ?? '—' }}</div>
            </div>

            {{-- Top partner --}}
            <div class="md:col-span-3">
                
                    
                        <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">
                            Top partner logo
                        </div>

                        @if($c->top_partner_logo_path)
                            <div class="mt-2 flex items-center gap-4">
                                <img
                                    src="{{ asset('storage/'.$c->top_partner_logo_path) }}"
                                    alt="Top partner logo"
                                    class="h-16 w-auto pixel-outline bg-white p-2"
                                />

                                <div class="text-xs text-slate-600">
                                    {{ $c->top_partner_logo_path }}
                                </div>
                            </div>
                        @else
                            <div class="mt-1 font-bold">—</div>
                        @endif
                   
               
            </div>
        </div>
    </div>
@endif
    </div>
</x-dashboard.layout>
