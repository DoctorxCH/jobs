<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SknacePosition extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'code',
        'title',
        'is_active',
        'sort',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort' => 'integer',
    ];
}
