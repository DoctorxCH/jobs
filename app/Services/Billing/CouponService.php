<?php

namespace App\Services\Billing;

use App\Models\Billing\Coupon;
use App\Models\Billing\CouponRedemption;

class CouponService
{
    public function validateAndCalculate(
        string $code,
        $company,
        $user,
        array $items,
        int $subtotalNetMinor,
        string $currency
    ): array {
        $coupon = Coupon::query()->where('code', $code)->first();

        if (! $coupon) {
            return ['discount_minor' => 0, 'coupon_id' => null];
        }

        return ['discount_minor' => 0, 'coupon_id' => $coupon->id];
    }

    public function recordRedemption(
        int $couponId,
        int $orderId,
        ?int $invoiceId,
        int $companyId,
        int $userId,
        int $discountMinor,
        string $currency
    ): CouponRedemption {
        return CouponRedemption::query()->create([
            'coupon_id' => $couponId,
            'order_id' => $orderId,
            'invoice_id' => $invoiceId,
            'company_id' => $companyId,
            'user_id' => $userId,
            'discount_minor' => $discountMinor,
            'currency' => $currency,
            'redeemed_at' => now(),
        ]);
    }
}
