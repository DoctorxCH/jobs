<?php

namespace App\Models\Billing;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'user_id',
        'currency',
        'status',
        'subtotal_net_minor',
        'discount_minor',
        'company_discount_minor',
        'tax_minor',
        'total_gross_minor',
        'tax_rule_applied',
        'reverse_charge',
        'tax_rate_percent_snapshot',
        'coupon_code_snapshot',
        'coupon_name_snapshot',
        'coupon_discount_type_snapshot',
        'coupon_discount_value_snapshot',
        'expires_at',
    ];

    protected $casts = [
        'subtotal_net_minor' => 'integer',
        'discount_minor' => 'integer',
        'company_discount_minor' => 'integer',
        'tax_minor' => 'integer',
        'total_gross_minor' => 'integer',
        'reverse_charge' => 'boolean',
        'tax_rate_percent_snapshot' => 'decimal:4',
        'coupon_discount_value_snapshot' => 'integer',
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

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }
}
