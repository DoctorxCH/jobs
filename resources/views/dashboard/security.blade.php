<x-dashboard.layout>
    <div class="flex flex-col gap-8">
        <div>
            <div class="text-s uppercase tracking-[0.2em] text-slate-800 font-bold">Security</div>
            <h1 class="mt-2 text-2xl font-bold">Change password</h1>
            <p class="mt-2 text-sm text-slate-600">
                Keep your account secure by using a strong password.
            </p>

            {{-- Live requirements --}}
                    <div class="mt-4 pixel-outline p-4">
                        <div class="text-s uppercase tracking-[0.2em] text-slate-800 font-bold">Password requirements</div>

                        <ul id="pw-req-list" class="mt-3 flex flex-col gap-2 text-sm">
                            @foreach ($requirements as $req)
                                <li
                                    class="pw-req flex items-start gap-2 text-slate-600"
                                    data-req-key="{{ $req['key'] }}"
                                    data-req-js="{{ $req['js'] }}"
                                >
                                    <span class="pw-req-dot mt-[3px] inline-block h-3 w-3 pixel-outline bg-slate-100"></span>
                                    <span>{{ $req['label'] }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <div class="mt-3 text-xs text-slate-500">
                            Tip: Use a unique password you do not use elsewhere.
                        </div>
                    </div>
        </div>

        {{-- Flash status --}}
        @if (session('status'))
            <div class="pixel-outline px-4 py-3 text-sm bg-emerald-50 text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        {{-- Global error --}}
        @if ($errors->has('error'))
            <div class="pixel-outline px-4 py-3 text-sm bg-rose-50 text-rose-900">
                {{ $errors->first('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('frontend.security.update') }}" class="pixel-outline p-6">
            @csrf

            <div class="grid gap-6 md:grid-cols-2">
                {{-- Current password --}}
                <div class="md:col-span-1">
                    <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">
                        Current password
                        <input
                            class="pixel-input mt-2 w-full px-4 py-3 text-sm text-slate-900 outline-none"
                            type="password"
                            name="current_password"
                            autocomplete="current-password"
                        />
                    </label>
                    @error('current_password')
                        <div class="mt-2 text-xs bg-rose-50 text-rose-900 pixel-outline px-3 py-2">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="md:col-span-1"></div>

                {{-- New password --}}
                <div class="md:col-span-1">
                    <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">
                        New password
                        <input
                            id="new-password"
                            class="pixel-input mt-2 w-full px-4 py-3 text-sm text-slate-900 outline-none"
                            type="password"
                            name="password"
                            autocomplete="new-password"
                        />
                    </label>

                    @error('password')
                        <div class="mt-2 text-xs bg-rose-50 text-rose-900 pixel-outline px-3 py-2">
                            {{ $message }}
                        </div>
                    @enderror

                    
                </div>

                {{-- Confirm new password --}}
                <div class="md:col-span-1">
                    <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">
                        Confirm new password
                        <input
                            class="pixel-input mt-2 w-full px-4 py-3 text-sm text-slate-900 outline-none"
                            type="password"
                            name="password_confirmation"
                            autocomplete="new-password"
                        />
                    </label>

                    @error('password_confirmation')
                        <div class="mt-2 text-xs bg-rose-50 text-rose-900 pixel-outline px-3 py-2">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3">
                <button class="pixel-button px-6 py-3 text-xs" type="submit">
                    Save password
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const input = document.getElementById('new-password');
                const items = Array.from(document.querySelectorAll('#pw-req-list .pw-req'));

                if (!input || items.length === 0) return;

                const setState = (li, ok) => {
                    li.classList.toggle('text-green-600', ok);
                    li.classList.toggle('text-slate-600', !ok);

                    const dot = li.querySelector('.pw-req-dot');
                    if (dot) {
                        dot.classList.toggle('bg-green-200', ok);
                        dot.classList.toggle('bg-slate-100', !ok);
                    }
                };

                const check = () => {
                    const value = input.value || '';
                    items.forEach(li => {
                        const expr = li.getAttribute('data-req-js') || 'false';
                        let ok = false;

                        try {
                            // expr uses "value" (string) as variable
                            ok = Function('value', `return (${expr});`)(value);
                        } catch (e) {
                            ok = false;
                        }

                        setState(li, ok);
                    });
                };

                input.addEventListener('input', check);
                check();
            });
        </script>
    @endpush
</x-dashboard.layout>
