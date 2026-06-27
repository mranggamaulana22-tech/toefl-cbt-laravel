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
        Schema::create('practice_progresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('question_ids')->nullable();
            $table->json('answers')->nullable();
            $table->unsignedSmallInteger('active_question')->default(0);
            $table->integer('time_left')->default(7200);
            $table->unsignedTinyInteger('tab_violation_count')->default(0);
            $table->timestamps();

            $table->unique('user_id');
            $table->index(['user_id', 'updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practice_progresses');
    }
};
