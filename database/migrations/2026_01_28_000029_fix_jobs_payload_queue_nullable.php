<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Make these queue-table leftovers non-blocking for job listing inserts
        DB::statement("ALTER TABLE `jobs` MODIFY `queue` VARCHAR(255) NULL DEFAULT NULL");
        DB::statement("ALTER TABLE `jobs` MODIFY `payload` LONGTEXT NULL");
    }

    public function down(): void
    {
        // best-effort revert
        DB::statement("ALTER TABLE `jobs` MODIFY `queue` VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE `jobs` MODIFY `payload` LONGTEXT NOT NULL");
    }
};
