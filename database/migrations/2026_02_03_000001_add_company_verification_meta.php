<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('verified_method', 20)->nullable()->after('verified_at');
            $table->foreignId('verified_by_user_id')->nullable()->constrained('users')->nullOnDelete()->after('verified_method');
            $table->string('verified_by_email')->nullable()->after('verified_by_user_id');
            $table->string('verification_ack_status', 20)->nullable()->default('pending')->after('verified_by_email');
            $table->timestamp('verification_ack_at')->nullable()->after('verification_ack_status');
            $table->foreignId('verification_ack_by')->nullable()->constrained('users')->nullOnDelete()->after('verification_ack_at');
            $table->text('verification_ack_note')->nullable()->after('verification_ack_by');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['verified_by_user_id']);
            $table->dropForeign(['verification_ack_by']);
            $table->dropColumn([
                'verified_method',
                'verified_by_user_id',
                'verified_by_email',
                'verification_ack_status',
                'verification_ack_at',
                'verification_ack_by',
                'verification_ack_note',
            ]);
        });
    }
};
