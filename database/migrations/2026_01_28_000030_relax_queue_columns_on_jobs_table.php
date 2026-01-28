<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // These columns belong to Laravel's DB queue "jobs" table.
        // In this project, "jobs" is used for job listings, so we relax them to avoid insert failures.
        DB::statement("ALTER TABLE `jobs` MODIFY `queue` VARCHAR(255) NULL DEFAULT NULL");
        DB::statement("ALTER TABLE `jobs` MODIFY `payload` LONGTEXT NULL");
        DB::statement("ALTER TABLE `jobs` MODIFY `attempts` TINYINT UNSIGNED NULL DEFAULT NULL");
        DB::statement("ALTER TABLE `jobs` MODIFY `reserved_at` INT UNSIGNED NULL DEFAULT NULL");
        DB::statement("ALTER TABLE `jobs` MODIFY `available_at` INT UNSIGNED NULL DEFAULT NULL");
    }

    public function down(): void
    {
        // best-effort revert to typical queue-table constraints (may not match original)
        DB::statement("ALTER TABLE `jobs` MODIFY `queue` VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE `jobs` MODIFY `payload` LONGTEXT NOT NULL");
        DB::statement("ALTER TABLE `jobs` MODIFY `attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE `jobs` MODIFY `reserved_at` INT UNSIGNED NULL");
        DB::statement("ALTER TABLE `jobs` MODIFY `available_at` INT UNSIGNED NOT NULL");
    }
};
