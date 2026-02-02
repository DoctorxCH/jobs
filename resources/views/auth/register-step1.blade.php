<x-layouts.pixel title="{{ __('main.register_step1_title') }}">
    <div class="mx-auto max-w-md">
        <div class="pixel-frame p-6">
            <div class="mb-6">
                <div class="text-xs uppercase tracking-[0.2em] text-slate-500">365jobs</div>
                <h1 class="mt-2 text-2xl font-bold">{{ __('main.register_title') }}</h1>
                <p class="mt-2 text-slate-600">{{ __('main.register_step1_subtitle') }}</p>

                <div class="mt-4 grid grid-cols-3 gap-2 text-[10px] uppercase tracking-[0.28em] text-slate-500">
                    <div class="pixel-outline px-3 py-2 text-center">{{ __('main.register_step1_label') }}</div>
                    <div class="pixel-outline px-3 py-2 text-center opacity-50">{{ __('main.register_step2_label') }}</div>
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

            <form method="POST" action="{{ route('frontend.register.step1.post') }}">
                @csrf
                <div class="pixel-outline p-6">
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.company_label') }}</div>

                    <label class="block mt-4">
                        <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.ico_label') }}</div>
                        <input
                            name="ico"
                            value="{{ old('ico', $reg['step1']['ico'] ?? '') }}"
                            class="mt-2 pixel-input w-full px-4 py-3 text-sm"
                            required
                            inputmode="numeric"
                            maxlength="8"
                            pattern="[0-9]{8}"
                            placeholder="12345678"
                        />
                        @error('ico')
                            <div class="mt-2 text-xs font-semibold text-red-700">{{ $message }}</div>
                        @enderror
                    </label>

                    <input type="hidden" name="country_code" value="SK">

                    <button class="pixel-button w-full px-4 py-3 text-sm mt-5" type="submit">
                        {{ __('main.next') }}
                    </button>
                </div>
            </form>

            <div class="mt-4 text-xs text-slate-500">
                {{ __('main.already_have_account') }}
                <a class="underline" href="{{ route('frontend.login') }}">{{ __('main.login') }}</a>
            </div>
        </div>
    </div>
</x-layouts.pixel>
