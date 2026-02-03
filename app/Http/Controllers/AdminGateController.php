<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

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

        $settings = null;
        if (Schema::hasTable('site_settings')) {
            $settings = SiteSetting::current();
        }

        $maxAttempts = $settings?->max_login_attempts ?: 10;
        $lockoutMinutes = $settings?->lockout_minutes ?: 15;
        $decaySeconds = $lockoutMinutes * 60;
        $key = 'admin-gate:' . Str::lower($data['email']) . '|' . $request->ip();
        $storeName = config('cache.default') === 'array' ? 'file' : config('cache.default');
        $cache = Cache::store($storeName);
        $now = now()->timestamp;
        $lockedUntil = (int) $cache->get($key . ':locked_until', 0);

        if ($lockedUntil > $now) {
            return back()
                ->withErrors(['email' => 'Zu viele Login-Versuche. Bitte später erneut versuchen.'])
                ->onlyInput('email');
        }

        $attempts = (int) $cache->get($key . ':attempts', 0);
        if ($attempts >= $maxAttempts) {
            $cache->put($key . ':locked_until', $now + $decaySeconds, $decaySeconds);
            return back()
                ->withErrors(['email' => 'Zu viele Login-Versuche. Bitte später erneut versuchen.'])
                ->onlyInput('email');
        }

        $remember = (bool) ($data['remember'] ?? false);

        if (! Auth::attempt(['email' => $data['email'], 'password' => $data['password']], $remember)) {
            $attempts++;
            $cache->put($key . ':attempts', $attempts, $decaySeconds);
            if ($attempts >= $maxAttempts) {
                $cache->put($key . ':locked_until', $now + $decaySeconds, $decaySeconds);
            }
            return back()
                ->withErrors(['email' => 'Login fehlgeschlagen (E-Mail oder Passwort falsch).'])
                ->onlyInput('email');
        }

        $cache->forget($key . ':attempts');
        $cache->forget($key . ':locked_until');

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

        session(['admin_gate' => true]);

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
