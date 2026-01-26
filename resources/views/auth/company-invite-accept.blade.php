<x-dashboard.layout title="Accept Invitation">
    <div class="max-w-md mx-auto mt-12 pixel-outline p-6">

        <h1 class="text-xl font-bold">You're invited</h1>

        <p class="mt-2 text-sm text-slate-600">
            You were invited to join
            <strong>{{ $invite->company->legal_name }}</strong>
            as <strong>{{ ucfirst($invite->role) }}</strong>.
        </p>

        <form method="POST" action="{{ route('company.invite.complete', $invite->token) }}" class="mt-6">
            @csrf

            <div>
                <label class="text-xs uppercase tracking-wider text-slate-500">
                    Email
                </label>
                <input
                    type="email"
                    value="{{ $invite->email }}"
                    disabled
                    class="mt-1 pixel-input w-full bg-slate-100"
                />
            </div>

            <div class="mt-4">
                <label class="text-xs uppercase tracking-wider text-slate-500">
                    Password
                </label>
                <input
                    type="password"
                    name="password"
                    required
                    class="mt-1 pixel-input w-full"
                />
            </div>

            <div class="mt-4">
                <label class="text-xs uppercase tracking-wider text-slate-500">
                    Confirm Password
                </label>
                <input
                    type="password"
                    name="password_confirmation"
                    required
                    class="mt-1 pixel-input w-full"
                />
            </div>

            <button class="mt-6 w-full bg-blue-600 text-white py-2 font-bold uppercase tracking-wide">
                Accept Invitation
            </button>
        </form>
    </div>
</x-dashboard.layout>
