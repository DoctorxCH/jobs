<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CompanyInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CompanyInvitationController extends Controller
{
    public function accept(string $token)
    {
        $invite = CompanyInvitation::where('token', $token)->firstOrFail();

        if ($invite->accepted_at) {
            abort(410, 'Invitation already used.');
        }

        if ($invite->expires_at && $invite->expires_at->isPast()) {
            abort(410, 'Invitation expired.');
        }

        return view('auth.company-invite-accept', [
            'invite' => $invite,
        ]);
    }

    public function complete(Request $request, string $token)
    {
        $invite = CompanyInvitation::where('token', $token)->firstOrFail();

        if ($invite->accepted_at) {
            abort(410);
        }

        $data = $request->validate([
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = User::where('email', $invite->email)->first();

        if (! $user) {
            $user = User::create([
                'name' => Str::before($invite->email, '@'),
                'email' => $invite->email,
                'password' => Hash::make($data['password']),
            ]);
        }

        // Attach user to company
        $invite->company->users()->syncWithoutDetaching([
            $user->id => [
                'role' => $invite->role,
            ],
        ]);

        $invite->update([
            'accepted_at' => Carbon::now(),
        ]);

        Auth::login($user);

        return redirect()->route('frontend.dashboard');
    }
}
