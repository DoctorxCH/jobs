<?php

namespace App\Http\Middleware;

use App\Models\CookieSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CookieConsentMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $settings = CookieSetting::current();

        $consent = $request->cookie('cookie_consent'); // essential|stats|null
        $version = (int) ($request->cookie('cookie_consent_version') ?? 0);

        $needsBanner = empty($consent) || $version !== (int) $settings->consent_version;

        view()->share('cookieSettings', $settings);
        view()->share('showCookieBanner', $needsBanner);

        return $next($request);
    }
}
