<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create exam_sessions table to persist exam progress in database.
     * 
     * This replaces session-based storage for exam state, enabling:
     * - Resume exam from different device/browser
     * - Better data durability
     * - Audit trail of exam attempts
     * - Automatic cleanup via timestamps
     */
    public function up(): void
    {
        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_settings_id')->constrained()->cascadeOnDelete();
            $table->integer('exam_cycle');
            $table->json('question_ids'); // Ordered array of question IDs: [1, 5, 23, ...]
            $table->integer('current_question_index')->default(0); // 0-based index of current question
            $table->json('answers')->nullable(); // {question_id => user_answer, ...}
            $table->enum('status', ['in_progress', 'submitted', 'abandoned'])->default('in_progress');
            $table->timestamp('started_at');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('abandoned_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'exam_cycle']);
            $table->index(['exam_settings_id', 'status']);
            $table->index('created_at'); // For cleanup queries
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_sessions');
    }
};
