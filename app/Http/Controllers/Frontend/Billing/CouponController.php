<?php

namespace App\Http\Controllers\Frontend\Billing;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\Billing\CouponService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class CouponController extends Controller
{
    public function apply(Request $request, CouponService $couponService): RedirectResponse
    {
        $data = $request->validate([
            'coupon_code' => ['required', 'string', 'max:50'],
            'qty' => ['required', 'integer', 'min:1', 'max:100'],
            'unit_net_minor' => ['required', 'integer', 'min:0'],
            'currency' => ['nullable', 'string', 'max:3'],
            'product_id' => ['nullable', 'integer'],
            'category_id' => ['nullable', 'integer'],
        ]);

        $user = $request->user();
        $companyId = $this->resolveCompanyId($user?->id);

        $subtotalNetMinor = (int) $data['qty'] * (int) $data['unit_net_minor'];

        $applied = collect(session('checkout.coupons', []));
        $code = mb_strtoupper(trim($data['coupon_code']));

        if ($applied->firstWhere('code', $code)) {
            return back()->withErrors(['coupon_code' => 'Coupon already applied.']);
        }

        $context = [
            'subtotal_net_minor' => $subtotalNetMinor,
            'available_net_minor' => $this->availableNetMinor($subtotalNetMinor, $applied),
            'currency' => strtoupper($data['currency']),
            'company_id' => $companyId,
            'user_id' => $user?->id,
            'product_id' => $data['product_id'] ?? null,
            'category_id' => $data['category_id'] ?? null,
        ];

        $result = $couponService->validateCoupon($code, $context);

        if (! $result['valid']) {
            return back()->withErrors(['coupon_code' => $result['message'] ?? 'Coupon invalid.']);
        }

        $coupon = $result['coupon'];

        if (! $coupon->stackable && $applied->isNotEmpty()) {
            return back()->withErrors(['coupon_code' => 'This coupon cannot be combined with other coupons.']);
        }

        if ($applied->contains(fn ($c) => Arr::get($c, 'stackable') === false)) {
            return back()->withErrors(['coupon_code' => 'You already have a non-stackable coupon applied.']);
        }

        $applied->push([
            'id' => $coupon->id,
            'code' => $coupon->code,
            'name' => $coupon->name,
            'discount_type' => $coupon->discount_type,
            'discount_value' => (float) $coupon->discount_value,
            'currency' => $coupon->currency,
            'max_discount_amount_minor' => $coupon->max_discount_amount_minor,
            'stackable' => (bool) $coupon->stackable,
        ]);

        session(['checkout.coupons' => $applied->values()->all()]);

        return back()->with('coupon_applied', 'Coupon applied.');
    }

    public function remove(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'coupon_code' => ['required', 'string', 'max:50'],
        ]);

        $code = mb_strtoupper(trim($data['coupon_code']));

        $applied = collect(session('checkout.coupons', []))
            ->reject(fn ($c) => Arr::get($c, 'code') === $code)
            ->values()
            ->all();

        session(['checkout.coupons' => $applied]);

        return back()->with('coupon_removed', 'Coupon removed.');
    }

    protected function resolveCompanyId(?int $userId): ?int
    {
        if (! $userId) {
            return null;
        }

        $user = auth()->user();
        if ($user && method_exists($user, 'effectiveCompanyId')) {
            $companyId = $user->effectiveCompanyId();
            if ($companyId) {
                return $companyId;
            }
        }

        return Company::query()
            ->where('owner_user_id', $userId)
            ->value('id');
    }

    protected function availableNetMinor(int $subtotalNetMinor, $applied): int
    {
        $totalDiscount = 0;
        foreach ($applied as $coupon) {
            $discount = 0;
            $type = $coupon['discount_type'] ?? null;
            $value = (float) ($coupon['discount_value'] ?? 0);
            if ($type === 'percent') {
                $discount = (int) round($subtotalNetMinor * ($value / 100), 0);
            } elseif ($type === 'fixed') {
                $discount = (int) round($value * 100, 0);
            }

            $max = $coupon['max_discount_amount_minor'] ?? null;
            if ($max) {
                $discount = min($discount, (int) $max);
            }

            $totalDiscount += max(0, $discount);
        }

        return max(0, $subtotalNetMinor - $totalDiscount);
    }
}
