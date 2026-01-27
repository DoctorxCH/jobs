<?php

namespace App\Models\Billing;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'currency',
        'status',
        'tax_rule_applied',
        'reverse_charge',
        'tax_rate_percent_snapshot',
        'subtotal_net_minor',
        'discount_minor',
        'tax_minor',
        'total_gross_minor',
        'coupon_id',
        'coupon_code_snapshot',
        'coupon_discount_minor',
        'company_discount_minor',
        'expires_at',
    ];

    protected $casts = [
        'reverse_charge' => 'boolean',
        'tax_rate_percent_snapshot' => 'decimal:2',
        'subtotal_net_minor' => 'integer',
        'discount_minor' => 'integer',
        'tax_minor' => 'integer',
        'total_gross_minor' => 'integer',
        'coupon_discount_minor' => 'integer',
        'company_discount_minor' => 'integer',
        'expires_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }
}
