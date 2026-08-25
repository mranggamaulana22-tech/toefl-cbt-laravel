<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menyimpan paket soal mana yang dipilih admin saat membuka sesi ujian
     * (bersamaan dengan generate access_code / passkey). Nempel di
     * exam_settings karena itu singleton yang sama tempat access_code
     * disimpan — tidak mengubah mekanisme passkey yang sudah berjalan.
     *
     * restrictOnDelete(): paket yang pernah dipakai sesi ujian tidak boleh
     * terhapus begitu saja, supaya riwayat/audit tetap valid.
     */
    public function up(): void
    {
        Schema::table('exam_settings', function (Blueprint $table) {
            $table->foreignId('paket_soal_id')
                ->nullable()
                ->after('current_cycle')
                ->constrained('paket_soals')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('exam_settings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('paket_soal_id');
        });
    }
};