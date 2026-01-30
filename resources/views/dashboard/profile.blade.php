<x-dashboard.layout title="Profile">
    @php
        $u = $user ?? auth()->user();
        $c = $company ?? null;

        $val = function(string $key, $fallback = null) use ($c) {
            return old($key, $c?->{$key} ?? $fallback);
        };
    @endphp

    <form method="POST" action="{{ route('frontend.profile.update') }}" enctype="multipart/form-data" class="flex flex-col gap-8">
        @csrf

        {{-- Header --}}
        <div>
            <div class="text-s uppercase tracking-[0.2em] text-slate-800 font-bold">Profile</div>
            <h1 class="mt-2 text-2xl font-bold">Your profile</h1>
            <p class="mt-2 text-sm text-slate-600">
                Update your account and company details. Fields marked with * are required.
            </p>
        </div>

        {{-- Account --}}
        <div class="pixel-outline p-6">
            <div class="text-s uppercase tracking-[0.2em] text-slate-800 font-bold">Account</div>

            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Full name</label>
                    <input
                        name="user_name"
                        class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none"
                        value="{{ old('user_name', $u->name) }}"
                        required
                    />
                    @error('user_name')
                        <div class="mt-2 text-xs text-red-700">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Email</label>
                    <input
                        class="mt-2 pixel-outline w-full px-4 py-3 text-sm text-slate-500 bg-slate-50"
                        value="{{ $u->email }}"
                        disabled
                    />
                    <div class="mt-2 text-[11px] text-slate-500">Your login email (cannot be changed here)</div>
                </div>
            </div>
        </div>

        @php
    $canManageTeam = auth()->check()
        && method_exists(auth()->user(), 'canCompanyManageTeam')
        && auth()->user()->canCompanyManageTeam();
