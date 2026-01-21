<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanyCategory extends Model
{
    use HasFactory;

    protected $table = 'company_categories';

    /**
     * Absichtlich offen gelassen, damit es nicht an Fillable scheitert,
     * falls die Migration mehr/weniger Spalten hat.
     */
    protected $guarded = [];

    protected $casts = [
        'active' => 'boolean',
        'sort' => 'integer',
    ];

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class, 'category_id');
    }
}
