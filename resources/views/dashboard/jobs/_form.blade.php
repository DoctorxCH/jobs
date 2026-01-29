@php
    $sknicePositions = $sknicePositions ?? collect();
    $benefits = $benefits ?? collect();
    $drivingLicenseCategories = $drivingLicenseCategories ?? collect();
    $skills = $skills ?? collect();
    $educationLevels = $educationLevels ?? collect();
    $educationFields = $educationFields ?? collect();

    $educationFieldsGrouped = $educationFields->groupBy(function ($field) {
        return \Illuminate\Support\Str::before((string) $field->label, ' / ');
    });

    $countries = $countries ?? collect();
    $regions = $regions ?? collect();
    $cities = $cities ?? collect();
    $teamMembers = $teamMembers ?? collect();
@endphp

@php
    $job = $job ?? null;

    $selectedBenefits = old('benefits', $job?->benefits->pluck('id')->all() ?? []);
    $selectedLicenses = old('driving_license_categories', $job?->drivingLicenseCategories->pluck('id')->all() ?? []);

    $languageRows = old('job_languages');
    if ($languageRows === null) {
        $languageRows = $job?->jobLanguages
            ? $job->jobLanguages->map(fn ($lang) => [
                'language_code' => $lang->language_code,
                'level' => $lang->level,
            ])->values()->all()
            : [];
    }
    if (empty($languageRows)) {
        $languageRows = [['language_code' => '', 'level' => '']];
    }

    $skillRows = old('job_skills');
    if ($skillRows === null) {
        $skillRows = $job?->jobSkills
            ? $job->jobSkills->map(fn ($skill) => [
                'skill_id' => $skill->skill_id,
                'level' => $skill->level,
            ])->values()->all()
            : [];
    }
    if (empty($skillRows)) {
        $skillRows = [['skill_id' => '', 'level' => '']];
    }
@endphp

@php
    $sectionTitleClass = 'text-xs uppercase tracking-[0.2em] text-[var(--muted)]';
    $dividerClass = 'my-6 border-t-2 border-[var(--ink)]/10';
@endphp

