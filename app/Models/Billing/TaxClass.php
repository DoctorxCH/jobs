<?php

namespace App\Models\Billing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaxClass extends Model
{
    protected $fillable = [
        'key',
        'name',
    ];

    public function taxRates(): HasMany
    {
        return $this->hasMany(TaxRate::class);
    }
}
