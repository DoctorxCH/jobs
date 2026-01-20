<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (! Schema::hasColumn('companies', 'seats_purchased')) {
                $table->unsignedSmallInteger('seats_purchased')->default(1)->after('owner_user_id');
            }
            if (! Schema::hasColumn('companies', 'seats_locked')) {
                $table->unsignedSmallInteger('seats_locked')->default(0)->after('seats_purchased');
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'seats_locked')) {
                $table->dropColumn('seats_locked');
            }
            if (Schema::hasColumn('companies', 'seats_purchased')) {
                $table->dropColumn('seats_purchased');
            }
        });
    }
};
