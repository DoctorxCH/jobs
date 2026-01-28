<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `jobs` MODIFY `created_at` DATETIME NULL");
        DB::statement("ALTER TABLE `jobs` MODIFY `updated_at` DATETIME NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `jobs` MODIFY `created_at` TIMESTAMP NULL DEFAULT NULL");
        DB::statement("ALTER TABLE `jobs` MODIFY `updated_at` TIMESTAMP NULL DEFAULT NULL");
    }
};
