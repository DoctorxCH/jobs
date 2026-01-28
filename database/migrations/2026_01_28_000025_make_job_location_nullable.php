<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop FKs (Laravel default names)
        DB::statement('ALTER TABLE `jobs` DROP FOREIGN KEY `jobs_country_id_foreign`');
        DB::statement('ALTER TABLE `jobs` DROP FOREIGN KEY `jobs_region_id_foreign`');
        DB::statement('ALTER TABLE `jobs` DROP FOREIGN KEY `jobs_city_id_foreign`');

        // Make nullable
        DB::statement('ALTER TABLE `jobs` MODIFY `country_id` BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `jobs` MODIFY `region_id` BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `jobs` MODIFY `city_id` BIGINT UNSIGNED NULL');

        // Re-add FKs
        DB::statement('ALTER TABLE `jobs` ADD CONSTRAINT `jobs_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries`(`id`) ON DELETE SET NULL');
        DB::statement('ALTER TABLE `jobs` ADD CONSTRAINT `jobs_region_id_foreign` FOREIGN KEY (`region_id`) REFERENCES `regions`(`id`) ON DELETE SET NULL');
        DB::statement('ALTER TABLE `jobs` ADD CONSTRAINT `jobs_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities`(`id`) ON DELETE SET NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `jobs` DROP FOREIGN KEY `jobs_country_id_foreign`');
        DB::statement('ALTER TABLE `jobs` DROP FOREIGN KEY `jobs_region_id_foreign`');
        DB::statement('ALTER TABLE `jobs` DROP FOREIGN KEY `jobs_city_id_foreign`');

        // Back to NOT NULL (fallback 1)
        DB::statement('ALTER TABLE `jobs` MODIFY `country_id` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `jobs` MODIFY `region_id` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `jobs` MODIFY `city_id` BIGINT UNSIGNED NOT NULL');

        DB::statement('ALTER TABLE `jobs` ADD CONSTRAINT `jobs_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries`(`id`)');
        DB::statement('ALTER TABLE `jobs` ADD CONSTRAINT `jobs_region_id_foreign` FOREIGN KEY (`region_id`) REFERENCES `regions`(`id`)');
        DB::statement('ALTER TABLE `jobs` ADD CONSTRAINT `jobs_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities`(`id`)');
    }
};
