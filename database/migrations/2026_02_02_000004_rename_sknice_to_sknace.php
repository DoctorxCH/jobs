<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sknice_positions') && ! Schema::hasTable('sknace_positions')) {
            Schema::rename('sknice_positions', 'sknace_positions');
        }

        if (Schema::hasColumn('jobs', 'sknice_position_id')) {
            Schema::table('jobs', function (Blueprint $table) {
                $table->dropForeign(['sknice_position_id']);
                $table->dropIndex('jobs_sknice_position_idx');
            });

            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE jobs CHANGE sknice_position_id sknace_position_id BIGINT UNSIGNED NULL');
            } elseif ($driver === 'pgsql') {
                DB::statement('ALTER TABLE jobs RENAME COLUMN sknice_position_id TO sknace_position_id');
            } else {
                DB::statement('ALTER TABLE jobs RENAME COLUMN sknice_position_id TO sknace_position_id');
            }

            Schema::table('jobs', function (Blueprint $table) {
                $table->foreign('sknace_position_id')
                    ->references('id')
                    ->on('sknace_positions');
                $table->index(['sknace_position_id'], 'jobs_sknace_position_idx');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('jobs', 'sknace_position_id')) {
            Schema::table('jobs', function (Blueprint $table) {
                $table->dropForeign(['sknace_position_id']);
                $table->dropIndex('jobs_sknace_position_idx');
            });

            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE jobs CHANGE sknace_position_id sknice_position_id BIGINT UNSIGNED NULL');
            } elseif ($driver === 'pgsql') {
                DB::statement('ALTER TABLE jobs RENAME COLUMN sknace_position_id TO sknice_position_id');
            } else {
                DB::statement('ALTER TABLE jobs RENAME COLUMN sknace_position_id TO sknice_position_id');
            }

            Schema::table('jobs', function (Blueprint $table) {
                $table->foreign('sknice_position_id')
                    ->references('id')
                    ->on('sknice_positions');
                $table->index(['sknice_position_id'], 'jobs_sknice_position_idx');
            });
        }

        if (Schema::hasTable('sknace_positions') && ! Schema::hasTable('sknice_positions')) {
            Schema::rename('sknace_positions', 'sknice_positions');
        }
    }
};
