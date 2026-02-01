<?php

namespace App\Models\Billing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxRate extends Model
{
    protected $table = 'tax_rates';

    protected $fillable = [
        'tax_class_id',
        'rate',
        'active',
        'valid_from',
        'valid_to',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'active' => 'boolean',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
    ];

    public function taxClass(): BelongsTo
    {
        return $this->belongsTo(TaxClass::class, 'tax_class_id');
    }
}
