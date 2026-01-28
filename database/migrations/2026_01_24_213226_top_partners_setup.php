<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->boolean('is_top_partner')->default(false)->index();
            $table->boolean('is_top_partner_active')->default(true)->index();

            $table->date('is_top_partner_from')->nullable()->index();   // aktiv seit
            $table->date('is_top_partner_until')->nullable()->index();  // aktiv bis

            $table->unsignedInteger('top_partner_sort')->default(0)->index();
            $table->string('top_partner_logo_path')->nullable();        // eigenes Logo fuer Homepage
            $table->timestamp('top_partner_activated_at')->nullable()->index();
        });

        Schema::create('top_partners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            $table->boolean('is_top_partner')->default(true)->index();
            $table->boolean('is_active')->default(true)->index();

            $table->date('active_from')->nullable()->index();
            $table->date('active_until')->nullable()->index();

            $table->string('logo_path')->nullable();
            $table->unsignedInteger('sort')->default(0)->index();

            $table->timestamp('activated_at')->nullable()->index();
            $table->timestamps();

            $table->unique('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('top_partners');

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'is_top_partner',
                'is_top_partner_active',
                'is_top_partner_from',
                'is_top_partner_until',
                'top_partner_sort',
                'top_partner_logo_path',
                'top_partner_activated_at',
            ]);
        });
    }
};
