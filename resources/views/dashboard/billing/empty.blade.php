<x-dashboard.layout title="Billing">
    <div class="flex flex-col gap-6">
        <div>
            <div class="text-xs uppercase tracking-[0.2em] text-slate-500">Billing</div>
            <h1 class="mt-2 text-2xl font-bold">Billing access</h1>
            <p class="mt-2 text-sm text-slate-600">
                {{ $message ?? 'Billing is available only for users linked to a company.' }}
            </p>
        </div>

        <div class="pixel-outline p-6 text-sm text-slate-600">
            Need help? Update your company profile or contact support to get access.
        </div>
    </div>
</x-dashboard.layout>
