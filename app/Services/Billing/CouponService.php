<?php

namespace App\Services\Billing;

use App\Models\Billing\Coupon;
use Illuminate\Support\Carbon;

class CouponService
{
    public function validateCoupon(string $code, array $context): array
    {
        $code = trim(mb_strtoupper($code));

        $coupon = Coupon::query()
            ->where('code', $code)
            ->first();

        if (! $coupon) {
            return $this->invalid('Coupon not found.');
        }

        if (! $coupon->active) {
            return $this->invalid('Coupon is inactive.');
        }

        $now = Carbon::now();
        if ($coupon->valid_from && $coupon->valid_from->gt($now)) {
            return $this->invalid('Coupon not active yet.');
        }
        if ($coupon->valid_to && $coupon->valid_to->lt($now)) {
            return $this->invalid('Coupon has expired.');
        }

        $subtotalNetMinor = (int) ($context['subtotal_net_minor'] ?? 0);
        if ($coupon->min_cart_amount_minor && $subtotalNetMinor < (int) $coupon->min_cart_amount_minor) {
            return $this->invalid('Cart total too low for this coupon.');
        }

        if ($coupon->discount_type === 'fixed') {
            $currency = strtoupper((string) ($context['currency'] ?? ''));
            if ($coupon->currency && $currency && strtoupper($coupon->currency) !== $currency) {
                return $this->invalid('Coupon currency mismatch.');
            }
        }

        $companyId = $context['company_id'] ?? null;
        $userId = $context['user_id'] ?? null;

        if ($coupon->usage_limit_total) {
            $used = $coupon->redemptions()->count();
            if ($used >= (int) $coupon->usage_limit_total) {
                return $this->invalid('Coupon usage limit reached.');
            }
        }

        if ($coupon->usage_limit_per_company && $companyId) {
            $used = $coupon->redemptions()->where('company_id', $companyId)->count();
            if ($used >= (int) $coupon->usage_limit_per_company) {
                return $this->invalid('Coupon usage limit reached for company.');
            }
        }

        if ($coupon->usage_limit_per_user && $userId) {
            $used = $coupon->redemptions()->where('user_id', $userId)->count();
            if ($used >= (int) $coupon->usage_limit_per_user) {
                return $this->invalid('Coupon usage limit reached for user.');
            }
        }

        if (! $this->scopeMatches($coupon, $context)) {
            return $this->invalid('Coupon not valid for this product.');
        }

        $availableNetMinor = (int) ($context['available_net_minor'] ?? $subtotalNetMinor);
        $discountMinor = $this->calculateDiscountMinor($coupon, $availableNetMinor);

        if ($discountMinor <= 0) {
            return $this->invalid('Coupon does not apply to this cart.');
        }

        return [
            'valid' => true,
            'coupon' => $coupon,
            'discount_minor' => $discountMinor,
        ];
    }

    protected function calculateDiscountMinor(Coupon $coupon, int $availableNetMinor): int
    {
        $discountMinor = 0;

        if ($coupon->discount_type === 'percent') {
            $discountMinor = (int) round($availableNetMinor * ((float) $coupon->discount_value / 100), 0);
        } elseif ($coupon->discount_type === 'fixed') {
            $discountMinor = (int) round(((float) $coupon->discount_value) * 100, 0);
        }

        if ($coupon->max_discount_amount_minor) {
            $discountMinor = min($discountMinor, (int) $coupon->max_discount_amount_minor);
        }

        return max(0, min($discountMinor, $availableNetMinor));
    }

    protected function scopeMatches(Coupon $coupon, array $context): bool
    {
        $scopes = $coupon->scopes()->get();
        if ($scopes->isEmpty()) {
            return true;
        }

        $companyId = $context['company_id'] ?? null;
        $productId = $context['product_id'] ?? null;
        $categoryId = $context['category_id'] ?? null;

        foreach ($scopes as $scope) {
            if ($scope->scope_type === 'global') {
                return true;
            }
            if ($scope->scope_type === 'company' && $companyId && (int) $scope->scope_id === (int) $companyId) {
                return true;
            }
            if ($scope->scope_type === 'product' && $productId && (int) $scope->scope_id === (int) $productId) {
                return true;
            }
            if ($scope->scope_type === 'category' && $categoryId && (int) $scope->scope_id === (int) $categoryId) {
                return true;
            }
        }

        return false;
    }

    protected function invalid(string $message): array
    {
        return [
            'valid' => false,
            'message' => $message,
        ];
    }
}
