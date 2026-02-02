<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobLanguageLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'label',
        'is_active',
        'sort',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort' => 'integer',
    ];
}