@endphp


        {{-- Company Identity --}}
        <div class="pixel-outline p-6">
            <div class="text-s uppercase tracking-[0.2em] text-slate-800 font-bold">Company identity</div>

            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Legal name <span class="text-red-500">*</span></label>

                    <input type="hidden" name="legal_name" value="{{ $val('legal_name') }}" />

                    <input
                        class="mt-2 pixel-outline w-full px-4 py-3 text-sm text-slate-500 bg-slate-50"
                        value="{{ $val('legal_name') }}"
                        disabled
                    />

                    <div class="mt-2 text-[11px] text-red-500">Legal name is locked after registration.</div>

                    @error('legal_name')
                        <div class="mt-2 text-xs text-red-700">{{ $message }}</div>
                    @enderror
                </div>
                


                <div>
                    <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">IČO (8 digits) <span class="text-red-500">*</span></label>

                    <input type="hidden" name="ico" value="{{ old('ico', $c?->ico ?? '') }}" />

                    <input
                        class="mt-2 pixel-outline w-full px-4 py-3 text-sm text-slate-500 bg-slate-50"
                        value="{{ old('ico', $c?->ico ?? '') }}"
                        disabled
                    />

                    <div class="mt-2 text-[11px] text-red-500">IČO is locked after registration.</div>

                    @error('ico')
                        <div class="mt-2 text-xs text-red-700">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Country<span class="text-red-500">*</span></label>
                    <select
                        name="country_code"
                        class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none"
                        value="{{ old('country_code', $c?->country_code ?? 'SK') }}"
                        required
                    >
                        <option value="AT">Austria</option>
                        <option value="BE">Belgium</option>
                        <option value="BG">Bulgaria</option>
                        <option value="HR">Croatia</option>
                        <option value="CY">Cyprus</option>
                        <option value="CZ">Czech Republic</option>
                        <option value="DK">Denmark</option>
                        <option value="EE">Estonia</option>
                        <option value="FI">Finland</option>
                        <option value="FR">France</option>
                        <option value="DE">Germany</option>
                        <option value="GB">United Kingdom</option>
                        <option value="GR">Greece</option>
                        <option value="HU">Hungary</option>
                        <option value="IE">Ireland</option>
                        <option value="IT">Italy</option>
                        <option value="LV">Latvia</option>
                        <option value="LT">Lithuania</option>
                        <option value="LU">Luxembourg</option>
                        <option value="MT">Malta</option>
                        <option value="NL">Netherlands</option>
                        <option value="PL">Poland</option>
                        <option value="PT">Portugal</option>
                        <option value="RO">Romania</option>
                        <option value="SK" selected>Slovakia</option>
                        <option value="SI">Slovenia</option>
                        <option value="ES">Spain</option>
                        <option value="SE">Sweden</option>
                        <option value="00">Other</option>
                    </select>
                    @error('country_code')
                        <div class="mt-2 text-xs text-red-700">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">DIČ</label>
                    <input
                        name="dic"
                        maxlength="10"
                        class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none"
                        value="{{ old('dic', $c?->dic ?? '') }}"
                        placeholder="(optional)"
                    />
                    @error('dic')
                        <div class="mt-2 text-xs text-red-700">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">IČ DPH</label>
                    <input
                        name="ic_dph"
                        maxlength="12"
                        class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none"
                        value="{{ old('ic_dph', $c?->ic_dph ?? '') }}"
                        placeholder="(optional)"
                    />
                    @error('ic_dph')
                        <div class="mt-2 text-xs text-red-700">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Contact --}}
        <div class="pixel-outline p-6">
            <div class="text-s uppercase tracking-[0.2em] text-slate-800 font-bold">Company contact</div>

            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">General email</label>
                    <input
                        name="general_email"
                        type="email"
                        class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none"
                        value="{{ $val('general_email') }}"
                        placeholder="contact@company.com"
                    />
                    @error('general_email')
                        <div class="mt-2 text-xs text-red-700">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Phone</label>
                    <input
                        name="phone"
                        class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none"
                        value="{{ $val('phone') }}"
                        placeholder="+421 ..."
                    />
                    @error('phone')
                        <div class="mt-2 text-xs text-red-700">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Contact first name</label>
                    <input
                        name="contact_first_name"
                        class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none"
                        value="{{ $val('contact_first_name') }}"
                    />
                    @error('contact_first_name')
                        <div class="mt-2 text-xs text-red-700">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Contact last name</label>
                    <input
                        name="contact_last_name"
                        class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none"
                        value="{{ $val('contact_last_name') }}"
                    />
                    @error('contact_last_name')
                        <div class="mt-2 text-xs text-red-700">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Contact email</label>
                    <input
                        name="contact_email"
                        type="email"
                        class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none"
                        value="{{ $val('contact_email') }}"
                    />
                    @error('contact_email')
                        <div class="mt-2 text-xs text-red-700">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Contact phone</label>
                    <input
                        name="contact_phone"
                        class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none"
                        value="{{ $val('contact_phone') }}"
                    />
                    @error('contact_phone')
                        <div class="mt-2 text-xs text-red-700">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Address --}}
        <div class="pixel-outline p-6">
            <div class="text-s uppercase tracking-[0.2em] text-slate-800 font-bold">Address</div>

            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Region</label>
                    <input name="region" class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none" value="{{ $val('region') }}" />
                    @error('region') <div class="mt-2 text-xs text-red-700">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">City</label>
                    <input name="city" class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none" value="{{ $val('city') }}" />
                    @error('city') <div class="mt-2 text-xs text-red-700">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Postal code</label>
                    <input name="postal_code" class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none" value="{{ $val('postal_code') }}" />
                    @error('postal_code') <div class="mt-2 text-xs text-red-700">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Street</label>
                    <input name="street" class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none" value="{{ $val('street') }}" />
                    @error('street') <div class="mt-2 text-xs text-red-700">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- Online / Branding --}}
        <div class="pixel-outline p-6">
            <div class="text-s uppercase tracking-[0.2em] text-slate-800 font-bold">Online & branding</div>

            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Website</label>
                    <input name="website_url" class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none" value="{{ $val('website_url') }}" placeholder="https://..." />
                    @error('website_url') <div class="mt-2 text-xs text-red-700">{{ $message }}</div> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Company logo</label>

                    <div class="mt-2 flex items-start gap-4">
                        <div class="flex-shrink-0">
                            @if($c?->logo_path)
                                <img
                                    src="{{ asset('storage/'.$c->logo_path) }}"
                                    alt="Company logo"
                                    class="h-16 w-40 object-contain bg-white"
                                    loading="lazy"
                                />
                            @endif
                        </div>

                        <div class="flex-1">
                            <input
                                type="file"
                                name="logo"
                                accept="image/png,image/jpeg,image/webp,image/svg+xml"
                                class="pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none"
                            />

                            <div class="mt-2 text-[11px] text-slate-500">PNG/JPG/WebP/SVG, max 2MB.</div>

                            @error('logo')
                                <div class="mt-2 text-xs text-red-700">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Short description</label>
                    <input name="description_short" class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none" value="{{ $val('description_short') }}" maxlength="280" />
                    @error('description_short') <div class="mt-2 text-xs text-red-700">{{ $message }}</div> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Bio</label>
                    <div class="mt-2 space-y-2" data-quill-wrapper>
                        <input type="hidden" name="bio" value="{{ old('bio', $c?->bio ?? '') }}" data-quill-input>
                        <div class="flex flex-wrap gap-2 text-[10px] uppercase tracking-[0.2em] text-slate-500" data-quill-toolbar>
                            <button type="button" class="pixel-outline px-2 py-1" data-quill-action="bold"><strong>B</strong></button>
                            <button type="button" class="pixel-outline px-2 py-1" data-quill-action="italic"><em>I</em></button>
                            <button type="button" class="pixel-outline px-2 py-1" data-quill-action="underline"><span class="underline">U</span></button>
                            <button type="button" class="pixel-outline px-2 py-1" data-quill-action="ordered">1.</button>
                            <button type="button" class="pixel-outline px-2 py-1" data-quill-action="bullet">•</button>
                            <button type="button" class="pixel-outline px-2 py-1" data-quill-action="link">Link</button>
                            <button type="button" class="pixel-outline px-2 py-1" data-quill-action="clean">Clear</button>
                        </div>
                        <div class="min-h-[160px] w-full border border-slate-300 bg-white text-sm text-slate-900" data-quill-editor></div>
                    </div>
                    @error('bio') <div class="mt-2 text-xs text-red-700">{{ $message }}</div> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Social links (JSON)</label>
                    <textarea
                        name="social_links"
                        rows="5"
                        class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none"
                        placeholder='{"linkedin":"...","facebook":"..."}'
                    >{{ old('social_links', $c?->social_links ? json_encode($c->social_links, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) : '') }}</textarea>
                    @error('social_links') <div class="mt-2 text-xs text-red-700">{{ $message }}</div> @enderror
                    <div class="mt-2 text-[11px] text-slate-500">Leave empty if you don’t want to use social links yet.</div>
                </div>
            </div>
        </div>

        {{-- Team --}}
        <div class="pixel-outline p-6">
            <div class="text-s uppercase tracking-[0.2em] text-slate-800 font-bold">Team</div>

            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Team size</label>
                    <input name="team_size" type="number" min="1" class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none" value="{{ old('team_size', $c?->team_size ?? '') }}" />
                    @error('team_size') <div class="mt-2 text-xs text-red-700">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Founded year</label>
                    <input name="founded_year" type="number" min="1800" max="{{ date('Y') }}" class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none" value="{{ old('founded_year', $c?->founded_year ?? '') }}" />
                    @error('founded_year') <div class="mt-2 text-xs text-red-700">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- Save --}}
        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" class="pixel-button px-6 py-3 text-xs">
                Save profile
            </button>

            @if($c)
                <div class="pixel-outline px-4 py-3 text-[11px] uppercase tracking-[0.2em] text-slate-600">
                    Company slug: <span class="text-slate-900 font-bold">{{ $c->slug }}</span>
                </div>
            @endif
        </div>
    </form>

    @push('head')
        <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
    @endpush
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-quill-wrapper]').forEach((wrapper) => {
                    const input = wrapper.querySelector('[data-quill-input]');
                    const editor = wrapper.querySelector('[data-quill-editor]');
                    const toolbar = wrapper.querySelector('[data-quill-toolbar]');

                    if (!input || !editor || !toolbar) {
                        return;
                    }

                    const quill = new Quill(editor, {
                        theme: 'snow',
                        modules: {
                            toolbar: false,
                        },
                    });

                    if (input.value) {
                        quill.clipboard.dangerouslyPasteHTML(input.value);
                    }

                    const sync = () => {
                        input.value = quill.root.innerHTML.trim();
                    };

                    quill.on('text-change', sync);

                    const form = wrapper.closest('form');
                    if (form) {
                        form.addEventListener('submit', sync);
                    }

                    toolbar.querySelectorAll('[data-quill-action]').forEach((button) => {
                        button.addEventListener('click', () => {
                            const action = button.dataset.quillAction;
                            if (action === 'ordered') {
                                quill.format('list', 'ordered');
                            } else if (action === 'bullet') {
                                quill.format('list', 'bullet');
                            } else if (action === 'link') {
                                const url = prompt('URL');
                                if (url) {
                                    const range = quill.getSelection(true);
                                    quill.format('link', url);
                                    if (range) {
                                        quill.setSelection(range.index + range.length, 0);
                                    }
                                }
                            } else if (action === 'clean') {
                                quill.removeFormat(0, quill.getLength());
                            } else {
                                quill.format(action, !quill.getFormat()[action]);
                            }
                        });
                    });
                });
            });
        </script>
    @endpush
</x-dashboard.layout>
