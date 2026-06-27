<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remove redundant question data from practice_result_items.
     * These columns store full question snapshots that can be retrieved
     * from the practice_question relationship, wasting ~500 bytes per item.
     * 
     * For auditing, question_hash and question_snapshot are retained.
     */
    public function up(): void
    {
        Schema::table('practice_result_items', function (Blueprint $table) {
            $table->dropColumn([
                'category',
                'question_text',
                'option_a',
                'option_b',
                'option_c',
                'option_d',
                'correct_answer',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('practice_result_items', function (Blueprint $table) {
            $table->string('category', 50)->after('question_order');
            $table->text('question_text')->after('category');
            $table->text('option_a')->after('question_text');
            $table->text('option_b')->after('option_a');
            $table->text('option_c')->after('option_b');
            $table->text('option_d')->after('option_c');
            $table->string('correct_answer', 1)->after('option_d');
        });
    }
};
