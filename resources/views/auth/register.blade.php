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

            <form method="POST" action="{{ route('frontend.register.submit') }}">
                @csrf

                {{-- STEP 1 --}}
                <div id="step1">
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
                            <div
                                id="ico_help"
                                class="mt-2 text-xs font-semibold text-red-700 hidden"
                            ></div>
                        </label>

                        <button
                            id="btnNext"
                            class="pixel-button w-full px-4 py-3 text-sm mt-5"
                            type="button"
                        >
                            Next
                        </button>
                    </div>
                </div>

                {{-- hidden company fields --}}
                <input type="hidden" id="company_legal_name" name="company_legal_name">
                <input type="hidden" id="dic" name="dic">
                <input type="hidden" id="ic_dph" name="ic_dph">
                <input type="hidden" id="street" name="street">
                <input type="hidden" id="postal_code" name="postal_code">
                <input type="hidden" id="city" name="city">

                {{-- STEP 2 --}}
                <div id="step2" class="hidden mt-6 space-y-4">
                    <div class="pixel-outline p-6">
                        <div class="text-xs uppercase tracking-[0.2em] text-slate-500">Company</div>
                        <div class="mt-3 text-sm">
                            <div><b>ICO:</b> <span id="summaryIco"></span></div>
                            <div><b>Name:</b> <span id="summaryName"></span></div>
                            <div><b>Address:</b> <span id="summaryAddress"></span></div>
                        </div>
                    </div>

                    <div class="pixel-outline p-6">
                        <div class="text-xs uppercase tracking-[0.2em] text-slate-500">Account</div>

                        <input class="pixel-input w-full mt-3" name="name" placeholder="Name" required>
                        <input class="pixel-input w-full mt-3" name="email" type="email" placeholder="Email" required>
                        <input class="pixel-input w-full mt-3" name="password" type="password" placeholder="Password" required>
                        <input class="pixel-input w-full mt-3" name="password_confirmation" type="password" placeholder="Confirm Password" required>

                        <div class="grid grid-cols-2 gap-3 mt-4">
                            <button id="btnBack" type="button" class="pixel-button">Back</button>
                            <button type="submit" class="pixel-button">Create Account</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

<script>
(() => {
    const ico = document.getElementById('ico');
    const help = document.getElementById('ico_help');
    const next = document.getElementById('btnNext');

    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');

    const name = document.getElementById('company_legal_name');
    const street = document.getElementById('street');
    const pc = document.getElementById('postal_code');
    const city = document.getElementById('city');

    const sIco = document.getElementById('summaryIco');
    const sName = document.getElementById('summaryName');
    const sAddr = document.getElementById('summaryAddress');

    function error(msg) {
        help.textContent = msg;
        help.classList.remove('hidden');
        ico.classList.add('border-2','border-red-600');
    }

    function clearError() {
        help.textContent = '';
        help.classList.add('hidden');
        ico.classList.remove('border-2','border-red-600');
    }

    function digits8(v){ return v.replace(/\D/g,'').slice(0,8); }

    next.onclick = async () => {
        clearError();
        ico.value = digits8(ico.value);

        if (ico.value.length !== 8) {
            error('ICO must contain exactly 8 digits.');
            return;
        }

        next.disabled = true;

        const res = await fetch(`/api/company-lookup?ico=${ico.value}`);

        if (res.status === 409) {
            error('This company is already registered.');
            next.disabled = false;
            return;
        }

        if (!res.ok) {
            error('Company not found.');
            next.disabled = false;
            return;
        }

        const json = await res.json();
        if (!json.ok) {
            error('Company not found.');
            next.disabled = false;
            return;
        }

        name.value = json.data.legal_name || '';
        street.value = json.data.address?.street || '';
        pc.value = json.data.address?.postal_code || '';
        city.value = json.data.address?.city || '';

        sIco.textContent = ico.value;
        sName.textContent = name.value;
        sAddr.textContent = [street.value, pc.value, city.value].filter(Boolean).join(', ');

        step1.classList.add('hidden');
        step2.classList.remove('hidden');
        next.disabled = false;
    };

    document.getElementById('btnBack').onclick = () => {
        step2.classList.add('hidden');
        step1.classList.remove('hidden');
        clearError();
    };
})();
</script>
</x-layouts.pixel>
