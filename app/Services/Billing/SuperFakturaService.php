<?php

namespace App\Services\Billing;

use App\Models\Billing\Invoice;

class SuperFakturaService
{
    public function buildInvoicePayload(Invoice $invoice): array
    {
        return [
            'number' => $invoice->payment_reference,
            'currency' => $invoice->currency,
            'issued_at' => $invoice->issued_at?->format('Y-m-d'),
            'due_at' => $invoice->due_at?->format('Y-m-d'),
            'customer' => [
                'name' => $invoice->customer_name_snapshot,
                'address' => $invoice->customer_address_snapshot,
                'country' => $invoice->customer_country_snapshot,
                'vat_id' => $invoice->customer_vat_id_snapshot,
            ],
            'totals' => [
                'subtotal_net_minor' => $invoice->subtotal_net_minor,
                'discount_minor' => $invoice->discount_minor,
                'tax_minor' => $invoice->tax_minor,
                'total_gross_minor' => $invoice->total_gross_minor,
            ],
        ];
    }

    public function createInvoice(array $payload): array
    {
        return [
            'external_id' => null,
            'external_number' => null,
            'pdf_url' => null,
            'status' => 'pending',
            'error' => null,
        ];
    }

    public function cancelInvoice(string $externalId): void
    {
    }

    public function createCreditNote(array $payload): void
    {
    }
}
