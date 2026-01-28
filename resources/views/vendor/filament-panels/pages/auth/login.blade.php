<x-filament-panels::page.simple>
    {{-- Dein eigenes Markup hier, aber: $this->form und Actions weiter nutzen --}}
    <div class="fi-pixel-login">
        <div class="fi-pixel-card">
            <div class="fi-pixel-head">
                <div class="fi-pixel-brand">
                    <span class="fi-pixel-dot"></span>
                    <span class="fi-pixel-title">365jobs</span>
                </div>
                <div class="fi-pixel-sub">Sign in</div>
            </div>

            {{ $this->form }}

            <div class="fi-pixel-foot">
                {{-- optional: z.B. kleine Hilfe / link / imprint --}}
            </div>
        </div>
    </div>
</x-filament-panels::page.simple>
