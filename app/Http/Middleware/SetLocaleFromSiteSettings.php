<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromSiteSettings
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Schema::hasTable('site_settings')) {
            $settings = SiteSetting::current();
            $locale = $settings?->default_locale;

            if (!empty($locale)) {
                app()->setLocale($locale);
                config(['app.locale' => $locale]);
            }
        }

        return $next($request);
    }
}
