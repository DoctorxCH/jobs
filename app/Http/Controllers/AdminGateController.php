<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminGateController extends Controller
{
    public function show(Request $request)
    {
        if (Auth::check()) {
            return redirect()->intended('/admin');
        }

        return view('auth.admin-gate');
    }

    public function authenticate(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $remember = (bool) ($data['remember'] ?? false);

        if (! Auth::attempt(['email' => $data['email'], 'password' => $data['password']], $remember)) {
            return back()
                ->withErrors(['email' => 'Login fehlgeschlagen (E-Mail oder Passwort falsch).'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        // Optional aber empfehlenswert: Nur Platform-Rollen duerfen ins Admin Panel
        $user = $request->user();

        if (method_exists($user, 'hasAnyRole')) {
            $allowed = [
                'platform.super_admin',
                'platform.admin',
                'platform.editor',
                'platform.moderator',
                'platform.finance',
                'platform.support',
            ];

            if (! $user->hasAnyRole($allowed)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Kein Admin-Zugriff (platform.* Rolle fehlt).',
                ]);
            }
        }

        return redirect()->intended('/admin');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/365gate');
    }
}
