<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    protected static string $view = 'filament.admin.auth.login-custom';

    protected function authenticated(): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        $hasPlatformRole = $user->getRoleNames()
            ->contains(fn (string $role) => Str::startsWith($role, 'platform.'));

        if (! $hasPlatformRole) {
            auth()->logout();

            throw ValidationException::withMessages([
                'data.email' => 'Access denied.',
            ]);
        }
    }
}
