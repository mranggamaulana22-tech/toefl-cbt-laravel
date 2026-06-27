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
            $table->longText('ai_suggestion')->nullable()->after('score_total');
            $table->timestamp('ai_generated_at')->nullable()->after('ai_suggestion');
        });

        Schema::table('practice_results', function (Blueprint $table) {
            $table->longText('ai_suggestion')->nullable()->after('score_total');
            $table->timestamp('ai_generated_at')->nullable()->after('ai_suggestion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropColumn(['ai_suggestion', 'ai_generated_at']);
        });

        Schema::table('practice_results', function (Blueprint $table) {
            $table->dropColumn(['ai_suggestion', 'ai_generated_at']);
        });
    }
};
