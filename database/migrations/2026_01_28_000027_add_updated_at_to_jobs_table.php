<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('jobs', 'updated_at')) {
            Schema::table('jobs', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            });
        }

        if (Schema::hasColumn('jobs', 'updated_at')) {
            DB::statement("UPDATE `jobs` SET `updated_at` = COALESCE(`updated_at`, `created_at`, NOW())");
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('jobs', 'updated_at')) {
            Schema::table('jobs', function (Blueprint $table) {
                $table->dropColumn('updated_at');
            });
        }
    }
};
