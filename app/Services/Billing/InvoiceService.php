<?php

namespace App\Services\Billing;

use App\Models\Billing\Invoice;
use App\Models\Billing\InvoiceExternal;
use App\Models\Billing\Payment;
use App\Models\SiteSetting;

class InvoiceService
{
    public function __construct(
        private SuperFakturaService $superFakturaService,
        private HistoryService $historyService,
        private FulfillmentService $fulfillmentService,
    ) {
    }

    public function exportToSuperfaktura(Invoice $invoice): InvoiceExternal
    {
        $settings = SiteSetting::current();

        if ($settings && $settings->superfaktura_enabled === false) {
            return InvoiceExternal::query()->updateOrCreate(
                ['invoice_id' => $invoice->id],
                [
                    'provider' => 'superfaktura',
                    'external_invoice_id' => null,
                    'external_invoice_number' => null,
                    'external_pdf_url' => null,
                    'sync_status' => 'disabled',
                    'last_synced_at' => now(),
                    'last_error' => 'SuperFaktura disabled in settings.',
                ]
            );
        }

        $payload = $this->superFakturaService->buildInvoicePayload($invoice);
        $timeoutSeconds = $settings?->superfaktura_timeout_seconds
            ? (int) $settings->superfaktura_timeout_seconds
            : null;
        $response = $this->superFakturaService->createInvoice($payload, $timeoutSeconds);

        return InvoiceExternal::query()->updateOrCreate(
            ['invoice_id' => $invoice->id],
            [
                'provider' => 'superfaktura',
                'external_invoice_id' => $response['external_id'] ?? null,
                'external_invoice_number' => $response['external_number'] ?? null,
                'external_pdf_url' => $response['pdf_url'] ?? null,
                'sync_status' => $response['status'] ?? 'pending',
                'last_synced_at' => now(),
                'last_error' => $response['error'] ?? null,
            ]
        );
    }

    public function retryExport(Invoice $invoice): InvoiceExternal
    {
        return $this->exportToSuperfaktura($invoice);
    }

    public function markPaid(Invoice $invoice, int $amountMinor, ?string $bankReference, $adminUser = null): Payment
    {
        if ($invoice->status === 'paid') {
            throw new \RuntimeException('Invoice is already paid.');
        }

        if (! in_array($invoice->status, ['issued_unpaid', 'overdue'], true)) {
            throw new \RuntimeException('Invoice cannot be paid from the current status.');
        }

        if ($amountMinor !== $invoice->total_gross_minor) {
            throw new \InvalidArgumentException('Partial payments are not allowed.');
        }

        $payment = Payment::query()->create([
            'invoice_id' => $invoice->id,
            'method' => 'bank_transfer',
            'status' => 'confirmed',
            'amount_minor' => $amountMinor,
            'currency' => $invoice->currency,
            'bank_reference' => $bankReference,
            'received_at' => now(),
            'created_by_admin_id' => $adminUser?->id,
        ]);

        $invoice->update(['status' => 'paid']);
        $this->fulfillmentService->grantEntitlementsFromInvoice($invoice);

        return $payment;
    }

    public function markOverdue(Invoice $invoice): void
    {
        $invoice->update(['status' => 'overdue']);
    }

    public function cancelUnpaid(Invoice $invoice): void
    {
        $invoice->update(['status' => 'cancelled']);
    }

    public function createCreditNoteFromPaid(Invoice $invoice, string $reason): Invoice
    {
        $creditNote = Invoice::query()->create([
            'company_id' => $invoice->company_id,
            'order_id' => $invoice->order_id,
            'status' => 'credit_note',
            'currency' => $invoice->currency,
            'issued_at' => now(),
            'payment_reference' => uniqid('CN-', true),
            'customer_name_snapshot' => $invoice->customer_name_snapshot,
            'customer_address_snapshot' => $invoice->customer_address_snapshot,
            'customer_country_snapshot' => $invoice->customer_country_snapshot,
            'customer_vat_id_snapshot' => $invoice->customer_vat_id_snapshot,
            'tax_rule_applied' => $invoice->tax_rule_applied,
            'reverse_charge' => $invoice->reverse_charge,
            'tax_rate_percent_snapshot' => $invoice->tax_rate_percent_snapshot,
            'subtotal_net_minor' => $invoice->subtotal_net_minor,
            'discount_minor' => $invoice->discount_minor,
            'tax_minor' => $invoice->tax_minor,
            'total_gross_minor' => $invoice->total_gross_minor,
            'cancelled_invoice_id' => $invoice->id,
        ]);

        $this->exportToSuperfaktura($creditNote);

        return $creditNote;
    }
}
