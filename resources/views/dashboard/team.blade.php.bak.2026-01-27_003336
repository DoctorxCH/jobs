<x-dashboard.layout title="Team Invitations">
    @php
        $c = $company ?? null;
        $invitations = $invitations ?? collect();
    @endphp

    <div class="flex flex-col gap-8">
        {{-- Header --}}
        <div>
            <div class="text-s uppercase tracking-[0.2em] text-slate-800 font-bold">Team</div>
            <h1 class="mt-2 text-2xl font-bold">Team Invitations</h1>
            <p class="mt-2 text-sm text-slate-600">
                Invite new members to your company team.
            </p>
        </div>

        @if($c)
            {{-- Send Invitation Form --}}
            <div class="pixel-outline p-6">
                <div class="text-s uppercase tracking-[0.2em] text-slate-800 font-bold">Send Invitation</div>

                <form method="POST" action="{{ route('frontend.team.invite') }}" class="mt-4">
                    @csrf

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Email</label>
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
                            <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Role</label>
                            <select
                                name="role"
                                class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none"
                                required
                            >
                                <option value="">Select Role</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="member" {{ old('role') == 'member' ? 'selected' : '' }}>Member</option>
                            </select>
                            @error('role')
                                <div class="mt-2 text-xs text-red-700">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="mt-4 px-4 py-2 bg-blue-600 text-white text-sm font-bold uppercase tracking-[0.2em] hover:bg-blue-700">
                        Send Invitation
                    </button>
                </form>
            </div>

            {{-- Pending Invitations --}}
            <div class="pixel-outline p-6">
                <div class="text-s uppercase tracking-[0.2em] text-slate-800 font-bold">Pending Invitations</div>

                @if($invitations->isEmpty())
                    <p class="mt-4 text-sm text-slate-600">No pending invitations.</p>
                @else
                    <div class="mt-4 space-y-4">
                        @foreach($invitations as $invitation)
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded">
                                <div>
                                    <div class="text-sm font-bold">{{ $invitation->email }}</div>
                                    <div class="text-xs text-slate-500">Role: {{ $invitation->role }} | Expires: {{ $invitation->expires_at ? $invitation->expires_at->format('d.m.Y') : 'Never' }}</div>
                                </div>
                                <div class="text-xs text-slate-500">
                                    @if($invitation->accepted_at)
                                        Accepted
                                    @else
                                        Pending
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @else
            <div class="pixel-outline p-6">
                <p class="text-sm text-slate-600">No company found. Please create a company first.</p>
            </div>
        @endif
    </div>
</x-dashboard.layout>