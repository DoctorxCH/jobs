<?php

namespace App\Services\Billing;

use App\Models\Billing\Invoice;
use App\Models\Billing\Order;

class OrderService
{
    public function createDraft($company, $user, string $currency, array $items, ?string $couponCode = null): Order
    {
        return Order::query()->create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'currency' => $currency,
            'status' => 'draft',
            'tax_rule_applied' => 'SK_DOMESTIC',
            'reverse_charge' => false,
            'tax_rate_percent_snapshot' => 0,
            'subtotal_net_minor' => 0,
            'discount_minor' => 0,
            'tax_minor' => 0,
            'total_gross_minor' => 0,
            'company_discount_minor' => 0,
        ]);
    }

    public function createInvoiceForOrder(Order $order, $issuedByUser = null): Invoice
    {
        return Invoice::query()->create([
            'company_id' => $order->company_id,
            'order_id' => $order->id,
            'status' => 'issued_unpaid',
            'currency' => $order->currency,
            'issued_at' => now(),
            'payment_reference' => uniqid('INV-', true),
            'customer_name_snapshot' => 'TODO',
            'customer_address_snapshot' => 'TODO',
            'customer_country_snapshot' => 'SK',
            'tax_rule_applied' => $order->tax_rule_applied,
            'reverse_charge' => $order->reverse_charge,
            'tax_rate_percent_snapshot' => $order->tax_rate_percent_snapshot,
            'subtotal_net_minor' => $order->subtotal_net_minor,
            'discount_minor' => $order->discount_minor,
            'tax_minor' => $order->tax_minor,
            'total_gross_minor' => $order->total_gross_minor,
            'created_by_admin_id' => $issuedByUser?->id,
        ]);
    }

    public function cancelOrder(Order $order): void
    {
        $order->update(['status' => 'cancelled']);
    }
}
