<x-layouts.pixel title="Register - Step 2">
    @php
        $v = $values ?? [];
        $l = $locked ?? [];
    @endphp
    <div class="mx-auto max-w-2xl">
        <div class="pixel-frame p-6">
            <div class="mb-6">
                <div class="text-xs uppercase tracking-[0.2em] text-slate-500">365jobs</div>
                <h1 class="mt-2 text-2xl font-bold">{{ __('main.register_title') }}</h1>
                <p class="mt-2 text-slate-600">{{ __('main.register_step2_subtitle') }}</p>

                <div class="mt-4 grid grid-cols-3 gap-2 text-[10px] uppercase tracking-[0.28em] text-slate-500">
                    <div class="pixel-outline px-3 py-2 text-center opacity-50">{{ __('main.register_step1_label') }}</div>
                    <div class="pixel-outline px-3 py-2 text-center">{{ __('main.register_step2_label') }}</div>
                    <div class="pixel-outline px-3 py-2 text-center opacity-50">{{ __('main.register_step3_label') }}</div>
                </div>
            </div>

            @if ($errors->any())
                <div class="pixel-outline mb-5 p-4">
                    <div class="text-sm font-bold">{{ __('main.error_title') }}</div>
                    <ul class="mt-2 list-disc pl-5 text-sm text-slate-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('frontend.register.step2.post') }}" class="space-y-4">
                @csrf

                {{-- Locked (from registry) --}}
                <div class="pixel-outline p-6">
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.locked_from_registry') }}</div>

                    <div class="grid gap-4 md:grid-cols-2 mt-4">
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.ico') }}</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm opacity-80" value="{{ $l['ico'] ?? '' }}" readonly>
                        </div>
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.country') }}</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm opacity-80" value="{{ $l['country_code'] ?? 'SK' }}" readonly>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.legal_name') }}</div>
                        <input class="mt-2 pixel-input w-full px-4 py-3 text-sm opacity-80" value="{{ $l['legal_name'] ?? '' }}" readonly>
                    </div>

                    <div class="grid gap-4 md:grid-cols-3 mt-4">
                        <div class="md:col-span-2">
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.street') }}</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm opacity-80" value="{{ $l['street'] ?? '' }}" readonly>
                        </div>
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.postal') }}</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm opacity-80" value="{{ $l['postal_code'] ?? '' }}" readonly>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.city') }}</div>
                        <input class="mt-2 pixel-input w-full px-4 py-3 text-sm opacity-80" value="{{ $l['city'] ?? '' }}" readonly>
                    </div>
                </div>

                {{-- Optional IDs --}}
                <div class="pixel-outline p-6">
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.ids') }}</div>

                    <div class="grid gap-4 md:grid-cols-2 mt-4">
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.dic') }}</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm" name="dic" value="{{ old('dic', $v['dic'] ?? '') }}">
                            @error('dic')<div class="mt-2 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.ic_dph') }}</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm" name="ic_dph" value="{{ old('ic_dph', $v['ic_dph'] ?? '') }}">
                            @error('ic_dph')<div class="mt-2 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- Required: Web/Contacts --}}
                <div class="pixel-outline p-6">
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.company_contact_required') }}</div>

                    <div class="grid gap-4 md:grid-cols-2 mt-4">
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.general_email') }}</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm" name="general_email" type="email" required value="{{ old('general_email', $v['general_email'] ?? '') }}">
                            @error('general_email')<div class="mt-2 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.phone_required') }}</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm" name="phone" required value="{{ old('phone', $v['phone'] ?? '') }}">
                            @error('phone')<div class="mt-2 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2 mt-4">
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.website') }}</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm" name="website_url" value="{{ old('website_url', $v['website_url'] ?? '') }}">
                        </div>
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.region') }}</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm" name="region" value="{{ old('region', $v['region'] ?? '') }}">
                        </div>
                    </div>
                </div>

                {{-- Required: Contact person --}}
                <div class="pixel-outline p-6">
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.contact_person_required') }}</div>

                    <div class="grid gap-4 md:grid-cols-2 mt-4">
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.first_name') }}</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm" name="contact_first_name" required value="{{ old('contact_first_name', $v['contact_first_name'] ?? '') }}">
                            @error('contact_first_name')<div class="mt-2 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.last_name') }}</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm" name="contact_last_name" required value="{{ old('contact_last_name', $v['contact_last_name'] ?? '') }}">
                            @error('contact_last_name')<div class="mt-2 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2 mt-4">
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.email_required') }}</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm" name="contact_email" type="email" required value="{{ old('contact_email', $v['contact_email'] ?? '') }}">
                            @error('contact_email')<div class="mt-2 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.phone_required') }}</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm" name="contact_phone" required value="{{ old('contact_phone', $v['contact_phone'] ?? '') }}">
                            @error('contact_phone')<div class="mt-2 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- Required: Company facts --}}
                <div class="pixel-outline p-6">
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.company_facts_required') }}</div>

                    <div class="grid gap-4 md:grid-cols-2 mt-4">
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.team_size') }}</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm" name="team_size" type="number" min="1" required value="{{ old('team_size', $v['team_size'] ?? '') }}">
                            @error('team_size')<div class="mt-2 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.founded_year') }}</div>
                            <input class="mt-2 pixel-input w-full px-4 py-3 text-sm" name="founded_year" type="number" min="1800" max="{{ date('Y') }}" required value="{{ old('founded_year', $v['founded_year'] ?? '') }}">
                            @error('founded_year')<div class="mt-2 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- Required: Bio --}}
                <div class="pixel-outline p-6">
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.company_bio_required') }}</div>
                    <div class="mt-4">
                        <label class="mb-2 block text-xs uppercase tracking-[0.2em] text-slate-600">{{ __('main.bio') }}</label>
                        <textarea class="pixel-input w-full px-4 py-3 text-sm" name="bio" rows="6" required>{{ old('bio', $v['bio'] ?? '') }}</textarea>
                        @error('bio')<div class="mt-2 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('frontend.register.step1') }}" class="pixel-button text-center">{{ __('main.back') }}</a>
                    <button type="submit" class="pixel-button">{{ __('main.next') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.pixel>
