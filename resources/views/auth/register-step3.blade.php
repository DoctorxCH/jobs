<x-layouts.pixel title="Register - Step 3">

    @php
        $locked = $reg['locked'] ?? [];
        $company = $reg['company'] ?? [];

        $ico = $locked['ico'] ?? '';
        $prefillEmail = $company['general_email'] ?? '';
    @endphp

    @php
        $locked = $reg['locked'] ?? [];
        $c = $reg['company'] ?? [];
    @endphp
    <div class="mx-auto max-w-md">
        <div class="pixel-frame p-6">
            <div class="mb-6">
                <div class="text-xs uppercase tracking-[0.2em] text-slate-500">365jobs</div>
                <h1 class="mt-2 text-2xl font-bold">Register</h1>
                <p class="mt-2 text-slate-600">Step 3: Account</p>

                <div class="mt-4 grid grid-cols-3 gap-2 text-[10px] uppercase tracking-[0.28em] text-slate-500">
                    <div class="pixel-outline px-3 py-2 text-center opacity-50">1) ICO</div>
                    <div class="pixel-outline px-3 py-2 text-center opacity-50">2) Company</div>
                    <div class="pixel-outline px-3 py-2 text-center">3) Account</div>
                </div>
            </div>

            <div class="pixel-outline p-6 mb-4">
                <div class="text-xs uppercase tracking-[0.2em] text-slate-500">Summary</div>
                <div class="mt-3 text-sm">
                    <div><b>ICO:</b> {{ $ico }}</div>
                    <div><b>Name:</b> {{ $company['legal_name'] ?? ($locked['legal_name'] ?? '') }}</div>
                    <div><b>City:</b> {{ $company['city'] ?? ($locked['city'] ?? '') }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('frontend.register.step3.post') }}">
                @csrf

                <div class="pixel-outline p-6">
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-500">Account</div>

                    <input class="mt-2 pixel-input w-full px-4 py-3 text-sm" name="name" value="{{ old('name') }}" placeholder="Name" required>
                    @error('name')<div class="mt-2 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror

                    <input
                        class="mt-2 pixel-input w-full px-4 py-3 text-sm"
                        name="email"
                        type="email"
                        value="{{ old('email', $prefillEmail) }}"
                        placeholder="Email"
                        required
                    >

                    @error('email')<div class="mt-2 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror

                    <input class="mt-2 pixel-input w-full px-4 py-3 text-sm" name="password" type="password" placeholder="Password" required>
                    @error('password')<div class="mt-2 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror

                    <input class="mt-2 pixel-input w-full px-4 py-3 text-sm" name="password_confirmation" type="password" placeholder="Confirm Password" required>

                    <div class="grid grid-cols-2 gap-3 mt-4">
                        <a href="{{ route('frontend.register.step2') }}" class="pixel-button text-center">Back</a>
                        <button type="submit" class="pixel-button">Create Account</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.pixel>
