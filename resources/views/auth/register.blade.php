<x-layouts.pixel title="Register">
    <div class="mx-auto max-w-md">
        <div class="pixel-frame p-6">
            <div class="mb-6">
                <div class="text-xs uppercase tracking-[0.2em] text-slate-500">365jobs</div>
                <h1 class="mt-2 text-2xl font-bold">Register</h1>
                <p class="mt-2 text-slate-600">Create a login for your company</p>

                <div class="mt-4 grid grid-cols-2 gap-2 text-[10px] uppercase tracking-[0.28em] text-slate-500">
                    <div id="stepLabel1" class="pixel-outline px-3 py-2 text-center">1) Company</div>
                    <div id="stepLabel2" class="pixel-outline px-3 py-2 text-center opacity-50">2) Account</div>
                </div>
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

            <form id="registerForm" method="POST" action="{{ route('frontend.register.submit') }}" class="space-y-4">
                @csrf

                {{-- STEP 1: ICO only --}}
                <div id="step1" class="space-y-4">
                    <div class="pixel-outline p-6">
                        <div class="text-xs uppercase tracking-[0.2em] text-slate-500">Company</div>

                        <label class="block mt-4">
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">ICO (8 digits) *</div>
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
                            Next
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
                        <div class="text-xs uppercase tracking-[0.2em] text-slate-500">Company (from ICO)</div>

                        <div class="mt-4 space-y-2 text-sm text-slate-700">
                            <div><span class="text-xs uppercase tracking-[0.2em] text-slate-500">IČO:</span> <span id="summaryIco">-</span></div>
                            <div><span class="text-xs uppercase tracking-[0.2em] text-slate-500">Legal name:</span> <span id="summaryName">-</span></div>
                            <div><span class="text-xs uppercase tracking-[0.2em] text-slate-500">Address:</span> <span id="summaryAddress">-</span></div>
                            <div class="grid grid-cols-2 gap-3">
                                <div><span class="text-xs uppercase tracking-[0.2em] text-slate-500">DIČ:</span> <span id="summaryDic">-</span></div>
                                <div><span class="text-xs uppercase tracking-[0.2em] text-slate-500">IČ DPH:</span> <span id="summaryIcdph">-</span></div>
                            </div>
                        </div>

                        <div class="mt-4 text-[10px] uppercase tracking-[0.28em] text-slate-500">
                            If this is not your company, go back and enter a different IČO.
                        </div>
                    </div>

                    <div class="pixel-outline p-6">
                        <div class="text-xs uppercase tracking-[0.2em] text-slate-500">Account</div>

                        <div class="mt-4">
                            <label class="mb-2 block text-xs uppercase tracking-[0.2em] text-slate-600">Company bio</label>
                            <textarea class="pixel-input w-full px-4 py-3 text-sm" name="bio" rows="5" required>{{ old('bio') }}</textarea>
                        </div>

                        <div class="mt-4">
                            <label class="mb-2 block text-xs uppercase tracking-[0.2em] text-slate-600">Full Name <span class="text-xs cursive">(not legal company name)</span></label>
                            <input class="pixel-input w-full px-4 py-3 text-sm" type="text" name="name" value="{{ old('name') }}" required>
                        </div>

                        <div class="mt-4">
                            <label class="mb-2 block text-xs uppercase tracking-[0.2em] text-slate-600">Email</label>
                            <input class="pixel-input w-full px-4 py-3 text-sm" type="email" name="email" value="{{ old('email') }}" required>
                        </div>

                        <div class="mt-4">
                            <label class="mb-2 block text-xs uppercase tracking-[0.2em] text-slate-600">Password</label>
                            <input class="pixel-input w-full px-4 py-3 text-sm" type="password" name="password" required>
                        </div>

                        <div class="mt-4">
                            <label class="mb-2 block text-xs uppercase tracking-[0.2em] text-slate-600">Confirm Password</label>
                            <input class="pixel-input w-full px-4 py-3 text-sm" type="password" name="password_confirmation" required>
                        </div>

                        <div class="mt-6 grid gap-3 md:grid-cols-2">
                            <button id="btnBack" class="pixel-button w-full px-4 py-3 text-sm" type="button">
                                Back
                            </button>
                            <button class="pixel-button w-full px-4 py-3 text-sm" type="submit">
                                Create Account
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="mt-6 flex items-center justify-between text-xs uppercase tracking-[0.2em] text-slate-600">
                <a href="{{ route('frontend.login') }}">Login</a>
                <a href="{{ url('/') }}">Home</a>
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

            function digits8(v) { return (v || '').replace(/\D/g, '').slice(0, 8); }
            function setHelp(t) { helpEl.textContent = t || ''; }

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
                    setHelp('ICO must be exactly 8 digits.');
                    return false;
                }

                btnNext.disabled = true;
                setHelp('Looking up company...');

                try {
                    const res = await fetch(`/api/company-lookup?ico=${ico}`, {
                        headers: { 'Accept': 'application/json' },
                        credentials: 'same-origin',
                    });

                    if (!res.ok) {
                        setHelp('Company not found. Try another ICO.');
                        return false;
                    }

                    const json = await res.json();
                    if (!json.ok || !json.data) {
                        setHelp('Company not found.');
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

                    setHelp('Found.');
                    return true;
                } catch (e) {
                    setHelp('Lookup failed. Try again.');
                    return false;
                } finally {
                    btnNext.disabled = false;
                }
            }

            btnNext.addEventListener('click', async () => {
                const ok = await lookup();
                if (!ok) return;

                if (!nameEl.value) {
                    setHelp('Company name missing. Try another ICO.');
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
