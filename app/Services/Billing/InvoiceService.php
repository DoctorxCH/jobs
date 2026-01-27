<?php

namespace App\Services\Billing;

use App\Models\Billing\Invoice;
use App\Models\Billing\InvoiceStatusHistory;
use App\Models\User;

class InvoiceService
{
    public function markStatus(Invoice $invoice, string $toStatus, ?User $actor = null, ?string $note = null, array $meta = []): Invoice
    {
        $fromStatus = $invoice->status;
        $invoice->status = $toStatus;
        $invoice->save();

        InvoiceStatusHistory::query()->create([
            'invoice_id' => $invoice->id,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'changed_by_user_id' => $actor?->id,
            'note' => $note,
            'meta' => $meta,
            'changed_at' => now(),
        ]);

        return $invoice;
    }
}
