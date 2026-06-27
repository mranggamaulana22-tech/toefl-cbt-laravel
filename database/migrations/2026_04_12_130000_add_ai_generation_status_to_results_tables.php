<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->string('ai_status', 24)->nullable()->after('ai_model_used');
            $table->text('ai_error')->nullable()->after('ai_status');
            $table->timestamp('ai_requested_at')->nullable()->after('ai_error');
            $table->timestamp('ai_completed_at')->nullable()->after('ai_requested_at');
            $table->index(['user_id', 'ai_status']);
        });

        Schema::table('practice_results', function (Blueprint $table) {
            $table->string('ai_status', 24)->nullable()->after('ai_model_used');
            $table->text('ai_error')->nullable()->after('ai_status');
            $table->timestamp('ai_requested_at')->nullable()->after('ai_error');
            $table->timestamp('ai_completed_at')->nullable()->after('ai_requested_at');
            $table->index(['user_id', 'ai_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'ai_status']);
            $table->dropColumn(['ai_status', 'ai_error', 'ai_requested_at', 'ai_completed_at']);
        });

        Schema::table('practice_results', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'ai_status']);
            $table->dropColumn(['ai_status', 'ai_error', 'ai_requested_at', 'ai_completed_at']);
        });
    }
};
