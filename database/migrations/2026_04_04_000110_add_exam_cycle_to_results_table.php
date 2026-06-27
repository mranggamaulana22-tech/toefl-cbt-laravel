<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->unsignedInteger('exam_cycle')->nullable()->after('user_id');
            $table->index('exam_cycle');
        });
    }

    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropIndex(['exam_cycle']);
            $table->dropColumn('exam_cycle');
        });
    }
};
