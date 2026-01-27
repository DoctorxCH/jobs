<?php

namespace App\Services\Billing;

use App\Models\Billing\Invoice;
use App\Models\Billing\Payment;
use App\Models\Billing\PaymentStatusHistory;
use App\Models\User;

class PaymentService
{
    public function createPayment(Invoice $invoice, int $amountMinor, string $currency, ?User $actor = null, ?string $bankReference = null): Payment
    {
        $payment = Payment::query()->create([
            'invoice_id' => $invoice->id,
            'method' => 'bank_transfer',
            'status' => 'pending',
            'amount_minor' => $amountMinor,
            'currency' => $currency,
            'received_at' => null,
            'bank_reference' => $bankReference,
            'created_by_admin_id' => $actor?->id,
        ]);

        PaymentStatusHistory::query()->create([
            'payment_id' => $payment->id,
            'from_status' => null,
            'to_status' => 'pending',
            'changed_by_user_id' => $actor?->id,
            'note' => 'Payment created.',
            'meta' => [],
            'changed_at' => now(),
        ]);

        return $payment;
    }

    public function confirmPayment(Payment $payment, ?User $actor = null): Payment
    {
        return $this->markStatus($payment, 'confirmed', $actor);
    }

    public function rejectPayment(Payment $payment, ?User $actor = null, ?string $note = null): Payment
    {
        return $this->markStatus($payment, 'rejected', $actor, $note);
    }

    public function markStatus(Payment $payment, string $toStatus, ?User $actor = null, ?string $note = null): Payment
    {
        $fromStatus = $payment->status;
        $payment->status = $toStatus;
        $payment->save();

        PaymentStatusHistory::query()->create([
            'payment_id' => $payment->id,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'changed_by_user_id' => $actor?->id,
            'note' => $note,
            'meta' => [],
            'changed_at' => now(),
        ]);

        return $payment;
    }
}
