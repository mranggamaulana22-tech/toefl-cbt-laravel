<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->timestamp('started_at')->nullable()->after('exam_cycle');
            $table->timestamp('submitted_at')->nullable()->after('score_total');
        });
    }

    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropColumn(['started_at', 'submitted_at']);
        });
    }
};
