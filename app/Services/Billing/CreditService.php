<?php

namespace App\Services\Billing;

use App\Models\Billing\CreditLedger;
use App\Models\Billing\CreditReservation;

class CreditService
{
    public function reserveForJob($company, int $jobId): CreditReservation
    {
        return CreditReservation::query()->create([
            'company_id' => $company->id,
            'amount' => 1,
            'purpose' => 'job_post',
            'reference_type' => 'job',
            'reference_id' => $jobId,
            'status' => 'active',
            'expires_at' => now()->addDays(7),
        ]);
    }

    public function consumeReservation(CreditReservation $reservation, int $jobId): void
    {
        $reservation->update(['status' => 'consumed']);

        CreditLedger::query()->create([
            'company_id' => $reservation->company_id,
            'change' => -1,
            'reason' => 'job_post',
            'reference_type' => 'job',
            'reference_id' => $jobId,
            'created_at' => now(),
        ]);
    }

    public function releaseReservation(CreditReservation $reservation): void
    {
        $reservation->update(['status' => 'released']);
    }
}
