<?php

namespace App\Services\Billing;

use App\Models\Billing\CreditLedger;
use App\Models\Billing\Entitlement;
use App\Models\Billing\Invoice;

class FulfillmentService
{
    public function grantEntitlementsFromInvoice(Invoice $invoice): void
    {
        foreach ($invoice->items as $item) {
            Entitlement::query()->create([
                'company_id' => $invoice->company_id,
                'type' => 'credits',
                'quantity_total' => $item->qty,
                'quantity_remaining' => $item->qty,
                'starts_at' => now(),
                'source_invoice_item_id' => $item->id,
            ]);

            CreditLedger::query()->create([
                'company_id' => $invoice->company_id,
                'change' => $item->qty,
                'reason' => 'purchase',
                'reference_type' => 'invoice_item',
                'reference_id' => $item->id,
                'created_by_admin_id' => $invoice->created_by_admin_id,
                'created_at' => now(),
            ]);
        }
    }
}
