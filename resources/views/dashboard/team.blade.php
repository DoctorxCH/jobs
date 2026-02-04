<x-dashboard.layout title="{{ __('main.team_invitations_title') }}">
    @php
        $c = $company ?? null;
        $invitations = $invitations ?? collect();
    @endphp

    <div class="flex flex-col gap-8">
        <div>
            <div class="text-s uppercase tracking-[0.2em] text-slate-800 font-bold">{{ __('main.team') }}</div>
            <h1 class="mt-2 text-2xl font-bold">{{ __('main.team_invitations_title') }}</h1>
            <p class="mt-2 text-sm text-slate-600">
                {{ __('main.team_invitations_intro') }}
            </p>
        </div>

        @if($c)
            <div class="pixel-outline p-6">
                <div class="text-s uppercase tracking-[0.2em] text-slate-800 font-bold">{{ __('main.send_invitation') }}</div>

                <form method="POST" action="{{ route('frontend.team.invite') }}" class="mt-4">
                    @csrf

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.email') }}</label>
                            <input
                                name="email"
                                type="email"
                                class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none"
                                value="{{ old('email') }}"
                                required
                            />
                            @error('email')
                                <div class="mt-2 text-xs text-red-700">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.role') }}</label>
                            <select
                                name="role"
                                class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none"
                                required
                            >
                                <option value="">{{ __('main.select_role') }}</option>
                                <option value="member" {{ old('role') === 'member' ? 'selected' : '' }}>{{ __('main.role_member') }}</option>
                                <option value="recruiter" {{ old('role') === 'recruiter' ? 'selected' : '' }}>{{ __('main.role_recruiter') }}</option>
                                <option value="viewer" {{ old('role') === 'viewer' ? 'selected' : '' }}>{{ __('main.role_viewer') }}</option>
                            </select>
                            @error('role')
                                <div class="mt-2 text-xs text-red-700">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="mt-4 px-4 py-2 bg-blue-600 text-white text-sm font-bold uppercase tracking-[0.2em] hover:bg-blue-700">
                        {{ __('main.send_invitation_button') }}
                    </button>
                </form>
            </div>

            <div class="pixel-outline p-6">
                <div class="text-s uppercase tracking-[0.2em] text-slate-800 font-bold">{{ __('main.pending_invitations') }}</div>

                @if($invitations->isEmpty())
                    <p class="mt-4 text-sm text-slate-600">{{ __('main.no_pending_invitations') }}</p>
                @else
                    <div class="mt-4 space-y-4">
                        @foreach($invitations as $invitation)
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded">
                                <div>
                                    <div class="text-sm font-bold">{{ $invitation->email }}</div>
                                    <div class="text-xs text-slate-500">
                                        {{ __('main.role') }}: {{ $invitation->role }}
                                        | {{ __('main.expires') }}: {{ $invitation->expires_at ? $invitation->expires_at->format('d.m.Y') : __('main.never') }}
                                    </div>
                                </div>
                                <div class="text-xs text-slate-500">
                                    @if($invitation->accepted_at)
                                        {{ __('main.accepted') }}
                                    @else
                                        {{ __('main.pending') }}
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @else
            <div class="pixel-outline p-6">
                <p class="text-sm text-slate-600">{{ __('main.no_company_found') }}</p>
            </div>
        @endif
    </div>
</x-dashboard.layout>
