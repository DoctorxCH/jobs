<?php

namespace App\Services\Billing;

use App\Models\Billing\Invoice;

class SuperFakturaService
{
    public function exportInvoice(Invoice $invoice): array
    {
        return [
            'status' => 'pending',
            'message' => 'TODO: Implement SuperFaktura API export.',
        ];
    }

    public function fetchInvoicePdf(Invoice $invoice): ?string
    {
        return null;
    }
}
