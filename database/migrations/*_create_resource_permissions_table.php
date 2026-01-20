<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('resource_permissions', function (Blueprint $table) {
            $table->id();

            $table->string('resource', 80); // e.g. companies, platform_users
            $table->string('role_name', 100); // e.g. platform.moderator

            $table->boolean('can_view')->default(false);
            $table->boolean('can_create')->default(false);
            $table->boolean('can_edit')->default(false);
            $table->boolean('can_delete')->default(false);

            $table->timestamps();

            $table->unique(['resource', 'role_name'], 'res_perm_unique');
            $table->index(['resource'], 'res_perm_res_idx');
            $table->index(['role_name'], 'res_perm_role_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resource_permissions');
    }
};

