<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private const UNIQUE_NAME = 'company_invite_unique';

    private function indexExists(string $indexName): bool
    {
        $row = DB::selectOne(
            "SELECT 1
             FROM information_schema.statistics
             WHERE table_schema = DATABASE()
               AND table_name = 'company_invitations'
               AND index_name = ?
             LIMIT 1",
            [$indexName]
        );

        return $row !== null;
    }

    public function up(): void
    {
        // expires_at nullable machen (ohne doctrine/dbal)
        DB::statement('ALTER TABLE company_invitations MODIFY expires_at TIMESTAMP NULL');

        // Unique(company_id, email) nur hinzufügen, wenn er noch nicht existiert
        if (! $this->indexExists(self::UNIQUE_NAME)) {
            Schema::table('company_invitations', function (Blueprint $table) {
                $table->unique(['company_id', 'email'], self::UNIQUE_NAME);
            });
        }
    }

    public function down(): void
    {
        // Unique nur entfernen, wenn er existiert
        if ($this->indexExists(self::UNIQUE_NAME)) {
            Schema::table('company_invitations', function (Blueprint $table) {
                $table->dropUnique(self::UNIQUE_NAME);
            });
        }

        // expires_at wieder NOT NULL machen
        DB::statement('ALTER TABLE company_invitations MODIFY expires_at TIMESTAMP NOT NULL');
    }
};
