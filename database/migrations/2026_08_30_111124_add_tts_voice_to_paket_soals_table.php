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
        Schema::table('paket_soals', function (Blueprint $table) {
            $table->string('tts_voice_woman')->nullable()->after('nama');
            $table->string('tts_voice_man')->nullable()->after('tts_voice_woman');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paket_soals', function (Blueprint $table) {
            $table->dropColumn(['tts_voice_woman', 'tts_voice_man']);
        });
    }
};