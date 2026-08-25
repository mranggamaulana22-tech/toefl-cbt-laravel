<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * exam_settings.paket_soal_id itu singleton yang nilainya berubah tiap
     * sesi baru dibuka — begitu admin ganti paket untuk sesi berikutnya,
     * jejak "hasil ujian si A pakai paket yang mana" akan hilang kalau
     * tidak dicatat di sini juga. Kolom ini snapshot permanen per attempt,
     * berguna untuk investigasi kalau ada kecurigaan kebocoran soal.
     *
     * set null on delete: kalau paket dihapus, histori hasil ujian tetap
     * ada (skor tidak boleh hilang), cuma referensi paketnya jadi null.
     */
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->foreignId('paket_soal_id')
                ->nullable()
                ->after('exam_cycle')
                ->constrained('paket_soals')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropConstrainedForeignId('paket_soal_id');
        });
    }
};