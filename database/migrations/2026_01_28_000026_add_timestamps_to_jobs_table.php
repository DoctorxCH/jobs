<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            if (! Schema::hasColumn('jobs', 'created_at')) {
                $table->timestamp('created_at')->nullable()->after('hr_phone');
            }

            if (! Schema::hasColumn('jobs', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            }
        });

        if (Schema::hasColumn('jobs', 'created_at')) {
            DB::statement("UPDATE `jobs` SET `created_at` = COALESCE(`created_at`, NOW())");
        }

        if (Schema::hasColumn('jobs', 'updated_at')) {
            DB::statement("UPDATE `jobs` SET `updated_at` = COALESCE(`updated_at`, `created_at`, NOW())");
        }
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            if (Schema::hasColumn('jobs', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
            if (Schema::hasColumn('jobs', 'created_at')) {
                $table->dropColumn('created_at');
            }
        });
    }
};
