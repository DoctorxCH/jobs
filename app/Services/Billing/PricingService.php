<?php

namespace App\Services\Billing;

use App\Models\Billing\Product;

class PricingService
{
    public function priceOrderDraft($company, string $currency, array $items, ?string $couponCode = null): array
    {
        return [
            'currency' => $currency,
            'items' => array_map(function (array $item) {
                return [
                    'product_id' => $item['product_id'] ?? null,
                    'name_snapshot' => $item['name'] ?? 'TODO',
                    'qty' => $item['qty'] ?? 1,
                    'unit_net_minor' => $item['unit_net_minor'] ?? 0,
                    'tax_rate_percent' => $item['tax_rate_percent'] ?? 0,
                    'tax_minor' => $item['tax_minor'] ?? 0,
                    'total_gross_minor' => $item['total_gross_minor'] ?? 0,
                ];
            }, $items),
            'subtotal_net_minor' => 0,
            'discount_minor' => 0,
            'tax_minor' => 0,
            'total_gross_minor' => 0,
            'coupon_code' => $couponCode,
        ];
    }
}
