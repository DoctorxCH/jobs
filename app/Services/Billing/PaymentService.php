<?php

namespace App\Services\Billing;

use App\Models\Billing\Invoice;
use App\Models\Billing\Payment;

class PaymentService
{
    public function confirmFullBankPayment(Invoice $invoice, int $amountMinor, ?string $bankReference, $adminUser = null): Payment
    {
        if ($amountMinor !== $invoice->total_gross_minor) {
            throw new \InvalidArgumentException('Partial payments are not allowed.');
        }

        return Payment::query()->create([
            'invoice_id' => $invoice->id,
            'method' => 'bank_transfer',
            'status' => 'confirmed',
            'amount_minor' => $amountMinor,
            'currency' => $invoice->currency,
            'bank_reference' => $bankReference,
            'received_at' => now(),
            'created_by_admin_id' => $adminUser?->id,
        ]);
    }
}
