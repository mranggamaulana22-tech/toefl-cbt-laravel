<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practice_result_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice_result_id')->constrained()->cascadeOnDelete();
            $table->foreignId('practice_question_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('question_order');
            $table->string('category', 50);
            $table->text('question_text');
            $table->text('option_a');
            $table->text('option_b');
            $table->text('option_c');
            $table->text('option_d');
            $table->string('correct_answer', 1);
            $table->string('user_answer', 1)->nullable();
            $table->boolean('is_correct')->default(false);
            $table->string('question_hash', 64)->index();
            $table->json('question_snapshot')->nullable();
            $table->timestamps();

            $table->index(['practice_result_id', 'question_order']);
        });

        Schema::create('practice_question_reviews', function (Blueprint $table) {
            $table->id();
            $table->string('question_hash', 64)->unique();
            $table->json('question_snapshot');
            $table->json('ai_review_json')->nullable();
            $table->longText('ai_review_text')->nullable();
            $table->string('ai_model_used')->nullable();
            $table->timestamp('ai_generated_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('practice_review_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('practice_result_item_id')->constrained('practice_result_items')->cascadeOnDelete();
            $table->string('question_hash', 64)->index();
            $table->boolean('from_cache')->default(false);
            $table->boolean('generated')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'generated', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practice_review_usages');
        Schema::dropIfExists('practice_question_reviews');
        Schema::dropIfExists('practice_result_items');
    }
};
