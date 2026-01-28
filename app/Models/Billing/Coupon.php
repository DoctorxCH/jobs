<?php

namespace App\Models\Billing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'currency',
        'valid_from',
        'valid_to',
        'min_cart_amount_minor',
        'max_discount_amount_minor',
        'usage_limit_total',
        'usage_limit_per_company',
        'usage_limit_per_user',
        'stackable',
        'active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
        'min_cart_amount_minor' => 'integer',
        'max_discount_amount_minor' => 'integer',
        'usage_limit_total' => 'integer',
        'usage_limit_per_company' => 'integer',
        'usage_limit_per_user' => 'integer',
        'stackable' => 'boolean',
        'active' => 'boolean',
    ];

    public function scopes(): HasMany
    {
        return $this->hasMany(CouponScope::class);
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(CouponRedemption::class);
    }
}
