<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SecurityController extends Controller
{
    /**
     * Central place for password policy.
     * Later: load this from DB / Filament Content Manager.
     */
    private function passwordPolicy(): array
    {
        // Later replace this with something like:
        // $policy = config('security.password'); or PasswordPolicy::current();
        // For now: keep it explicit here.

        $min = 10;

        return [
            'min' => $min,

            // Validation rule used by Laravel
            'rule' => Password::min($min)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols(),

            // UI requirements (structured for live checking)
            'requirements' => [
                [
                    'key' => 'min',
                    'label' => "At least {$min} characters",
                    'js' => "value.length >= {$min}",
                ],
                [
                    'key' => 'lower',
                    'label' => 'At least one lowercase letter (a-z)',
                    'js' => "/[a-z]/.test(value)",
                ],
                [
                    'key' => 'upper',
                    'label' => 'At least one uppercase letter (A-Z)',
                    'js' => "/[A-Z]/.test(value)",
                ],
                [
                    'key' => 'number',
                    'label' => 'At least one number (0-9)',
                    'js' => "/[0-9]/.test(value)",
                ],
                [
                    'key' => 'symbol',
                    'label' => 'At least one symbol (!@#$...)',
                    'js' => "/[^A-Za-z0-9]/.test(value)",
                ],
            ],
        ];
    }

    public function edit()
    {
        $policy = $this->passwordPolicy();

        return view('dashboard.security', [
            'requirements' => $policy['requirements'],
            'minLength' => $policy['min'],
        ]);
    }

    public function update(Request $request)
    {
        $policy = $this->passwordPolicy();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', $policy['rule']],
        ], [
            'current_password.current_password' => 'Your current password is incorrect.',
            'password.confirmed' => 'The new passwords do not match.',
        ]);

        $user = $request->user();
        $user->password = Hash::make($validated['password']);
        $user->save();

        return back()->with('status', __('main.password_updated'));
    }
}
