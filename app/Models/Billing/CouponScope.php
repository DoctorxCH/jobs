<?php

namespace App\Models\Billing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponScope extends Model
{
    protected $table = 'coupon_scopes';

    protected $fillable = [
        'coupon_id',
        'scope_type',
        'scope_id',
    ];

    protected $casts = [
        'scope_id' => 'integer',
    ];

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }
}
