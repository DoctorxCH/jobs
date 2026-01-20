<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('company_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // company role (NOT spatie role)
            $table->string('role', 20)->default('member');   // owner|member|recruiter|viewer
            $table->string('status', 20)->default('active'); // active|invited|disabled

            $table->timestamp('invited_at')->nullable();
            $table->timestamp('accepted_at')->nullable();

            $table->timestamps();

            $table->unique(['company_id', 'user_id'], 'company_user_unique');
            $table->index(['company_id', 'status'], 'company_user_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_user');
    }
};
