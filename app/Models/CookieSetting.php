<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CookieSetting extends Model
{
    protected $fillable = [
        'title',
        'message',
        'btn_essential',
        'btn_stats',
        'position',
        'align',
        'theme',
        'consent_days',
        'ga_enabled',
        'ga_measurement_id',
        'consent_version',
    ];

    public static function current(): self
    {
        return static::query()->firstOrFail();
    }
}
