<?php

namespace App\Models\Billing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPrice extends Model
{
    protected $fillable = [
        'product_id',
        'currency',
        'unit_net_amount_minor',
        'tax_class_id',
        'valid_from',
        'valid_to',
        'active',
    ];

    protected $casts = [
        'unit_net_amount_minor' => 'integer',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
        'active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function taxClass(): BelongsTo
    {
        return $this->belongsTo(TaxClass::class);
    }
}
