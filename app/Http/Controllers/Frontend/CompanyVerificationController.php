<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyVerificationRequest;
use App\Notifications\CompanyVerificationCodeNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyVerificationController extends Controller
{
    public function index(Request $request): View
    {
        $company = $this->companyForUser($request, requireManageJobs: false);
        abort_unless($company, 403);

        $latestRequest = CompanyVerificationRequest::query()
            ->where('company_id', $company->id)
            ->latest()
            ->first();

        return view('company.verification.index', [
            'company' => $company,
            'latestRequest' => $latestRequest,
        ]);
    }

    public function startCode(Request $request): RedirectResponse
    {
        $company = $this->companyForUser($request, requireManageJobs: true);
        abort_unless($company, 403);

        $user = $request->user();
        $email = $request->validate([
            'email' => ['required', 'email'],
        ])['email'];

        if (strtolower($email) !== strtolower($user->email)) {
            return back()->withErrors(['email' => 'Bitte nutze deine Konto-E-Mail-Adresse.']);
        }

        if ($this->isFreemail($email)) {
            return back()->withErrors(['email' => 'Bitte nutze eine Firmen-E-Mail-Adresse.']);
        }

        $domainMismatch = $this->isCompanyDomainMismatch($company, $email);

        $code = (string) random_int(100000, 999999);
        $hashedCode = hash('sha256', $code);

        $verificationRequest = CompanyVerificationRequest::query()
            ->where('company_id', $company->id)
            ->where('method', 'code')
            ->whereIn('status', ['pending', 'code_sent'])
            ->latest()
            ->first();

        if (! $verificationRequest) {
            $verificationRequest = CompanyVerificationRequest::query()->create([
                'company_id' => $company->id,
                'method' => 'code',
                'status' => 'code_sent',
                'requested_by_user_id' => $user->id,
                'requested_by_email' => $user->email,
                'code_sent_to_email' => $email,
                'code_hash' => $hashedCode,
                'code_expires_at' => now()->addMinutes(10),
                'attempts' => 0,
                'last_sent_at' => now(),
                'ack_status' => 'pending',
                'admin_note' => $domainMismatch ? 'Email domain differs from company website.' : null,
            ]);
        } else {
            $verificationRequest->fill([
                'requested_by_user_id' => $user->id,
                'requested_by_email' => $user->email,
                'code_sent_to_email' => $email,
                'code_hash' => $hashedCode,
                'code_expires_at' => now()->addMinutes(10),
                'last_sent_at' => now(),
                'status' => 'code_sent',
            ]);

            if ($domainMismatch && empty($verificationRequest->admin_note)) {
                $verificationRequest->admin_note = 'Email domain differs from company website.';
            }

            $verificationRequest->save();
        }

        $user->notify(new CompanyVerificationCodeNotification($code));

        return back()->with('success', 'Verifizierungscode wurde gesendet.');
    }

    public function resendCode(Request $request): RedirectResponse
    {
        $company = $this->companyForUser($request, requireManageJobs: true);
        abort_unless($company, 403);

        $verificationRequest = CompanyVerificationRequest::query()
            ->where('company_id', $company->id)
            ->where('method', 'code')
            ->where('status', 'code_sent')
            ->latest()
            ->first();

        if (! $verificationRequest) {
            return back()->withErrors(['code' => 'Kein aktiver Code vorhanden.']);
        }

        if ($verificationRequest->last_sent_at && $verificationRequest->last_sent_at->gt(now()->subSeconds(60))) {
            return back()->withErrors(['code' => 'Bitte warte kurz, bevor du den Code erneut sendest.']);
        }

        $code = (string) random_int(100000, 999999);
        $verificationRequest->fill([
            'code_hash' => hash('sha256', $code),
            'code_expires_at' => now()->addMinutes(10),
            'last_sent_at' => now(),
        ]);
        $verificationRequest->increment('attempts');
        $verificationRequest->save();

        $request->user()->notify(new CompanyVerificationCodeNotification($code));

        return back()->with('success', 'Code erneut gesendet.');
    }

    public function confirmCode(Request $request): RedirectResponse
    {
        $company = $this->companyForUser($request, requireManageJobs: true);
        abort_unless($company, 403);

        $data = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $verificationRequest = CompanyVerificationRequest::query()
            ->where('company_id', $company->id)
            ->where('method', 'code')
            ->where('status', 'code_sent')
            ->latest()
            ->first();

        if (! $verificationRequest) {
            return back()->withErrors(['code' => 'Kein aktiver Verifizierungsprozess gefunden.']);
        }

        if (! $verificationRequest->code_expires_at || $verificationRequest->code_expires_at->isPast()) {
            $verificationRequest->update(['status' => 'expired']);
            return back()->withErrors(['code' => 'Der Code ist abgelaufen.']);
        }

        if (! hash_equals($verificationRequest->code_hash ?? '', hash('sha256', $data['code']))) {
            $verificationRequest->increment('attempts');
            return back()->withErrors(['code' => 'Ungültiger Code.']);
        }

        $verificationRequest->update([
            'status' => 'auto_verified',
            'auto_verified_at' => now(),
            'ack_status' => 'pending',
        ]);

        if (! $company->verified_at) {
            $company->forceFill([
                'verified_at' => now(),
            ]);
        }

        $company->forceFill([
            'verified_method' => 'code',
            'verified_by_user_id' => $request->user()->id,
            'verified_by_email' => $request->user()->email,
            'verification_ack_status' => 'pending',
            'verification_ack_at' => null,
            'verification_ack_by' => null,
            'verification_ack_note' => null,
        ])->save();

        return redirect()
            ->route('frontend.company.verification.index')
            ->with('success', 'Firma wurde verifiziert.');
    }

    public function startInvoice(Request $request): RedirectResponse
    {
        $company = $this->companyForUser($request, requireManageJobs: true);
        abort_unless($company, 403);

        $user = $request->user();

        CompanyVerificationRequest::query()->create([
            'company_id' => $company->id,
            'method' => 'invoice',
            'status' => 'awaiting_payment',
            'requested_by_user_id' => $user->id,
            'requested_by_email' => $user->email,
            'ack_status' => 'pending',
        ]);

        return redirect()
            ->route('frontend.billing.products.index')
            ->with('success', 'Bitte Credits bestellen, um die Verifizierung abzuschliessen.');
    }

    private function companyForUser(Request $request, bool $requireManageJobs = true): ?Company
    {
        $user = $request->user();
        if (! $user) {
            return null;
        }

        if ($requireManageJobs && method_exists($user, 'canCompanyManageJobs') && ! $user->canCompanyManageJobs()) {
            abort(403);
        }

        $companyId = method_exists($user, 'effectiveCompanyId')
            ? $user->effectiveCompanyId()
            : $user->company_id;

        if (! $companyId) {
            return null;
        }

        return Company::query()
            ->where('id', $companyId)
            ->whereNull('deleted_at')
            ->first();
    }

    private function isFreemail(string $email): bool
    {
        $domain = strtolower(substr(strrchr($email, '@') ?: '', 1));
        $blocked = [
            'gmail.com',
            'outlook.com',
            'hotmail.com',
            'yahoo.com',
            'icloud.com',
            'proton.me',
            'protonmail.com',
            'gmx.de',
            'gmx.net',
            'gmx.ch',
            'gmx.at',
            'gmx.com',
        ];

        if (in_array($domain, $blocked, true)) {
            return true;
        }

        return str_starts_with($domain, 'gmx.');
    }

    private function isCompanyDomainMismatch(Company $company, string $email): bool
    {
        if (! $company->website_url) {
            return false;
        }

        $companyHost = parse_url($company->website_url, PHP_URL_HOST);
        if (! $companyHost) {
            return false;
        }

        $companyHost = strtolower($companyHost);
        $companyHost = preg_replace('/^www\./', '', $companyHost);

        $emailDomain = strtolower(substr(strrchr($email, '@') ?: '', 1));

        return ! str_ends_with($emailDomain, $companyHost);
    }
}
