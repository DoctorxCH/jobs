<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromSiteSettings
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Schema::hasTable('site_settings')) {
            $locale = Cache::remember('site_settings.default_locale', 60, function () {
                return SiteSetting::query()->value('default_locale');
            });

            if (!empty($locale)) {
                app()->setLocale($locale);
                config(['app.locale' => $locale]);
            }
        }

        return $next($request);
    }
}
