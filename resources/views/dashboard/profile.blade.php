{{-- resources/views/dashboard/profile.blade.php --}}

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
                        required
                    >
                        @php($cc = old('country_code', $c?->country_code ?? 'SK'))
                        <option value="AT" @selected($cc==='AT')>Austria</option>
                        <option value="BE" @selected($cc==='BE')>Belgium</option>
                        <option value="BG" @selected($cc==='BG')>Bulgaria</option>
                        <option value="HR" @selected($cc==='HR')>Croatia</option>
                        <option value="CY" @selected($cc==='CY')>Cyprus</option>
                        <option value="CZ" @selected($cc==='CZ')>Czech Republic</option>
                        <option value="DK" @selected($cc==='DK')>Denmark</option>
                        <option value="EE" @selected($cc==='EE')>Estonia</option>
                        <option value="FI" @selected($cc==='FI')>Finland</option>
                        <option value="FR" @selected($cc==='FR')>France</option>
                        <option value="DE" @selected($cc==='DE')>Germany</option>
                        <option value="GB" @selected($cc==='GB')>United Kingdom</option>
                        <option value="GR" @selected($cc==='GR')>Greece</option>
                        <option value="HU" @selected($cc==='HU')>Hungary</option>
                        <option value="IE" @selected($cc==='IE')>Ireland</option>
                        <option value="IT" @selected($cc==='IT')>Italy</option>
                        <option value="LV" @selected($cc==='LV')>Latvia</option>
                        <option value="LT" @selected($cc==='LT')>Lithuania</option>
                        <option value="LU" @selected($cc==='LU')>Luxembourg</option>
                        <option value="MT" @selected($cc==='MT')>Malta</option>
                        <option value="NL" @selected($cc==='NL')>Netherlands</option>
                        <option value="PL" @selected($cc==='PL')>Poland</option>
                        <option value="PT" @selected($cc==='PT')>Portugal</option>
                        <option value="RO" @selected($cc==='RO')>Romania</option>
                        <option value="SK" @selected($cc==='SK')>Slovakia</option>
                        <option value="SI" @selected($cc==='SI')>Slovenia</option>
                        <option value="ES" @selected($cc==='ES')>Spain</option>
                        <option value="SE" @selected($cc==='SE')>Sweden</option>
                        <option value="00" @selected($cc==='00')>Other</option>
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

                    <div class="mt-2" data-quill-wrapper>
                        <input type="hidden" name="bio" value="{{ old('bio', $c?->bio ?? '') }}" data-quill-input>

                        <div class="pixel-quill">
                            <div data-quill-toolbar></div>
                            <div class="min-h-[220px] text-sm text-slate-900" data-quill-editor></div>
                        </div>
                    </div>

                    @error('bio')
                        <div class="mt-2 text-xs text-red-700">{{ $message }}</div>
                    @enderror
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
        <style>
            /* Keep Quill icons visible even with global button resets */
            .ql-snow .ql-toolbar button,
            .ql-snow.ql-toolbar button { background-color: transparent !important; }

            .ql-snow .ql-stroke { stroke: currentColor !important; }
            .ql-snow .ql-fill   { fill: currentColor !important; }

            /* Fit Pixel frame */
            .ql-toolbar.ql-snow { border: 0 !important; border-bottom: 1px solid rgba(15,23,42,.15) !important; }
            .ql-container.ql-snow { border: 0 !important; }
            .ql-editor { min-height: 220px; }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-quill-wrapper]').forEach((wrapper) => {
                    const input = wrapper.querySelector('[data-quill-input]');
                    const editor = wrapper.querySelector('[data-quill-editor]');
                    const toolbar = wrapper.querySelector('[data-quill-toolbar]');

                    if (!input || !editor || !toolbar) return;

                    const toolbarOptions = [
                        [{ header: [1, 2, 3, false] }],
                        [{ font: ['Mono Space', 'roboto', 'helvetica', 'arial', 'sans'] }],
                    //    [{ size: ['small', false, 'large'] }],
                        ['bold', 'italic', 'underline'],
                        [{ color: [] }, { background: [] }],
                    //    [{ script: 'sub' }, { script: 'super' }],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        [{ indent: '-1' }, { indent: '+1' }],
                        [{ align: [] }],
                        ['blockquote', 'code-block'],
                    //    ['link'],
                        ['clean'],
                    ];

                    const Font = Quill.import('formats/font');
                    Font.whitelist = ['Mono Space', 'roboto', 'helvetica', 'arial', 'sans'];
                    Quill.register(Font, true);

                    const quill = new Quill(editor, {
                        theme: 'snow',
                        modules: {
                            toolbar: {
                                container: toolbar,
                                handlers: {},
                            }
                        }
                    });

                    // IMPORTANT: build toolbar DOM BEFORE Quill scans it
                    // -> easiest: let Quill build it via array config by creating a temp Quill, OR:
                    // We rebuild correctly by re-initializing after setting toolbar HTML.
                    // Instead: init directly with array config on this toolbar container:
                    quill.destroy?.();

                    const quill2 = new Quill(editor, {
                        theme: 'snow',
                        modules: {
                            toolbar: toolbarOptions,
                        }
                    });

                    if (input.value) {
                        quill2.clipboard.dangerouslyPasteHTML(input.value);
                    }

                    const sync = () => {
                        input.value = quill2.root.innerHTML.trim();
                    };

                    quill2.on('text-change', sync);

                    const form = wrapper.closest('form');
                    if (form) form.addEventListener('submit', sync);
                });
            });
        </script>
    @endpush
</x-dashboard.layout>
