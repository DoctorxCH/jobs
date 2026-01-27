<?php

namespace App\Models\Billing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxRate extends Model
{
    protected $fillable = [
        'country_code',
        'tax_class_id',
        'rate_percent',
        'valid_from',
        'valid_to',
    ];

    protected $casts = [
        'rate_percent' => 'decimal:2',
        'valid_from' => 'date',
        'valid_to' => 'date',
    ];

    public function taxClass(): BelongsTo
    {
        return $this->belongsTo(TaxClass::class);
    }
}
