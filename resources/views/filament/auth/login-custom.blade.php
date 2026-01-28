<div class="fi-simple-layout min-h-screen">
    <div class="min-h-screen flex items-center justify-center px-6">
        <div class="w-full max-w-md">

            {{-- Dein eigenes Branding (ohne Security-Leaks) --}}
            <div class="mb-6 text-center">
                <div class="mx-auto mb-4 h-10 w-10 border-2 border-[color:var(--ink)] grid place-items-center font-bold">
                    3
                </div>
                <div class="text-xs tracking-[0.25em] uppercase text-[color:var(--muted)]">
                    Admin Console
                </div>
                <div class="mt-2 text-2xl font-bold tracking-tight text-[color:var(--ink)]">
                    Sign in
                </div>
            </div>

            {{-- Komplett eigener Card-Container --}}
            <div class="login-card">
                {{ $this->form }}
            </div>

            {{-- Optional unten: neutraler Hinweis --}}
            <div class="mt-6 text-center text-xs text-[color:var(--muted)]">
                Authorized access only.
            </div>
        </div>
    </div>
</div>
