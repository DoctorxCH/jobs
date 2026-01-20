<?php

namespace App\Http\Controllers;

use App\Models\CompanyInvitation;
use App\Models\CompanyUser;
use Illuminate\Http\Request;

class CompanyInvitationController extends Controller
{
    public function accept(string $token)
    {
        $invite = CompanyInvitation::where('token', $token)
            ->whereNull('accepted_at')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->firstOrFail();

        $user = auth()->user();
        if (! $user) {
            return redirect()->route('login');
        }

        CompanyUser::firstOrCreate(
            [
                'company_id' => $invite->company_id,
                'user_id' => $user->id,
            ],
            [
                'role' => $invite->role,
                'status' => 'active',
                'accepted_at' => now(),
            ]
        );

        $invite->update(['accepted_at' => now()]);

        return redirect('/admin')->with('success', 'You joined the company.');
    }
}
