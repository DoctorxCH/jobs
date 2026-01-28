<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class City extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'region_id',
        'name',
        'is_active',
        'sort',
    ];

    protected $casts = [
        'region_id' => 'integer',
        'is_active' => 'boolean',
        'sort' => 'integer',
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}
