<x-layouts.pixel title="Login">
    <div class="mx-auto max-w-md">
        <div class="pixel-frame p-6">
            <div class="mb-6">
                <div class="text-xs uppercase tracking-[0.2em] text-slate-500">365jobs</div>
                <h1 class="mt-2 text-2xl font-bold">Login</h1>

                <p class="mt-2 text-slate-600">Login form for company users</p>

                <p class="mt-2 text-slate-600">Currently, we don't support accounts for candidates. You can use every function on our Jobportal for free as a candidate.</p>
            </div>

            @if ($errors->any())
                <div class="pixel-outline mb-5 p-4">
                    <div class="text-sm font-bold">Error</div>
                    <ul class="mt-2 list-disc pl-5 text-sm text-slate-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('frontend.login.submit') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="mb-2 block text-xs uppercase tracking-[0.2em] text-slate-600">Email</label>
                    <input class="pixel-input w-full px-4 py-3 text-sm" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                </div>

                <div>
                    <label class="mb-2 block text-xs uppercase tracking-[0.2em] text-slate-600">Password</label>
                    <input class="pixel-input w-full px-4 py-3 text-sm" type="password" name="password" required autocomplete="current-password">
                </div>

                <label class="flex items-center gap-3 text-sm text-slate-700">
                    <input class="h-4 w-4" type="checkbox" name="remember" value="1">
                    Remember me
                </label>

                <button class="pixel-button w-full px-4 py-3 text-sm" type="submit">
                    Log in
                </button>
            </form>

            <div class="mt-6 flex items-center justify-between text-xs uppercase tracking-[0.2em] text-slate-600">
                <a class="hover:text-blue-700" href="{{ route('frontend.register') }}">Register</a>
                <a class="hover:text-blue-700" href="{{ url('/') }}">Home</a>
            </div>
        </div>
    </div>
</x-layouts.pixel>
