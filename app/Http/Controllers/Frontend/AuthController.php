<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
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

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            // User
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(10)],

            // Company
            'company_legal_name' => ['required', 'string', 'max:255'],
            'ico' => ['required', 'regex:/^\d{8}$/', 'unique:companies,ico'],

            // Optional company fields (autofill)
            'dic' => ['nullable', 'string', 'max:32'],
            'ic_dph' => ['nullable', 'string', 'max:32'],
            'street' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:32'],
            'city' => ['nullable', 'string', 'max:120'],
        ], [
            'ico.regex' => 'ICO must be exactly 8 digits.',
        ]);

        Role::firstOrCreate(['name' => 'company.owner', 'guard_name' => 'web']);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole('company.owner');

        $slugBase = Str::slug($data['company_legal_name']) ?: 'company';
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

        $company = Company::create([
            'owner_user_id' => $user->id,
            'legal_name' => $data['company_legal_name'],
            'slug' => $slug,
            'ico' => $data['ico'],

            'dic' => $data['dic'] ?? null,
            'ic_dph' => $data['ic_dph'] ?? null,
            'street' => $data['street'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'city' => $data['city'] ?? null,
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

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('frontend.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('frontend.login');
    }
}