<div class="space-y-6">

    {{-- 1) Basics --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between gap-4">
            <div class="text-xs uppercase tracking-[0.25em]">Basics</div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="{{ $sectionTitleClass }}">Title</label>
                <input type="text" name="title" value="{{ old('title', $job?->title) }}" class="mt-2 w-full pixel-outline px-3 py-2" required>
            </div>

            <div>
                <label class="{{ $sectionTitleClass }}">SKNICE Position</label>
                <select name="sknice_position_id" class="mt-2 w-full pixel-outline px-3 py-2" required>
                    <option value="">Select</option>
                    @foreach ($sknicePositions as $position)
                        <option value="{{ $position->id }}" @selected((string) old('sknice_position_id', $job?->sknice_position_id) === (string) $position->id)>
                            {{ $position->title }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="{{ $sectionTitleClass }}">Description</label>
            <textarea name="description" rows="7" class="mt-2 w-full pixel-outline px-3 py-2" required>{{ old('description', $job?->description) }}</textarea>
        </div>
    </div>

    <div class="{{ $dividerClass }}"></div>

    {{-- 2) Work --}}
    <div class="space-y-4">
        <div class="text-xs uppercase tracking-[0.25em]">Work</div>

        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <label class="{{ $sectionTitleClass }}">Employment type</label>
                <select name="employment_type" class="mt-2 w-full pixel-outline px-3 py-2" required>
                    <option value="">Select</option>
                    @foreach (['full_time' => 'Full time', 'part_time' => 'Part time', 'contract' => 'Contract', 'freelance' => 'Freelance', 'internship' => 'Internship'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('employment_type', $job?->employment_type) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="{{ $sectionTitleClass }}">Workload min (%)</label>
                <input type="number" name="workload_min" min="0" max="100" value="{{ old('workload_min', $job?->workload_min) }}" class="mt-2 w-full pixel-outline px-3 py-2" required>
            </div>

            <div>
                <label class="{{ $sectionTitleClass }}">Workload max (%)</label>
                <input type="number" name="workload_max" min="0" max="100" value="{{ old('workload_max', $job?->workload_max) }}" class="mt-2 w-full pixel-outline px-3 py-2" required>
            </div>
        </div>

        <div class="flex flex-wrap gap-4">
            <label class="flex items-center gap-2 text-xs uppercase tracking-[0.2em]">
                <input type="checkbox" name="is_remote" value="1" @checked(old('is_remote', $job?->is_remote))> Remote
            </label>
            <label class="flex items-center gap-2 text-xs uppercase tracking-[0.2em]">
                <input type="checkbox" name="is_hybrid" value="1" @checked(old('is_hybrid', $job?->is_hybrid))> Hybrid
            </label>
            <label class="flex items-center gap-2 text-xs uppercase tracking-[0.2em]">
                <input type="checkbox" name="travel_required" value="1" @checked(old('travel_required', $job?->travel_required))> Travel required
            </label>
            <label class="flex items-center gap-2 text-xs uppercase tracking-[0.2em]">
                <input type="checkbox" name="has_company_car" value="1" @checked(old('has_company_car', $job?->has_company_car))> Company car
            </label>
        </div>
    </div>

    <div class="{{ $dividerClass }}"></div>

    {{-- 3) Location --}}
    <div class="space-y-4">
        <div class="text-xs uppercase tracking-[0.25em]">Location</div>

        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <label class="{{ $sectionTitleClass }}">Country</label>
                <select name="country_id" id="country-select" class="mt-2 w-full pixel-outline px-3 py-2" required>
                    <option value="">Select</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}" @selected((string) old('country_id', $job?->country_id) === (string) $country->id)>{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="{{ $sectionTitleClass }}">Region</label>
                <select name="region_id" id="region-select" class="mt-2 w-full pixel-outline px-3 py-2" required>
                    <option value="">Select</option>
                    @foreach ($regions as $region)
                        <option value="{{ $region->id }}" data-country="{{ $region->country_id }}" @selected((string) old('region_id', $job?->region_id) === (string) $region->id)>{{ $region->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="{{ $sectionTitleClass }}">City</label>
                <select name="city_id" id="city-select" class="mt-2 w-full pixel-outline px-3 py-2" required>
                    <option value="">Select</option>
                    @foreach ($cities as $city)
                        <option value="{{ $city->id }}" data-region="{{ $city->region_id }}" @selected((string) old('city_id', $job?->city_id) === (string) $city->id)>{{ $city->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="{{ $dividerClass }}"></div>

    {{-- 4) Dates & positions --}}
    <div class="space-y-4">
        <div class="text-xs uppercase tracking-[0.25em]">Dates & positions</div>

        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <label class="{{ $sectionTitleClass }}">Available from</label>
                <input type="date" name="available_from" value="{{ old('available_from', optional($job?->available_from)->format('Y-m-d')) }}" class="mt-2 w-full pixel-outline px-3 py-2">
            </div>

            <div>
                <label class="{{ $sectionTitleClass }}">Application deadline</label>
                <input type="date" name="application_deadline" value="{{ old('application_deadline', optional($job?->application_deadline)->format('Y-m-d')) }}" class="mt-2 w-full pixel-outline px-3 py-2">
            </div>

            <div>
                <label class="{{ $sectionTitleClass }}">Open positions</label>
                <input type="number" name="open_positions" min="1" value="{{ old('open_positions', $job?->open_positions ?? 1) }}" class="mt-2 w-full pixel-outline px-3 py-2" required>
            </div>
        </div>
    </div>

    <div class="{{ $dividerClass }}"></div>

    {{-- 5) Salary --}}
    <div class="space-y-4">
        <div class="text-xs uppercase tracking-[0.25em]">Salary</div>

        <div class="grid gap-4 md:grid-cols-4">
            <div>
                <label class="{{ $sectionTitleClass }}">Salary min (gross/month)</label>
                <input type="number" name="salary_min_gross_month" min="0" value="{{ old('salary_min_gross_month', $job?->salary_min_gross_month) }}" class="mt-2 w-full pixel-outline px-3 py-2">
            </div>

            <div>
                <label class="{{ $sectionTitleClass }}">Salary max (gross/month)</label>
                <input type="number" name="salary_max_gross_month" min="0" value="{{ old('salary_max_gross_month', $job?->salary_max_gross_month) }}" class="mt-2 w-full pixel-outline px-3 py-2">
            </div>

            <div>
                <label class="{{ $sectionTitleClass }}">Currency</label>
                <input type="text" name="salary_currency" value="{{ old('salary_currency', $job?->salary_currency ?? 'EUR') }}" class="mt-2 w-full pixel-outline px-3 py-2" maxlength="3">
            </div>

            <div>
                <label class="{{ $sectionTitleClass }}">Salary months</label>
                <select name="salary_months" class="mt-2 w-full pixel-outline px-3 py-2">
                    <option value="">Select</option>
                    @foreach (['12', '13'] as $value)
                        <option value="{{ $value }}" @selected(old('salary_months', $job?->salary_months) === $value)>{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="{{ $sectionTitleClass }}">Salary note</label>
            <input type="text" name="salary_note" value="{{ old('salary_note', $job?->salary_note) }}" class="mt-2 w-full pixel-outline px-3 py-2">
        </div>
    </div>

    <div class="{{ $dividerClass }}"></div>

    {{-- 6) Requirements --}}
    <div class="space-y-4">
        <div class="text-xs uppercase tracking-[0.25em]">Requirements</div>

        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <label class="{{ $sectionTitleClass }}">Education level</label>
                <select name="education_level_id" class="mt-2 w-full pixel-outline px-3 py-2">
                    <option value="">Select</option>
                    @foreach ($educationLevels as $level)
                        <option value="{{ $level->id }}" @selected((string) old('education_level_id', $job?->education_level_id) === (string) $level->id)>{{ $level->label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="{{ $sectionTitleClass }}">Education field</label>
                <select name="education_field_id" class="mt-2 w-full pixel-outline px-3 py-2">
                    <option value="">Select</option>
                    @foreach ($educationFieldsGrouped as $category => $fields)
                        <optgroup label="{{ $category }}">
                            @foreach ($fields as $field)
                                <option value="{{ $field->id }}" @selected((string) old('education_field_id', $job?->education_field_id) === (string) $field->id)>
                                    {{ trim(\Illuminate\Support\Str::after($field->label, ' / ')) }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="{{ $sectionTitleClass }}">Min years experience</label>
                <input type="number" name="min_years_experience" min="0" value="{{ old('min_years_experience', $job?->min_years_experience) }}" class="mt-2 w-full pixel-outline px-3 py-2">
            </div>
        </div>

        <div class="flex flex-wrap gap-4">
            <label class="flex items-center gap-2 text-xs uppercase tracking-[0.2em]">
                <input type="checkbox" name="is_for_graduates" value="1" @checked(old('is_for_graduates', $job?->is_for_graduates))> For graduates
            </label>
            <label class="flex items-center gap-2 text-xs uppercase tracking-[0.2em]">
                <input type="checkbox" name="is_for_disabled" value="1" @checked(old('is_for_disabled', $job?->is_for_disabled))> For disabled candidates
            </label>
        </div>

        <div>
            <label class="{{ $sectionTitleClass }}">Candidate note</label>
            <textarea name="candidate_note" rows="4" class="mt-2 w-full pixel-outline px-3 py-2">{{ old('candidate_note', $job?->candidate_note) }}</textarea>
        </div>
    </div>

    <div class="{{ $dividerClass }}"></div>

    {{-- 7) HR / Contact --}}
    <div class="space-y-4">
        <div class="text-xs uppercase tracking-[0.25em]">HR / Contact</div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="{{ $sectionTitleClass }}">HR team member</label>
                <select name="hr_team_member_id" class="mt-2 w-full pixel-outline px-3 py-2">
                    <option value="">Select</option>
                    @foreach ($teamMembers as $member)
                        <option value="{{ $member->id }}" @selected((string) old('hr_team_member_id', $job?->hr_team_member_id) === (string) $member->id)>
                            {{ $member->user?->name ?? 'Team member' }} ({{ $member->user?->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="{{ $sectionTitleClass }}">Employer reference</label>
                <input type="text" name="employer_reference" value="{{ old('employer_reference', $job?->employer_reference) }}" class="mt-2 w-full pixel-outline px-3 py-2" maxlength="80">
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="{{ $sectionTitleClass }}">HR email</label>
                <input type="email" name="hr_email" value="{{ old('hr_email', $job?->hr_email) }}" class="mt-2 w-full pixel-outline px-3 py-2">
            </div>
            <div>
                <label class="{{ $sectionTitleClass }}">HR phone</label>
                <input type="text" name="hr_phone" value="{{ old('hr_phone', $job?->hr_phone) }}" class="mt-2 w-full pixel-outline px-3 py-2">
            </div>
        </div>
    </div>

    <div class="{{ $dividerClass }}"></div>

    {{-- 8) Benefits & Licenses --}}
    <div class="space-y-4">
        <div class="text-xs uppercase tracking-[0.25em]">Benefits & Licenses</div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="{{ $sectionTitleClass }}">Benefits</label>

                <input type="text" id="benefit-search" placeholder="Search..." class="mt-2 w-full pixel-outline px-3 py-2">

                <div id="benefit-list" class="mt-2 pixel-outline p-3 max-h-56 overflow-auto space-y-2">
                    @foreach ($benefits as $benefit)
                        @php($benefitLabel = (string) $benefit->label)
                        <label class="flex items-center gap-2 benefit-item" data-label="{{ \Illuminate\Support\Str::lower($benefitLabel) }}">
                            <input type="checkbox" name="benefits[]" value="{{ $benefit->id }}" @checked(in_array($benefit->id, $selectedBenefits, true))>
                            <span class="text-sm">{{ $benefitLabel }}</span>
                        </label>
                    @endforeach

                    @if ($benefits->isEmpty())
                        <div class="text-sm opacity-70">No benefits available.</div>
                    @endif
                </div>
            </div>

            <div>
                <label class="{{ $sectionTitleClass }}">Driving license categories</label>

                <input type="text" id="license-search" placeholder="Search..." class="mt-2 w-full pixel-outline px-3 py-2">

                <div id="license-list" class="mt-2 pixel-outline p-3 max-h-56 overflow-auto space-y-2">
                    @foreach ($drivingLicenseCategories as $license)
                        @php($licenseText = trim((string) $license->code . " - " . (string) $license->label))
                        <label class="flex items-center gap-2 license-item" data-label="{{ \Illuminate\Support\Str::lower($licenseText) }}">
                            <input type="checkbox" name="driving_license_categories[]" value="{{ $license->id }}" @checked(in_array($license->id, $selectedLicenses, true))>
                            <span class="text-sm">{{ $licenseText }}</span>
                        </label>
                    @endforeach

                    @if ($drivingLicenseCategories->isEmpty())
                        <div class="text-sm opacity-70">No licenses available.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="text-[11px] opacity-70">Tip: Type to filter. Click to toggle.</div>
    </div>

    <div class="{{ $dividerClass }}"></div>
    {{-- 9) Languages --}}
    <div class="space-y-4">
        <div class="text-xs uppercase tracking-[0.25em]">Languages</div>

        <div id="language-rows" class="space-y-2">
            @foreach ($languageRows as $index => $row)
                <div class="grid gap-2 md:grid-cols-[1fr_1fr_auto] items-start">
                    <select name="job_languages[{{ $index }}][language_code]" class="w-full pixel-outline px-3 py-2">
                        <option value="">Language</option>
                        @foreach ($languageOptions as $code => $label)
                            <option value="{{ $code }}" @selected(($row['language_code'] ?? '') === $code)>{{ $label }}</option>
                        @endforeach
                    </select>

                    <select name="job_languages[{{ $index }}][level]" class="w-full pixel-outline px-3 py-2">
                        <option value="">Level</option>
                        @foreach ($languageLevels as $level)
                            <option value="{{ $level }}" @selected(($row['level'] ?? '') === $level)>{{ $level }}</option>
                        @endforeach
                    </select>

                    <button type="button" class="pixel-outline px-3 py-2 text-xs remove-language">Remove</button>
                </div>
            @endforeach
        </div>

        <div class="flex items-center gap-3">
            <button type="button" id="add-language" class="pixel-outline px-4 py-2 text-xs uppercase tracking-[0.2em]">Add language</button>
            <div class="text-[11px] opacity-70">Add multiple languages with level.</div>
        </div>
    </div>

    <div class="{{ $dividerClass }}"></div>

    {{-- 10) Skills --}}
    <div class="space-y-4">
        <div class="text-xs uppercase tracking-[0.25em]">Skills</div>

        <div id="skill-rows" class="space-y-2">
            @foreach ($skillRows as $index => $row)
                <div class="grid gap-2 md:grid-cols-[1fr_1fr_auto] items-start">
                    <select name="job_skills[{{ $index }}][skill_id]" class="w-full pixel-outline px-3 py-2">
                        <option value="">Skill</option>
                        @foreach ($skills as $skill)
                            <option value="{{ $skill->id }}" @selected((string) ($row['skill_id'] ?? '') === (string) $skill->id)>{{ $skill->name }}</option>
                        @endforeach
                    </select>

                    <select name="job_skills[{{ $index }}][level]" class="w-full pixel-outline px-3 py-2">
                        <option value="">Level</option>
                        @foreach ($skillLevels as $level)
                            <option value="{{ $level }}" @selected(($row['level'] ?? '') === $level)>{{ $level }}</option>
                        @endforeach
                    </select>

                    <button type="button" class="pixel-outline px-3 py-2 text-xs remove-skill">Remove</button>
                </div>
            @endforeach
        </div>

        <div class="flex items-center gap-3">
            <button type="button" id="add-skill" class="pixel-outline px-4 py-2 text-xs uppercase tracking-[0.2em]">Add skill</button>
            <div class="text-[11px] opacity-70">Add multiple skills with level.</div>
        </div>
    </div>

</div>

<script>
    const countrySelect = document.getElementById('country-select');
    const regionSelect = document.getElementById('region-select');
    const citySelect = document.getElementById('city-select');

    function filterRegions() {
        const countryId = String(countrySelect?.value || '');

        Array.from(regionSelect.options).forEach((option) => {
            if (!option.value) { option.hidden = false; return; } // "Select" always visible
            const optCountry = String(option.dataset.country || '');
            option.hidden = countryId !== '' && optCountry !== countryId;
        });

        if (countryId && regionSelect.selectedOptions[0]?.hidden) {
            regionSelect.value = '';
        }

        filterCities();
    }

    function filterCities() {
        const regionId = String(regionSelect?.value || '');

        Array.from(citySelect.options).forEach((option) => {
            if (!option.value) { option.hidden = false; return; } // "Select" always visible
            const optRegion = String(option.dataset.region || '');
            option.hidden = regionId !== '' && optRegion !== regionId;
        });

        if (regionId && citySelect.selectedOptions[0]?.hidden) {
            citySelect.value = '';
        }
    }

    countrySelect?.addEventListener('change', filterRegions);
    regionSelect?.addEventListener('change', filterCities);
    filterRegions();

    const languageRowsEl = document.getElementById('language-rows');
    const skillRowsEl = document.getElementById('skill-rows');

    function addRow(container, template) {
        const index = container.children.length;
        container.insertAdjacentHTML('beforeend', template(index));
    }

    document.getElementById('add-language')?.addEventListener('click', () => {
        addRow(languageRowsEl, (index) => `
            <div class="grid gap-2 md:grid-cols-[1fr_1fr_auto] items-start">
                <select name="job_languages[${index}][language_code]" class="w-full pixel-outline px-3 py-2">
                    <option value="">Language</option>
                    ${Object.entries(@json($languageOptions)).map(([code,label]) => `<option value="${code}">${label}</option>`).join('')}
                </select>
                <select name="job_languages[${index}][level]" class="w-full pixel-outline px-3 py-2">
                    <option value="">Level</option>
                    ${@json($languageLevels).map(level => `<option value="${level}">${level}</option>`).join('')}
                </select>
                <button type="button" class="pixel-outline px-3 py-2 text-xs remove-language">Remove</button>
            </div>
        `);
    });

    document.getElementById('add-skill')?.addEventListener('click', () => {
        addRow(skillRowsEl, (index) => `
            <div class="grid gap-2 md:grid-cols-[1fr_1fr_auto] items-start">
                <select name="job_skills[${index}][skill_id]" class="w-full pixel-outline px-3 py-2">
                    <option value="">Skill</option>
                    ${@json($skills->map(fn ($skill) => ['id' => $skill->id, 'name' => $skill->name])->values()).map(skill => `<option value="${skill.id}">${skill.name}</option>`).join('')}
                </select>
                <select name="job_skills[${index}][level]" class="w-full pixel-outline px-3 py-2">
                    <option value="">Level</option>
                    ${@json($skillLevels).map(level => `<option value="${level}">${level}</option>`).join('')}
                </select>
                <button type="button" class="pixel-outline px-3 py-2 text-xs remove-skill">Remove</button>
            </div>
        `);
    });

    document.addEventListener('click', (event) => {
        if (event.target.classList.contains('remove-language')) {
            event.target.closest('.grid')?.remove();
        }
        if (event.target.classList.contains('remove-skill')) {
            event.target.closest('.grid')?.remove();
        }
    });
</script>
