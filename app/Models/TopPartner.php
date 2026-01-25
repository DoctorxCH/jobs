<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class TopPartner extends Model
{
    protected $fillable = [
        'company_id',
        'is_top_partner',
        'is_active',
        'active_from',
        'active_until',
        'logo_path',
        'sort',
        'activated_at',
    ];

    protected $casts = [
        'is_top_partner' => 'boolean',
        'is_active' => 'boolean',
        'active_from' => 'date',
        'active_until' => 'date',
        'sort' => 'integer',
        'activated_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    protected static function booted(): void
    {
        static::saving(function (self $p) {
            if ($p->is_top_partner && $p->is_active && empty($p->activated_at)) {
                $p->activated_at = now();
            }
        });

        static::saved(function (self $p) {
            if ($p->company) {
                $p->company->forceFill([
                    'is_top_partner' => (bool) $p->is_top_partner,
                    'is_top_partner_active' => (bool) $p->is_active,
                    'is_top_partner_from' => $p->active_from,
                    'is_top_partner_until' => $p->active_until,
                    'top_partner_sort' => (int) $p->sort,
                    'top_partner_logo_path' => $p->logo_path,
                    'top_partner_activated_at' => $p->activated_at,
                ])->saveQuietly();
            }

            Cache::forget('home_top_partners_v1');
        });

        static::deleted(function (self $p) {
            if ($p->company) {
                $p->company->forceFill([
                    'is_top_partner' => false,
                    'is_top_partner_active' => true,
                    'is_top_partner_from' => null,
                    'is_top_partner_until' => null,
                    'top_partner_sort' => 0,
                    'top_partner_logo_path' => null,
                    'top_partner_activated_at' => null,
                ])->saveQuietly();
            }

            Cache::forget('home_top_partners_v1');
        });
    }
}
