<x-layouts.pixel title="{{ __('main.register_title') }}">
    <div class="mx-auto max-w-md">
        <div class="pixel-frame p-6">
            <div class="mb-6">
                <div class="text-xs uppercase tracking-[0.2em] text-slate-500">365jobs</div>
                <h1 class="mt-2 text-2xl font-bold">{{ __('main.register_title') }}</h1>
                <p class="mt-2 text-slate-600">{{ __('main.register_subtitle') }}</p>

                <div class="mt-4 grid grid-cols-2 gap-2 text-[10px] uppercase tracking-[0.28em] text-slate-500">
                    <div id="stepLabel1" class="pixel-outline px-3 py-2 text-center">{{ __('main.register_step_company') }}</div>
                    <div id="stepLabel2" class="pixel-outline px-3 py-2 text-center opacity-50">{{ __('main.register_step_account') }}</div>
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

            <form id="registerForm" method="POST" action="{{ route('frontend.register.submit') }}" class="space-y-4">
                @csrf

                {{-- STEP 1: ICO only --}}
                <div id="step1" class="space-y-4">
                    <div class="pixel-outline p-6">
                        <div class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.company_label') }}</div>

                        <label class="block mt-4">
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.ico_label') }}</div>
                            <input
                                id="ico"
                                name="ico"
                                value="{{ old('ico') }}"
                                class="mt-2 pixel-input w-full px-4 py-3 text-sm"
                                required
                                inputmode="numeric"
                                maxlength="8"
                                pattern="[0-9]{8}"
                                placeholder="12345678"
                            />
                            <div id="ico_help" class="mt-2 text-[10px] uppercase tracking-[0.28em] text-slate-500"></div>
                        </label>

                        <button id="btnNext" class="pixel-button w-full px-4 py-3 text-sm mt-5" type="button">
                            {{ __('main.next') }}
                        </button>
                    </div>
                </div>

                {{-- hidden fields (used for submit) --}}
                <input type="hidden" id="company_legal_name" name="company_legal_name" value="{{ old('company_legal_name') }}">
                <input type="hidden" id="dic" name="dic" value="{{ old('dic') }}">
                <input type="hidden" id="ic_dph" name="ic_dph" value="{{ old('ic_dph') }}">
                <input type="hidden" id="street" name="street" value="{{ old('street') }}">
                <input type="hidden" id="postal_code" name="postal_code" value="{{ old('postal_code') }}">
                <input type="hidden" id="city" name="city" value="{{ old('city') }}">

                {{-- STEP 2: Account + Company summary --}}
                <div id="step2" class="space-y-4 hidden">
                    <div class="pixel-outline p-6">
                        <div class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.company_from_ico') }}</div>

                        <div class="mt-4 space-y-2 text-sm text-slate-700">
                            <div><span class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.ico') }}:</span> <span id="summaryIco">-</span></div>
                            <div><span class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.legal_name') }}:</span> <span id="summaryName">-</span></div>
                            <div><span class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.address') }}:</span> <span id="summaryAddress">-</span></div>
                            <div class="grid grid-cols-2 gap-3">
                                <div><span class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.dic') }}:</span> <span id="summaryDic">-</span></div>
                                <div><span class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.ic_dph') }}:</span> <span id="summaryIcdph">-</span></div>
                            </div>
                        </div>

                        <div class="mt-4 text-[10px] uppercase tracking-[0.28em] text-slate-500">
                            {{ __('main.register_company_not_yours') }}
                        </div>
                    </div>

                    <div class="pixel-outline p-6">
                        <div class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.account') }}</div>

                        <div class="mt-4">
                            <label class="mb-2 block text-xs uppercase tracking-[0.2em] text-slate-600">{{ __('main.company_bio') }}</label>
                            <textarea class="pixel-input w-full px-4 py-3 text-sm" name="bio" rows="5" required>{{ old('bio') }}</textarea>
                        </div>

                        <div class="mt-4">
                            <label class="mb-2 block text-xs uppercase tracking-[0.2em] text-slate-600">{{ __('main.full_name') }} <span class="text-xs cursive">({{ __('main.full_name_note') }})</span></label>
                            <input class="pixel-input w-full px-4 py-3 text-sm" type="text" name="name" value="{{ old('name') }}" required>
                        </div>

                        <div class="mt-4">
                            <label class="mb-2 block text-xs uppercase tracking-[0.2em] text-slate-600">{{ __('main.email') }}</label>
                            <input class="pixel-input w-full px-4 py-3 text-sm" type="email" name="email" value="{{ old('email') }}" required>
                        </div>

                        <div class="mt-4">
                            <label class="mb-2 block text-xs uppercase tracking-[0.2em] text-slate-600">{{ __('main.password') }}</label>
                            <input class="pixel-input w-full px-4 py-3 text-sm" type="password" name="password" required>
                        </div>

                        <div class="mt-4">
                            <label class="mb-2 block text-xs uppercase tracking-[0.2em] text-slate-600">{{ __('main.confirm_password') }}</label>
                            <input class="pixel-input w-full px-4 py-3 text-sm" type="password" name="password_confirmation" required>
                        </div>

                        <div class="mt-6 grid gap-3 md:grid-cols-2">
                            <button id="btnBack" class="pixel-button w-full px-4 py-3 text-sm" type="button">
                                {{ __('main.back') }}
                            </button>
                            <button class="pixel-button w-full px-4 py-3 text-sm" type="submit">
                                {{ __('main.create_account') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="mt-6 flex items-center justify-between text-xs uppercase tracking-[0.2em] text-slate-600">
                <a href="{{ route('frontend.login') }}">{{ __('main.login') }}</a>
                <a href="{{ url('/') }}">{{ __('main.home') }}</a>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const step1 = document.getElementById('step1');
            const step2 = document.getElementById('step2');
            const stepLabel1 = document.getElementById('stepLabel1');
            const stepLabel2 = document.getElementById('stepLabel2');

            const icoEl = document.getElementById('ico');
            const helpEl = document.getElementById('ico_help');
            const btnNext = document.getElementById('btnNext');
            const btnBack = document.getElementById('btnBack');

            const nameEl = document.getElementById('company_legal_name');
            const dicEl = document.getElementById('dic');
            const icDphEl = document.getElementById('ic_dph');
            const streetEl = document.getElementById('street');
            const postalEl = document.getElementById('postal_code');
            const cityEl = document.getElementById('city');

            const sIco = document.getElementById('summaryIco');
            const sName = document.getElementById('summaryName');
            const sAddress = document.getElementById('summaryAddress');
            const sDic = document.getElementById('summaryDic');
            const sIcdph = document.getElementById('summaryIcdph');

            const t = {
                ico_invalid: @json(__('main.ico_invalid')),
                lookup_in_progress: @json(__('main.company_lookup_in_progress')),
                company_not_found_try_another: @json(__('main.company_not_found_try_another')),
                company_not_found: @json(__('main.company_not_found')),
                found: @json(__('main.found')),
                lookup_failed: @json(__('main.company_lookup_failed')),
                company_name_missing_try_another: @json(__('main.company_name_missing_try_another')),
            };

            function digits8(v) { return (v || '').replace(/\D/g, '').slice(0, 8); }
            function setHelp(text) { helpEl.textContent = text || ''; }

            function showStep(n) {
                if (n === 1) {
                    step1.classList.remove('hidden');
                    step2.classList.add('hidden');
                    stepLabel1.classList.remove('opacity-50');
                    stepLabel2.classList.add('opacity-50');
                } else {
                    step1.classList.add('hidden');
                    step2.classList.remove('hidden');
                    stepLabel1.classList.add('opacity-50');
                    stepLabel2.classList.remove('opacity-50');
                }
                window.scrollTo({ top: 0 });
            }

            function fillSummary() {
                const ico = icoEl.value || '-';
                const name = nameEl.value || '-';
                const street = streetEl.value || '';
                const pc = postalEl.value || '';
                const city = cityEl.value || '';
                const addr = [street, [pc, city].filter(Boolean).join(' ')].filter(Boolean).join(', ') || '-';

                sIco.textContent = ico;
                sName.textContent = name;
                sAddress.textContent = addr;
                sDic.textContent = dicEl.value || '-';
                sIcdph.textContent = icDphEl.value || '-';
            }

            async function lookup() {
                const ico = digits8(icoEl.value);
                icoEl.value = ico;

                if (ico.length !== 8) {
                    setHelp(t.ico_invalid);
                    return false;
                }

                btnNext.disabled = true;
                setHelp(t.lookup_in_progress);

                try {
                    const res = await fetch(`/api/company-lookup?ico=${ico}`, {
                        headers: { 'Accept': 'application/json' },
                        credentials: 'same-origin',
                    });

                    if (!res.ok) {
                        setHelp(t.company_not_found_try_another);
                        return false;
                    }

                    const json = await res.json();
                    if (!json.ok || !json.data) {
                        setHelp(t.company_not_found);
                        return false;
                    }

                    const d = json.data;
                    nameEl.value = d.legal_name || '';
                    dicEl.value = d.dic || '';
                    icDphEl.value = d.ic_dph || '';

                    const a = d.address || {};
                    streetEl.value = a.street || '';
                    postalEl.value = a.postal_code || '';
                    cityEl.value = a.city || '';

                    setHelp(t.found);
                    return true;
                } catch (e) {
                    setHelp(t.lookup_failed);
                    return false;
                } finally {
                    btnNext.disabled = false;
                }
            }

            btnNext.addEventListener('click', async () => {
                const ok = await lookup();
                if (!ok) return;

                if (!nameEl.value) {
                    setHelp(t.company_name_missing_try_another);
                    return;
                }

                fillSummary();
                showStep(2);
            });

            btnBack.addEventListener('click', () => showStep(1));

            showStep(1);
        })();
    </script>
</x-layouts.pixel>
