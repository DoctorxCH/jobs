<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cookie_settings', function (Blueprint $table) {
            $table->id();

            // Banner content
            $table->string('title')->default('Cookies');
            $table->text('message')->nullable();

            $table->string('btn_essential')->default('Nur notwendige Cookies');
            $table->string('btn_stats')->default('Statistik erlauben');

            // Banner UI
            $table->enum('position', ['bottom', 'top'])->default('bottom');
            $table->enum('align', ['left', 'center', 'right'])->default('center');
            $table->string('theme')->default('dark');

            // Consent cookie
            $table->unsignedInteger('consent_days')->default(180);

            // Analytics
            $table->boolean('ga_enabled')->default(false);
            $table->string('ga_measurement_id')->nullable();

            // Re-consent
            $table->unsignedInteger('consent_version')->default(1);

            $table->timestamps();
        });

        DB::table('cookie_settings')->insert([
            'title' => 'Cookies',
            'message' => 'Wir verwenden nur notwendige Cookies fuer den Betrieb der Website. Optional kannst du Statistik-Cookies erlauben (Google Analytics).',
            'btn_essential' => 'Nur notwendige Cookies',
            'btn_stats' => 'Statistik erlauben',
            'position' => 'bottom',
            'align' => 'center',
            'theme' => 'dark',
            'consent_days' => 180,
            'ga_enabled' => false,
            'ga_measurement_id' => null,
            'consent_version' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('cookie_settings');
    }
};
