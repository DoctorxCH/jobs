<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Services\CompanyLookup\CompanyLookupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    private const REG_KEY = 'reg';

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('frontend.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $remember = (bool) ($data['remember'] ?? false);

        if (! Auth::attempt(['email' => $data['email'], 'password' => $data['password']], $remember)) {
            return back()
                ->withErrors(['email' => 'Login failed.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->route('frontend.dashboard');
    }

    /** /register -> step1 */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('frontend.dashboard');
        }

        return redirect()->route('frontend.register.step1');
    }

    /* ---------------------------
     | Registration Step 1 (ICO lookup)
     |---------------------------*/

    public function showRegisterStep1(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('frontend.dashboard');
        }

        $reg = $request->session()->get(self::REG_KEY, []);

        return view('auth.register-step1', [
            'reg' => $reg,
        ]);
    }

    public function postRegisterStep1(Request $request, \App\Services\CompanyLookup\CompanyLookupService $lookup)
    {
        if (Auth::check()) {
            return redirect()->route('frontend.dashboard');
        }

        $data = $request->validate([
            'ico' => ['required', 'string', 'regex:/^\d{8}$/'],
            'country_code' => ['nullable', 'string', 'size:2'],
        ], [
            'ico.regex' => 'ICO must be exactly 8 digits.',
        ]);

        $ico = (string) $data['ico'];

        // uniqueness
        if (\App\Models\Company::where('ico', $ico)->exists()) {
            return back()->withErrors(['ico' => 'This company is already registered.'])->withInput();
        }

        $country = strtoupper($data['country_code'] ?? 'SK');

        $prefill = $lookup->lookup($country, $ico);
        if (! $prefill) {
            return back()->withErrors(['ico' => 'Company not found.'])->withInput();
        }

        $reg = $request->session()->get(self::REG_KEY, []);

        $reg['locked'] = [
            'ico' => $ico,
            'country_code' => $country,
            'legal_name' => $prefill['legal_name'] ?? null,
            'street' => $prefill['address']['street'] ?? null,
            'postal_code' => $prefill['address']['postal_code'] ?? null,
            'city' => $prefill['address']['city'] ?? null,
        ];

        // optional from api (not locked)
        $reg['api'] = [
            'dic' => $prefill['dic'] ?? null,
            'ic_dph' => $prefill['ic_dph'] ?? null,
        ];

        $request->session()->put(self::REG_KEY, $reg);

        return redirect()->route('frontend.register.step2');
    }

    /* ---------------------------
     | Registration Step 2 (Company fields)
     |---------------------------*/

    public function showRegisterStep2(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('frontend.dashboard');
        }

        $reg = $request->session()->get(self::REG_KEY, []);
        if (empty($reg['locked']['ico'])) {
            return redirect()->route('frontend.register.step1');
        }

        $locked = $reg['locked'] ?? [];
        $api = $reg['api'] ?? [];
        $saved = $reg['company'] ?? [];

        // values priority: saved (from step2 post) -> api -> locked
        $values = array_merge($locked, $api, $saved);

        return view('auth.register-step2', [
            'reg' => $reg,
            'locked' => $locked,
            'values' => $values,
        ]);
    }
    
    public function postRegisterStep2(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('frontend.dashboard');
        }

        $reg = $request->session()->get(self::REG_KEY, []);
        if (empty($reg['locked']['ico'])) {
            return redirect()->route('frontend.register.step1');
        }

        $locked = $reg['locked'];

        // Validate ONLY editable fields + required fields
        $data = $request->validate([
            // optional ids
            'dic' => ['nullable', 'string', 'max:10'],
            'ic_dph' => ['nullable', 'string', 'max:12'],

            // REQUIRED
            'general_email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],

            'contact_first_name' => ['required', 'string', 'max:255'],
            'contact_last_name' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:255'],

            'team_size' => ['required', 'integer', 'min:1', 'max:100000'],
            'founded_year' => ['required', 'integer', 'min:1800', 'max:' . (int) date('Y')],

            // optional
            'website_url' => ['nullable', 'string', 'max:255'],
            'region' => ['nullable', 'string', 'max:255'],
        ]);

        // normalize dic/ic_dph (empty string => null)
        foreach (['dic', 'ic_dph'] as $k) {
            if (array_key_exists($k, $data)) {
                $v = trim((string) $data[$k]);
                $data[$k] = ($v === '') ? null : $v;
            }
        }

        // Hard uniqueness re-check
        $ico = (string) $locked['ico'];
        if (Company::where('ico', $ico)->exists()) {
            return redirect()->route('frontend.register.step1')
                ->withErrors(['ico' => 'This company is already registered.']);
        }

        if (!empty($data['dic']) && Company::where('dic', $data['dic'])->exists()) {
            return back()->withErrors(['dic' => 'DIC is already used.'])->withInput();
        }

        if (!empty($data['ic_dph']) && Company::where('ic_dph', $data['ic_dph'])->exists()) {
            return back()->withErrors(['ic_dph' => 'IC DPH is already used.'])->withInput();
        }

        // Store step2 data (editable only). Locked stays separate.
        $reg['company'] = $data;
        $request->session()->put(self::REG_KEY, $reg);

        return redirect()->route('frontend.register.step3');
    }

    /* ---------------------------
     | Registration Step 3 (Account + create)
     |---------------------------*/

    public function showRegisterStep3(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('frontend.dashboard');
        }

        $reg = $request->session()->get(self::REG_KEY, []);
            if (empty($reg['locked']['ico']) || empty($reg['company'])) {
            return redirect()->route('frontend.register.step1');
        }

        return view('auth.register-step3', [
            'reg' => $reg,
        ]);
    }

    public function postRegisterStep3(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('frontend.dashboard');
        }

        $reg = $request->session()->get(self::REG_KEY, []);

        // must have step1 lookup + step2 data
        if (empty($reg['locked']['ico']) || empty($reg['locked']['legal_name']) || empty($reg['company'])) {
            return redirect()->route('frontend.register.step1');
        }

        $locked = $reg['locked'];
        $companyData = $reg['company'];

        $account = $request->validate([
            'name' => ['required', 'string', 'max:120'],

            // optional: prefill in blade, but still validate here
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],

            'password' => ['required', 'confirmed', Password::min(10)],
        ]);

        // final uniqueness guard
        $ico = (string) $locked['ico'];
        if (Company::where('ico', $ico)->exists()) {
            return redirect()->route('frontend.register.step1')
                ->withErrors(['ico' => 'This company is already registered.']);
        }

        Role::firstOrCreate(['name' => 'company.owner', 'guard_name' => 'web']);

        $user = null;

        DB::transaction(function () use (&$user, $account, $companyData, $locked) {
            $user = User::create([
                'name' => $account['name'],
                'email' => $account['email'],
                'password' => Hash::make($account['password']),
            ]);

            $user->assignRole('company.owner');

            $slug = $this->makeUniqueCompanySlug((string) $locked['legal_name']);

            $company = Company::create([
                'owner_user_id' => $user->id,
                'category_id' => null,

                // LOCKED FROM API
                'legal_name' => $locked['legal_name'],
                'slug' => $slug,
                'ico' => $locked['ico'],
                'country_code' => strtoupper($locked['country_code'] ?? 'SK'),
                'street' => $locked['street'] ?? null,
                'postal_code' => $locked['postal_code'] ?? null,
                'city' => $locked['city'] ?? null,

                // editable / required from step2
                'dic' => $companyData['dic'] ?? null,
                'ic_dph' => $companyData['ic_dph'] ?? null,

                'website_url' => $companyData['website_url'] ?? null,
                'general_email' => $companyData['general_email'],
                'phone' => $companyData['phone'],

                'region' => $companyData['region'] ?? null,

                'contact_first_name' => $companyData['contact_first_name'],
                'contact_last_name' => $companyData['contact_last_name'],
                'contact_email' => $companyData['contact_email'],
                'contact_phone' => $companyData['contact_phone'],

                'team_size' => $companyData['team_size'],
                'founded_year' => $companyData['founded_year'],

                'status' => 'pending',
                'active' => true,
            ]);

            // Pivot membership
            if (method_exists($user, 'companies')) {
                $user->companies()->syncWithoutDetaching([
                    $company->id => [
                        'role' => 'owner',
                        'status' => 'active',
                        'accepted_at' => now(),
                    ],
                ]);
            }

            // legacy columns only if they exist
            if (Schema::hasColumn('users', 'company_id')) {
                $user->company_id = $company->id;
            }
            if (Schema::hasColumn('users', 'is_company_owner')) {
                $user->is_company_owner = true;
            }
            if ($user->isDirty()) {
                $user->save();
            }
        });

        Auth::login($user);
        $request->session()->regenerate();

        // clear registration session state
        $request->session()->forget(self::REG_KEY);

        return redirect()->route('frontend.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('frontend.login');
    }

    private function makeUniqueCompanySlug(string $legalName): string
    {
        $base = Str::slug($legalName);
        if ($base === '') $base = 'company';

        $slug = $base;
        $i = 2;

        while (Company::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;
            if ($i > 9999) {
                $slug = $base . '-' . Str::random(6);
                break;
            }
        }

        return $slug;
    }
}
