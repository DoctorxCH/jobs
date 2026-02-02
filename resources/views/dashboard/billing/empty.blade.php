<x-dashboard.layout title="{{ __('main.billing') }}">
    <div class="flex flex-col gap-6">
        <div>
            <div class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.billing') }}</div>
            <h1 class="mt-2 text-2xl font-bold">{{ __('main.billing_access') }}</h1>
            <p class="mt-2 text-sm text-slate-600">
                {{ $message ?? __('main.billing_company_only') }}
            </p>
        </div>

        <div class="pixel-outline p-6 text-sm text-slate-600">
            {{ __('main.billing_help_hint') }}
        </div>
    </div>
</x-dashboard.layout>
