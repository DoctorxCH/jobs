<x-dashboard.layout title="Company invitation">
    <div class="max-w-xl">
        <h1 class="text-xl font-bold mb-4">Invitation</h1>

        <div class="text-sm mb-6">
            You have been invited to join <strong>{{ $company->legal_name }}</strong> as <strong>{{ $invite->role }}</strong>.
            <div class="mt-2 text-slate-600">Invitation email: {{ $invite->email }}</div>
        </div>

        <div class="flex gap-3">
            <a class="btn" href="{{ route('login') }}">Login</a>
            <a class="btn" href="{{ route('register') }}">Create account</a>
        </div>

        <div class="text-xs text-slate-500 mt-4">
            After login/registration, open this invitation link again to accept.
        </div>
    </div>
</x-dashboard.layout>
