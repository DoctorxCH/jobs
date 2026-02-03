<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->unsignedInteger('session_rev')->default(1)->after('default_locale');
            $table->string('force_logout_message', 255)->nullable()->after('session_rev');
            $table->unsignedInteger('idle_timeout_minutes')->nullable()->after('force_logout_message');
            $table->unsignedInteger('reauth_for_sensitive_minutes')->nullable()->after('idle_timeout_minutes');
            $table->unsignedInteger('max_login_attempts')->nullable()->after('reauth_for_sensitive_minutes');
            $table->unsignedInteger('lockout_minutes')->nullable()->after('max_login_attempts');

            $table->boolean('maintenance_banner_enabled')->default(false)->after('lockout_minutes');
            $table->text('maintenance_banner_text')->nullable()->after('maintenance_banner_enabled');

            $table->boolean('superfaktura_enabled')->default(true)->after('maintenance_banner_text');
            $table->unsignedInteger('superfaktura_timeout_seconds')->nullable()->after('superfaktura_enabled');
            $table->text('webhook_signing_secret')->nullable()->after('superfaktura_timeout_seconds');

            $table->unsignedInteger('max_logo_kb')->nullable()->after('webhook_signing_secret');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
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
            ]);
        });
    }
};