<x-dashboard.layout>
    <div class="flex flex-col gap-8">
        <div>
            <div class="text-xs uppercase tracking-[0.2em] text-slate-500">Profile</div>
            <h1 class="mt-2 text-2xl font-bold">Your profile</h1>
            <p class="mt-2 text-sm text-slate-600">
                Basic account data and company details will be editable here.
            </p>
        </div>

        {{-- User --}}
        <div class="pixel-outline p-6">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-500">User</div>

            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Name</div>
                    <div class="mt-2 pixel-outline px-4 py-3 text-sm">{{ auth()->user()->name }}</div>
                </div>

                <div>
                    <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Email</div>
                    <div class="mt-2 pixel-outline px-4 py-3 text-sm">{{ auth()->user()->email }}</div>
                </div>
            </div>
        </div>

        {{-- Company (placeholder for later) --}}
        <div class="pixel-outline p-6">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-500">Company</div>
            <p class="mt-2 text-sm text-slate-600">
                Company fields will be loaded here (owner / recruiter view) and later managed via Filament Content Manager.
            </p>

            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Company name</div>
                    <div class="mt-2 pixel-outline px-4 py-3 text-sm text-slate-400">—</div>
                </div>

                <div>
                    <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">VAT / ICO</div>
                    <div class="mt-2 pixel-outline px-4 py-3 text-sm text-slate-400">—</div>
                </div>

                <div class="md:col-span-2">
                    <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Address</div>
                    <div class="mt-2 pixel-outline px-4 py-3 text-sm text-slate-400">—</div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard.layout>
