<?php

namespace App\Services\Billing;

use App\Models\Billing\ProductPrice;

class PricingService
{
    public function calculateNetTotal(int $unitNetAmountMinor, int $quantity): int
    {
        return $unitNetAmountMinor * $quantity;
    }

    public function calculateTaxTotal(int $netAmountMinor, float $taxRatePercent): int
    {
        return (int) round($netAmountMinor * ($taxRatePercent / 100));
    }

    public function calculateGrossTotal(int $netAmountMinor, int $taxAmountMinor): int
    {
        return $netAmountMinor + $taxAmountMinor;
    }

    public function resolvePrice(ProductPrice $price, int $quantity): array
    {
        $netTotal = $this->calculateNetTotal($price->unit_net_amount_minor, $quantity);

        return [
            'unit_net_minor' => $price->unit_net_amount_minor,
            'total_net_minor' => $netTotal,
            'tax_class_id' => $price->tax_class_id,
        ];
    }
}
