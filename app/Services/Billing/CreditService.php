<?php

namespace App\Services\Billing;

use App\Models\Billing\CreditLedger;
use App\Models\Billing\CreditReservation;
use App\Models\Company;
use App\Models\User;

class CreditService
{
    public function addLedgerEntry(Company $company, int $change, string $reason, string $referenceType, int $referenceId, ?User $actor = null): CreditLedger
    {
        return CreditLedger::query()->create([
            'company_id' => $company->id,
            'change' => $change,
            'reason' => $reason,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'created_by_admin_id' => $actor?->id,
            'created_at' => now(),
        ]);
    }

    public function reserveCredits(Company $company, int $amount, string $purpose, string $referenceType, int $referenceId, ?string $status = 'active'): CreditReservation
    {
        return CreditReservation::query()->create([
            'company_id' => $company->id,
            'amount' => $amount,
            'purpose' => $purpose,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'status' => $status,
        ]);
    }
}
