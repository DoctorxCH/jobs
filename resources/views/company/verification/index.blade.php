<x-dashboard.layout>
@php
    $company = $company ?? null;
    $latestRequest = $latestRequest ?? null;
    $ackStatus = $company?->verification_ack_status;
    $isVerified = (bool) $company?->verified_at;
    $resendCooldown = 0;
    if ($latestRequest && $latestRequest->last_sent_at) {
        $resendCooldown = max(0, 60 - $latestRequest->last_sent_at->diffInSeconds(now()));
    }
@endphp

<div class="space-y-6">
    <div>
        <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Company Verification</div>
        <h1 class="mt-2 text-2xl font-bold">Firma verifizieren</h1>
    </div>

    @if(session('success'))
        <div class="pixel-outline bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="pixel-outline bg-red-50 p-4 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="pixel-outline bg-red-50 p-4 text-sm text-red-700">
            <ul class="space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="pixel-outline p-4 text-sm">
        <div class="font-semibold">Aktueller Status</div>
        <div class="mt-2">
            @if($isVerified && $ackStatus === 'ok')
                <span class="text-emerald-600 font-semibold">Verified (checked)</span>
            @elseif($isVerified && $ackStatus === 'flagged')
                <span class="text-orange-600 font-semibold">In Prüfung</span>
            @elseif($isVerified)
                <span class="text-blue-600 font-semibold">Verified (auto), wartet auf Admin-Check</span>
            @else
                <span class="text-slate-600">Noch nicht verifiziert</span>
            @endif
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div class="pixel-outline p-6 space-y-4">
            <div>
                <div class="text-sm font-semibold">Verifizieren mit Code (Firmenmail, 10 min)</div>
                <div class="text-xs text-slate-600">Code wird an deine Firmen-Mail gesendet.</div>
            </div>

            <form method="POST" action="{{ route('frontend.company.verification.code.start') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="text-xs uppercase tracking-[0.2em] text-slate-500">E-Mail</label>
                    <input type="email" name="email" readonly value="{{ auth()->user()->email }}" class="mt-2 w-full rounded border border-slate-200 px-3 py-2 text-sm" />
                </div>
                <button type="submit" class="pixel-button px-4 py-2 text-xs uppercase tracking-[0.2em]">Send code</button>
            </form>

            <form method="POST" action="{{ route('frontend.company.verification.code.confirm') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Code bestätigen</label>
                    <input type="text" name="code" maxlength="6" class="mt-2 w-full rounded border border-slate-200 px-3 py-2 text-sm" placeholder="123456" />
                </div>
                <button type="submit" class="pixel-button px-4 py-2 text-xs uppercase tracking-[0.2em]">Confirm code</button>
            </form>

            <form method="POST" action="{{ route('frontend.company.verification.code.resend') }}">
                @csrf
                <button type="submit" id="resend-button" class="pixel-outline px-4 py-2 text-xs uppercase tracking-[0.2em]" @if($resendCooldown > 0) disabled @endif>
                    Resend code
                </button>
                <span id="resend-timer" class="ml-2 text-xs text-slate-500"></span>
            </form>
        </div>

        <div class="pixel-outline p-6 space-y-4">
            <div>
                <div class="text-sm font-semibold">Verifizieren mit Rechnung (ohne Domain-Mail)</div>
                <div class="text-xs text-slate-600">Kreditkauf per Rechnung, Bestätigung durch Admin.</div>
            </div>

            <form method="POST" action="{{ route('frontend.company.verification.invoice.start') }}">
                @csrf
                <button type="submit" class="pixel-button px-4 py-2 text-xs uppercase tracking-[0.2em]">Invoice request starten</button>
            </form>
        </div>
    </div>
</div>

@if($resendCooldown > 0)
    <script>
        (function() {
            var remaining = {{ $resendCooldown }};
            var button = document.getElementById('resend-button');
            var label = document.getElementById('resend-timer');
            function tick() {
                if (!label) return;
                if (remaining <= 0) {
                    button.removeAttribute('disabled');
                    label.textContent = '';
                    return;
                }
                label.textContent = '(' + remaining + 's)';
                remaining -= 1;
                setTimeout(tick, 1000);
            }
            tick();
        })();
    </script>
@endif
</x-dashboard.layout>
