<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE job_languages MODIFY language_code VARCHAR(10)");
            DB::statement("ALTER TABLE job_languages MODIFY level VARCHAR(20)");
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE job_languages ALTER COLUMN language_code TYPE VARCHAR(10)');
            DB::statement('ALTER TABLE job_languages ALTER COLUMN level TYPE VARCHAR(20)');
        }
        // sqlite: enum is not enforced, no change needed
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE job_languages MODIFY language_code CHAR(2)");
            DB::statement("ALTER TABLE job_languages MODIFY level ENUM('A1','A2','B1','B2','C1','C2','native')");
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE job_languages ALTER COLUMN language_code TYPE CHAR(2)');
            DB::statement('ALTER TABLE job_languages ALTER COLUMN level TYPE VARCHAR(20)');
        }
        // sqlite: no change needed
    }
};
