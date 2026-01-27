<?php

namespace App\Services\Billing;

use App\Models\Billing\Coupon;

class CouponService
{
    public function isValid(Coupon $coupon, int $cartSubtotalMinor, ?string $currency): bool
    {
        if (! $coupon->active) {
            return false;
        }

        if ($coupon->currency && $coupon->currency !== $currency) {
            return false;
        }

        if ($coupon->min_cart_amount_minor && $cartSubtotalMinor < $coupon->min_cart_amount_minor) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(Coupon $coupon, int $subtotalMinor): int
    {
        if ($coupon->discount_type === 'percent') {
            $discount = (int) round($subtotalMinor * ($coupon->discount_value / 100));
        } else {
            $discount = $coupon->discount_value;
        }

        if ($coupon->max_discount_amount_minor) {
            $discount = min($discount, $coupon->max_discount_amount_minor);
        }

        return max(0, $discount);
    }
}
