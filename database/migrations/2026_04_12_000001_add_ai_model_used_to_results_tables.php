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
            $table->string('ai_model_used', 191)->nullable()->after('ai_generated_at');
        });

        Schema::table('practice_results', function (Blueprint $table) {
            $table->string('ai_model_used', 191)->nullable()->after('ai_generated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropColumn('ai_model_used');
        });

        Schema::table('practice_results', function (Blueprint $table) {
            $table->dropColumn('ai_model_used');
        });
    }
};
