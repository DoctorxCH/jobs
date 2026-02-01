<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('name', 150);
            $table->string('email', 150);
            $table->string('subject', 200);
            $table->text('message');

            $table->string('status', 30)->default('new');
            $table->unsignedBigInteger('assigned_to_user_id')->nullable();
            $table->text('internal_notes')->nullable();

            $table->text('reply_body')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->unsignedBigInteger('reply_sent_by')->nullable();

            $table->timestamps();

            $table->index(['company_id']);
            $table->index(['user_id']);
            $table->index(['status']);
            $table->index(['assigned_to_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_requests');
    }
};
