<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'company_id')) {
                $table->foreignId('company_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('companies')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('users', 'is_company_owner')) {
                $table->boolean('is_company_owner')->default(false)->after('company_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_company_owner')) {
                $table->dropColumn('is_company_owner');
            }

            if (Schema::hasColumn('users', 'company_id')) {
                $table->dropConstrainedForeignId('company_id');
            }
        });
    }
};
