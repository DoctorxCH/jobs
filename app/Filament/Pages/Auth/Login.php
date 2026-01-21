<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class Login extends BaseLogin
{
    protected function authenticated(): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        // Nur platform.* Rollen erlauben
        $hasPlatformRole = $user->getRoleNames()
            ->contains(fn (string $role) => Str::startsWith($role, 'platform.'));

        if (! $hasPlatformRole) {
            auth()->logout();

            throw ValidationException::withMessages([
                'data.email' => 'Access denied. Employees only ;) But you are smart, do you want to work for us? Maybe there is a position for you! Look at https://365jobs.sk/inzeraty/kariera',
            ]);
        }
    }
}
