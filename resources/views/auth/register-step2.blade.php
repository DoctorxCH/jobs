<x-layouts.pixel title="Register - Step 2">
    @php
        $v = $values ?? [];
        $l = $locked ?? [];
    @endphp

    <div class="mx-auto max-w-2xl">
        <div class="pixel-frame p-6">
            <div class="mb-6">
                <div class="text-xs uppercase tracking-[0.2em] text-slate-500">365jobs</div>
                <h1 class="mt-2 text-2xl font-bold">Register</h1>
                <p class="mt-2 text-slate-600">Step 2: Company details</p>

                <div class="mt-4 grid grid-cols-3 gap-2 text-[10px] uppercase tracking-[0.28em] text-slate-500">
                    <div class="pixel-outline px-3 py-2 text-center opacity-50">1) ICO</div>
                    <div class="pixel-outline px-3 py-2 text-center">2) Company</div>
                    <div class="pixel-outline px-3 py-2 text-center opacity-50">3) Account</div>
                </div>
            </div>

            <form method="POST" action="{{ route('frontend.register.step2.post') }}" class="space-y-4">
                @csrf

                {{-- LOCKED (read-only, from API/session) --}}
                <div class="pixel-outline p-6">
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-500">Locked (from registry)</div>

                    <div class="grid gap-4 md:grid-cols-2 mt-4">
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">ICO</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm opacity-80" value="{{ $l['ico'] ?? '' }}" readonly>
                        </div>
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Country</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm opacity-80" value="{{ $l['country_code'] ?? 'SK' }}" readonly>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Legal name</div>
                        <input class="mt-2 pixel-input w-full px-4 py-3 text-sm opacity-80" value="{{ $l['legal_name'] ?? '' }}" readonly>
                    </div>

                    <div class="grid gap-4 md:grid-cols-3 mt-4">
                        <div class="md:col-span-2">
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Street</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm opacity-80" value="{{ $l['street'] ?? '' }}" readonly>
                        </div>
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Postal</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm opacity-80" value="{{ $l['postal_code'] ?? '' }}" readonly>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">City</div>
                        <input class="mt-2 pixel-input w-full px-4 py-3 text-sm opacity-80" value="{{ $l['city'] ?? '' }}" readonly>
                    </div>
                </div>

                {{-- OPTIONAL IDs --}}
                <div class="pixel-outline p-6">
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-500">IDs</div>

                    <div class="grid gap-4 md:grid-cols-2 mt-4">
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">DIC</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm" name="dic" value="{{ old('dic', $v['dic'] ?? '') }}">
                            @error('dic')<div class="mt-2 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">IC DPH</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm" name="ic_dph" value="{{ old('ic_dph', $v['ic_dph'] ?? '') }}">
                            @error('ic_dph')<div class="mt-2 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- REQUIRED: Web/Contacts --}}
                <div class="pixel-outline p-6">
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-500">Company contact (required)</div>

                    <div class="grid gap-4 md:grid-cols-2 mt-4">
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">General email *</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm" name="general_email" type="email" required value="{{ old('general_email', $v['general_email'] ?? '') }}">
                            @error('general_email')<div class="mt-2 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Phone *</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm" name="phone" required value="{{ old('phone', $v['phone'] ?? '') }}">
                            @error('phone')<div class="mt-2 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2 mt-4">
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Website</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm" name="website_url" value="{{ old('website_url', $v['website_url'] ?? '') }}">
                        </div>
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Region</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm" name="region" value="{{ old('region', $v['region'] ?? '') }}">
                        </div>
                    </div>
                </div>

                {{-- REQUIRED: Contact person --}}
                <div class="pixel-outline p-6">
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-500">Contact person (required)</div>

                    <div class="grid gap-4 md:grid-cols-2 mt-4">
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">First name *</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm" name="contact_first_name" required value="{{ old('contact_first_name', $v['contact_first_name'] ?? '') }}">
                            @error('contact_first_name')<div class="mt-2 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Last name *</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm" name="contact_last_name" required value="{{ old('contact_last_name', $v['contact_last_name'] ?? '') }}">
                            @error('contact_last_name')<div class="mt-2 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2 mt-4">
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Email *</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm" name="contact_email" type="email" required value="{{ old('contact_email', $v['contact_email'] ?? '') }}">
                            @error('contact_email')<div class="mt-2 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Phone *</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm" name="contact_phone" required value="{{ old('contact_phone', $v['contact_phone'] ?? '') }}">
                            @error('contact_phone')<div class="mt-2 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- REQUIRED: Company facts --}}
                <div class="pixel-outline p-6">
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-500">Company facts (required)</div>

                    <div class="grid gap-4 md:grid-cols-2 mt-4">
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Team size *</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm" name="team_size" type="number" min="1" required value="{{ old('team_size', $v['team_size'] ?? '') }}">
                            @error('team_size')<div class="mt-2 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Founded year *</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm" name="founded_year" type="number" min="1800" max="{{ date('Y') }}" required value="{{ old('founded_year', $v['founded_year'] ?? '') }}">
                            @error('founded_year')<div class="mt-2 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('frontend.register.step1') }}" class="pixel-button text-center">Back</a>
                    <button type="submit" class="pixel-button">Next</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.pixel>
