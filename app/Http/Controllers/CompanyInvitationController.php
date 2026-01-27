<?php

namespace App\Http\Controllers;

use App\Mail\CompanyInviteMail;
use App\Models\Company;
use App\Models\CompanyInvitation;
use App\Models\CompanyUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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

    /**
     * Owner/Member: send an invitation mail.
     * Route: POST /dashboard/team/invite (frontend.team.invite)
     */
    public function send(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            abort(401);
        }

        if (! method_exists($user, 'canCompanyManageTeam') || ! $user->canCompanyManageTeam()) {
            abort(403, 'Not allowed to invite.');
        }

        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'role' => ['required', 'in:member,recruiter,viewer'],
        ]);

        $companyId = method_exists($user, 'effectiveCompanyId') ? $user->effectiveCompanyId() : $user->company_id;
        if (! $companyId) {
            abort(422, 'No company context.');
        }

        $company = Company::findOrFail($companyId);

        // Optional: prevent inviting yourself
        if (strtolower($data['email']) === strtolower($user->email)) {
            return back()->with('status', 'Cannot invite your own email.');
        }

        // Create invitation
        $invite = CompanyInvitation::create([
            'company_id' => $company->id,
            'email' => strtolower($data['email']),
            'role' => $data['role'],
            'token' => Str::random(64),
            'expires_at' => Carbon::now()->addDays(7),
            'accepted_at' => null,
        ]);

        // Send mail
        Mail::to($invite->email)->send(new CompanyInviteMail($invite));

        return back()->with('status', 'Invitation sent.');
    }

    public function complete(Request $request, string $token)
    {
        $invite = CompanyInvitation::where('token', $token)->firstOrFail();

        if ($invite->accepted_at) {
            abort(410, 'Invitation already used.');
        }

        if ($invite->expires_at && $invite->expires_at->isPast()) {
            abort(410, 'Invitation expired.');
        }

        $data = $request->validate([
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $pivotRole = in_array($invite->role, ['member', 'recruiter', 'viewer'], true)
            ? $invite->role
            : 'member';

        $spatieRole = 'company.' . $pivotRole;

        $user = User::where('email', $invite->email)->first();

        if (! $user) {
            $user = User::create([
                'name' => Str::before($invite->email, '@'),
                'email' => $invite->email,
                'password' => Hash::make($data['password']),
            ]);
        } else {
            $user->password = Hash::make($data['password']);
            $user->save();
        }

        // company_user has UNIQUE(user_id) => upsert
        CompanyUser::updateOrCreate(
            ['user_id' => $user->id],
            [
                'company_id' => $invite->company_id,
                'role' => $pivotRole,
                'status' => 'active',
                'invited_at' => $invite->created_at ?? Carbon::now(),
                'accepted_at' => Carbon::now(),
            ]
        );

        // Keep legacy primary context aligned
        $user->company_id = $invite->company_id;
        $user->is_company_owner = false;
        $user->save();

        // keep only one company.* role (strict)
        $user->syncRoles([$spatieRole]);

        $invite->accepted_at = Carbon::now();
        $invite->save();

        Auth::login($user);

        return redirect()->route('frontend.dashboard');
    }
}
