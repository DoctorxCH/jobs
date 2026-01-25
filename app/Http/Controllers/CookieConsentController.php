<?php

namespace App\Http\Controllers;

use App\Models\CookieSetting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class CookieConsentController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'level' => ['required', 'in:essential,stats'],
            'redirect' => ['nullable', 'string'],
        ]);

        $settings = CookieSetting::current();
        $minutes = (int) $settings->consent_days * 24 * 60;

        $redirect = $data['redirect'] ?: url()->previous();

        return redirect($redirect)
            ->withCookie(cookie('cookie_consent', $data['level'], $minutes))
            ->withCookie(cookie('cookie_consent_version', (string) $settings->consent_version, $minutes))
            ->withCookie(cookie('cookie_consent_at', now()->toIso8601String(), $minutes));
    }
}
