<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        $company = null;
        $effectiveCompanyId = method_exists($user, 'effectiveCompanyId') ? $user->effectiveCompanyId() : null;

        if ($effectiveCompanyId) {
            $company = Company::query()->find($effectiveCompanyId);
        }

        return view('dashboard.profile', [
            'user' => $user,
            'company' => $company,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $company = null;
        $effectiveCompanyId = method_exists($user, 'effectiveCompanyId') ? $user->effectiveCompanyId() : null;
        if ($effectiveCompanyId) {
            $company = Company::query()->find($effectiveCompanyId);
        }

        $isOwner = ($company && (int) $company->owner_user_id === (int) $user->id);

        // If user is member of a company but not owner: do NOT allow creating a new company here.
        if (! $company && method_exists($user, 'companyMembership') && $user->companyMembership()) {
            abort(403, 'You are already a member of a company. Company creation is not allowed here.');
        }

        $companyId = $company?->id;
        $isExistingCompany = (bool) $company;

        // Locked fields if company exists (and user is not owner => fully locked)
        $lockedLegalName = $company?->legal_name;
        $lockedIco = $company?->ico;

        $legalNameRules = ['required', 'string', 'max:255'];
        $icoRules = [
            'required',
            'string',
            'size:8',
            'regex:/^\d{8}$/',
            Rule::unique('companies', 'ico')->ignore($companyId),
        ];

        if ($isExistingCompany && (! $isOwner)) {
            // non-owner: cannot change identity fields at all
            if ($lockedLegalName !== null) $legalNameRules[] = Rule::in([$lockedLegalName]);
            if ($lockedIco !== null) $icoRules[] = Rule::in([$lockedIco]);
        }

        $data = $request->validate([
            // User
            'user_name' => ['required', 'string', 'max:120'],

            // Company Identity
            'legal_name' => $legalNameRules,
            'ico' => $icoRules,

            'dic' => ['nullable', 'string', 'max:10', Rule::unique('companies', 'dic')->ignore($companyId)],
            'ic_dph' => ['nullable', 'string', 'max:12', Rule::unique('companies', 'ic_dph')->ignore($companyId)],

            // Web / Contacts
            'website_url' => ['nullable', 'string', 'max:255'],
            'general_email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],

            // Branding
            'logo_path' => ['nullable', 'string', 'max:255'],
            'description_short' => ['nullable', 'string', 'max:280'],
            'bio' => ['nullable', 'string', 'max:5000'],

            'social_links' => ['nullable', 'json'],

            // Address
            'country_code' => ['required', 'string', 'size:2'],
            'region' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:255'],
            'street' => ['nullable', 'string', 'max:255'],

            // Contact person
            'contact_first_name' => ['nullable', 'string', 'max:255'],
            'contact_last_name' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:255'],

            // Team
            'team_size' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'founded_year' => ['nullable', 'integer', 'min:1800', 'max:' . (int) date('Y')],
        ], [
            'ico.size' => 'ICO must be exactly 8 digits.',
            'ico.regex' => 'ICO must contain only digits (8 digits).',
            'legal_name.in' => 'Legal name is locked.',
            'ico.in' => 'IČO is locked.',
        ]);

        foreach (['dic', 'ic_dph'] as $key) {
            if (array_key_exists($key, $data)) {
                $val = trim((string) $data[$key]);
                $data[$key] = ($val === '') ? null : $val;
            }
        }

        $socialLinks = null;
        if (!empty($data['social_links'])) {
            $decoded = json_decode($data['social_links'], true);
            $socialLinks = is_array($decoded) ? $decoded : null;
        }

        // Update user
        $user->name = $data['user_name'];
        $user->save();

        // Create company only if user has none AND is not member
        if (! $company) {
            $company = new Company();
            $company->owner_user_id = $user->id;
            $company->country_code = strtoupper($data['country_code'] ?? 'SK');
            $company->slug = $this->makeUniqueCompanySlug($data['legal_name']);
        }

        if (empty($company->slug)) {
            $company->slug = $this->makeUniqueCompanySlug($data['legal_name'], $company->id);
        }

        // Only owner can set identity fields
        if (! $isExistingCompany || $isOwner) {
            $company->legal_name = $data['legal_name'];
            $company->ico = $data['ico'];
        }

        // The rest: if member, you may still want to lock these later, but for now allow
        $company->dic = $data['dic'] ?? null;
        $company->ic_dph = $data['ic_dph'] ?? null;

        $company->website_url = $data['website_url'] ?? null;
        $company->general_email = $data['general_email'] ?? null;
        $company->phone = $data['phone'] ?? null;

        $company->logo_path = $data['logo_path'] ?? null;
        $company->description_short = $data['description_short'] ?? null;
        $company->bio = $data['bio'] ?? null;

        $company->social_links = $socialLinks;

        $company->country_code = strtoupper($data['country_code'] ?? 'SK');
        $company->region = $data['region'] ?? null;
        $company->city = $data['city'] ?? null;
        $company->postal_code = $data['postal_code'] ?? null;
        $company->street = $data['street'] ?? null;

        $company->contact_first_name = $data['contact_first_name'] ?? null;
        $company->contact_last_name = $data['contact_last_name'] ?? null;
        $company->contact_email = $data['contact_email'] ?? null;
        $company->contact_phone = $data['contact_phone'] ?? null;

        $company->team_size = $data['team_size'] ?? null;
        $company->founded_year = $data['founded_year'] ?? null;

        $company->save();

        // Ensure user's primary company_id aligns
        if ((int) $user->company_id !== (int) $company->id) {
            $user->company_id = $company->id;
            $user->save();
        }

        return back()->with('status', 'Profile updated successfully.');
    }

    private function makeUniqueCompanySlug(string $legalName, ?int $ignoreCompanyId = null): string
    {
        $base = Str::slug($legalName);
        if ($base === '') $base = 'company';

        $slug = $base;
        $i = 2;

        while ($this->companySlugExists($slug, $ignoreCompanyId)) {
            $slug = $base . '-' . $i;
            $i++;
            if ($i > 9999) {
                $slug = $base . '-' . Str::random(6);
                break;
            }
        }

        return $slug;
    }

    private function companySlugExists(string $slug, ?int $ignoreCompanyId = null): bool
    {
        $q = Company::query()->where('slug', $slug);
        if ($ignoreCompanyId) $q->where('id', '!=', $ignoreCompanyId);
        return $q->exists();
    }
}
