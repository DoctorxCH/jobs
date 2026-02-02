<x-dashboard.layout title="{{ __('main.invite_accept_title') }}">
    <div class="max-w-md mx-auto mt-12 pixel-outline p-6">

        <h1 class="text-xl font-bold">{{ __('main.invite_heading') }}</h1>

        <p class="mt-2 text-sm text-slate-600">
            {!! __('main.invite_body', ['company' => e($invite->company->legal_name), 'role' => e(ucfirst($invite->role))]) !!}
        </p>

        <form method="POST" action="{{ route('company.invite.complete', $invite->token) }}" class="mt-6">
            @csrf

            <div>
                <label class="text-xs uppercase tracking-wider text-slate-500">
                    {{ __('main.email') }}
                </label>
                <input
                    type="email"
                    value="{{ $invite->email }}"
                    disabled
                    class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none"
                />
            </div>

            <div class="mt-4">
                <label class="text-xs uppercase tracking-wider text-slate-500">
                    {{ __('main.password') }}
                </label>
                <input
                    type="password"
                    name="password"
                    required
                    class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none"
                />
            </div>

            <div class="mt-4">
                <label class="text-xs uppercase tracking-wider text-slate-500">
                    {{ __('main.confirm_password') }}
                </label>
                <input
                    type="password"
                    name="password_confirmation"
                    required
                    class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none"
                />
            </div>

            <button class="mt-6 w-full bg-blue-600 text-white py-2 font-bold uppercase tracking-wide">
                {{ __('main.invite_accept_button') }}
            </button>
        </form>
    </div>
</x-dashboard.layout>
