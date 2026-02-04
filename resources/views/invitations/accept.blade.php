<x-dashboard.layout title="{{ __('main.company_invitation_title') }}">
    <div class="max-w-xl">
        <h1 class="text-xl font-bold mb-4">{{ __('main.invitation_title') }}</h1>

        <div class="text-sm mb-6">
            {!! __('main.invitation_body', ['company' => e($company->legal_name), 'role' => e($invite->role)]) !!}
            <div class="mt-2 text-slate-600">{{ __('main.invitation_email', ['email' => $invite->email]) }}</div>
        </div>

        <div class="flex gap-3">
            <a class="btn" href="{{ route('login') }}">{{ __('main.login') }}</a>
            <a class="btn" href="{{ route('register') }}">{{ __('main.create_account') }}</a>
        </div>

        <div class="text-xs text-slate-500 mt-4">
            {{ __('main.invitation_login_hint') }}
        </div>
    </div>
</x-dashboard.layout>
