<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private const UNIQUE_USER = 'company_user_user_id_unique';
    private const UNIQUE_COMPOSITE = 'company_user_company_id_user_id_unique';

    private function indexExists(string $indexName): bool
    {
        $row = DB::selectOne(
            "SELECT 1
             FROM information_schema.statistics
             WHERE table_schema = DATABASE()
               AND table_name = 'company_user'
               AND index_name = ?
             LIMIT 1",
            [$indexName]
        );

        return $row !== null;
    }

    public function up(): void
    {
        // 1) Alte Unique(company_id, user_id) entfernen, falls vorhanden
        if ($this->indexExists(self::UNIQUE_COMPOSITE)) {
            Schema::table('company_user', function (Blueprint $table) {
                $table->dropUnique(self::UNIQUE_COMPOSITE);
            });
        }

        // 2) Neue Unique(user_id) hinzufügen, falls noch nicht vorhanden
        if (! $this->indexExists(self::UNIQUE_USER)) {
            Schema::table('company_user', function (Blueprint $table) {
                $table->unique(['user_id'], self::UNIQUE_USER);
            });
        }
    }

    public function down(): void
    {
        // rollback: Unique(user_id) entfernen
        if ($this->indexExists(self::UNIQUE_USER)) {
            Schema::table('company_user', function (Blueprint $table) {
                $table->dropUnique(self::UNIQUE_USER);
            });
        }

        // rollback: Unique(company_id, user_id) wieder herstellen
        if (! $this->indexExists(self::UNIQUE_COMPOSITE)) {
            Schema::table('company_user', function (Blueprint $table) {
                $table->unique(['company_id', 'user_id'], self::UNIQUE_COMPOSITE);
            });
        }
    }
};
