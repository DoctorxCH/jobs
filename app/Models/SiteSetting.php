<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SiteSetting extends Model
{
    protected $fillable = [
        'default_locale',
        'session_rev',
        'force_logout_message',
        'idle_timeout_minutes',
        'reauth_for_sensitive_minutes',
        'max_login_attempts',
        'lockout_minutes',
        'maintenance_banner_enabled',
        'maintenance_banner_text',
        'superfaktura_enabled',
        'superfaktura_timeout_seconds',
        'webhook_signing_secret',
        'max_logo_kb',
    ];

    protected $casts = [
        'session_rev' => 'integer',
        'idle_timeout_minutes' => 'integer',
        'reauth_for_sensitive_minutes' => 'integer',
        'max_login_attempts' => 'integer',
        'lockout_minutes' => 'integer',
        'superfaktura_timeout_seconds' => 'integer',
        'max_logo_kb' => 'integer',
        'maintenance_banner_enabled' => 'boolean',
        'superfaktura_enabled' => 'boolean',
    ];

    public static function current(): ?self
    {
        if (! Schema::hasTable('site_settings')) {
            return null;
        }

        return Cache::remember('site_settings.current', 60, function () {
            return self::ensureRow();
        });
    }

    public static function ensureRow(): self
    {
        if (! Schema::hasTable('site_settings')) {
            return new self();
        }

        $defaults = [
            'default_locale' => config('app.locale', 'en'),
        ];

        if (Schema::hasColumn('site_settings', 'session_rev')) {
            $defaults['session_rev'] = 1;
        }
        if (Schema::hasColumn('site_settings', 'maintenance_banner_enabled')) {
            $defaults['maintenance_banner_enabled'] = false;
        }
        if (Schema::hasColumn('site_settings', 'superfaktura_enabled')) {
            $defaults['superfaktura_enabled'] = true;
        }

        return self::query()->firstOrCreate(['id' => 1], $defaults);
    }

    protected function webhookSigningSecret(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (blank($value)) {
                    return null;
                }

                try {
                    return Crypt::decryptString($value);
                } catch (\Throwable $e) {
                    return null;
                }
            },
            set: function ($value) {
                if (blank($value)) {
                    return null;
                }

                return Crypt::encryptString(Str::of($value)->toString());
            },
        );
    }
}
