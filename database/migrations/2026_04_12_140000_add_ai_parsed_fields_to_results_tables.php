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
            $table->json('ai_parsed_json')->nullable()->after('ai_model_used');
            $table->string('ai_parser_version', 40)->nullable()->after('ai_parsed_json');
        });

        Schema::table('practice_results', function (Blueprint $table) {
            $table->json('ai_parsed_json')->nullable()->after('ai_model_used');
            $table->string('ai_parser_version', 40)->nullable()->after('ai_parsed_json');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropColumn(['ai_parsed_json', 'ai_parser_version']);
        });

        Schema::table('practice_results', function (Blueprint $table) {
            $table->dropColumn(['ai_parsed_json', 'ai_parser_version']);
        });
    }
};
