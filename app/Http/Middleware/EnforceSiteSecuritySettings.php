<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class EnforceSiteSecuritySettings
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Schema::hasTable('site_settings')) {
            return $next($request);
        }

        $settings = SiteSetting::current();
        if (! $settings) {
            return $next($request);
        }

        if (Auth::check()) {
            $currentRev = (int) ($settings->session_rev ?? 1);
            $sessionRev = (int) $request->session()->get('site_session_rev', 0);

            if ($sessionRev && $sessionRev < $currentRev) {
                return $this->logoutWithMessage($request, $settings->force_logout_message);
            }

            if (! $sessionRev) {
                $request->session()->put('site_session_rev', $currentRev);
            }

            $idleMinutes = $settings->idle_timeout_minutes ? (int) $settings->idle_timeout_minutes : null;
            if ($idleMinutes) {
                $lastActivity = (int) $request->session()->get('site_last_activity', 0);
                $now = now()->timestamp;

                if ($lastActivity > 0 && ($now - $lastActivity) > ($idleMinutes * 60)) {
                    return $this->logoutWithMessage($request, $settings->force_logout_message);
                }

                $request->session()->put('site_last_activity', $now);
            }

            $reauthMinutes = $settings->reauth_for_sensitive_minutes
                ? (int) $settings->reauth_for_sensitive_minutes
                : null;

            if ($reauthMinutes && $request->attributes->get('requires_reauth') === true) {
                $lastReauth = (int) $request->session()->get('site_last_reauth', 0);
                $now = now()->timestamp;

                if ($lastReauth === 0 || ($now - $lastReauth) > ($reauthMinutes * 60)) {
                    return $this->logoutWithMessage($request, $settings->force_logout_message);
                }
            }
        }

        return $next($request);
    }

    private function logoutWithMessage(Request $request, ?string $message): Response
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $fallback = 'Bitte melden Sie sich erneut an.';

        if ($request->is('admin*')) {
            return redirect('/365gate')->with('status', $message ?: $fallback);
        }

        return redirect()->route('frontend.login')->with('status', $message ?: $fallback);
    }
}