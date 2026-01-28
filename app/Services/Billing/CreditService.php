<?php

namespace App\Services\Billing;

use App\Models\Billing\CreditLedger;
use App\Models\Billing\CreditReservation;
use Illuminate\Support\Facades\DB;

class CreditService
{
    public function availableCredits(int $companyId): int
    {
        $creditsTotal = (int) DB::table('credit_ledger')
            ->where('company_id', $companyId)
            ->sum('change');

        $creditsReserved = (int) DB::table('credit_reservations')
            ->where('company_id', $companyId)
            ->whereIn('status', ['active', 'reserved'])
            ->where('expires_at', '>', now())
            ->sum('amount');

        return max(0, $creditsTotal - $creditsReserved);
    }

    public function reserveForJob($company, int $jobId, int $days = 1): CreditReservation
    {
        return CreditReservation::query()->create([
            'company_id' => $company->id,
            'amount' => $days,
            'purpose' => 'job_post',
            'reference_type' => 'job',
            'reference_id' => $jobId,
            'status' => 'active',
            'expires_at' => now()->addDays(7),
        ]);
    }

    public function consumeReservation(CreditReservation $reservation, int $jobId, ?int $days = null): void
    {
        $amount = $days ?? (int) $reservation->amount ?? 1;
        $reservation->update(['status' => 'consumed']);

        for ($i = 0; $i < $amount; $i++) {
            CreditLedger::query()->create([
                'company_id' => $reservation->company_id,
                'change' => -1,
                'reason' => 'job_post',
                'reference_type' => 'job',
                'reference_id' => $jobId,
                'created_at' => now(),
            ]);
        }
    }

    public function releaseReservation(CreditReservation $reservation): void
    {
        $reservation->update(['status' => 'released']);
    }
}
