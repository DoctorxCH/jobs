<?php

namespace App\Services\Billing;

use App\Models\Billing\Invoice;
use App\Models\Billing\InvoiceStatusHistory;
use App\Models\Billing\Order;
use App\Models\Billing\OrderStatusHistory;
use App\Models\Billing\Payment;
use App\Models\Billing\PaymentStatusHistory;

class HistoryService
{
    public function orderStatus(Order $order, ?string $from, string $to, $user = null, ?string $note = null, ?array $meta = null): OrderStatusHistory
    {
        return OrderStatusHistory::query()->create([
            'order_id' => $order->id,
            'from_status' => $from,
            'to_status' => $to,
            'changed_by_user_id' => $user?->id,
            'note' => $note,
            'meta' => $meta,
            'changed_at' => now(),
        ]);
    }

    public function invoiceStatus(Invoice $invoice, ?string $from, string $to, $user = null, ?string $note = null, ?array $meta = null): InvoiceStatusHistory
    {
        return InvoiceStatusHistory::query()->create([
            'invoice_id' => $invoice->id,
            'from_status' => $from,
            'to_status' => $to,
            'changed_by_user_id' => $user?->id,
            'note' => $note,
            'meta' => $meta,
            'changed_at' => now(),
        ]);
    }

    public function paymentStatus(Payment $payment, ?string $from, string $to, $user = null, ?string $note = null, ?array $meta = null): PaymentStatusHistory
    {
        return PaymentStatusHistory::query()->create([
            'payment_id' => $payment->id,
            'from_status' => $from,
            'to_status' => $to,
            'changed_by_user_id' => $user?->id,
            'note' => $note,
            'meta' => $meta,
            'changed_at' => now(),
        ]);
    }
}
