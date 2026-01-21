<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function authenticate(): ?LoginResponse
    {
        $response = parent::authenticate();

        $user = auth()->user();
        if (! $user) {
            return $response;
        }

        $hasPlatformRole = $user->getRoleNames()
            ->contains(fn (string $role) => Str::startsWith($role, 'platform.'));

        if (! $hasPlatformRole) {
            auth()->logout();

            throw ValidationException::withMessages([
                'data.email' => 'Access denied. Employees only ;) But you are smart, do you want to work for us? Maybe there is a position for you! Look at https://365jobs.sk/inzeraty/kariera',
            ]);
        }

        return $response;
    }
}
