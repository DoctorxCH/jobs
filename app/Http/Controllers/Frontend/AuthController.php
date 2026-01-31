<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CompanyLookup\CompanyLookupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\Company;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    private const REG_KEY = 'register.steps';

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

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('frontend.dashboard');
        }

        return redirect()->route('frontend.register.step1');
    }

    public function showRegisterStep1()
    {
        if (Auth::check()) {
            return redirect()->route('frontend.dashboard');
        }

        $reg = session(self::REG_KEY, []);

        return view('auth.register-step1', [
            'reg' => $reg,
        ]);
    }

    public function postRegisterStep1(Request $request, CompanyLookupService $lookup)
    {
        if (Auth::check()) {
            return redirect()->route('frontend.dashboard');
        }

        $data = $request->validate([
            'ico' => ['required', 'regex:/^\d{8}$/'],
            'country_code' => ['nullable', 'string', 'size:2'],
        ], [
            'ico.regex' => 'ICO must be exactly 8 digits.',
        ]);

        $ico = (string) $data['ico'];
        if (Company::where('ico', $ico)->exists()) {
            return back()->withErrors(['ico' => 'This company is already registered.'])->withInput();
        }

        $country = strtoupper($data['country_code'] ?? 'SK');
        $prefill = $lookup->lookup($country, $ico);

        if (!$prefill) {
            return back()->withErrors(['ico' => 'Company not found.'])->withInput();
        }

        $reg = $request->session()->get(self::REG_KEY, []);
        $reg['step1'] = [
            'ico' => $ico,
            'country_code' => $country,
        ];

        $toString = static function ($value): ?string {
            if (is_array($value)) {
                $filtered = array_filter($value, fn ($v) => is_scalar($v) && (string) $v !== '');
                return $filtered ? implode(', ', array_map('strval', $filtered)) : null;
            }

            return is_scalar($value) ? (string) $value : null;
        };

        $address = $prefill['address'] ?? null;
        $addressStreet = null;
        $addressCity = null;
        $addressPostal = null;
        if (is_array($address)) {
            $addressStreet = $address['street'] ?? $address['line1'] ?? null;
            $addressCity = $address['city'] ?? $address['town'] ?? $address['municipality'] ?? null;
            $addressPostal = $address['postal_code'] ?? $address['zip'] ?? $address['psc'] ?? null;
        }

        $street = $toString($prefill['street'] ?? $addressStreet ?? $address ?? null);
        if ($street) {
            $street = trim(preg_replace('/\s*\/0\s*/', ' ', $street));
        }
        $city = $toString($prefill['city'] ?? $prefill['town'] ?? $prefill['municipality'] ?? $addressCity ?? null);
        $postal = $toString($prefill['postal_code'] ?? $prefill['zip'] ?? $prefill['psc'] ?? $addressPostal ?? null);

        if ($street && (empty($city) || empty($postal)) && str_contains($street, ',')) {
            [$streetPart, $cityPart] = array_map('trim', explode(',', $street, 2));
            if ($streetPart !== '') {
                $street = $streetPart;
            }

            if ($cityPart !== '') {
                if (empty($postal) && preg_match('/\b\d{3}\s?\d{2}\b/', $cityPart, $m)) {
                    $postal = str_replace(' ', '', $m[0]);
                    $cityPart = trim(str_replace($m[0], '', $cityPart));
                }

                if (empty($city)) {
                    $city = $cityPart;
                }
            }
        }

        $reg['locked'] = [
            'ico' => $ico,
            'country_code' => $country,
            'legal_name' => $toString($prefill['legal_name'] ?? $prefill['name'] ?? null),
            'street' => $street,
            'postal_code' => $postal,
            'city' => $city,
        ];

        $request->session()->put(self::REG_KEY, $reg);

        return redirect()->route('frontend.register.step2');
    }

    public function showRegisterStep2()
    {
        if (Auth::check()) {
            return redirect()->route('frontend.dashboard');
        }

        $reg = session(self::REG_KEY, []);
        if (empty($reg['locked'])) {
            return redirect()->route('frontend.register.step1');
        }

        return view('auth.register-step2', [
            'locked' => $reg['locked'],
            'values' => $reg['company'] ?? [],
        ]);
    }

    public function postRegisterStep2(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('frontend.dashboard');
        }

        $reg = $request->session()->get(self::REG_KEY, []);
        if (empty($reg['locked'])) {
            return redirect()->route('frontend.register.step1');
        }

        $data = $request->validate([
            // Required
            'bio' => ['required', 'string', 'max:2000'],
            'general_email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'contact_first_name' => ['required', 'string', 'max:120'],
            'contact_last_name' => ['required', 'string', 'max:120'],
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:50'],
            'team_size' => ['required', 'integer', 'min:1'],
            'founded_year' => ['required', 'integer', 'min:1800', 'max:' . date('Y')],

            // Optional
            'dic' => ['nullable', 'string', 'max:32'],
            'ic_dph' => ['nullable', 'string', 'max:32'],
            'website_url' => ['nullable', 'string', 'max:255'],
            'region' => ['nullable', 'string', 'max:255'],
        ]);

        foreach (['dic', 'ic_dph'] as $k) {
            if (array_key_exists($k, $data) && $data[$k] === '') {
                $data[$k] = null;
            }
        }

        if (!empty($data['dic']) && Company::where('dic', $data['dic'])->exists()) {
            return back()->withErrors(['dic' => 'DIC is already used.'])->withInput();
        }

        if (!empty($data['ic_dph']) && Company::where('ic_dph', $data['ic_dph'])->exists()) {
            return back()->withErrors(['ic_dph' => 'IC DPH is already used.'])->withInput();
        }

        $reg['company'] = $data;
        $request->session()->put(self::REG_KEY, $reg);

        return redirect()->route('frontend.register.step3');
    }

    public function showRegisterStep3()
    {
        if (Auth::check()) {
            return redirect()->route('frontend.dashboard');
        }

        $reg = session(self::REG_KEY, []);
        if (empty($reg['locked']) || empty($reg['company'])) {
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
        if (empty($reg['locked']) || empty($reg['company'])) {
            return redirect()->route('frontend.register.step1');
        }

        $account = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(10)],
            'terms' => ['accepted'],
        ]);

        Role::firstOrCreate(['name' => 'company.owner', 'guard_name' => 'web']);

        $user = null;
        DB::transaction(function () use (&$user, $account, $reg): void {
            $locked = $reg['locked'];
            $companyData = $reg['company'];

            $user = User::create([
                'name' => $account['name'],
                'email' => $account['email'],
                'password' => Hash::make($account['password']),
            ]);

            $user->assignRole('company.owner');

            $slug = $this->makeUniqueCompanySlug((string) ($locked['legal_name'] ?? 'company'));

            $company = Company::create([
                'owner_user_id' => $user->id,
                'category_id' => null,

                // Locked from registry
                'legal_name' => $locked['legal_name'] ?? null,
                'slug' => $slug,
                'ico' => $locked['ico'] ?? null,
                'country_code' => $locked['country_code'] ?? 'SK',
                'street' => $locked['street'] ?? null,
                'postal_code' => $locked['postal_code'] ?? null,
                'city' => $locked['city'] ?? null,

                // Step 2 data
                'bio' => $companyData['bio'] ?? null,
                'general_email' => $companyData['general_email'] ?? null,
                'phone' => $companyData['phone'] ?? null,
                'contact_first_name' => $companyData['contact_first_name'] ?? null,
                'contact_last_name' => $companyData['contact_last_name'] ?? null,
                'contact_email' => $companyData['contact_email'] ?? null,
                'contact_phone' => $companyData['contact_phone'] ?? null,
                'team_size' => $companyData['team_size'] ?? null,
                'founded_year' => $companyData['founded_year'] ?? null,
                'dic' => $companyData['dic'] ?? null,
                'ic_dph' => $companyData['ic_dph'] ?? null,
                'website_url' => $companyData['website_url'] ?? null,
                'region' => $companyData['region'] ?? null,
            ]);

            if (method_exists($user, 'companies')) {
                $user->companies()->syncWithoutDetaching([
                    $company->id => [
                        'role' => 'owner',
                        'status' => 'active',
                        'accepted_at' => now(),
                    ],
                ]);
            }
        });

        $request->session()->forget(self::REG_KEY);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('frontend.dashboard');
    }

    protected function makeUniqueCompanySlug(string $name): string
    {
        $slugBase = Str::slug($name) ?: 'company';
        $slug = $slugBase;
        $i = 2;

        while (Company::where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . $i;
            $i++;
            if ($i > 9999) {
                $slug = $slugBase . '-' . Str::random(6);
                break;
            }
        }

        return $slug;
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('frontend.login');
    }
}
