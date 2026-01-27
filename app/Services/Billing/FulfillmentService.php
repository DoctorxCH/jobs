<?php

namespace App\Services\Billing;

use App\Models\Billing\Invoice;

class FulfillmentService
{
    public function fulfillInvoice(Invoice $invoice): void
    {
        // TODO: Issue entitlements and credits after payment confirmation.
    }
}
