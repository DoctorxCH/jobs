<?php

namespace App\Models\Billing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaxClass extends Model
{
    protected $table = 'tax_classes';

    protected $fillable = [
        'key',
        'name',
        ];

    protected $casts = [
        'name' => 'string',
    ];

    public function taxRates(): HasMany
    {
        return $this->hasMany(TaxRate::class, 'tax_class_id');
    }
}
