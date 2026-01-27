<?php

namespace App\Models\Billing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'key',
        'name',
        'description',
        'product_type',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }
}
